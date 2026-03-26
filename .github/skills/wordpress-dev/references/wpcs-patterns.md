# WPCS Patterns — Thor Metal Art

## Function Naming
All custom functions use `tma_` prefix:
```php
function tma_enqueue_scripts() { ... }
function tma_register_post_types() { ... }
function tma_custom_meta_box() { ... }
```

## Escaping Output
```php
// HTML content
echo esc_html( $variable );

// HTML attributes
echo esc_attr( $variable );

// URLs
echo esc_url( $url );

// Translated + escaped
echo esc_html__( 'Text', 'thormetalart' );
esc_html_e( 'Text', 'thormetalart' );

// Rich HTML (whitelisted tags only)
echo wp_kses( $html, 'post' );
echo wp_kses_post( $html );
```

## Sanitizing Input
```php
// Text fields
$clean = sanitize_text_field( wp_unslash( $_POST['field'] ) );

// Email
$email = sanitize_email( $_POST['email'] );

// Integer
$id = absint( $_GET['id'] );

// URL
$url = esc_url_raw( $_POST['url'] );
```

## Database Queries
```php
// ALWAYS use prepare()
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}tma_leads WHERE status = %s AND value > %d",
        $status,
        $min_value
    )
);
```

## Nonces
```php
// In form
wp_nonce_field( 'tma_save_action', 'tma_nonce' );

// Verify
if ( ! wp_verify_nonce( $_POST['tma_nonce'], 'tma_save_action' ) ) {
    wp_die( 'Security check failed' );
}
```

## Hooks Pattern
```php
// Actions
add_action( 'init', 'tma_register_post_types' );
add_action( 'wp_enqueue_scripts', 'tma_enqueue_scripts' );
add_action( 'admin_menu', 'tma_add_admin_pages' );

// Filters
add_filter( 'the_content', 'tma_modify_content' );
add_filter( 'document_title_parts', 'tma_custom_title' );
```

## Translation
```php
// Simple string
__( 'Custom Metal Gates', 'thormetalart' )

// Echo translated
_e( 'Contact Us', 'thormetalart' )

// With placeholder
sprintf( __( 'Showing %d results', 'thormetalart' ), $count )

// Plural
_n( '%d review', '%d reviews', $count, 'thormetalart' )
```
