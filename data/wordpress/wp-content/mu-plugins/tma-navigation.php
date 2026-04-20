<?php
/**
 * Thor Metal Art — Navigation Helpers
 *
 * Registers menus and utility shortcodes used by block templates.
 *
 * @package ThorMetalArt
 */

defined( 'ABSPATH' ) || exit;

/**
 * Resolve current language code (en|es) with TranslatePress fallback.
 *
 * @return string
 */
if ( ! function_exists( 'tma_get_current_language_code' ) ) {
function tma_get_current_language_code() {
	if ( function_exists( 'trp_get_current_language' ) ) {
		$current = (string) trp_get_current_language();
		if ( 0 === strpos( strtolower( $current ), 'es' ) ) {
			return 'es';
		}
		return 'en';
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
	$request_uri = strtolower( $request_uri );

	if ( '/es' === $request_uri || 0 === strpos( $request_uri, '/es/' ) ) {
		return 'es';
	}

	return 'en';
}
}

/**
 * Translate portfolio UI labels to Spanish when current language is ES.
 *
 * @param string $text English source text.
 * @return string
 */
function tma_translate_portfolio_ui_label( $text ) {
	if ( 'es' !== tma_get_current_language_code() ) {
		return $text;
	}

	$map = array(
		'Home'                                           => 'Inicio',
		'Portfolio'                                      => 'Portafolio',
		'All'                                            => 'Todos',
		'Location:'                                      => 'Ubicacion:',
		'Year:'                                          => 'Ano:',
		'Material:'                                      => 'Material:',
		'Our Work'                                       => 'Nuestro Trabajo',
		'Every piece is custom. Every project is unique.' => 'Cada pieza es personalizada. Cada proyecto es unico.',
		'Portfolio coming soon. Contact us to see examples of our work.' => 'Portafolio en camino. Contactanos para ver ejemplos de nuestro trabajo.',
		'Request a Quote'                                => 'Solicitar Cotizacion',
		'Back to Portfolio'                              => 'Volver al Portafolio',
		'No projects found in this category yet.'        => 'No se encontraron proyectos en esta categoria.',
	);

	return isset( $map[ $text ] ) ? $map[ $text ] : $text;
}

/**
 * Translate project type names on ES pages.
 *
 * @param string $name English term name.
 * @return string
 */
function tma_translate_project_type_name( $name ) {
	if ( 'es' !== tma_get_current_language_code() ) {
		return $name;
	}

	$map = array(
		'Art'       => 'Arte',
		'Gates'     => 'Portones',
		'Railings'  => 'Barandas',
		'Fences'    => 'Cercas',
		'Furniture' => 'Muebles',
		'Stairs'    => 'Escaleras',
	);

	return isset( $map[ $name ] ) ? $map[ $name ] : $name;
}

/**
 * Register navigation menu locations.
 */
function tma_register_nav_menus() {
	register_nav_menus(
		array(
			'tma-primary'  => __( 'TMA Primary Menu', 'thormetalart' ),
			'tma-services' => __( 'TMA Services Menu', 'thormetalart' ),
			'tma-footer'   => __( 'TMA Footer Menu', 'thormetalart' ),
		)
	);
}
add_action( 'init', 'tma_register_nav_menus' );

/**
 * Seed default menu structure for Website V1.
 */
function tma_seed_navigation_menus() {
	$already_seeded = get_option( 'tma_nav_seeded', false );
	if ( $already_seeded ) {
		return;
	}

	$menus = array(
		'tma-primary'  => 'TMA Primary Menu',
		'tma-services' => 'TMA Services Menu',
		'tma-footer'   => 'TMA Footer Menu',
	);

	$menu_ids = array();
	foreach ( $menus as $location => $name ) {
		$menu = wp_get_nav_menu_object( $name );
		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $name );
		} else {
			$menu_id = (int) $menu->term_id;
		}
		$menu_ids[ $location ] = $menu_id;
	}

	$service_links = array(
		array( 'label' => 'Custom Gates', 'url' => home_url( '/custom-metal-gates-miami/' ) ),
		array( 'label' => 'Metal Railings', 'url' => home_url( '/metal-railings-miami/' ) ),
		array( 'label' => 'Metal Fences', 'url' => home_url( '/metal-fences-miami/' ) ),
		array( 'label' => 'Custom Furniture', 'url' => home_url( '/custom-metal-furniture-miami/' ) ),
		array( 'label' => 'Metal Stairs', 'url' => home_url( '/metal-stairs-miami/' ) ),
	);

	$services_menu_id = $menu_ids['tma-services'];
	foreach ( $service_links as $item ) {
		wp_update_nav_menu_item(
			$services_menu_id,
			0,
			array(
				'menu-item-title'  => $item['label'],
				'menu-item-url'    => $item['url'],
				'menu-item-status' => 'publish',
			)
		);
	}

	$primary_menu_id  = $menu_ids['tma-primary'];
	$services_parent  = wp_update_nav_menu_item(
		$primary_menu_id,
		0,
		array(
			'menu-item-title'  => 'Services',
			'menu-item-url'    => '#',
			'menu-item-status' => 'publish',
		)
	);

	foreach ( $service_links as $item ) {
		wp_update_nav_menu_item(
			$primary_menu_id,
			0,
			array(
				'menu-item-title'     => $item['label'],
				'menu-item-url'       => $item['url'],
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $services_parent,
			)
		);
	}

	$primary_links = array(
		array( 'label' => 'Art', 'url' => home_url( '/art-commissions/' ) ),
		array( 'label' => 'How We Work', 'url' => home_url( '/how-we-work/' ) ),
		array( 'label' => 'Portfolio', 'url' => home_url( '/portfolio/' ) ),
		array( 'label' => 'Contact', 'url' => home_url( '/contact/' ) ),
	);

	foreach ( $primary_links as $item ) {
		wp_update_nav_menu_item(
			$primary_menu_id,
			0,
			array(
				'menu-item-title'  => $item['label'],
				'menu-item-url'    => $item['url'],
				'menu-item-status' => 'publish',
			)
		);
	}

	$footer_menu_id = $menu_ids['tma-footer'];
	$footer_links   = array(
		array( 'label' => 'Home', 'url' => home_url( '/' ) ),
		array( 'label' => 'Custom Gates', 'url' => home_url( '/custom-metal-gates-miami/' ) ),
		array( 'label' => 'Metal Railings', 'url' => home_url( '/metal-railings-miami/' ) ),
		array( 'label' => 'Metal Fences', 'url' => home_url( '/metal-fences-miami/' ) ),
		array( 'label' => 'Custom Furniture', 'url' => home_url( '/custom-metal-furniture-miami/' ) ),
		array( 'label' => 'Metal Stairs', 'url' => home_url( '/metal-stairs-miami/' ) ),
		array( 'label' => 'Art', 'url' => home_url( '/art-commissions/' ) ),
		array( 'label' => 'Portfolio', 'url' => home_url( '/portfolio/' ) ),
		array( 'label' => 'Contact', 'url' => home_url( '/contact/' ) ),
		array( 'label' => 'Privacy Policy', 'url' => home_url( '/privacy-policy/' ) ),
	);

	foreach ( $footer_links as $item ) {
		wp_update_nav_menu_item(
			$footer_menu_id,
			0,
			array(
				'menu-item-title'  => $item['label'],
				'menu-item-url'    => $item['url'],
				'menu-item-status' => 'publish',
			)
		);
	}

	set_theme_mod(
		'nav_menu_locations',
		array(
			'tma-primary'  => $primary_menu_id,
			'tma-services' => $services_menu_id,
			'tma-footer'   => $footer_menu_id,
		)
	);

	update_option( 'tma_nav_seeded', 1, false );
}
add_action( 'init', 'tma_seed_navigation_menus', 30 );

