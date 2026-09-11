<?php

/**
 * Thor Metal Art — Child Theme Functions
 *
 * @package ThorMetalArt
 * @since   1.0.0
 */

defined('ABSPATH') || exit;

/**
 * Return a cache-busting version for a theme asset.
 *
 * @param string $relative_path Path relative to the child theme directory.
 * @return string Theme version or asset modification timestamp.
 */
function tma_asset_version($relative_path)
{
	$asset_path = get_stylesheet_directory() . $relative_path;

	if (is_readable($asset_path)) {
		return (string) filemtime($asset_path);
	}

	return wp_get_theme()->get('Version');
}

/**
 * Enqueue parent and child theme styles.
 */
function tma_enqueue_styles()
{
	wp_enqueue_style(
		'twentytwentyfive-style',
		get_template_directory_uri() . '/style.css',
		array(),
		wp_get_theme('twentytwentyfive')->get('Version')
	);

	wp_enqueue_style(
		'thormetalart-style',
		get_stylesheet_directory_uri() . '/style.css',
		array('twentytwentyfive-style'),
		tma_asset_version('/style.css')
	);

	// Google Fonts: Cormorant Garamond + DM Sans.
	wp_enqueue_style(
		'thormetalart-fonts',
		'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@400;500;700&display=swap',
		array(),
		null
	);

	// Google Fonts: rediseño "Lujo Forjado" — Archivo Expanded + Fraunces + Inter.
	wp_enqueue_style(
		'thormetalart-forjado-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo+Expanded:wght@500;600;700;800&family=Fraunces:ital,wght@1,500;1,600&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null
	);
}
add_action('wp_enqueue_scripts', 'tma_enqueue_styles');

/**
 * Enqueue global navigation interactions.
 */
function tma_enqueue_navigation_script()
{
	wp_enqueue_script(
		'thormetalart-navigation',
		get_stylesheet_directory_uri() . '/assets/js/tma-navigation.js',
		array(),
		tma_asset_version('/assets/js/tma-navigation.js'),
		true
	);
}
add_action('wp_enqueue_scripts', 'tma_enqueue_navigation_script');

/**
 * Enqueue the "Lujo Forjado" homepage interactions script.
 *
 * Only loaded on the front page: header solid-on-scroll, scroll-reveal,
 * animated sparks and the infinite client-logos marquee only exist there.
 */
function tma_enqueue_forjado_script()
{
	if (! is_front_page()) {
		return;
	}

	wp_enqueue_script(
		'thormetalart-forjado',
		get_stylesheet_directory_uri() . '/assets/js/tma-forjado.js',
		array(),
		tma_asset_version('/assets/js/tma-forjado.js'),
		true
	);
}
add_action('wp_enqueue_scripts', 'tma_enqueue_forjado_script');

/**
 * Preload Google Fonts for better web performance (LCP).
 */
