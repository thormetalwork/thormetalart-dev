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
 * Resolve SEO mapping for current request.
 *
 * @return array<string, string>
 */
function tma_get_current_seo_data() {
	$map = tma_get_seo_mapping();

	if ( is_front_page() ) {
		return $map['home'];
	}

	if ( is_post_type_archive( 'tma_portfolio' ) || is_singular( 'tma_portfolio' ) || is_tax( 'tma_project_type' ) ) {
		return $map['portfolio'];
	}

	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( isset( $map[ $slug ] ) ) {
			return $map[ $slug ];
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
	$url = home_url( '/' );
	if ( is_singular() ) {
		$url = get_permalink();
	} elseif ( is_post_type_archive( 'tma_portfolio' ) ) {
		$url = get_post_type_archive_link( 'tma_portfolio' );
	}

	$og = array(
		'og:site_name'   => 'Thor Metal Art',
		'og:locale'      => 'en_US',
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
	if ( ! is_page() && ! is_singular( 'tma_portfolio' ) ) {
		return;
	}

	$url = get_permalink();
	printf( '<link rel="alternate" hreflang="en" href="%s" />' . "\n", esc_url( $url ) );
	printf( '<link rel="alternate" hreflang="es" href="%s" />' . "\n", esc_url( $url ) );
	printf( '<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url( $url ) );
}
add_action( 'wp_head', 'tma_hreflang_tags', 5 );

/**
 * Canonical URL.
 */
function tma_canonical_url() {
	if ( is_front_page() ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( home_url( '/' ) ) );
		return;
	}

	if ( is_singular() ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( get_permalink() ) );
		return;
	}

	if ( is_post_type_archive( 'tma_portfolio' ) ) {
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( get_post_type_archive_link( 'tma_portfolio' ) ) );
	}
}
add_action( 'wp_head', 'tma_canonical_url', 6 );