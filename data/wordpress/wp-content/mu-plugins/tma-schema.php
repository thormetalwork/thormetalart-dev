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
	$offers = array();
	foreach (tma_get_service_catalog() as $slug => $service) {
		$offers[] = array(
			'@type'       => 'Offer',
			'itemOffered' => array(
				'@type'    => 'Service',
				'name'     => $service['label']['en'],
				'provider' => array('@id' => tma_schema_localbusiness_id()),
				'url'      => home_url('/' . $slug . '/'),
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

	$slugs   = array_keys(tma_get_service_catalog());
	$slugs[] = 'art-commissions';

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

	$slug = get_post_field('post_name', get_queried_object_id());
	$services = tma_get_service_catalog();
	if (! isset($services[$slug])) {
		return;
	}

	$entities = array();
	foreach ($services[$slug]['faqs'] as $faq) {
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