function tma_preload_fonts()
{
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
add_action('wp_head', 'tma_preload_fonts', 1);

/**
 * Output favicon tags — SVG (modern browsers) + PNG fallback (legacy/Apple).
 * favicon.svg: full THOR METAL ART wordmark on obsidian background, 512×512.
 */
function tma_favicon()
{
	$base     = get_stylesheet_directory_uri();
	$svg_url  = $base . '/assets/images/favicon.svg';
	$png_url  = $base . '/Logo.png';
	echo '<link rel="icon" type="image/svg+xml" href="' . esc_url($svg_url) . '">' . "\n";
	echo '<link rel="icon" type="image/png" href="' . esc_url($png_url) . '" sizes="any">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url($png_url) . '">' . "\n";
}
add_action('wp_head', 'tma_favicon', 1);

/**
 * Google Analytics 4 — gtag.js.
 *
 * Measurement ID loaded from wp-config constant or environment.
 * Disabled when WP_DEBUG is true to avoid polluting analytics during development.
 */
function tma_ga4_tracking()
{
	if (defined('WP_DEBUG') && WP_DEBUG) {
		return;
	}

	$measurement_id = defined('GA4_MEASUREMENT_ID') ? GA4_MEASUREMENT_ID : getenv('GA4_MEASUREMENT_ID');
	if (empty($measurement_id)) {
		return;
	}

	printf(
		'<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script>' . "\n"
			. '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}'
			. "gtag('js',new Date());gtag('config','%1\$s');</script>" . "\n",
		esc_attr($measurement_id)
	);
}
add_action('wp_head', 'tma_ga4_tracking', 2);

/**
 * Set up theme support.
 */
function tma_setup()
{
	load_child_theme_textdomain('thormetalart', get_stylesheet_directory() . '/languages');

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 280,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support('editor-styles');
	add_editor_style('style.css');
}
add_action('after_setup_theme', 'tma_setup');

/**
 * Register custom block pattern category.
 */
function tma_register_block_pattern_category()
{
	if (! function_exists('register_block_pattern_category')) {
		return;
	}

	register_block_pattern_category(
		'thormetalart',
		array(
			'label' => __('Thor Metal Art', 'thormetalart'),
		)
	);
}
add_action('init', 'tma_register_block_pattern_category');

/**
 * Custom excerpt length.
 *
 * @param int $length Default excerpt length.
 * @return int
 */
function tma_excerpt_length($length)
{
	return 30;
}
add_filter('excerpt_length', 'tma_excerpt_length');
/**
 * Compact language toggle for the header.
 *
 * Outputs a simple EN | ES pill instead of the full TranslatePress widget.
 * Uses TranslatePress URL converter when available.
 *
 * @return string HTML for the language toggle.
 */
function tma_lang_toggle_shortcode()
{
	$current_lang = 'en';
	$switch_url   = '/es/';
	$switch_label = 'ES';

	// Detect current language from URL.
	$request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '/';
	if (preg_match('#^/es(/|$)#', $request_uri)) {
		$current_lang = 'es';
		// Remove /es/ prefix to get English URL.
		$switch_url   = preg_replace('#^/es(/|$)#', '/', $request_uri);
		$switch_label = 'EN';
	} else {
		$switch_url = '/es' . $request_uri;
	}

	return sprintf(
		'<a class="tma-lang-pill" href="%s" data-no-translation>%s<span class="tma-lang-sep">|</span>%s</a>',
		esc_url($switch_url),
		esc_html($current_lang === 'en' ? 'EN' : 'ES'),
		esc_html($switch_label)
	);
}
add_shortcode('tma-lang-toggle', 'tma_lang_toggle_shortcode');

/**
 * Fallback translation for portfolio post titles in Spanish.
 *
 * TranslatePress does not always resolve custom post titles for tma_portfolio
 * reliably across environments, so we provide a lightweight slug-based map.
 *
 * @param string $title Existing title.
 * @param int    $post_id Post ID.
 * @return string
 */
function tma_translate_portfolio_title($title, $post_id = 0)
{
	if (is_admin() || empty($post_id)) {
		return $title;
	}

	$post = get_post($post_id);
	if (! $post || 'tma_portfolio' !== $post->post_type) {
		return $title;
	}

	$current_language = 'en';
	if (function_exists('trp_get_current_language')) {
		$current_language = trp_get_current_language();
	} elseif (defined('TRP_LANGUAGE')) {
		$current_language = TRP_LANGUAGE;
	} else {
		$uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
		if (preg_match('#^/es(/|$)#', $uri)) {
			$current_language = 'es';
		}
	}

	if ('es' !== $current_language && 'es_ES' !== $current_language && 'es-es' !== $current_language) {
		return $title;
	}

	$translations = array(
		'tig-welding-structural-work'            => 'Soldadura TIG — Trabajo Estructural en Acero',
		'artisan-blade-hand-forged'             => 'Cuchillo Artesanal — Forjado a Mano para Chef',
		'stainless-steel-custom-fabrication'     => 'Fabricación Personalizada en Acero Inoxidable',
		'forged-art-piece-twisted-form'         => 'Pieza Artística Forjada — Forma Retorcida',
		'decorative-water-jet-panel'            => 'Panel Decorativo con Chorro de Agua',
		'phoenix-metal-sculpture'                => 'Escultura de Metal Fénix',
		'lobby-art-commission-downtown-miami'   => 'Comisión de Arte para Lobby – Downtown Miami',
		'spiral-stair-installation-miami-lakes' => 'Instalación de Escalera de Caracol – Miami Lakes',
		'boutique-console-collection-midtown'   => 'Colección de Consolas para Boutique – Midtown',
		'decorative-fence-panels-coconut-grove' => 'Paneles de Cerca Decorativos – Coconut Grove',
		'pool-deck-railings-aventura'           => 'Barandales de Cubierta de Piscina – Aventura',
		'motorized-driveway-gate-kendall'       => 'Puerta de Entrada Motorizada – Kendall',
		'courtyard-sculpture-wynwood'           => 'Escultura de Patio – Wynwood',
		'floating-stair-structure-pinecrest'    => 'Estructura de Escalera Flotante – Pinecrest',
		'custom-dining-base-brickell'           => 'Base de Comedor Personalizada – Brickell',
		'perimeter-security-fence-doral'        => 'Cerca de Seguridad Perimetral – Doral',
		'balcony-railing-set-miami-beach'       => 'Conjunto de Barandales de Balcón – Miami Beach',
		'modern-entry-gate-coral-gables'        => 'Puerta de Entrada Moderna – Coral Gables',
	);

	if (isset($translations[$post->post_name])) {
		return $translations[$post->post_name];
	}

	return $title;
}
add_filter('the_title', 'tma_translate_portfolio_title', 20, 2);

/**
 * Noindex junk default WordPress pages/posts.
 *
 * Prevents the default "Sample Page" and "Hello World" from passing PageRank
 * to low-value content. Applies to both EN and ES (TranslatePress) variants.
 */
function tma_noindex_junk(): void
{
	$uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
	if (str_contains($uri, '/sample-page') || str_contains($uri, '/hello-world')) {
		echo '<meta name="robots" content="noindex, nofollow">' . "\n";
	}
}
add_action('wp_head', 'tma_noindex_junk', 1);

/**
 * Restrict author archive pages — only Karel Frometa (business owner) is public.
 * All other user author pages redirect to homepage (OWASP user enumeration prevention).
 */
add_action('template_redirect', function (): void {
	if (is_author()) {
		$author = get_queried_object();
		if (! $author instanceof WP_User || 'karel-frometa' !== $author->user_nicename) {
			wp_safe_redirect(home_url('/'), 301);
			exit;
		}
	}
});
