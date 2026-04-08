<?php
/**
 * Thor Metal Art — Website Page Provisioning
 *
 * Creates and updates service pages and key sales pages for Website V1.
 *
 * @package ThorMetalArt
 */

defined( 'ABSPATH' ) || exit;

/**
 * Return services dataset.
 *
 * @return array<string, array<string, mixed>>
 */
function tma_get_services() {
	return array(
		'custom-metal-gates-miami' => array(
			'title'        => 'Custom Metal Gates Miami',
			'hero_heading' => 'Custom Metal Gates Miami',
			'subheading'   => 'Hand-Crafted. Built to Last. Designed for You.',
			'intro'        => 'Your gate is the first thing people see. At Thor Metal Art, we design and fabricate custom metal gates that combine security with style. Every gate is built for your property dimensions, design direction, and long-term durability in South Florida conditions.',
			'includes'     => array(
				'Custom design from sketch, inspiration images, or from-scratch concept',
				'Water jet precision cutting for clean and exact shapes',
				'MIG and TIG welding for structural integrity',
				'Custom finish: powder coat, paint, patina, or raw steel',
				'Professional installation and permit support',
				'Free estimate before any commitment',
			),
			'faqs'         => array(
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
			'spanish_title'   => 'Servicio en Espanol',
			'spanish_heading' => 'Portones de Metal Personalizados en Miami',
			'spanish_body'    => 'Fabricamos portones de metal a medida para residencias y negocios. Combinamos seguridad, diseno y durabilidad con fabricacion local en Miami-Dade.',
		),
		'metal-railings-miami' => array(
			'title'        => 'Metal Railings Miami',
			'hero_heading' => 'Metal Railings Miami',
			'subheading'   => 'Custom Design for Stairs, Balconies and Decks.',
			'intro'        => 'Every railing should protect and elevate the space visually. We fabricate custom metal railings for staircases, balconies, and pool decks with full code compliance and premium finishes.',
			'includes'     => array(
				'Interior and exterior custom railing systems',
				'Exact on-site measurement and fabrication to spec',
				'Architectural detailing and clean visual lines',
				'Multiple finishes for residential and commercial use',
				'Code-compliant installation',
				'Free estimate and timeline planning',
			),
			'faqs'         => array(
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
			'spanish_title'   => 'Servicio en Espanol',
			'spanish_heading' => 'Barandas de Metal Personalizadas en Miami',
			'spanish_body'    => 'Disenamos e instalamos barandas de metal para escaleras, balcones y terrazas. Seguridad y estilo en una sola solucion.',
		),
		'metal-fences-miami' => array(
			'title'        => 'Custom Metal Fences Miami',
			'hero_heading' => 'Custom Metal Fences Miami',
			'subheading'   => 'Decorative and Security Solutions Built to Last.',
			'intro'        => 'A fence should protect the property while matching the architecture. We design and fabricate decorative and security metal fences for homes and businesses in South Florida.',
			'includes'     => array(
				'Perimeter security fence systems',
				'Decorative patterns and modern privacy options',
				'Custom gates integrated with fence layout',
				'Rust-resistant coating options for Miami weather',
				'Professional installation',
				'Free estimate and phased project planning',
			),
			'faqs'         => array(
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
			'spanish_title'   => 'Servicio en Espanol',
			'spanish_heading' => 'Cercas de Metal Personalizadas en Miami',
			'spanish_body'    => 'Creamos cercas de metal decorativas y de seguridad para residencias y comercios, con acabados duraderos para el clima del sur de Florida.',
		),
		'custom-metal-furniture-miami' => array(
			'title'        => 'Custom Metal Furniture Miami',
			'hero_heading' => 'Custom Metal Furniture Miami',
			'subheading'   => 'One-of-a-Kind Pieces Built to Order.',
			'intro'        => 'Our custom furniture combines industrial precision with artisan craft. We fabricate tables, shelving, frames, and statement pieces designed around your space and concept.',
			'includes'     => array(
				'Design consultation for dimensions and style',
				'Custom fabrication in steel, iron, and mixed materials',
				'Collaboration with wood, stone, or glass elements',
				'Hand-finished details and durable coatings',
				'Delivery and installation support',
				'Free estimate with transparent scope',
			),
			'faqs'         => array(
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
			'spanish_title'   => 'Servicio en Espanol',
			'spanish_heading' => 'Muebles de Metal Personalizados en Miami',
			'spanish_body'    => 'Fabricamos muebles unicos en metal para hogares y negocios: mesas, estanterias, bases y piezas decorativas hechas a medida.',
		),
		'metal-stairs-miami' => array(
			'title'        => 'Metal Stairs Miami',
			'hero_heading' => 'Metal Stairs and Handrails Miami',
			'subheading'   => 'Structural Precision with Visual Impact.',
			'intro'        => 'We fabricate custom stair systems and handrails for residential and commercial spaces: floating stairs, spiral designs, and industrial-style structures with architectural presence.',
			'includes'     => array(
				'Floating and spiral stair options',
				'Handrails integrated with stair architecture',
				'Structural fabrication with code-aware detailing',
				'Custom finish packages for interior and exterior use',
				'On-site installation and alignment',
				'Free estimate and technical walkthrough',
			),
			'faqs'         => array(
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
			'spanish_title'   => 'Servicio en Espanol',
			'spanish_heading' => 'Escaleras de Metal y Pasamanos en Miami',
			'spanish_body'    => 'Desarrollamos escaleras y pasamanos de metal para espacios residenciales y comerciales, con precision estructural y diseno personalizado.',
		),
	);
}

/**
 * Build block content for one service page.
 *
 * @param array<string, mixed> $service Service data.
 * @return string
 */
function tma_service_page_content( $service ) {
	$includes_items = '';
	foreach ( $service['includes'] as $item ) {
		$includes_items .= '<!-- wp:list-item --><li>' . esc_html( $item ) . '</li><!-- /wp:list-item -->';
	}

	$faq_html = '';
	foreach ( $service['faqs'] as $faq ) {
		$faq_html .= '<!-- wp:html --><details class="tma-faq-item"><summary>' . esc_html( $faq['q'] ) . '</summary><p>' . esc_html( $faq['a'] ) . '</p></details><!-- /wp:html -->';
	}

	return '<!-- wp:group {"layout":{"type":"constrained","contentSize":"1200px"}} --><div class="wp-block-group">'
		. '<!-- wp:group {"className":"tma-service-hero-fallback"} --><div class="wp-block-group tma-service-hero-fallback"><!-- wp:heading {"textAlign":"center","level":1} --><h1 class="wp-block-heading has-text-align-center">' . esc_html( $service['hero_heading'] ) . '</h1><!-- /wp:heading --><!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">' . esc_html( $service['subheading'] ) . '</p><!-- /wp:paragraph --></div><!-- /wp:group -->'
		. '<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">' . esc_html( $service['intro'] ) . '</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">What\'s Included</h2><!-- /wp:heading -->'
		. '<!-- wp:list --><ul>' . $includes_items . '</ul><!-- /wp:list -->'
		. '<!-- wp:shortcode -->[tma_testimonials limit="2" service="' . esc_attr( sanitize_title( $service['title'] ) ) . '"]<!-- /wp:shortcode -->'
		. '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Frequently Asked Questions</h2><!-- /wp:heading -->'
		. $faq_html
		. '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">' . esc_html( $service['spanish_title'] ) . '</h2><!-- /wp:heading -->'
		. '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html( $service['spanish_heading'] ) . '</h3><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>' . esc_html( $service['spanish_body'] ) . '</p><!-- /wp:paragraph -->'
		. '<!-- wp:group {"className":"tma-final-cta"} --><div class="wp-block-group tma-final-cta"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Ready to start your project?</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Request a free estimate with no commitment. We respond within 24 hours.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Get a Free Estimate</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->'
		. '</div><!-- /wp:group -->';
}

/**
 * Return additional static pages.
 *
 * @return array<string, array<string, string>>
 */
function tma_get_core_pages() {
	return array(
		'art-commissions' => array(
			'title'   => 'Metal as Art & Commissions',
			'content' => '<!-- wp:group {"layout":{"type":"constrained","contentSize":"1200px"}} --><div class="wp-block-group"><!-- wp:group {"className":"tma-service-hero-fallback"} --><div class="wp-block-group tma-service-hero-fallback"><!-- wp:heading {"textAlign":"center","level":1} --><h1 class="wp-block-heading has-text-align-center">Metal as Art</h1><!-- /wp:heading --><!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">Original Sculptures and Commissioned Pieces by Karel Frometa — Miami</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Artist Statement</h2><!-- /wp:heading --><!-- wp:paragraph --><p>I have been working with metal as both a fabricator and an artist. Every weld, cut, and surface decision is technical and aesthetic at the same time. My work ranges from interior statement pieces to large commissioned installations.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Each commission is unique and developed in direct conversation with the client.</p><!-- /wp:paragraph --><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">How to Commission a Piece</h2><!-- /wp:heading --><!-- wp:list --><ul><li>Conversation: we define concept, size, material, and budget.</li><li>Concept and Proposal: sketch and timeline with clear scope.</li><li>Fabrication: built in our Miami studio with progress updates.</li><li>Delivery and Installation: final delivery with on-site support if required.</li></ul><!-- /wp:list --><!-- wp:group {"className":"tma-image-placeholder"} --><div class="wp-block-group tma-image-placeholder"><!-- wp:paragraph {"align":"center"} --><p class="has-text-align-center">Art gallery placeholder — upload sculptures and commissions</p><!-- /wp:paragraph --></div><!-- /wp:group --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Commission a Piece</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->',
		),
		'how-we-work'     => array(
			'title'   => 'How We Work',
			'content' => '<!-- wp:group {"layout":{"type":"constrained","contentSize":"1200px"}} --><div class="wp-block-group"><!-- wp:heading {"level":1} --><h1 class="wp-block-heading">How We Work</h1><!-- /wp:heading --><!-- wp:paragraph --><p>From first call to finished installation — everything is handled in-house by our Miami team.</p><!-- /wp:paragraph --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><h3>1. Free Estimate</h3><p>Tell us your idea and constraints.</p></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><h3>2. Design &amp; Quote</h3><p>We provide concept and clear pricing.</p></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><h3>3. Production</h3><p>Water jet cutting, welding, and finishing.</p></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><h3>4. Quality Check</h3><p>Structural and finish verification before install.</p></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><h3>5. Installation</h3><p>Final installation and handover with clean-up.</p></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:list --><ul><li>Everything in-house</li><li>Water jet + MIG/TIG welding</li><li>Response within 24 hours</li><li>Licensed and insured</li></ul><!-- /wp:list --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Ready to Start? Get a Free Estimate</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->',
		),
		'contact'          => array(
			'title'   => 'Contact Thor Metal Art',
			'content' => '<!-- wp:paragraph --><p>This page uses the dedicated block template page-contact.html. Update direct contact details in the content template as needed.</p><!-- /wp:paragraph -->',
		),
	);
}

/**
 * Create page if missing and update generated content.
 *
 * @param string $slug    Page slug.
 * @param string $title   Title.
 * @param string $content Block content.
 * @param string $type    Marker type.
 */
function tma_create_or_update_generated_page( $slug, $title, $content, $type ) {
	$existing = get_page_by_path( $slug );

	if ( $existing ) {
		if ( '1' === get_post_meta( $existing->ID, '_tma_generated_page', true ) ) {
			wp_update_post(
				array(
					'ID'           => $existing->ID,
					'post_title'   => $title,
					'post_content' => $content,
				)
			);
		}
		return;
	}

	$post_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => 1,
			'meta_input'   => array(
				'_tma_generated_page' => '1',
				'_tma_generated_type' => $type,
			),
		)
	);

	if ( is_wp_error( $post_id ) ) {
		return;
	}
}

/**
 * Provision Website V1 pages.
 */
function tma_provision_website_v1_pages() {
	$services = tma_get_services();
	foreach ( $services as $slug => $service ) {
		tma_create_or_update_generated_page( $slug, $service['title'], tma_service_page_content( $service ), 'service' );
	}

	$core_pages = tma_get_core_pages();
	foreach ( $core_pages as $slug => $page ) {
		tma_create_or_update_generated_page( $slug, $page['title'], $page['content'], 'core' );
	}
}
add_action( 'after_switch_theme', 'tma_provision_website_v1_pages' );

/**
 * Provision pages once after deployment.
 */
function tma_maybe_provision_website_v1_pages_once() {
	$version = get_option( 'tma_pages_version', '' );
	if ( 'v1' === $version ) {
		return;
	}

	tma_provision_website_v1_pages();
	update_option( 'tma_pages_version', 'v1', false );
}
add_action( 'init', 'tma_maybe_provision_website_v1_pages_once', 50 );

/**
 * Admin action to manually trigger generation.
 */
function tma_maybe_provision_website_v1_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! isset( $_GET['tma_create_pages'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) );
	if ( ! wp_verify_nonce( $nonce, 'tma_create_pages' ) ) {
		return;
	}

	tma_provision_website_v1_pages();
	wp_safe_redirect( admin_url( 'edit.php?post_type=page&tma_pages_created=1' ) );
	exit;
}
add_action( 'admin_init', 'tma_maybe_provision_website_v1_pages' );

/**
 * Contact map shortcode.
 *
 * @return string
 */
function tma_shortcode_contact_map() {
	$public_address = get_option( 'tma_public_address', '' );
	if ( empty( $public_address ) ) {
		return '<p>Serving Miami-Dade and Broward County, Florida.</p>';
	}

	$map_query = rawurlencode( $public_address );
	$src       = 'https://www.google.com/maps?q=' . $map_query . '&output=embed';

	return '<iframe title="Thor Metal Art Map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="width:100%;min-height:320px;border:0;border-radius:10px" src="' . esc_url( $src ) . '"></iframe>';
}
add_shortcode( 'tma_contact_map', 'tma_shortcode_contact_map' );