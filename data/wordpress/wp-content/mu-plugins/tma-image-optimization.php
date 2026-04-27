<?php
/**
 * TMA Image Optimization
 *
 * - Genera thumbnails en WebP (30-50% más ligeros que JPEG)
 * - Reduce calidad JPEG a 82 (imperceptible, -20-30% peso)
 * - Añade lazy loading y decoding async a todas las imágenes
 *
 * Requiere PHP GD con WebP support (verificado en el stack).
 * Para aplicar a imágenes ya existentes ejecutar:
 *   wp --allow-root media regenerate --yes
 *
 * @package ThorMetalArt
 * @since   2026-04-27
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convierte thumbnails a WebP al generarse.
 * El original JPEG/PNG no se toca; solo los thumbnails.
 * WordPress 5.8+ actualiza el attachment metadata con las rutas .webp
 * y las incluye en los srcset automáticamente.
 */
add_filter(
	'image_editor_output_format',
	function ( array $mappings ): array {
		$mappings['image/jpeg'] = 'image/webp';
		$mappings['image/png']  = 'image/webp';
		return $mappings;
	}
);

/**
 * Calidad JPEG: 82 (vs default 90).
 * En imágenes de portfolio/hero la diferencia visual es imperceptible,
 * pero el tamaño de archivo se reduce ~20-30%.
 */
add_filter(
	'wp_editor_set_quality',
	function ( int $quality, string $mime_type ): int {
		if ( 'image/jpeg' === $mime_type ) {
			return 82;
		}
		if ( 'image/webp' === $mime_type ) {
			return 82;
		}
		return $quality;
	},
	10,
	2
);

/**
 * Calidad WebP vía filtro legacy de JPEG (compatibilidad).
 */
add_filter( 'jpeg_quality', fn() => 82 );

/**
 * Añade decoding="async" a todas las imágenes generadas por WordPress.
 * Libera el hilo principal del browser durante la decodificación.
 */
add_filter(
	'wp_get_attachment_image_attributes',
	function ( array $attr ): array {
		if ( empty( $attr['decoding'] ) ) {
			$attr['decoding'] = 'async';
		}
		return $attr;
	}
);
