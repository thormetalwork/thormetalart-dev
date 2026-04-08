<?php
/**
 * TMA Panel — Document Pipeline
 *
 * Manages cached HTML docs and secure content serving for panel viewer.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Docs {

	/**
	 * Absolute path to plugin HTML cache.
	 */
	private const CACHE_DIR = TMA_PANEL_PATH . 'cache/html/';

	/**
	 * Migrate legacy portal docs into plugin cache.
	 */
	public static function migrate_portal_docs_to_cache(): void {
		if ( ! is_dir( self::CACHE_DIR ) ) {
			wp_mkdir_p( self::CACHE_DIR );
		}

		$base_dir    = dirname( dirname( dirname( dirname( dirname( TMA_PANEL_PATH ) ) ) ) );
		$source_dirs = array(
			$base_dir . '/portal/docs/',
			$base_dir . '/_archive/portal/docs/',
		);

		$source_dir = '';
		foreach ( $source_dirs as $dir ) {
			if ( is_dir( $dir ) ) {
				$source_dir = $dir;
				break;
			}
		}

		if ( '' === $source_dir ) {
			return;
		}

		$files = glob( $source_dir . '*.html' );
		if ( empty( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			$target = self::CACHE_DIR . basename( $file );
			if ( ! file_exists( $target ) ) {
				copy( $file, $target ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_copy
			}
		}
	}

	/**
	 * Get HTML content for a document code from cache.
	 *
	 * @param string $code Document code/slug (without .html).
	 * @return array|WP_Error
	 */
	public static function get_document_content( string $code ) {
		$clean_code = sanitize_title( $code );
		if ( '' === $clean_code ) {
			return new WP_Error( 'invalid_doc_code', __( 'Invalid document code.', 'thormetalart' ), array( 'status' => 400 ) );
		}

		$file = self::resolve_cached_file( $clean_code );
		if ( ! file_exists( $file ) ) {
			return new WP_Error( 'doc_not_found', __( 'Document not found.', 'thormetalart' ), array( 'status' => 404 ) );
		}

		$content = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $content || '' === trim( $content ) ) {
			return new WP_Error( 'doc_empty', __( 'Document is empty.', 'thormetalart' ), array( 'status' => 500 ) );
		}

		return array(
			'code'       => $clean_code,
			'html'       => wp_kses_post( $content ),
			'updated_at' => gmdate( 'Y-m-d H:i:s', filemtime( $file ) ),
		);
	}

	/**
	 * Resolve cached document file path by slug.
	 * Supports both current slug-based names and legacy prefixed names (01_slug.html).
	 *
	 * @param string $clean_code Sanitized slug.
	 * @return string
	 */
	private static function resolve_cached_file( string $clean_code ): string {
		$direct = self::CACHE_DIR . $clean_code . '.html';
		if ( file_exists( $direct ) ) {
			return $direct;
		}

		$matches = glob( self::CACHE_DIR . '*_' . $clean_code . '.html' );
		if ( ! empty( $matches ) ) {
			return (string) $matches[0];
		}

		return $direct;
	}
}
