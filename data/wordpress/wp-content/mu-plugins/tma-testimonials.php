<?php
/**
 * Thor Metal Art — Testimonials
 *
 * Adds testimonial CPT and shortcode rendering.
 *
 * @package ThorMetalArt
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register testimonial post type.
 */
function tma_register_testimonials_cpt() {
	$labels = array(
		'name'          => __( 'Testimonials', 'thormetalart' ),
		'singular_name' => __( 'Testimonial', 'thormetalart' ),
		'add_new_item'  => __( 'Add New Testimonial', 'thormetalart' ),
		'edit_item'     => __( 'Edit Testimonial', 'thormetalart' ),
	);

	register_post_type(
		'tma_testimonial',
		array(
			'labels'       => $labels,
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'supports'     => array( 'title', 'editor' ),
		)
	);
}
add_action( 'init', 'tma_register_testimonials_cpt' );

/**
 * Render testimonial cards.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function tma_shortcode_testimonials( $atts ) {
	$atts = shortcode_atts(
		array(
			'limit'   => 3,
			'service' => '',
		),
		$atts,
		'tma_testimonials'
	);

	$args = array(
		'post_type'      => 'tma_testimonial',
		'posts_per_page' => max( 1, absint( $atts['limit'] ) ),
		'post_status'    => 'publish',
	);

	if ( ! empty( $atts['service'] ) ) {
		$args['meta_query'] = array(
			array(
				'key'   => 'tma_service',
				'value' => sanitize_text_field( $atts['service'] ),
			),
		);
	}

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return '';
	}

	$out = '<div class="wp-block-columns tma-testimonials-grid">';
	while ( $query->have_posts() ) {
		$query->the_post();
		$client_name  = get_post_meta( get_the_ID(), 'tma_client_name', true );
		$project_type = get_post_meta( get_the_ID(), 'tma_project_type', true );
		$location     = get_post_meta( get_the_ID(), 'tma_location', true );
		$rating       = max( 1, min( 5, absint( get_post_meta( get_the_ID(), 'tma_rating', true ) ) ) );

		if ( 0 === $rating ) {
			$rating = 5;
		}

		$out .= '<div class="wp-block-column"><div class="tma-testimonial-card">';
		$out .= '<p>' . str_repeat( '★', $rating ) . '</p>';
		$out .= '<p>"' . esc_html( wp_strip_all_tags( get_the_content() ) ) . '"</p>';

		$meta_line = trim( implode( ' - ', array_filter( array( $client_name, $project_type, $location ) ) ) );
		if ( $meta_line ) {
			$out .= '<p><strong>' . esc_html( $meta_line ) . '</strong></p>';
		}

		$out .= '</div></div>';
	}
	$out .= '</div>';
	wp_reset_postdata();

	return $out;
}
add_shortcode( 'tma_testimonials', 'tma_shortcode_testimonials' );