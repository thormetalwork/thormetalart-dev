<?php

/**
 * Thor Metal Art — Website Page Provisioning
 *
 * Creates and updates service pages and key sales pages for Website V1.
 *
 * @package ThorMetalArt
 */

defined('ABSPATH') || exit;

/**
 * Return services dataset.
 *
 * @return array<string, array<string, mixed>>
 */
function tma_get_service_catalog()
{
	return array(
		'custom-metal-gates-miami'     => array(
			'title'           => 'Custom Metal Gates Miami',
			'label'           => array('en' => 'Custom Gates', 'es' => 'Portones personalizados'),
			'form_value'      => 'custom-gates',
			'portfolio_term'  => 'gates',
			'premium'         => true,
			'hero_heading'    => 'Custom Metal Gates Miami',
			'subheading'      => 'Hand-Crafted. Built to Last. Designed for You.',
			'hero_image'      => '/wp-content/uploads/2026/04/tma-portfolio-waterjet-panel.jpg',
			'intro'           => 'Your gate is the first thing people see. At Thor Metal Art, we design and fabricate custom metal gates that combine security with style. Every gate is built for your property dimensions, design direction, and long-term durability in South Florida conditions.',
			'includes'        => array(
				'Custom design from sketch, inspiration images, or from-scratch concept',
				'Water jet precision cutting for clean and exact shapes',
				'MIG and TIG welding for structural integrity',
				'Custom finish: powder coat, paint, patina, or raw steel',
				'Professional installation and permit support',
				'Free estimate before any commitment',
			),
			'faqs'            => array(
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
		'metal-railings-miami'         => array(
			'title'           => 'Metal Railings Miami',
			'label'           => array('en' => 'Metal Railings', 'es' => 'Barandas y pasamanos'),
			'form_value'      => 'railings',
			'portfolio_term'  => 'railings',
			'premium'         => false,
			'hero_heading'    => 'Metal Railings Miami',
			'subheading'      => 'Custom Design for Stairs, Balconies and Decks.',
			'hero_image'      => '/wp-content/uploads/2026/04/tma-portfolio-tig-welding.jpg',
			'intro'           => 'Every railing should protect and elevate the space visually. We fabricate custom metal railings for staircases, balconies, and pool decks with full code compliance and premium finishes.',
			'includes'        => array(
				'Interior and exterior custom railing systems',
				'Exact on-site measurement and fabrication to spec',
				'Architectural detailing and clean visual lines',
				'Multiple finishes for residential and commercial use',
				'Code-compliant installation',
				'Free estimate and timeline planning',
			),
			'faqs'            => array(
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
		'metal-fences-miami'           => array(
			'title'           => 'Custom Metal Fences Miami',
			'label'           => array('en' => 'Metal Fences', 'es' => 'Cercas ornamentales'),
			'form_value'      => 'fences',
			'portfolio_term'  => 'fences',
			'premium'         => false,
			'hero_heading'    => 'Custom Metal Fences Miami',
			'subheading'      => 'Decorative and Security Solutions Built to Last.',
			'hero_image'      => '/wp-content/uploads/2026/04/tma-workshop-facade.jpg',
			'intro'           => 'A fence should protect the property while matching the architecture. We design and fabricate decorative and security metal fences for homes and businesses in South Florida.',
			'includes'        => array(
				'Perimeter security fence systems',
				'Decorative patterns and modern privacy options',
				'Custom gates integrated with fence layout',
				'Rust-resistant coating options for Miami weather',
				'Professional installation',
				'Free estimate and phased project planning',
			),
			'faqs'            => array(
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
			'title'           => 'Custom Metal Furniture Miami',
			'label'           => array('en' => 'Custom Furniture', 'es' => 'Mobiliario metalico'),
			'form_value'      => 'furniture',
			'portfolio_term'  => 'furniture',
			'premium'         => false,
			'hero_heading'    => 'Custom Metal Furniture Miami',
			'subheading'      => 'One-of-a-Kind Pieces Built to Order.',
			'hero_image'      => '/wp-content/uploads/2026/04/tma-portfolio-stainless-steel.jpg',
			'intro'           => 'Our custom furniture combines industrial precision with artisan craft. We fabricate tables, shelving, frames, and statement pieces designed around your space and concept.',
			'includes'        => array(
				'Design consultation for dimensions and style',
				'Custom fabrication in steel, iron, and mixed materials',
				'Collaboration with wood, stone, or glass elements',
				'Hand-finished details and durable coatings',
				'Delivery and installation support',
				'Free estimate with transparent scope',
			),
			'faqs'            => array(
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
		'metal-stairs-miami'           => array(
			'title'           => 'Metal Stairs Miami',
			'label'           => array('en' => 'Metal Stairs', 'es' => 'Escaleras de metal'),
			'form_value'      => 'stairs',
			'portfolio_term'  => 'stairs',
			'premium'         => false,
			'hero_heading'    => 'Metal Stairs and Handrails Miami',
			'subheading'      => 'Structural Precision with Visual Impact.',
			'hero_image'      => '/wp-content/uploads/2026/04/tma-process-bending.jpg',
			'intro'           => 'We fabricate custom stair systems and handrails for residential and commercial spaces: floating stairs, spiral designs, and industrial-style structures with architectural presence.',
			'includes'        => array(
				'Floating and spiral stair options',
				'Handrails integrated with stair architecture',
				'Structural fabrication with code-aware detailing',
				'Custom finish packages for interior and exterior use',
				'On-site installation and alignment',
				'Free estimate and technical walkthrough',
			),
			'faqs'            => array(
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
 * Backward-compatible service dataset alias.
 *
 * @return array<string, array<string, mixed>>
 */
function tma_get_services()
{
	return tma_get_service_catalog();
}

/**
 * Render the five canonical services for homepage discovery.
 *
 * @return string
 */
function tma_shortcode_service_catalog()
{
	$lang  = function_exists('tma_get_current_language_code') ? tma_get_current_language_code() : 'en';
	$items = '';
	foreach (tma_get_service_catalog() as $slug => $service) {
		$items .= sprintf(
			'<li><a href="%1$s">%2$s</a><span>%3$s</span></li>',
			esc_url(home_url('/' . $slug . '/')),
			esc_html($service['label'][$lang]),
			esc_html($service['subheading'])
		);
	}

	return '<ul class="tma-service-catalog">' . $items . '</ul>';
}
add_shortcode('tma_service_catalog', 'tma_shortcode_service_catalog');

/**
 * Build shared page shell markup for generated pages.
 *
 * @param string $heading Hero heading.
 * @param string $subheading Hero subheading.
 * @param string $hero_image Hero image URL.
 * @param string $body_content Body content.
 * @return string
 */
function tma_page_shell_markup($heading, $subheading, $hero_image, $body_content)
{
	return '<!-- wp:group {"className":"tma-page-shell","layout":{"type":"constrained","wideSize":"1200px"}} --><div class="wp-block-group tma-page-shell">'
		. '<!-- wp:group {"className":"tma-page-hero","style":{"spacing":{"padding":{"top":"48px","bottom":"48px","left":"32px","right":"32px"}},"border":{"radius":"24px"}},"layout":{"type":"constrained","contentSize":"1120px"}} --><div class="wp-block-group tma-page-hero" style="padding-top:48px;padding-right:32px;padding-bottom:48px;padding-left:32px;border-radius:24px">'
		. '<!-- wp:shortcode -->[tma_breadcrumbs]<!-- /wp:shortcode -->'
		. '<!-- wp:cover {"url":"' . esc_url_raw($hero_image) . '","dimRatio":60,"overlayColor":"obsidian","isDark":true,"minHeight":320,"minHeightUnit":"px","className":"tma-service-hero-cover","focalPoint":{"x":0.5,"y":0.45}} --><div class="wp-block-cover is-dark tma-service-hero-cover" style="min-height:320px"><span aria-hidden="true" class="wp-block-cover__background has-obsidian-background-color has-background-dim-60 has-background-dim"></span><img class="wp-block-cover__image-background" alt="Thor Metal Art custom project" src="' . esc_url_raw($hero_image) . '" style="object-position:50% 45%" data-object-fit="cover" data-object-position="50% 45%"/><div class="wp-block-cover__inner-container"><!-- wp:heading {"textAlign":"center","level":2,"style":{"typography":{"fontFamily":"var(--wp--preset--font-family--forjado-display)","fontWeight":"700","textTransform":"uppercase"}}} --><h2 class="wp-block-heading has-text-align-center" style="font-family:var(--wp--preset--font-family--forjado-display);font-weight:700;text-transform:uppercase">' . esc_html($heading) . '</h2><!-- /wp:heading --><!-- wp:paragraph {"align":"center","style":{"typography":{"fontSize":"17px","lineHeight":"1.6"}}} --><p class="has-text-align-center" style="font-size:17px;line-height:1.6">' . esc_html($subheading) . '</p><!-- /wp:paragraph --></div></div><!-- /wp:cover -->'
		. '</div><!-- /wp:group -->'
		. '<!-- wp:group {"className":"tma-page-content","style":{"spacing":{"padding":{"top":"32px","bottom":"32px","left":"32px","right":"32px"}}},"layout":{"type":"constrained","contentSize":"1120px"}} --><div class="wp-block-group tma-page-content" style="padding-top:32px;padding-right:32px;padding-bottom:32px;padding-left:32px">'
		. $body_content
		. '</div><!-- /wp:group -->'
		. '</div><!-- /wp:group -->';
}

/**
 * Build block content for one service page.
 *
 * @param array<string, mixed> $service Service data.
 * @return string
 */
function tma_service_page_content($service)
{
	$includes_items = '';
	foreach ($service['includes'] as $item) {
		$includes_items .= '<!-- wp:list-item --><li>' . esc_html($item) . '</li><!-- /wp:list-item -->';
	}

	$faq_html = '';
	foreach ($service['faqs'] as $faq) {
		$faq_html .= '<!-- wp:html --><details class="tma-faq-item"><summary>' . esc_html($faq['q']) . '</summary><p>' . esc_html($faq['a']) . '</p></details><!-- /wp:html -->';
	}

	$body_content = '<!-- wp:paragraph {"fontSize":"large"} --><p class="has-large-font-size">' . esc_html($service['intro']) . '</p><!-- /wp:paragraph -->'
		. '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">What\'s Included</h2><!-- /wp:heading -->'
		. '<!-- wp:list --><ul>' . $includes_items . '</ul><!-- /wp:list -->'
		. '<!-- wp:shortcode -->[tma_testimonials limit="2" service="' . esc_attr(sanitize_title($service['title'])) . '"]<!-- /wp:shortcode -->'
		. '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Frequently Asked Questions</h2><!-- /wp:heading -->'
		. $faq_html
		. '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">' . esc_html($service['spanish_title']) . '</h2><!-- /wp:heading -->'
		. '<!-- wp:heading {"level":3} --><h3 class="wp-block-heading">' . esc_html($service['spanish_heading']) . '</h3><!-- /wp:heading -->'
		. '<!-- wp:paragraph --><p>' . esc_html($service['spanish_body']) . '</p><!-- /wp:paragraph -->'
		. '<!-- wp:shortcode -->[tma_related_work]<!-- /wp:shortcode -->'
		. '<!-- wp:group {"className":"tma-forjado-cta"} --><div class="wp-block-group tma-forjado-cta"><!-- wp:heading {"level":3} --><h3 class="wp-block-heading">Ready to start your project?</h3><!-- /wp:heading --><!-- wp:paragraph --><p>Request a free estimate with no commitment. We respond within 24 hours.</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Get a Free Estimate</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->';

	return $body_content;
}

/**
 * Render up to three portfolio projects related to the current service.
 *
 * @return string
 */
function tma_shortcode_related_work()
{
	if (! is_page()) {
		return '';
	}

	$slug     = (string) get_post_field('post_name', get_queried_object_id());
	$services = tma_get_service_catalog();
	if (! isset($services[$slug])) {
		return '';
	}

	$query = new WP_Query(
		array(
			'post_type'      => 'tma_portfolio',
			'post_status'    => 'publish',
			'posts_per_page' => 3,
			'no_found_rows'  => true,
			'tax_query'      => array(
				array(
					'taxonomy' => 'tma_project_type',
					'field'    => 'slug',
					'terms'    => $services[$slug]['portfolio_term'],
				),
			),
		)
	);

	$lang    = function_exists('tma_get_current_language_code') ? tma_get_current_language_code() : 'en';
	$title   = 'es' === $lang ? 'Trabajos relacionados' : 'Related work';
	$empty   = 'es' === $lang ? 'Estamos documentando proyectos de este servicio. Contactanos para ver ejemplos recientes.' : 'We are documenting projects for this service. Contact us to see recent examples.';
	$content = '<section class="tma-related-work"><h2>' . esc_html($title) . '</h2>';

	if (! $query->have_posts()) {
		return $content . '<p class="tma-related-work__empty">' . esc_html($empty) . '</p></section>';
	}

	$content .= '<div class="tma-related-work__grid">';
	while ($query->have_posts()) {
		$query->the_post();
		$image = get_the_post_thumbnail(get_the_ID(), 'large', array('class' => 'tma-related-work__image'));
		$content .= sprintf(
			'<article class="tma-related-work__card"><a href="%1$s">%2$s<h3>%3$s</h3></a></article>',
			esc_url(get_permalink()),
			$image,
			esc_html(get_the_title())
		);
	}
	wp_reset_postdata();

	return $content . '</div></section>';
}
add_shortcode('tma_related_work', 'tma_shortcode_related_work');

/**
 * Render the hero owned by the dedicated service template.
 *
 * @return string
 */
function tma_shortcode_service_hero()
{
	if (! is_page()) {
		return '';
	}

	$slug     = (string) get_post_field('post_name', get_queried_object_id());
	$services = tma_get_service_catalog();
	if (! isset($services[$slug])) {
		return '';
	}

	$service = $services[$slug];
	return sprintf(
		'<section class="tma-page-hero tma-service-hero"><div class="tma-service-hero__media"><img src="%1$s" alt="%2$s"></div><div class="tma-service-hero__overlay" aria-hidden="true"></div><div class="tma-service-hero__content">%3$s<p class="tma-page-kicker">Crafted in Miami</p><h1 class="tma-page-title">%4$s</h1><p class="tma-service-hero__subheading">%5$s</p></div></section>',
		esc_url($service['hero_image']),
		esc_attr($service['title']),
		do_shortcode('[tma_breadcrumbs]'),
		esc_html($service['hero_heading']),
		esc_html($service['subheading'])
	);
}
add_shortcode('tma_service_hero', 'tma_shortcode_service_hero');

/**
 * Return additional static pages.
 *
 * @return array<string, array<string, string>>
 */
function tma_get_core_pages()
{
	return array(
		'art-commissions' => array(
			'title'   => 'Metal as Art & Commissions',
			'content' => tma_page_shell_markup(
				'Metal as Art',
				'Original Sculptures and Commissioned Pieces by Karel Frometa - Miami',
				'/wp-content/uploads/2026/04/tma-portfolio-fenix-sculpture.jpg',
				'<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">Artist Statement</h2><!-- /wp:heading --><!-- wp:paragraph --><p>I have been working with metal as both a fabricator and an artist. Every weld, cut, and surface decision is technical and aesthetic at the same time. My work ranges from interior statement pieces to large commissioned installations.</p><!-- /wp:paragraph --><!-- wp:paragraph --><p>Each commission is unique and developed in direct conversation with the client.</p><!-- /wp:paragraph --><!-- wp:heading {"level":2} --><h2 class="wp-block-heading">How to Commission a Piece</h2><!-- /wp:heading --><!-- wp:list --><ul><li>Conversation: we define concept, size, material, and budget.</li><li>Concept and Proposal: sketch and timeline with clear scope.</li><li>Fabrication: built in our Miami studio with progress updates.</li><li>Delivery and Installation: final delivery with on-site support if required.</li></ul><!-- /wp:list --><!-- wp:gallery {"linkTo":"none","columns":3,"className":"tma-service-gallery"} --><figure class="wp-block-gallery has-nested-images columns-3 is-cropped tma-service-gallery"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="/wp-content/uploads/2026/04/tma-portfolio-forged-art-piece.jpg" alt="Forged art piece" /></figure><!-- /wp:image --><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="/wp-content/uploads/2026/04/tma-portfolio-artisan-blade.jpg" alt="Artisan blade metalwork" /></figure><!-- /wp:image --><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="/wp-content/uploads/2026/04/tma-fenix-full.jpg" alt="Phoenix sculpture full body" /></figure><!-- /wp:image --></figure><!-- /wp:gallery --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Commission a Piece</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->'
			),
		),
		'how-we-work'     => array(
			'title'   => 'How We Work',
			'content' => tma_page_shell_markup(
				'How We Work',
				'From first call to finished installation - everything is handled in-house by our Miami team.',
				'/wp-content/uploads/2026/04/tma-karel-welding.jpg',
				'<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><h3>1. Free Estimate</h3><p>Tell us your idea and constraints.</p></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><h3>2. Design &amp; Quote</h3><p>We provide concept and clear pricing.</p></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><h3>3. Production</h3><p>Water jet cutting, welding, and finishing.</p></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><h3>4. Quality Check</h3><p>Structural and finish verification before install.</p></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><h3>5. Installation</h3><p>Final installation and handover with clean-up.</p></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:gallery {"linkTo":"none","columns":4,"className":"tma-service-gallery"} --><figure class="wp-block-gallery has-nested-images columns-4 is-cropped tma-service-gallery"><!-- wp:image {"sizeSlug":"medium_large","linkDestination":"none"} --><figure class="wp-block-image size-medium_large"><img src="/wp-content/uploads/2026/04/tma-process-cutting.jpg" alt="Metal cutting process" /></figure><!-- /wp:image --><!-- wp:image {"sizeSlug":"medium_large","linkDestination":"none"} --><figure class="wp-block-image size-medium_large"><img src="/wp-content/uploads/2026/04/tma-process-bending.jpg" alt="Metal bending process" /></figure><!-- /wp:image --><!-- wp:image {"sizeSlug":"medium_large","linkDestination":"none"} --><figure class="wp-block-image size-medium_large"><img src="/wp-content/uploads/2026/04/tma-process-machine.jpg" alt="Machine precision process" /></figure><!-- /wp:image --><!-- wp:image {"sizeSlug":"medium_large","linkDestination":"none"} --><figure class="wp-block-image size-medium_large"><img src="/wp-content/uploads/2026/04/tma-process-polishing.jpg" alt="Polishing and finishing process" /></figure><!-- /wp:image --></figure><!-- /wp:gallery --><!-- wp:list --><ul><li>Everything in-house</li><li>Water jet + MIG/TIG welding</li><li>Response within 24 hours</li><li>Licensed and insured</li></ul><!-- /wp:list --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button --><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/contact/">Ready to Start? Get a Free Estimate</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div><!-- /wp:group -->'
			),
		),
		'contact'         => array(
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
function tma_create_or_update_generated_page($slug, $title, $content, $type)
{
	$existing = get_page_by_path($slug);
	$template = 'service' === $type ? 'page-service' : 'default';
	$hash     = hash('sha256', $content);

	if ($existing) {
		if ('1' === get_post_meta($existing->ID, '_tma_generated_page', true)) {
			$stored_hash = (string) get_post_meta($existing->ID, '_tma_generated_content_hash', true);
			$current_hash = hash('sha256', (string) $existing->post_content);
			if ('' === $stored_hash || hash_equals($stored_hash, $current_hash)) {
				wp_update_post(
					array(
						'ID'           => $existing->ID,
						'post_title'   => $title,
						'post_content' => $content,
					)
				);
				update_post_meta($existing->ID, '_tma_generated_content_hash', $hash);
			}

			update_post_meta($existing->ID, '_wp_page_template', $template);
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
				'_tma_generated_page'        => '1',
				'_tma_generated_type'        => $type,
				'_tma_generated_content_hash' => $hash,
				'_wp_page_template'           => $template,
			),
		)
	);

	if (is_wp_error($post_id)) {
		return;
	}
}

/**
 * Provision Website V1 pages.
 */
function tma_provision_website_v1_pages()
{
	$services = tma_get_service_catalog();
	foreach ($services as $slug => $service) {
		tma_create_or_update_generated_page($slug, $service['title'], tma_service_page_content($service), 'service');
	}

	$core_pages = tma_get_core_pages();
	foreach ($core_pages as $slug => $page) {
		tma_create_or_update_generated_page($slug, $page['title'], $page['content'], 'core');
	}
}
add_action('after_switch_theme', 'tma_provision_website_v1_pages');

/**
 * Provision pages once after deployment.
 */
function tma_maybe_provision_website_v1_pages_once()
{
	$version = get_option('tma_pages_version', '');
	if ('v6' === $version) {
		return;
	}

	tma_provision_website_v1_pages();
	update_option('tma_pages_version', 'v6', false);
}
add_action('init', 'tma_maybe_provision_website_v1_pages_once', 50);

/**
 * Find attachment id by _wp_attached_file value.
 *
 * @param string $relative_file Relative file path in uploads.
 * @return int
 */
function tma_get_attachment_id_by_file($relative_file)
{
	$attachments = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_wp_attached_file',
			'meta_value'     => $relative_file,
		)
	);

	return ! empty($attachments) ? (int) $attachments[0] : 0;
}

/**
 * Pick an image file for a legacy portfolio post based on taxonomy.
 *
 * @param array<int, string> $term_slugs Term slugs.
 * @param int                $index      Loop index.
 * @return string
 */
function tma_pick_legacy_thumbnail_file($term_slugs, $index)
{
	$map = array(
		'gates'     => array('tma-portfolio-waterjet-panel.jpg', 'tma-workshop-facade.jpg'),
		'railings'  => array('tma-portfolio-tig-welding.jpg', 'tma-detail-weld.jpg'),
		'fences'    => array('tma-workshop-facade.jpg', 'tma-portfolio-waterjet-panel.jpg'),
		'furniture' => array('tma-portfolio-stainless-steel.jpg', 'tma-process-polishing.jpg'),
		'stairs'    => array('tma-process-bending.jpg', 'tma-portfolio-tig-welding.jpg'),
		'art'       => array('tma-portfolio-fenix-sculpture.jpg', 'tma-portfolio-forged-art-piece.jpg'),
	);

	foreach ($term_slugs as $slug) {
		if (isset($map[$slug])) {
			$images = $map[$slug];
			return $images[$index % count($images)];
		}
	}

	$fallback = array('tma-portfolio-fenix-sculpture.jpg', 'tma-karel-welding.jpg');
	return $fallback[$index % count($fallback)];
}

/**
 * Backfill thumbnails for legacy portfolio posts created without featured image.
 */
function tma_backfill_legacy_portfolio_thumbnails_once()
{
	$version = get_option('tma_portfolio_thumbnails_version', '');
	if ('v2' === $version) {
		return;
	}

	$portfolio_ids = get_posts(
		array(
			'post_type'      => 'tma_portfolio',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	foreach ($portfolio_ids as $index => $portfolio_id) {
		if (has_post_thumbnail($portfolio_id)) {
			continue;
		}

		$term_slugs = wp_get_post_terms($portfolio_id, 'tma_project_type', array('fields' => 'slugs'));
		$image_file = tma_pick_legacy_thumbnail_file(is_array($term_slugs) ? $term_slugs : array(), $index);
		$attach_id  = tma_get_attachment_id_by_file('2026/04/' . $image_file);

		if ($attach_id > 0) {
			set_post_thumbnail($portfolio_id, $attach_id);
		}
	}

	update_option('tma_portfolio_thumbnails_version', 'v2', false);
}
add_action('init', 'tma_backfill_legacy_portfolio_thumbnails_once', 60);

/**
 * Admin action to manually trigger generation.
 */
function tma_maybe_provision_website_v1_pages()
{
	if (! current_user_can('manage_options')) {
		return;
	}

	if (! isset($_GET['tma_create_pages'])) {
		return;
	}

	$nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce'] ?? ''));
	if (! wp_verify_nonce($nonce, 'tma_create_pages')) {
		return;
	}

	tma_provision_website_v1_pages();
	wp_safe_redirect(admin_url('edit.php?post_type=page&tma_pages_created=1'));
	exit;
}
add_action('admin_init', 'tma_maybe_provision_website_v1_pages');

/**
 * Contact map shortcode.
 *
 * @return string
 */
function tma_shortcode_contact_map()
{
	$search_query = get_option('tma_public_address', 'Thor Metal Art LLC, Miami, FL');
	$api_key      = defined('GCP_API_KEY') ? GCP_API_KEY : '';

	if (! empty($api_key)) {
		$src = 'https://www.google.com/maps/embed/v1/place?key='
			. rawurlencode($api_key)
			. '&q=' . rawurlencode($search_query)
			. '&zoom=13';
	} else {
		$src = 'https://www.google.com/maps?q=' . rawurlencode($search_query) . '&output=embed';
	}

	return '<iframe title="Thor Metal Art Map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="width:100%;min-height:320px;border:0;border-radius:10px" allowfullscreen src="' . esc_url($src) . '"></iframe>';
}
add_shortcode('tma_contact_map', 'tma_shortcode_contact_map');

// ═══════════════════════════════════════════════════════════════════
// TICKET-WP-036 — Blog: Page, Categories & WordPress Settings
// ═══════════════════════════════════════════════════════════════════

/**
 * Return blog categories dataset.
 *
 * @return array<string, array<string, string>>
 */
function tma_get_blog_categories()
{
	return array(
		'fabrication'    => array(
			'name'        => 'Fabrication Guides',
			'description' => 'Technical guides on metal fabrication processes used at Thor Metal Art.',
		),
		'design-ideas'   => array(
			'name'        => 'Design Ideas',
			'description' => 'Custom metal gate, fence, and furniture design inspiration for Miami properties.',
		),
		'miami-projects' => array(
			'name'        => 'Miami Projects',
			'description' => 'Behind-the-scenes looks at completed metalwork projects in Miami-Dade and Broward.',
		),
		'care-tips'      => array(
			'name'        => 'Care & Maintenance',
			'description' => 'How to maintain metal gates, railings, and fences in South Florida conditions.',
		),
		'metal-art'      => array(
			'name'        => 'Metal Art',
			'description' => 'Custom metal sculptures and commissioned art pieces by Karel Frometa.',
		),
	);
}

/**
 * Create blog categories if they do not exist.
 */
function tma_provision_blog_categories()
{
	$categories = tma_get_blog_categories();

	foreach ($categories as $slug => $data) {
		if (! term_exists($slug, 'category')) {
			wp_insert_term(
				$data['name'],
				'category',
				array(
					'slug'        => $slug,
					'description' => $data['description'],
				)
			);
		}
	}
}

/**
 * Create the Blog index page and configure WordPress blog settings.
 */
function tma_provision_blog()
{
	// 1. Create /blog/ page if it does not exist.
	$existing = get_page_by_path('blog');
	if (! $existing) {
		$blog_page_id = wp_insert_post(
			array(
				'post_title'   => 'Blog',
				'post_name'    => 'blog',
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_author'  => 3,
				'meta_input'   => array(
					'_tma_generated_page' => '1',
					'_tma_generated_type' => 'blog-index',
				),
			)
		);
	} else {
		$blog_page_id = $existing->ID;
	}

	if (is_wp_error($blog_page_id) || ! $blog_page_id) {
		return;
	}

	// 2. Create blog categories.
	tma_provision_blog_categories();

	// 3. Configure WordPress reading settings.
	update_option('show_on_front', 'page', true);
	update_option('page_for_posts', (int) $blog_page_id, true);

	// Keep page_on_front at 0 — FSE front-page.html handles root URL.
	if ('0' === (string) get_option('page_on_front', '0')) {
		update_option('page_on_front', 0, true);
	}

	// 4. Set permalink structure for blog SEO.
	update_option('permalink_structure', '/%category%/%postname%/', true);
	flush_rewrite_rules(false);
}

/**
 * Provision blog once on init.
 */
function tma_maybe_provision_blog_once()
{
	$version = get_option('tma_blog_version', '');
	if ('v1' === $version) {
		return;
	}

	tma_provision_blog();
	update_option('tma_blog_version', 'v1', false);
}
add_action('init', 'tma_maybe_provision_blog_once', 55);
