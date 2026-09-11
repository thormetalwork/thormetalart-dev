<?php

/**
 * Thor Metal Art — Seed Content
 *
 * Creates starter portfolio and testimonials content for Website V1.
 *
 * @package ThorMetalArt
 */

defined('ABSPATH') || exit;

/**
 * Seed initial content once.
 */
function tma_seed_website_content()
{
	$seeded = get_option('tma_seed_content_v1', false);
	if ($seeded) {
		return;
	}

	$portfolio_count = wp_count_posts('tma_portfolio');
	if ($portfolio_count && ! empty($portfolio_count->publish)) {
		update_option('tma_seed_content_v1', 1, false);
		return;
	}

	$projects = array(
		array('title' => 'Modern Entry Gate - Coral Gables', 'type' => 'gates', 'material' => 'Powder-coated steel'),
		array('title' => 'Balcony Railing Set - Miami Beach', 'type' => 'railings', 'material' => 'Wrought iron'),
		array('title' => 'Perimeter Security Fence - Doral', 'type' => 'fences', 'material' => 'Galvanized steel'),
		array('title' => 'Custom Dining Base - Brickell', 'type' => 'furniture', 'material' => 'Steel + wood'),
		array('title' => 'Floating Stair Structure - Pinecrest', 'type' => 'stairs', 'material' => 'Steel + oak treads'),
		array('title' => 'Courtyard Sculpture - Wynwood', 'type' => 'art', 'material' => 'Mixed metal'),
		array('title' => 'Motorized Driveway Gate - Kendall', 'type' => 'gates', 'material' => 'Aluminum + steel frame'),
		array('title' => 'Pool Deck Railings - Aventura', 'type' => 'railings', 'material' => 'Stainless steel'),
		array('title' => 'Decorative Fence Panels - Coconut Grove', 'type' => 'fences', 'material' => 'Laser-cut steel'),
		array('title' => 'Boutique Console Collection - Midtown', 'type' => 'furniture', 'material' => 'Steel + stone top'),
		array('title' => 'Spiral Stair Installation - Miami Lakes', 'type' => 'stairs', 'material' => 'Steel with textured finish'),
		array('title' => 'Lobby Art Commission - Downtown Miami', 'type' => 'art', 'material' => 'Patinated steel'),
	);

	foreach ($projects as $index => $project) {
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'tma_portfolio',
				'post_status'  => 'draft',
				'post_title'   => $project['title'],
				'post_content' => 'Project summary placeholder. Replace with real project narrative and image gallery from client-delivered media assets.',
			)
		);

		if (! $post_id || is_wp_error($post_id)) {
			continue;
		}

		wp_set_post_terms($post_id, array($project['type']), 'tma_project_type', false);
		update_post_meta($post_id, 'tma_project_location', 'Miami-Dade, FL');
		update_post_meta($post_id, 'tma_project_year', (string) (2023 + ($index % 4)));
		update_post_meta($post_id, 'tma_project_material', $project['material']);
	}

	$testimonial_count = wp_count_posts('tma_testimonial');
	if (! $testimonial_count || empty($testimonial_count->publish)) {
		$legacy_testimonials = array(
			array(
				'title'   => 'Review - Entry Gate',
				'quote'   => 'The gate quality and installation were excellent. The process was clear from start to finish.',
				'name'    => 'Luis M.',
				'service' => 'custom-metal-gates-miami',
				'type'    => 'Custom Gate',
			),
			array(
				'title'   => 'Review - Railings',
				'quote'   => 'Great communication, clean work, and beautiful final result for our staircase railings.',
				'name'    => 'Carla R.',
				'service' => 'metal-railings-miami',
				'type'    => 'Metal Railings',
			),
			array(
				'title'   => 'Review - Art Piece',
				'quote'   => 'Karel delivered an original metal piece that completely transformed our lobby.',
				'name'    => 'Andrea P.',
				'service' => 'art-commissions',
				'type'    => 'Art Commission',
			),
		);

		foreach ($legacy_testimonials as $testimonial) {
			$id = wp_insert_post(
				array(
					'post_type'    => 'tma_testimonial',
					'post_status'  => 'draft',
					'post_title'   => $testimonial['title'],
					'post_content' => $testimonial['quote'],
				)
			);

			if (! $id || is_wp_error($id)) {
				continue;
			}

			update_post_meta($id, 'tma_client_name', $testimonial['name']);
			update_post_meta($id, 'tma_project_type', $testimonial['type']);
			update_post_meta($id, 'tma_location', 'Miami');
			update_post_meta($id, 'tma_rating', 5);
			update_post_meta($id, 'tma_service', $testimonial['service']);
		}
	}

	update_option('tma_seed_content_v1', 1, false);
}
add_action('init', 'tma_seed_website_content', 40);

/**
 * Retire identifiable placeholder content from earlier development seeds.
 */
function tma_retire_unverified_seed_content()
{
	global $wpdb;

	if (get_option('tma_unverified_seed_content_retired_v2', false)) {
		return;
	}

	$legacy_testimonials = array(
		'Review - Entry Gate' => 'The gate quality and installation were excellent. The process was clear from start to finish.',
		'Review - Railings'   => 'Great communication, clean work, and beautiful final result for our staircase railings.',
		'Review - Art Piece'  => 'Karel delivered an original metal piece that completely transformed our lobby.',
	);
	$testimonials = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND post_title IN (%s, %s, %s)",
			'tma_testimonial',
			'publish',
			'Review - Entry Gate',
			'Review - Railings',
			'Review - Art Piece'
		)
	);
	$testimonial_ids = array();
	foreach ($testimonials as $testimonial) {
		if (isset($legacy_testimonials[$testimonial->post_title]) && hash_equals($legacy_testimonials[$testimonial->post_title], $testimonial->post_content)) {
			$testimonial_ids[] = (int) $testimonial->ID;
		}
	}
	$project_ids     = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND post_content = %s",
			'tma_portfolio',
			'publish',
			'Project summary placeholder. Replace with real project narrative and image gallery from client-delivered media assets.'
		)
	);

	$updated = true;
	foreach (array_merge($testimonial_ids, $project_ids) as $post_id) {
		$result  = wp_update_post(
			array(
				'ID'          => (int) $post_id,
				'post_status' => 'draft',
			),
			true
		);
		$updated = ! is_wp_error($result) && $updated;
	}

	if ($updated) {
		update_option('tma_unverified_seed_content_retired_v2', 1, false);
	}
}
add_action('init', 'tma_retire_unverified_seed_content', 45);
