<?php
/**
 * Thor Metal Art — Meta Tags & Open Graph
 *
 * SEO metadata aligned with Website V1 copy deck.
 *
 * @package ThorMetalArt
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve current language code (en|es) with TranslatePress fallback.
 *
 * @return string
 */
if ( ! function_exists( 'tma_get_current_language_code' ) ) {
function tma_get_current_language_code() {
	if ( function_exists( 'trp_get_current_language' ) ) {
		$current = (string) trp_get_current_language();
		if ( 0 === strpos( strtolower( $current ), 'es' ) ) {
			return 'es';
		}
		return 'en';
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$request_uri = strtolower( $request_uri );

	if ( '/es' === $request_uri || 0 === strpos( $request_uri, '/es/' ) ) {
		return 'es';
	}

	return 'en';
}
}

/**
 * Return current request URL preserving language prefix.
 *
 * @return string
 */
function tma_get_current_request_url() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$path        = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
	$path        = ! empty( $path ) ? $path : '/';

	return tma_build_absolute_url( $path );
}

/**
 * Build absolute URL without language plugin URL filters.
 *
 * @param string $path Site-relative path.
 * @return string
 */
function tma_build_absolute_url( $path ) {
	$base = rtrim( (string) get_option( 'home' ), '/' );
	$path = '/' . ltrim( (string) $path, '/' );

	return $base . $path;
}

/**
 * Resolve EN and ES language URLs for current request path.
 *
 * @return array<string, string>
 */
function tma_get_language_alternate_urls() {
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$path        = (string) wp_parse_url( $request_uri, PHP_URL_PATH );
	$path        = ! empty( $path ) ? $path : '/';

	if ( '/es' === $path || 0 === strpos( $path, '/es/' ) ) {
		$en_path = '/' . ltrim( substr( $path, 3 ), '/' );
		$en_path = '/' === $en_path ? '/' : untrailingslashit( $en_path ) . '/';
		$es_path = '/es/' === $path || '/es' === $path ? '/es/' : untrailingslashit( $path ) . '/';
	} else {
		$en_path = '/' === $path ? '/' : untrailingslashit( $path ) . '/';
		$es_path = '/' === $path ? '/es/' : '/es' . untrailingslashit( $path ) . '/';
	}

	return array(
		'en' => tma_build_absolute_url( $en_path ),
		'es' => tma_build_absolute_url( $es_path ),
	);
}

/**
 * Return SEO mapping by page slug.
 *
 * @return array<string, array<string, string>>
 */
function tma_get_seo_mapping() {
	return array(
		'home' => array(
			'title'       => 'Thor Metal Art Miami | Custom Metal Fabrication & Artistic Metalwork',
			'description' => 'Custom metal gates, railings, fences, furniture and sculpture in Miami. Exclusive design, free estimates, licensed and insured. Serving Miami-Dade and Broward.',
		),
		'custom-metal-gates-miami' => array(
			'title'       => 'Custom Metal Gates Miami | Thor Metal Art',
			'description' => 'Handcrafted custom metal gates for residential and commercial properties in Miami. Water jet precision and artistic finish with free estimates.',
		),
		'metal-railings-miami' => array(
			'title'       => 'Metal Railings Miami | Custom Design | Thor Metal Art',
			'description' => 'Custom metal railings for stairs, balconies and porches in Miami. Every railing is designed and fabricated to order with code-compliant installation.',
		),
		'metal-fences-miami' => array(
			'title'       => 'Custom Metal Fences Miami | Thor Metal Art',
			'description' => 'Decorative and security metal fences custom-designed for Miami homes and businesses. Durable, beautiful, and built to last.',
		),
		'custom-metal-furniture-miami' => array(
			'title'       => 'Custom Metal Furniture Miami | Thor Metal Art',
			'description' => 'Unique custom metal furniture designed and fabricated in Miami. Tables, shelves, beds and one-of-a-kind pieces built to your specifications.',
		),
		'metal-stairs-miami' => array(
			'title'       => 'Metal Stairs Miami | Modern Design | Thor Metal Art',
			'description' => 'Custom metal stairs and handrails for residential and commercial spaces in Miami. Floating, spiral and industrial designs built for impact.',
		),
		'art-commissions' => array(
			'title'       => 'Metal Sculpture & Art Miami | Thor Metal Art',
			'description' => 'Original metal sculptures and commissioned art by Miami-based artist Karel Frometa. Residential, commercial and hospitality installations.',
		),
		'how-we-work' => array(
			'title'       => 'How We Work | Custom Metal Fabrication Process | Thor Metal Art',
			'description' => 'From concept to installation: design, water jet cutting, fabrication, finishing and delivery by our Miami-based team.',
		),
		'contact' => array(
			'title'       => 'Contact Thor Metal Art Miami | Free Estimate',
			'description' => 'Get a free estimate from Thor Metal Art. Custom metal fabrication and art in Miami. Call, WhatsApp, or send your project details.',
		),
		'portfolio' => array(
			'title'       => 'Metal Work Portfolio Miami | Thor Metal Art',
			'description' => 'Portfolio of custom gates, railings, fences, stairs, furniture and artistic metalwork projects built in Miami.',
		),
	);
}

