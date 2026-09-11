<?php

/**
 * Thor Metal Art — Schema Markup (JSON-LD)
 *
 * @package ThorMetalArt
 */

defined('ABSPATH') || exit;

/**
 * Build a stable LocalBusiness @id independent of language path.
 *
 * @return string
 */
function tma_schema_localbusiness_id()
{
	$base = untrailingslashit((string) get_option('home'));
	return $base . '/#localbusiness';
}

/**
 * Output LocalBusiness schema globally.
 */
function tma_schema_local_business()
{
	$schema = array(
		'@context'                  => 'https://schema.org',
		'@type'                     => array('LocalBusiness', 'HomeAndConstructionBusiness'),
		'@id'                       => tma_schema_localbusiness_id(),
		'name'                      => 'Thor Metal Art',
		'description'               => 'Custom metal fabrication, artistic metalwork, gates, railings, fences, stairs, and furniture in Miami.',
		'url'                       => home_url('/'),
		'telephone'                 => '+1-786-854-7309',
		'email'                     => 'contact@thormetalart.com',
		'priceRange'                => '$$-$$$$',
		'currenciesAccepted'        => 'USD',
		'paymentAccepted'           => 'Cash, Credit Card, Check',
		'address'                   => array(
			'@type'           => 'PostalAddress',
			'addressLocality' => 'Miami',
			'addressRegion'   => 'FL',
			'postalCode'      => '33135',
			'addressCountry'  => 'US',
		),
		'geo'                       => array(
			'@type'     => 'GeoCoordinates',
			'latitude'  => 25.7617,
			'longitude' => -80.1918,
		),
		'hasMap'                    => 'https://maps.google.com/?q=Thor+Metal+Art+Miami+FL',
		'areaServed'                => array(
			array('@type' => 'City', 'name' => 'Miami', 'sameAs' => 'https://en.wikipedia.org/wiki/Miami'),
			array('@type' => 'City', 'name' => 'Coral Gables'),
			array('@type' => 'City', 'name' => 'Aventura'),
			array('@type' => 'City', 'name' => 'Brickell'),
			array('@type' => 'City', 'name' => 'Wynwood'),
			array('@type' => 'City', 'name' => 'Pinecrest'),
			array('@type' => 'AdministrativeArea', 'name' => 'Miami-Dade County'),
			array('@type' => 'AdministrativeArea', 'name' => 'Broward County'),
		),
		'logo'                      => array(
			'@type'  => 'ImageObject',
			'url'    => get_stylesheet_directory_uri() . '/Logo.png',
			'width'  => 1000,
			'height' => 1000,
		),
		'image'                     => get_stylesheet_directory_uri() . '/Logo.png',
		'sameAs'                    => array(
			'https://www.instagram.com/thormetalart/',
			'https://www.facebook.com/thormetalart',
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
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
		'hasOfferCatalog'           => array(
			'@type'           => 'OfferCatalog',
			'name'            => 'Metal Fabrication Services',
			'itemListElement' => tma_schema_service_catalog(),
		),
	);

	tma_output_jsonld($schema);
}
add_action('wp_head', 'tma_schema_local_business', 1);

/**
 * Build service catalog list.
 *
 * @return array<int, array<string, mixed>>
 */
function tma_schema_service_catalog()
{
	$services = array(
		array(
			'name' => 'Custom Metal Gates',
			'url'  => home_url('/custom-metal-gates-miami/'),
		),
		array(
			'name' => 'Metal Railings',
			'url'  => home_url('/metal-railings-miami/'),
		),
		array(
			'name' => 'Metal Fences',
			'url'  => home_url('/metal-fences-miami/'),
		),
		array(
			'name' => 'Custom Metal Furniture',
			'url'  => home_url('/custom-metal-furniture-miami/'),
		),
		array(
			'name' => 'Metal Stairs',
			'url'  => home_url('/metal-stairs-miami/'),
		),
		array(
			'name' => 'Art Commissions',
			'url'  => home_url('/art-commissions/'),
		),
	);

	$offers = array();
	foreach ($services as $service) {
		$offers[] = array(
			'@type'       => 'Offer',
			'itemOffered' => array(
				'@type'    => 'Service',
				'name'     => $service['name'],
				'provider' => array('@id' => tma_schema_localbusiness_id()),
				'url'      => $service['url'],
			),
		);
	}

	return $offers;
}

/**
 * Output Service schema on service pages.
 */
function tma_schema_service_page()
{
	if (! is_page()) {
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

	$slug = get_post_field('post_name', get_queried_object_id());
	if (! in_array($slug, $slugs, true)) {
		return;
	}

	$schema = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Service',
		'name'        => get_the_title(),
		'description' => wp_strip_all_tags(get_the_excerpt() ? get_the_excerpt() : get_the_title()),
		'url'         => get_permalink(),
		'provider'    => array('@id' => tma_schema_localbusiness_id()),
		'areaServed'  => array(
			'@type' => 'City',
			'name'  => 'Miami',
		),
		'serviceType' => 'Custom Metal Fabrication',
	);

	tma_output_jsonld($schema);
}
add_action('wp_head', 'tma_schema_service_page', 2);

/**
 * Output FAQPage schema from predefined FAQs per service slug.
 */
function tma_schema_faq_page()
{
	if (! is_page()) {
		return;
	}

	$faqs_by_slug = array(
		'custom-metal-gates-miami'     => array(
			array(
				'q' => 'How long does a custom gate take?',
				'a' => 'Most projects take between 3 and 5 weeks after design approval, depending on complexity and permit requirements.',
			),
			array(
				'q' => 'Do you handle permits in Miami-Dade and Broward?',
				'a' => 'Yes. We can manage permits directly or guide your team through the process when required by code.',
			),
			array(
				'q' => 'What is the typical price range?',
				'a' => 'Pricing depends on size, material, automation, and design detail. We provide a clear quote after a free consultation.',
			),
		),
		'metal-railings-miami'         => array(
			array(
				'q' => 'Can you match an existing railing style?',
				'a' => 'Yes. We can replicate or reinterpret existing styles while improving structural performance and finish quality.',
			),
			array(
				'q' => 'Are your railings code compliant?',
				'a' => 'Yes. We fabricate based on local safety requirements and project conditions.',
			),
			array(
				'q' => 'Do you install for both homes and businesses?',
				'a' => 'Absolutely. We handle residential and commercial installations across Miami-Dade and Broward.',
			),
		),
		'metal-fences-miami'           => array(
			array(
				'q' => 'What material works best for Miami weather?',
				'a' => 'We select material and finish based on exposure and maintenance preferences, with strong anti-corrosion options.',
			),
			array(
				'q' => 'Can you do privacy-focused fence designs?',
				'a' => 'Yes. We can fabricate patterns and panel combinations that increase privacy while maintaining airflow and style.',
			),
			array(
				'q' => 'Do you offer commercial perimeter fences?',
				'a' => 'Yes. We build custom fence systems for commercial properties including controlled access points.',
			),
		),
		'custom-metal-furniture-miami' => array(
			array(
				'q' => 'Can you build from inspiration photos?',
				'a' => 'Yes. We can work from references, refine proportions, and deliver a custom piece tailored to your space.',
			),
			array(
				'q' => 'Do you offer matching furniture sets?',
				'a' => 'Yes. We can fabricate cohesive sets for dining, living, office, or hospitality environments.',
			),
			array(
				'q' => 'What is the average lead time?',
				'a' => 'Lead time depends on complexity and quantity. We provide a schedule in the quote phase.',
			),
		),
		'metal-stairs-miami'           => array(
			array(
				'q' => 'Do you build stairs for renovations and new construction?',
				'a' => 'Yes. We work with homeowners, contractors, and designers for both renovation and ground-up projects.',
			),
			array(
				'q' => 'Can stairs be fabricated with mixed materials?',
				'a' => 'Yes. We can combine metal structures with wood, stone, or glass depending on your concept.',
			),
			array(
				'q' => 'Do you offer modern minimalist designs?',
				'a' => 'Yes. Minimal line designs are one of our most requested solutions for contemporary spaces.',
			),
		),
	);

	$slug = get_post_field('post_name', get_queried_object_id());
	if (! isset($faqs_by_slug[$slug])) {
		return;
	}

	$entities = array();
	foreach ($faqs_by_slug[$slug] as $faq) {
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

	tma_output_jsonld($schema);
}
add_action('wp_head', 'tma_schema_faq_page', 3);

/**
 * Output BreadcrumbList schema for non-home routes.
 */
function tma_schema_breadcrumbs()
{
	if (is_front_page()) {
		return;
	}

	$items = array(
		array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => 'Home',
			'item'     => home_url('/'),
		),
	);

	$position = 2;
	if (is_post_type_archive('tma_portfolio')) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => 'Portfolio',
			'item'     => get_post_type_archive_link('tma_portfolio'),
		);
	} elseif (is_singular('tma_portfolio')) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => 'Portfolio',
			'item'     => get_post_type_archive_link('tma_portfolio'),
		);
		++$position;
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	} elseif (is_page()) {
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $position,
			'name'     => get_the_title(),
			'item'     => get_permalink(),
		);
	}

	if (count($items) < 2) {
		return;
	}

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	);

	tma_output_jsonld($schema);
}
add_action('wp_head', 'tma_schema_breadcrumbs', 4);

