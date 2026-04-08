<?php
/**
 * Thor Metal Art — XML Sitemap
 *
 * Lightweight dynamic sitemap for pages + portfolio.
 *
 * @package ThorMetalArt
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register rewrite rules.
 */
function tma_register_sitemap_rewrite() {
	add_rewrite_rule( '^sitemap\.xml/?$', 'index.php?tma_sitemap=1', 'top' );
}
add_action( 'init', 'tma_register_sitemap_rewrite' );

/**
 * Register query vars.
 *
 * @param array $vars Existing vars.
 * @return array
 */
function tma_sitemap_query_vars( $vars ) {
	$vars[] = 'tma_sitemap';
	return $vars;
}
add_filter( 'query_vars', 'tma_sitemap_query_vars' );

/**
 * Render XML sitemap.
 */
function tma_render_sitemap_xml() {
	$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
	$is_path_hit = ( false !== strpos( $request_uri, '/sitemap.xml' ) );

	if ( '1' !== get_query_var( 'tma_sitemap' ) && ! $is_path_hit ) {
		return;
	}

	$urls = array();

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		)
	);

	foreach ( $pages as $page ) {
		$urls[] = array(
			'loc'     => get_permalink( $page ),
			'lastmod' => gmdate( 'c', strtotime( $page->post_modified_gmt ?: $page->post_modified ) ),
		);
	}

	$portfolio = get_posts(
		array(
			'post_type'      => 'tma_portfolio',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		)
	);

	$archive_link = get_post_type_archive_link( 'tma_portfolio' );
	if ( $archive_link ) {
		$urls[] = array(
			'loc'     => $archive_link,
			'lastmod' => gmdate( 'c' ),
		);
	}

	foreach ( $portfolio as $project ) {
		$urls[] = array(
			'loc'     => get_permalink( $project ),
			'lastmod' => gmdate( 'c', strtotime( $project->post_modified_gmt ?: $project->post_modified ) ),
		);
	}

	header( 'Content-Type: application/xml; charset=UTF-8' );
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	foreach ( $urls as $url ) {
		echo '<url>';
		echo '<loc>' . esc_url( $url['loc'] ) . '</loc>';
		echo '<lastmod>' . esc_html( $url['lastmod'] ) . '</lastmod>';
		echo '</url>';
	}
	echo '</urlset>';
	exit;
}
add_action( 'template_redirect', 'tma_render_sitemap_xml' );

/**
 * Prevent canonical redirect for sitemap endpoint.
 *
 * @param string|false $redirect_url Redirect URL.
 * @return string|false
 */
function tma_disable_sitemap_canonical_redirect( $redirect_url ) {
	$request_uri = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ) );
	if ( false !== strpos( $request_uri, '/sitemap.xml' ) ) {
		return false;
	}

	return $redirect_url;
}
add_filter( 'redirect_canonical', 'tma_disable_sitemap_canonical_redirect' );

/**
 * Flush rewrite once when plugin first runs.
 */
function tma_maybe_flush_sitemap_rules() {
	$flag = get_option( 'tma_sitemap_rules_flushed', false );
	if ( $flag ) {
		return;
	}

	tma_register_sitemap_rewrite();
	flush_rewrite_rules( false );
	update_option( 'tma_sitemap_rules_flushed', 1, false );
}
add_action( 'init', 'tma_maybe_flush_sitemap_rules', 20 );