/**
 * Return Spanish SEO mapping by page slug.
 *
 * @return array<string, array<string, string>>
 */
function tma_get_seo_mapping_es() {
	return array(
		'home' => array(
			'title'       => 'Thor Metal Art Miami | Fabricacion de Metal Personalizada y Arte en Metal',
			'description' => 'Portones, barandas, cercas, muebles y escultura en metal en Miami. Diseno exclusivo, estimados gratis, licenciado y asegurado.',
		),
		'custom-metal-gates-miami' => array(
			'title'       => 'Portones de Metal Personalizados Miami | Thor Metal Art',
			'description' => 'Portones de metal personalizados para residencias y comercios en Miami. Precision water jet y acabado artistico con estimados gratis.',
		),
		'metal-railings-miami' => array(
			'title'       => 'Barandas de Metal Miami | Diseno Personalizado | Thor Metal Art',
			'description' => 'Barandas de metal para escaleras, balcones y terrazas en Miami. Diseno a medida e instalacion conforme a codigo.',
		),
		'metal-fences-miami' => array(
			'title'       => 'Cercas de Metal Personalizadas Miami | Thor Metal Art',
			'description' => 'Cercas de metal decorativas y de seguridad para casas y negocios en Miami. Duraderas, elegantes y hechas para resistir.',
		),
		'custom-metal-furniture-miami' => array(
			'title'       => 'Muebles de Metal Personalizados Miami | Thor Metal Art',
			'description' => 'Muebles unicos de metal disenados y fabricados en Miami. Mesas, estanterias y piezas especiales hechas a medida.',
		),
		'metal-stairs-miami' => array(
			'title'       => 'Escaleras de Metal Miami | Diseno Moderno | Thor Metal Art',
			'description' => 'Escaleras de metal y pasamanos para espacios residenciales y comerciales en Miami. Disenos flotantes, espirales e industriales.',
		),
		'art-commissions' => array(
			'title'       => 'Escultura y Arte en Metal Miami | Thor Metal Art',
			'description' => 'Esculturas originales en metal y comisiones artisticas por Karel Frometa en Miami para proyectos residenciales y comerciales.',
		),
		'how-we-work' => array(
			'title'       => 'Como Trabajamos | Proceso de Fabricacion de Metal | Thor Metal Art',
			'description' => 'Del concepto a la instalacion: diseno, corte water jet, fabricacion, acabado y entrega por nuestro equipo en Miami.',
		),
		'contact' => array(
			'title'       => 'Contacto Thor Metal Art Miami | Estimado Gratis',
			'description' => 'Solicita un estimado gratis en Thor Metal Art. Fabricacion de metal y arte en Miami. Llamanos, WhatsApp o envia tu proyecto.',
		),
		'portfolio' => array(
			'title'       => 'Portafolio de Trabajos en Metal Miami | Thor Metal Art',
			'description' => 'Portafolio de portones, barandas, cercas, escaleras, muebles y arte en metal fabricados en Miami.',
		),
	);
}

/**
 * Resolve SEO mapping for current request.
 *
 * @return array<string, string>
 */