/**
 * Return current year string for footer.
 *
 * @return string
 */
function tma_shortcode_current_year() {
	return '&copy; ' . esc_html( gmdate( 'Y' ) );
}
add_shortcode( 'tma_current_year', 'tma_shortcode_current_year' );

/**
 * Build breadcrumb UI based on current query.
 *
 * @return string
 */
function tma_shortcode_breadcrumbs() {
	if ( is_front_page() ) {
		return '';
	}

	$items   = array();
	$items[] = sprintf( '<a href="%s">%s</a>', esc_url( home_url( '/' ) ), esc_html( tma_translate_portfolio_ui_label( 'Home' ) ) );

	if ( is_post_type_archive( 'tma_portfolio' ) ) {
		$items[] = '<span class="current-item">' . esc_html( tma_translate_portfolio_ui_label( 'Portfolio' ) ) . '</span>';
	} elseif ( is_singular( 'tma_portfolio' ) ) {
		$items[] = sprintf( '<a href="%s">%s</a>', esc_url( get_post_type_archive_link( 'tma_portfolio' ) ), esc_html( tma_translate_portfolio_ui_label( 'Portfolio' ) ) );
		$items[] = '<span class="current-item">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_page() ) {
		$items[] = '<span class="current-item">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_tax( 'tma_project_type' ) ) {
		$term    = get_queried_object();
		$items[] = sprintf( '<a href="%s">%s</a>', esc_url( get_post_type_archive_link( 'tma_portfolio' ) ), esc_html( tma_translate_portfolio_ui_label( 'Portfolio' ) ) );
		$items[] = '<span class="current-item">' . esc_html( tma_translate_project_type_name( $term->name ) ) . '</span>';
	}

	return '<nav class="tma-breadcrumbs" aria-label="Breadcrumb">' . implode( ' &gt; ', $items ) . '</nav>';
}
add_shortcode( 'tma_breadcrumbs', 'tma_shortcode_breadcrumbs' );

