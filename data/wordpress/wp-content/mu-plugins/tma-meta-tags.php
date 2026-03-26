<?php
/**
 * Thor Metal Art — Meta Tags & Open Graph
 *
 * Dynamic meta description, Open Graph, and Twitter Card tags.
 *
 * @package ThorMetalArt
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output meta description tag.
 */
function tma_meta_description() {
$desc = '';

if ( is_front_page() ) {
$desc = 'Thor Metal Art — Custom metal fabrication in Miami-Dade. Gates, railings, fences, furniture, stairs & art sculptures. Handcrafted quality.';
} elseif ( is_singular() ) {
$post = get_queried_object();
$desc = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 25, '...' );
} elseif ( is_post_type_archive( 'tma_portfolio' ) ) {
$desc = 'Portfolio of custom metal fabrication projects by Thor Metal Art in Miami-Dade, FL. Gates, railings, fences, furniture & sculptures.';
} elseif ( is_tax( 'tma_project_type' ) ) {
$term = get_queried_object();
$desc = $term->description ?: sprintf( 'Custom %s projects by Thor Metal Art in Miami, FL.', $term->name );
}

if ( $desc ) {
printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $desc ) );
}
}
add_action( 'wp_head', 'tma_meta_description', 3 );

/**
 * Output Open Graph and Twitter Card meta tags.
 */
function tma_open_graph_tags() {
$og = array(
'og:site_name' => 'Thor Metal Art',
'og:locale'    => 'en_US',
'og:type'      => 'website',
);

if ( is_front_page() ) {
$og['og:title']       = 'Thor Metal Art — Custom Metal Fabrication Miami';
$og['og:description'] = 'Handcrafted gates, railings, fences, furniture, stairs & art sculptures in Miami-Dade, FL.';
$og['og:url']         = home_url( '/' );
} elseif ( is_singular() ) {
$post                 = get_queried_object();
$og['og:title']       = get_the_title( $post ) . ' — Thor Metal Art';
$og['og:description'] = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 25, '...' );
$og['og:url']         = get_permalink( $post );
$og['og:type']        = 'article';

if ( has_post_thumbnail( $post ) ) {
$img_url              = get_the_post_thumbnail_url( $post, 'large' );
$og['og:image']       = $img_url;
$og['og:image:width'] = '1200';
$og['og:image:height'] = '630';
}
} elseif ( is_post_type_archive( 'tma_portfolio' ) ) {
$og['og:title']       = 'Portfolio — Thor Metal Art';
$og['og:description'] = 'Custom metal fabrication projects in Miami-Dade, FL.';
$og['og:url']         = get_post_type_archive_link( 'tma_portfolio' );
}

// Output OG tags.
foreach ( $og as $property => $content ) {
if ( $content ) {
printf( '<meta property="%s" content="%s" />' . "\n", esc_attr( $property ), esc_attr( $content ) );
}
}

// Twitter Card.
echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
if ( ! empty( $og['og:title'] ) ) {
printf( '<meta name="twitter:title" content="%s" />' . "\n", esc_attr( $og['og:title'] ) );
}
if ( ! empty( $og['og:description'] ) ) {
printf( '<meta name="twitter:description" content="%s" />' . "\n", esc_attr( $og['og:description'] ) );
}
if ( ! empty( $og['og:image'] ) ) {
printf( '<meta name="twitter:image" content="%s" />' . "\n", esc_attr( $og['og:image'] ) );
}
}
add_action( 'wp_head', 'tma_open_graph_tags', 4 );

/**
 * Add alternate hreflang hint for bilingual content.
 */
function tma_hreflang_tags() {
if ( ! is_singular() ) {
return;
}
$url = get_permalink();
printf( '<link rel="alternate" hreflang="en" href="%s" />' . "\n", esc_url( $url ) );
printf( '<link rel="alternate" hreflang="es" href="%s" />' . "\n", esc_url( $url ) );
printf( '<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url( $url ) );
}
add_action( 'wp_head', 'tma_hreflang_tags', 5 );

/**
 * Output canonical URL.
 */
function tma_canonical_url() {
if ( is_singular() ) {
printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( get_permalink() ) );
} elseif ( is_front_page() ) {
printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( home_url( '/' ) ) );
} elseif ( is_post_type_archive( 'tma_portfolio' ) ) {
printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( get_post_type_archive_link( 'tma_portfolio' ) ) );
}
}
add_action( 'wp_head', 'tma_canonical_url', 6 );
