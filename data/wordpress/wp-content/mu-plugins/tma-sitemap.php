<?php

/**
 * Thor Metal Art — XML Sitemap
 *
 * Lightweight dynamic sitemap for pages + portfolio.
 *
 * @package ThorMetalArt
 */

defined('ABSPATH') || exit;

/**
 * Register rewrite rules.
 */
function tma_register_sitemap_rewrite()
{
	add_rewrite_rule('^sitemap\.xml/?$', 'index.php?tma_sitemap=1', 'top');
}
add_action('init', 'tma_register_sitemap_rewrite');

/**
 * Register query vars.
 *
 * @param array $vars Existing vars.
 * @return array
 */
function tma_sitemap_query_vars($vars)
{
	$vars[] = 'tma_sitemap';
	return $vars;
}
add_filter('query_vars', 'tma_sitemap_query_vars');

/**
 * Disable WordPress core sitemaps to keep a single canonical sitemap source.
 *
 * @return bool
 */
function tma_disable_core_sitemaps()
{
	return false;
}
add_filter('wp_sitemaps_enabled', 'tma_disable_core_sitemaps');

/**
 * Render XML sitemap.
 */
function tma_render_sitemap_xml()
{
	$request_uri = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? ''));
	$is_path_hit = (false !== strpos($request_uri, '/sitemap.xml'));

	if ('1' !== get_query_var('tma_sitemap') && ! $is_path_hit) {
		return;
	}

	$urls = array();

	// Slugs that should never appear in the sitemap.
	$excluded_slugs = array(
		'sample-page',
		'privacy-policy',
		'hello-world',
	);

	$pages = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		)
	);

	foreach ($pages as $page) {
		if (in_array($page->post_name, $excluded_slugs, true)) {
			continue;
		}
		$urls[] = array(
			'loc'     => get_permalink($page),
			'lastmod' => gmdate('c', strtotime($page->post_modified_gmt ?: $page->post_modified)),
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

	$archive_link = get_post_type_archive_link('tma_portfolio');
	if ($archive_link) {
		$urls[] = array(
			'loc'     => $archive_link,
			'lastmod' => gmdate('c'),
		);
	}

	foreach ($portfolio as $project) {
		$urls[] = array(
			'loc'     => get_permalink($project),
			'lastmod' => gmdate('c', strtotime($project->post_modified_gmt ?: $project->post_modified)),
		);
	}

	$posts = get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		)
	);

	foreach ($posts as $post) {
		if (in_array($post->post_name, $excluded_slugs, true)) {
			continue;
		}

		$urls[] = array(
			'loc'     => get_permalink($post),
			'lastmod' => gmdate('c', strtotime($post->post_modified_gmt ?: $post->post_modified)),
		);
	}

	header('Content-Type: application/xml; charset=UTF-8');
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
	foreach ($urls as $url) {
		echo '<url>';
		echo '<loc>' . esc_url($url['loc']) . '</loc>';
		echo '<lastmod>' . esc_html($url['lastmod']) . '</lastmod>';
		echo '</url>';
	}
	echo '</urlset>';
	exit;
}
add_action('template_redirect', 'tma_render_sitemap_xml');

/**
 * Prevent canonical redirect for sitemap endpoint.
 *
 * @param string|false $redirect_url Redirect URL.
 * @return string|false
 */
function tma_disable_sitemap_canonical_redirect($redirect_url)
{
	$request_uri = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? ''));
	if (false !== strpos($request_uri, '/sitemap.xml')) {
		return false;
	}

	return $redirect_url;
}
add_filter('redirect_canonical', 'tma_disable_sitemap_canonical_redirect');

/**
 * Redirect core sitemap endpoints to the canonical custom sitemap.
 */
function tma_redirect_core_sitemaps()
{
	$request_uri = sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'] ?? ''));
	$path        = (string) wp_parse_url($request_uri, PHP_URL_PATH);

	if (preg_match('#^/wp-sitemap(?:-[^/]+)?\.xml$#', $path)) {
		wp_safe_redirect(home_url('/sitemap.xml'), 301);
		exit;
	}
}
add_action('template_redirect', 'tma_redirect_core_sitemaps', 1);

/**
 * Ensure robots.txt announces the canonical sitemap URL.
 *
 * @param string $output Current robots body.
 * @param bool   $public Whether the site is public.
 * @return string
 */
function tma_robots_txt_sitemap($output, $public)
{
	if (! $public) {
		return $output;
	}

	$lines = preg_split('/\r\n|\r|\n/', (string) $output);
	$lines = array_filter(
		$lines,
		static function ($line) {
			return 0 !== stripos(trim((string) $line), 'Sitemap:');
		}
	);

	$lines[] = 'Sitemap: ' . home_url('/sitemap.xml');

	return implode("\n", $lines) . "\n";
}
add_filter('robots_txt', 'tma_robots_txt_sitemap', 99, 2);

/**
 * Flush rewrite once when plugin first runs.
 */
function tma_maybe_flush_sitemap_rules()
{
	$flag = get_option('tma_sitemap_rules_flushed', false);
	if ($flag) {
		return;
	}

	tma_register_sitemap_rewrite();
	flush_rewrite_rules(false);
	update_option('tma_sitemap_rules_flushed', 1, false);
}
add_action('init', 'tma_maybe_flush_sitemap_rules', 20);
