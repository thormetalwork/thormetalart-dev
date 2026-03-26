<?php
/**
 * Thor Metal Art — Custom Post Types
 *
 * Registers the Portfolio CPT with taxonomies and meta fields.
 *
 * @package ThorMetalArt
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register Portfolio Custom Post Type.
 */
function tma_register_portfolio_cpt() {
$labels = array(
'name'                  => __( 'Portfolio', 'thormetalart' ),
'singular_name'         => __( 'Project', 'thormetalart' ),
'menu_name'             => __( 'Portfolio', 'thormetalart' ),
'add_new'               => __( 'Add Project', 'thormetalart' ),
'add_new_item'          => __( 'Add New Project', 'thormetalart' ),
'edit_item'             => __( 'Edit Project', 'thormetalart' ),
'new_item'              => __( 'New Project', 'thormetalart' ),
'view_item'             => __( 'View Project', 'thormetalart' ),
'search_items'          => __( 'Search Portfolio', 'thormetalart' ),
'not_found'             => __( 'No projects found', 'thormetalart' ),
'not_found_in_trash'    => __( 'No projects in trash', 'thormetalart' ),
'all_items'             => __( 'All Projects', 'thormetalart' ),
'archives'              => __( 'Portfolio Archive', 'thormetalart' ),
'featured_image'        => __( 'Project Cover Image', 'thormetalart' ),
'set_featured_image'    => __( 'Set cover image', 'thormetalart' ),
'remove_featured_image' => __( 'Remove cover image', 'thormetalart' ),
);

$args = array(
'labels'              => $labels,
'public'              => true,
'publicly_queryable'  => true,
'show_ui'             => true,
'show_in_menu'        => true,
'show_in_rest'        => true,
'menu_position'       => 5,
'menu_icon'           => 'dashicons-portfolio',
'capability_type'     => 'post',
'has_archive'         => 'portfolio',
'rewrite'             => array( 'slug' => 'portfolio', 'with_front' => false ),
'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
'taxonomies'          => array( 'tma_project_type' ),
'template'            => array(
array( 'core/image', array( 'align' => 'wide' ) ),
array( 'core/paragraph', array( 'placeholder' => __( 'Describe this project...', 'thormetalart' ) ) ),
array( 'core/gallery', array( 'columns' => 3 ) ),
),
);

register_post_type( 'tma_portfolio', $args );
}
add_action( 'init', 'tma_register_portfolio_cpt' );

/**
 * Register Project Type taxonomy.
 */
function tma_register_portfolio_taxonomy() {
$labels = array(
'name'              => __( 'Project Types', 'thormetalart' ),
'singular_name'     => __( 'Project Type', 'thormetalart' ),
'search_items'      => __( 'Search Types', 'thormetalart' ),
'all_items'         => __( 'All Types', 'thormetalart' ),
'parent_item'       => __( 'Parent Type', 'thormetalart' ),
'parent_item_colon' => __( 'Parent Type:', 'thormetalart' ),
'edit_item'         => __( 'Edit Type', 'thormetalart' ),
'update_item'       => __( 'Update Type', 'thormetalart' ),
'add_new_item'      => __( 'Add New Type', 'thormetalart' ),
'new_item_name'     => __( 'New Type Name', 'thormetalart' ),
'menu_name'         => __( 'Project Types', 'thormetalart' ),
);

register_taxonomy( 'tma_project_type', array( 'tma_portfolio' ), array(
'labels'            => $labels,
'hierarchical'      => true,
'public'            => true,
'show_ui'           => true,
'show_in_rest'      => true,
'show_admin_column' => true,
'rewrite'           => array( 'slug' => 'project-type', 'with_front' => false ),
) );
}
add_action( 'init', 'tma_register_portfolio_taxonomy' );

/**
 * Register custom meta fields for portfolio items.
 */
function tma_register_portfolio_meta() {
$meta_fields = array(
'_tma_project_location' => array(
'type'         => 'string',
'description'  => 'Project location (city/neighborhood)',
'single'       => true,
'show_in_rest' => true,
),
'_tma_project_year' => array(
'type'         => 'string',
'description'  => 'Year completed',
'single'       => true,
'show_in_rest' => true,
),
'_tma_project_material' => array(
'type'         => 'string',
'description'  => 'Primary material used',
'single'       => true,
'show_in_rest' => true,
),
);

foreach ( $meta_fields as $key => $args ) {
register_post_meta( 'tma_portfolio', $key, array_merge( $args, array(
'sanitize_callback' => 'sanitize_text_field',
'auth_callback'     => function() {
return current_user_can( 'edit_posts' );
},
) ) );
}
}
add_action( 'init', 'tma_register_portfolio_meta' );

/**
 * Create default project types on activation.
 */
function tma_create_default_project_types() {
$types = array(
'Gates'     => __( 'Custom metal gates and entry systems', 'thormetalart' ),
'Railings'  => __( 'Staircase, balcony, and deck railings', 'thormetalart' ),
'Fences'    => __( 'Security and decorative fencing', 'thormetalart' ),
'Furniture' => __( 'Custom tables, shelving, and fixtures', 'thormetalart' ),
'Stairs'    => __( 'Spiral, floating, and industrial stairs', 'thormetalart' ),
'Art'       => __( 'Sculptures and art commissions', 'thormetalart' ),
);

foreach ( $types as $name => $desc ) {
if ( ! term_exists( $name, 'tma_project_type' ) ) {
wp_insert_term( $name, 'tma_project_type', array( 'description' => $desc ) );
}
}
}
add_action( 'after_switch_theme', 'tma_create_default_project_types' );

/**
 * Admin action to seed default project types.
 */
function tma_maybe_seed_types() {
if ( ! current_user_can( 'manage_options' ) ) {
return;
}
if ( ! isset( $_GET['tma_seed_types'] ) ) {
return;
}
if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'tma_seed_types' ) ) {
return;
}

tma_create_default_project_types();

wp_safe_redirect( admin_url( 'edit-tags.php?taxonomy=tma_project_type&post_type=tma_portfolio&tma_seeded=1' ) );
exit;
}
add_action( 'admin_init', 'tma_maybe_seed_types' );

/**
 * Flush rewrite rules when portfolio CPT is first registered.
 */
function tma_flush_rewrite_once() {
if ( get_option( 'tma_portfolio_rewrite_flushed' ) ) {
return;
}
flush_rewrite_rules();
update_option( 'tma_portfolio_rewrite_flushed', 1 );
}
add_action( 'init', 'tma_flush_rewrite_once', 99 );
