<?php
/**
 * Thor Metal Art — Service Pages
 *
 * Registers service page patterns and creates pages programmatically
 * if they don't already exist.
 *
 * @package ThorMetalArt
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Service definitions — SEO-optimized slugs and content.
 *
 * @return array<string, array>
 */
function tma_get_services() {
return array(
'custom-metal-gates-miami' => array(
'title'       => 'Custom Metal Gates Miami',
'heading'     => 'Custom Metal Gates in Miami',
'heading_es'  => 'Portones de Metal Personalizados en Miami',
'description' => 'Handcrafted custom metal gates for residential and commercial properties in Miami-Dade. Driveway gates, pedestrian gates, and automated entry systems designed and fabricated locally.',
'desc_es'     => 'Portones de metal artesanales para propiedades residenciales y comerciales en Miami-Dade. Gates de entrada, peatonales y sistemas automatizados diseñados y fabricados localmente.',
'features'    => array(
'Driveway & entry gates',
'Pedestrian & side gates',
'Automated gate systems',
'Decorative iron work',
'Hurricane-rated options',
),
'features_es' => array(
'Portones de entrada vehicular',
'Puertas peatonales y laterales',
'Sistemas automatizados',
'Trabajo en hierro decorativo',
'Opciones resistentes a huracanes',
),
),
'metal-railings-miami' => array(
'title'       => 'Metal Railings Miami',
'heading'     => 'Custom Metal Railings in Miami',
'heading_es'  => 'Barandas de Metal Personalizadas en Miami',
'description' => 'Premium custom metal railings for staircases, balconies, and decks in Miami-Dade. Interior and exterior railing solutions crafted from steel, wrought iron, and aluminum.',
'desc_es'     => 'Barandas de metal premium para escaleras, balcones y terrazas en Miami-Dade. Soluciones de barandas interiores y exteriores fabricadas en acero, hierro forjado y aluminio.',
'features'    => array(
'Staircase railings',
'Balcony railings',
'Deck & pool fencing',
'Interior design pieces',
'Code-compliant installations',
),
'features_es' => array(
'Barandas de escalera',
'Barandas de balcón',
'Cercas de deck y piscina',
'Piezas de diseño interior',
'Instalaciones que cumplen código',
),
),
'metal-fences-miami' => array(
'title'       => 'Metal Fences Miami',
'heading'     => 'Custom Metal Fences in Miami',
'heading_es'  => 'Cercas de Metal Personalizadas en Miami',
'description' => 'Durable custom metal fences for homes and businesses in Miami-Dade. Security fencing, decorative perimeter fences, and property boundaries that combine strength with style.',
'desc_es'     => 'Cercas de metal duraderas para hogares y negocios en Miami-Dade. Cercas de seguridad, perímetros decorativos y límites de propiedad que combinan resistencia con estilo.',
'features'    => array(
'Security perimeter fences',
'Decorative property fences',
'Commercial fencing',
'Pool enclosures',
'Privacy screens',
),
'features_es' => array(
'Cercas perimetrales de seguridad',
'Cercas decorativas de propiedad',
'Cercas comerciales',
'Encierros de piscina',
'Pantallas de privacidad',
),
),
'custom-metal-furniture-miami' => array(
'title'       => 'Custom Metal Furniture Miami',
'heading'     => 'Custom Metal Furniture in Miami',
'heading_es'  => 'Muebles de Metal Personalizados en Miami',
'description' => 'One-of-a-kind custom metal furniture designed and handcrafted in Miami. Tables, shelving, frames, and decorative pieces combining industrial aesthetics with artisan craftsmanship.',
'desc_es'     => 'Muebles de metal únicos diseñados y fabricados a mano en Miami. Mesas, estanterías, marcos y piezas decorativas que combinan estética industrial con artesanía.',
'features'    => array(
'Dining & console tables',
'Shelving & storage units',
'Bed frames & headboards',
'Decorative furniture pieces',
'Commercial fixtures',
),
'features_es' => array(
'Mesas de comedor y consolas',
'Estanterías y unidades de almacenamiento',
'Marcos de cama y cabeceras',
'Piezas decorativas de mobiliario',
'Fixtures comerciales',
),
),
'metal-stairs-miami' => array(
'title'       => 'Metal Stairs Miami',
'heading'     => 'Custom Metal Stairs in Miami',
'heading_es'  => 'Escaleras de Metal Personalizadas en Miami',
'description' => 'Structural and decorative custom metal stairs for residential and commercial spaces in Miami-Dade. Spiral staircases, floating stairs, and industrial-style step systems.',
'desc_es'     => 'Escaleras de metal estructurales y decorativas para espacios residenciales y comerciales en Miami-Dade. Escaleras de caracol, flotantes y sistemas de estilo industrial.',
'features'    => array(
'Spiral staircases',
'Floating stair systems',
'Industrial-style stairs',
'Exterior access stairs',
'Fire escape & utility stairs',
),
'features_es' => array(
'Escaleras de caracol',
'Sistemas de escaleras flotantes',
'Escaleras estilo industrial',
'Escaleras de acceso exterior',
'Escaleras de emergencia y utilidad',
),
),
);
}