/**
 * Render portfolio meta fields in single view.
 *
 * @return string
 */
function tma_shortcode_portfolio_meta() {
	if ( ! is_singular( 'tma_portfolio' ) ) {
		return '';
	}

	$post_id  = get_the_ID();
	$location = get_post_meta( $post_id, 'tma_project_location', true );
	$year     = get_post_meta( $post_id, 'tma_project_year', true );
	$material = get_post_meta( $post_id, 'tma_project_material', true );

	$items = array();
	if ( $location ) {
		$items[] = '<strong>' . esc_html( tma_translate_portfolio_ui_label( 'Location:' ) ) . '</strong> ' . esc_html( $location );
	}
	if ( $year ) {
		$items[] = '<strong>' . esc_html( tma_translate_portfolio_ui_label( 'Year:' ) ) . '</strong> ' . esc_html( $year );
	}
	if ( $material ) {
		$items[] = '<strong>' . esc_html( tma_translate_portfolio_ui_label( 'Material:' ) ) . '</strong> ' . esc_html( $material );
	}

	if ( empty( $items ) ) {
		return '';
	}

	return '<p>' . implode( ' | ', $items ) . '</p>';
}
add_shortcode( 'tma_portfolio_meta', 'tma_shortcode_portfolio_meta' );

/**
 * Render simple portfolio taxonomy filters.
 *
 * @return string
 */
function tma_shortcode_portfolio_filters() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'tma_project_type',
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) ) {
		return '';
	}

	$current = get_query_var( 'tma_project_type' );
	$out     = '<div class="tma-portfolio-filters">';
	$out    .= sprintf( '<a class="%s" href="%s">%s</a>', empty( $current ) ? 'active' : '', esc_url( get_post_type_archive_link( 'tma_portfolio' ) ), esc_html( tma_translate_portfolio_ui_label( 'All' ) ) );

	foreach ( $terms as $term ) {
		$out .= sprintf(
			'<a class="%s" href="%s">%s</a>',
			$current === $term->slug ? 'active' : '',
			esc_url( get_term_link( $term ) ),
			esc_html( tma_translate_project_type_name( $term->name ) )
		);
	}

	$out .= '</div>';
	return $out;
}
add_shortcode( 'tma_portfolio_filters', 'tma_shortcode_portfolio_filters' );

