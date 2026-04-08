<?php
/**
 * Thor Metal Art — Schema Markup (JSON-LD)
 *
 * @package ThorMetalArt
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output LocalBusiness schema globally.
 */
function tma_schema_local_business() {
	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'LocalBusiness',
		'@id'         => home_url( '/#localbusiness' ),
		'name'        => 'Thor Metal Art',
		'description' => 'Custom metal fabrication, artistic metalwork, gates, railings, fences, stairs, and furniture in Miami.',
		'url'         => home_url( '/' ),
		'telephone'   => '+1-305-000-0000',
		'email'       => 'info@thormetalart.com',
		'priceRange'  => '$$-$$$$',
		'address'     => array(
			'@type'           => 'PostalAddress',
			'addressLocality' => 'Miami',
			'addressRegion'   => 'FL',
			'addressCountry'  => 'US',
		),
		'geo'         => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => 25.7617,
			'longitude' => -80.1918,
		),
		'sameAs'      => array(
			'https://www.instagram.com/thormetalart/',
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ),
				'opens'     => '08:00',
				'closes'    => '18:00',
			),
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => 'Saturday',
				'opens'     => '09:00',
				'closes'    => '14:00',
			),
		),
		'hasOfferCatalog' => array(
			'@type'           => 'OfferCatalog',
			'name'            => 'Metal Fabrication Services',
			'itemListElement' => tma_schema_service_catalog(),
		),
	);

	tma_output_jsonld( $schema );
}
add_action( 'wp_head', 'tma_schema_local_business', 1 );

/**
 * Build service catalog list.
 *
 * @return array<int, array<string, mixed>>
 */
function tma_schema_service_catalog() {
	$services = array(
		array( 'name' => 'Custom Metal Gates', 'url' => home_url( '/custom-metal-gates-miami/' ) ),
		array( 'name' => 'Metal Railings', 'url' => home_url( '/metal-railings-miami/' ) ),
		array( 'name' => 'Metal Fences', 'url' => home_url( '/metal-fences-miami/' ) ),
		array( 'name' => 'Custom Metal Furniture', 'url' => home_url( '/custom-metal-furniture-miami/' ) ),
		array( 'name' => 'Metal Stairs', 'url' => home_url( '/metal-stairs-miami/' ) ),
		array( 'name' => 'Art Commissions', 'url' => home_url( '/art-commissions/' ) ),
	);

	$offers = array();
	foreach ( $services as $service ) {
		$offers[] = array(
			'@type'       => 'Offer',
			'itemOffered' => array(
				'@type'    => 'Service',
				'name'     => $service['name'],
				'provider' => array( '@id' => home_url( '/#localbusiness' ) ),
				'url'      => $service['url'],
			),
		);
	}

	return $offers;
}

/**
 * Output Service schema on service pages.
 */
function tma_schema_service_page() {
	if ( ! is_page() ) {
		return;
	}

	$slugs = array(
		'custom-metal-gates-miami',
		'metal-railings-miami',
		'metal-fences-miami',
		'custom-metal-furniture-miami',
		'metal-stairs-miami',
		'art-commissions',
	);

	$slug = get_post_field( 'post_name', get_queried_object_id() );
	if ( ! in_array( $slug, $slugs, true ) ) {
		return;
	}

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Service',
		'name'        => get_the_title(),
		'description' => wp_strip_all_tags( get_the_excerpt() ?: get_the_title() ),
		'url'         => get_permalink(),
		'provider'    => array( '@id' => home_url( '/#localbusiness' ) ),
		'areaServed'  => array(
			'@type' => 'City',
			'name'  => 'Miami',
		),
		'serviceType' => 'Custom Metal Fabrication',
	);

	tma_output_jsonld( $schema );
}
add_action( 'wp_head', 'tma_schema_service_page', 2 );

/**
 * Output FAQPage schema from predefined FAQs per service slug.
 */
function tma_schema_faq_page() {
	if ( ! is_page() ) {
		return;
	}

	$faqs_by_slug = array(
		'custom-metal-gates-miami' => array(
			array( 'q' => 'How long does a custom gate take?', 'a' => 'Most custom gates are completed in 3 to 5 weeks after design approval.' ),
			array( 'q' => 'Do you handle permits?', 'a' => 'Yes, we can manage permits in Miami-Dade and Broward when required.' ),
		),
		'metal-railings-miami' => array(
			array( 'q' => 'Are railings code compliant?', 'a' => 'Yes, we fabricate and install based on local code requirements.' ),
			array( 'q' => 'Can you match existing design styles?', 'a' => 'Yes, we can replicate or modernize existing railing styles.' ),
		),
		'metal-fences-miami' => array(
			array( 'q' => 'Do you offer security and decorative fences?', 'a' => 'Yes, we build both functional security and decorative perimeter systems.' ),
			array( 'q' => 'What finish options are available?', 'a' => 'We provide finish options selected for durability in Miami weather.' ),
		),
		'custom-metal-furniture-miami' => array(
			array( 'q' => 'Can I commission a custom design?', 'a' => 'Yes, each furniture piece is built to order from your concept and dimensions.' ),
			array( 'q' => 'Do you work with mixed materials?', 'a' => 'Yes, we can combine metal with wood, glass, or stone elements.' ),
		),
		'metal-stairs-miami' => array(
			array( 'q' => 'Do you build floating and spiral stairs?', 'a' => 'Yes, we fabricate custom floating, spiral, and industrial stair systems.' ),
			array( 'q' => 'Do you install as well?', 'a' => 'Yes, our team handles fabrication and installation end to end.' ),
		),
	);

	$slug = get_post_field( 'post_name', get_queried_object_id() );
	if ( ! isset( $faqs_by_slug[ $slug ] ) ) {
		return;
	}

	$entities = array();
	foreach ( $faqs_by_slug[ $slug ] as $faq ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $faq['q'],
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $faq['a'],
			),
		);
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);

	tma_output_jsonld( $schema );
}
add_action( 'wp_head', 'tma_schema_faq_page', 3 );

/**
 * Output BreadcrumbList schema for non-home routes.
 */
function tma_schema_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Home',
			'item'     => home_url( '/' ),
		),
	);

	$position = 2;
	if ( is_post_type_archive( 'tma_portfolio' ) ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => 'Portfolio',
			'item'     => get_post_type_archive_link( 'tma_portfolio' ),
		);
	} elseif ( is_singular( 'tma_portfolio' ) ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => 'Portfolio',
			'item'     => get_post_type_archive_link( 'tma_portfolio' ),
		);
		++$position;
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	} elseif ( is_page() ) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	}

	if ( count( $items ) < 2 ) {
		return;
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);

	tma_output_jsonld( $schema );
}
add_action( 'wp_head', 'tma_schema_breadcrumbs', 4 );

/**
 * Print JSON-LD block.
 *
 * @param array<string, mixed> $data Schema data.
 */
function tma_output_jsonld( $data ) {
	echo '<script type="application/ld+json">';
	echo wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	echo '</script>' . "\n";
}