/**
 * Generate block content for a service page.
 *
 * @param array $service Service data.
 * @return string Block-editor compatible content.
 */
function tma_service_page_content( $service ) {
$features_html    = '';
$features_es_html = '';

foreach ( $service['features'] as $feat ) {
$features_html .= '<!-- wp:list-item --><li>' . esc_html( $feat ) . '</li><!-- /wp:list-item -->';
}
foreach ( $service['features_es'] as $feat ) {
$features_es_html .= '<!-- wp:list-item --><li>' . esc_html( $feat ) . '</li><!-- /wp:list-item -->';
}

$content = <<<BLOCKS
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">{$service['heading']}</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">{$service['description']}</p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">What We Offer</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>{$features_html}</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Our Process</h2>
<!-- /wp:heading -->

<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">1. Consultation</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>We discuss your vision, take measurements, and understand your requirements. Every project starts with a detailed conversation.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">2. Design &amp; Approval</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Our team creates detailed designs for your review. We refine until you are completely satisfied with the plan.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">3. Fabrication &amp; Install</h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Handcrafted in our Miami workshop, each piece is built to last. Professional installation included.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Servicio en Español</h2>
<!-- /wp:heading -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">{$service['heading_es']}</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">{$service['desc_es']}</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul>{$features_es_html}</ul>
<!-- /wp:list -->

<!-- wp:separator {"className":"is-style-wide"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-wide"/>
<!-- /wp:separator -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Request a Free Quote</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Ready to start your project? Contact Thor Metal Art today for a free consultation and estimate. We serve all of Miami-Dade County.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button {"backgroundColor":"accent","textColor":"surface"} -->
<div class="wp-block-button"><a class="wp-block-button__link has-surface-color has-accent-background-color has-text-color has-background wp-element-button">Get a Free Quote / Solicitar Cotización</a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
BLOCKS;

return $content;
}

/**
 * Create service pages on theme activation if they don't exist.
 */
function tma_create_service_pages() {
$services = tma_get_services();

foreach ( $services as $slug => $service ) {
$existing = get_page_by_path( $slug );
if ( $existing ) {
continue;
}

wp_insert_post( array(
'post_title'   => $service['title'],
'post_name'    => $slug,
'post_content' => tma_service_page_content( $service ),
'post_status'  => 'publish',
'post_type'    => 'page',
'post_author'  => 1,
'meta_input'   => array(
'_tma_service_page' => 1,
),
) );
}
}
add_action( 'after_switch_theme', 'tma_create_service_pages' );

/**
 * Admin action to manually trigger page creation.
 */
function tma_maybe_create_pages() {
if ( ! current_user_can( 'manage_options' ) ) {
return;
}

if ( ! isset( $_GET['tma_create_pages'] ) ) {
return;
}

if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'tma_create_pages' ) ) {
return;
}

tma_create_service_pages();

wp_safe_redirect( admin_url( 'edit.php?post_type=page&tma_pages_created=1' ) );
exit;
}
add_action( 'admin_init', 'tma_maybe_create_pages' );