/**
 * Keep project type names translated in frontend term output.
 *
 * @param string       $name Term name.
 * @param WP_Term|null $term Term object.
 * @return string
 */
function tma_filter_project_type_term_name( $name, $term ) {
	if ( is_admin() ) {
		return $name;
	}

	if ( ! is_object( $term ) || ! isset( $term->taxonomy ) || 'tma_project_type' !== $term->taxonomy ) {
		return $name;
	}

	return tma_translate_project_type_name( $name );
}
add_filter( 'term_name', 'tma_filter_project_type_term_name', 10, 2 );

/**
 * Render small EN/ES i18n strings for block templates.
 *
 * @param array<string, string> $atts Shortcode attributes.
 * @return string
 */
function tma_shortcode_i18n( $atts ) {
	$atts = shortcode_atts(
		array(
			'key' => '',
		),
		$atts,
		'tma_i18n'
	);

	$key  = sanitize_text_field( (string) $atts['key'] );
	$lang = tma_get_current_language_code();

	$map = array(
		'our_work' => array(
			'en' => 'Our Work',
			'es' => 'Nuestro Trabajo',
		),
		'portfolio_archive_subtitle' => array(
			'en' => 'Every piece is custom. Every project is unique.',
			'es' => 'Cada pieza es personalizada. Cada proyecto es unico.',
		),
		'portfolio_empty_state' => array(
			'en' => 'Portfolio coming soon. Contact us to see examples of our work.',
			'es' => 'Portafolio en camino. Contactanos para ver ejemplos de nuestro trabajo.',
		),
		'request_quote' => array(
			'en' => 'Request a Quote',
			'es' => 'Solicitar Cotizacion',
		),
		'back_to_portfolio' => array(
			'en' => 'Back to Portfolio',
			'es' => 'Volver al Portafolio',
		),
		'portfolio_tax_empty' => array(
			'en' => 'No projects found in this category yet.',
			'es' => 'No se encontraron proyectos en esta categoria.',
		),
	);

	if ( ! isset( $map[ $key ] ) ) {
		return '';
	}

	if ( isset( $map[ $key ][ $lang ] ) ) {
		return esc_html( $map[ $key ][ $lang ] );
	}

	return esc_html( $map[ $key ]['en'] );
}
add_shortcode( 'tma_i18n', 'tma_shortcode_i18n' );

/**
 * Sync selected pages into menus on publish.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function tma_sync_new_page_to_menus( $post_id, $post ) {
	if ( 'page' !== $post->post_type || 'publish' !== $post->post_status ) {
		return;
	}

	$tracked_slugs = array(
		'custom-metal-gates-miami'     => 'Custom Gates',
		'metal-railings-miami'         => 'Metal Railings',
		'metal-fences-miami'           => 'Metal Fences',
		'custom-metal-furniture-miami' => 'Custom Furniture',
		'metal-stairs-miami'           => 'Metal Stairs',
		'art-commissions'              => 'Art',
		'how-we-work'                  => 'How We Work',
		'contact'                      => 'Contact',
	);

	if ( ! isset( $tracked_slugs[ $post->post_name ] ) ) {
		return;
	}

	$locations = get_nav_menu_locations();
	if ( empty( $locations['tma-footer'] ) ) {
		return;
	}

	$footer_menu_id = (int) $locations['tma-footer'];
	$existing       = wp_get_nav_menu_items( $footer_menu_id );
	$exists         = false;
	foreach ( (array) $existing as $item ) {
		if ( (int) $item->object_id === (int) $post_id ) {
			$exists = true;
			break;
		}
	}

	if ( ! $exists ) {
		wp_update_nav_menu_item(
			$footer_menu_id,
			0,
			array(
				'menu-item-title'     => $tracked_slugs[ $post->post_name ],
				'menu-item-object-id' => $post_id,
				'menu-item-object'    => 'page',
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			)
		);
	}
}
add_action( 'save_post', 'tma_sync_new_page_to_menus', 10, 2 );