/**
 * Output BlogPosting schema for single blog posts.
 */
function tma_schema_blog_posting()
{
	if (! is_single() || ! is_singular('post')) {
		return;
	}

	$post = get_post();
	if (! $post) {
		return;
	}

	$author_name   = get_the_author_meta('display_name', (int) $post->post_author);
	$author_url    = get_author_posts_url((int) $post->post_author);
	$thumbnail_url = has_post_thumbnail($post->ID)
		? get_the_post_thumbnail_url($post->ID, 'large')
		: home_url('/wp-content/uploads/2026/04/tma-portfolio-waterjet-panel.jpg');

	$excerpt = wp_strip_all_tags(get_the_excerpt($post->ID));
	if (! $excerpt) {
		$excerpt = wp_strip_all_tags(wp_trim_words($post->post_content, 30));
	}

	$schema = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'@id'              => get_permalink($post->ID) . '#blogposting',
		'headline'         => get_the_title($post->ID),
		'description'      => $excerpt,
		'url'              => get_permalink($post->ID),
		'datePublished'    => get_the_date('c', $post->ID),
		'dateModified'     => get_the_modified_date('c', $post->ID),
		'image'            => array(
			'@type' => 'ImageObject',
			'url'   => $thumbnail_url,
		),
		'author'           => array(
			'@type' => 'Person',
			'name'  => $author_name,
			'url'   => $author_url,
		),
		'publisher'        => array(
			'@id'  => tma_schema_localbusiness_id(),
			'name' => 'Thor Metal Art',
		),
		'mainEntityOfPage' => array(
			'@type' => 'WebPage',
			'@id'   => get_permalink($post->ID),
		),
	);

	tma_output_jsonld($schema);
}
add_action('wp_head', 'tma_schema_blog_posting', 5);

/**
 * Print JSON-LD block.
 *
 * @param array<string, mixed> $data Schema data.
 */
function tma_output_jsonld($data)
{
	echo '<script type="application/ld+json">';
	echo wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	echo '</script>' . "\n";
}