function tma_get_current_seo_data() {
	$map = tma_get_seo_mapping();
	$map_es = tma_get_seo_mapping_es();
	$lang = tma_get_current_language_code();

	$resolve_map = static function( $slug ) use ( $map, $map_es, $lang ) {
		if ( 'es' === $lang && isset( $map_es[ $slug ] ) ) {
			return $map_es[ $slug ];
		}

		return isset( $map[ $slug ] ) ? $map[ $slug ] : array();
	};

	if ( is_front_page() ) {
		return $resolve_map( 'home' );
	}

	if ( is_singular( 'tma_portfolio' ) ) {
		$post = get_queried_object();

		if ( ! empty( $post ) && isset( $post->post_content ) ) {
			$title_prefix = 'es' === $lang ? 'Proyecto de Portafolio' : 'Portfolio Project';
			return array(
				'title'       => get_the_title( $post ) . ' | ' . $title_prefix . ' | Thor Metal Art',
				'description' => has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '...' ),
			);
		}
	}

	if ( is_post_type_archive( 'tma_portfolio' ) || is_tax( 'tma_project_type' ) ) {
		return $resolve_map( 'portfolio' );
	}

	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( isset( $map[ $slug ] ) || isset( $map_es[ $slug ] ) ) {
			return $resolve_map( $slug );
		}
	}

	if ( is_singular() ) {
		$post = get_queried_object();
		return array(
			'title'       => get_the_title( $post ) . ' | Thor Metal Art',
			'description' => has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28, '...' ),
		);
	}

	return array(
		'title'       => 'Thor Metal Art',
		'description' => 'Custom metal fabrication and artistic metalwork in Miami-Dade.',
	);
}

/**
 * Override document title.
 *
 * @param string $title Current title.
 * @return string
 */
function tma_filter_document_title( $title ) {
	$seo = tma_get_current_seo_data();
	return $seo['title'] ?: $title;
}
add_filter( 'pre_get_document_title', 'tma_filter_document_title' );

/**
 * Output standard meta description.
 */
function tma_meta_description() {
	$seo = tma_get_current_seo_data();
	if ( empty( $seo['description'] ) ) {
		return;
	}

	printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $seo['description'] ) );
}
add_action( 'wp_head', 'tma_meta_description', 3 );

/**
 * Output Open Graph and Twitter tags.
 */
function tma_open_graph_tags() {
	$seo = tma_get_current_seo_data();
	$lang = tma_get_current_language_code();
	$url = tma_get_current_request_url();

	$og = array(
		'og:site_name'   => 'Thor Metal Art',
		'og:locale'      => 'es' === $lang ? 'es_ES' : 'en_US',
		'og:type'        => is_singular() ? 'article' : 'website',
		'og:title'       => $seo['title'],
		'og:description' => $seo['description'],
		'og:url'         => $url,
	);

	if ( is_singular() && has_post_thumbnail() ) {
		$og['og:image'] = get_the_post_thumbnail_url( null, 'large' );
	}

	foreach ( $og as $property => $content ) {
		if ( ! empty( $content ) ) {
			printf( '<meta property="%s" content="%s" />' . "\n", esc_attr( $property ), esc_attr( $content ) );
		}
	}

	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $seo['title'] ) );
	printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $seo['description'] ) );
	if ( ! empty( $og['og:image'] ) ) {
		printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_attr( $og['og:image'] ) );
	}
}
add_action( 'wp_head', 'tma_open_graph_tags', 4 );

/**
 * Add hreflang hints for bilingual pages.
 */
function tma_hreflang_tags() {
	if ( ! is_front_page() && ! is_page() && ! is_singular( 'tma_portfolio' ) && ! is_post_type_archive( 'tma_portfolio' ) && ! is_tax( 'tma_project_type' ) ) {
		return;
	}

	$urls = tma_get_language_alternate_urls();

	printf( '<link rel="alternate" hreflang="en" href="%s" />' . "\n", esc_url( $urls['en'] ) );
	printf( '<link rel="alternate" hreflang="es" href="%s" />' . "\n", esc_url( $urls['es'] ) );
	printf( '<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url( $urls['en'] ) );
}
add_action( 'wp_head', 'tma_hreflang_tags', 5 );

/**
 * Canonical URL.
 */
function tma_canonical_url() {
	if ( is_front_page() || is_page() || is_singular() || is_post_type_archive( 'tma_portfolio' ) || is_tax( 'tma_project_type' ) ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( tma_get_current_request_url() ) );
	}
}
add_action( 'wp_head', 'tma_canonical_url', 6 );
