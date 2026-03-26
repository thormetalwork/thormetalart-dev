<?php
/**
 * Thor Metal Art — Schema Markup (JSON-LD)
 *
 * Outputs LocalBusiness and Service structured data for SEO.
 *
 * @package ThorMetalArt
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output LocalBusiness JSON-LD on all pages.
 */
function tma_schema_local_business() {
$schema = array(
'@context'    => 'https://schema.org',
'@type'       => 'LocalBusiness',
'@id'         => home_url( '/#localbusiness' ),
'name'        => 'Thor Metal Art',
'description' => 'Custom metal fabrication, gates, railings, fences, furniture, stairs, and art sculptures in Miami-Dade, FL.',
'url'         => home_url( '/' ),
'telephone'   => '+1-305-000-0000',
'email'       => 'info@thormetalart.com',
'image'       => get_custom_logo() ? wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) : '',
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
'areaServed'  => array(
'@type' => 'GeoCircle',
'geoMidpoint' => array(
'@type'     => 'GeoCoordinates',
'latitude'  => 25.7617,
'longitude' => -80.1918,
),
'geoRadius' => '50000',
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
'sameAs'      => array(
'https://www.instagram.com/thormetalart/',
),
'hasOfferCatalog' => array(
'@type'           => 'OfferCatalog',
'name'            => 'Metal Fabrication Services',
'itemListElement' => tma_get_service_offers(),
),
);

// Remove empty image.
if ( empty( $schema['image'] ) ) {
unset( $schema['image'] );
}

tma_output_jsonld( $schema );
}
add_action( 'wp_head', 'tma_schema_local_business', 1 );

/**
 * Build service offer items for catalog.
 *
 * @return array
 */
function tma_get_service_offers() {
$services = array(
array(
'name'        => 'Custom Metal Gates',
'description' => 'Handcrafted custom metal gates for residential and commercial properties in Miami-Dade.',
'url'         => home_url( '/custom-metal-gates-miami/' ),
),
array(
'name'        => 'Metal Railings',
'description' => 'Premium custom metal railings for staircases, balconies, and decks in Miami-Dade.',
'url'         => home_url( '/metal-railings-miami/' ),
),
array(
'name'        => 'Metal Fences',
'description' => 'Durable custom metal fences for homes and businesses in Miami-Dade.',
'url'         => home_url( '/metal-fences-miami/' ),
),
array(
'name'        => 'Custom Metal Furniture',
'description' => 'One-of-a-kind custom metal furniture designed and handcrafted in Miami.',
'url'         => home_url( '/custom-metal-furniture-miami/' ),
),
array(
'name'        => 'Metal Stairs',
'description' => 'Structural and decorative custom metal stairs for residential and commercial spaces.',
'url'         => home_url( '/metal-stairs-miami/' ),
),
);

$offers = array();
foreach ( $services as $svc ) {
$offers[] = array(
'@type'           => 'OfferCatalog',
'name'            => $svc['name'],
'itemListElement' => array(
array(
'@type'       => 'Offer',
'itemOffered' => array(
'@type'       => 'Service',
'name'        => $svc['name'],
'description' => $svc['description'],
'url'         => $svc['url'],
'provider'    => array( '@id' => home_url( '/#localbusiness' ) ),
'areaServed'  => array(
'@type' => 'City',
'name'  => 'Miami',
),
),
),
),
);
}

return $offers;
}

/**
 * Output individual Service schema on service pages.
 */
function tma_schema_service_page() {
if ( ! is_page() ) {
return;
}

$service_slugs = array(
'custom-metal-gates-miami' => 'Custom Metal Gates Miami',
'metal-railings-miami'     => 'Metal Railings Miami',
'metal-fences-miami'       => 'Metal Fences Miami',
'custom-metal-furniture-miami' => 'Custom Metal Furniture Miami',
'metal-stairs-miami'       => 'Metal Stairs Miami',
);

$current_slug = get_post_field( 'post_name', get_queried_object_id() );

if ( ! isset( $service_slugs[ $current_slug ] ) ) {
return;
}

$post = get_queried_object();

$schema = array(
'@context'    => 'https://schema.org',
'@type'       => 'Service',
'name'        => $service_slugs[ $current_slug ],
'description' => wp_strip_all_tags( get_the_excerpt( $post ) ),
'url'         => get_permalink( $post ),
'provider'    => array( '@id' => home_url( '/#localbusiness' ) ),
'areaServed'  => array(
'@type' => 'City',
'name'  => 'Miami',
),
'serviceType' => 'Metal Fabrication',
);

tma_output_jsonld( $schema );
}
add_action( 'wp_head', 'tma_schema_service_page', 2 );

/**
 * Output JSON-LD script tag.
 *
 * @param array $data Schema data.
 */
function tma_output_jsonld( $data ) {
echo '<script type="application/ld+json">';
echo wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
echo '</script>' . "\n";
}
