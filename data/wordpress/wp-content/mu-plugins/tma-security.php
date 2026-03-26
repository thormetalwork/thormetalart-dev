<?php
/**
 * Thor Metal Art — WordPress Security Hardening
 *
 * Disables XML-RPC, restricts REST API, removes version info,
 * and applies common security best practices.
 *
 * @package ThorMetalArt
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════
   1. Disable XML-RPC completely
   ═══════════════════════════════════════════ */

add_filter( 'xmlrpc_enabled', '__return_false' );

// Return 403 for any XML-RPC request.
function tma_block_xmlrpc() {
if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
http_response_code( 403 );
exit( 'XML-RPC is disabled.' );
}
}
add_action( 'init', 'tma_block_xmlrpc', 1 );

// Remove XML-RPC link from head.
remove_action( 'wp_head', 'rsd_link' );

/* ═══════════════════════════════════════════
   2. Restrict REST API to authenticated users
   ═══════════════════════════════════════════ */

function tma_restrict_rest_api( $result ) {
if ( is_user_logged_in() ) {
return $result;
}

$allowed_namespaces = array(
'oembed/1.0',
'wp-site-health/v1',
);

$rest_route = trim( $GLOBALS['wp']->query_vars['rest_route'] ?? '', '/' );

foreach ( $allowed_namespaces as $ns ) {
if ( strpos( $rest_route, $ns ) === 0 ) {
return $result;
}
}

return new WP_Error(
'rest_not_logged_in',
__( 'Authentication required.', 'thormetalart' ),
array( 'status' => 401 )
);
}
add_filter( 'rest_authentication_errors', 'tma_restrict_rest_api' );

/* ═══════════════════════════════════════════
   3. Remove WordPress version from headers/meta
   ═══════════════════════════════════════════ */

remove_action( 'wp_head', 'wp_generator' );

function tma_remove_version_from_scripts( $src ) {
if ( strpos( $src, 'ver=' ) ) {
$src = remove_query_arg( 'ver', $src );
}
return $src;
}
add_filter( 'style_loader_src', 'tma_remove_version_from_scripts', 9999 );
add_filter( 'script_loader_src', 'tma_remove_version_from_scripts', 9999 );

/* ═══════════════════════════════════════════
   4. Disable file editing from admin
   ═══════════════════════════════════════════ */

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
define( 'DISALLOW_FILE_EDIT', true );
}

/* ═══════════════════════════════════════════
   5. Remove unnecessary header clutter
   ═══════════════════════════════════════════ */

remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'feed_links_extra', 3 );

/* ═══════════════════════════════════════════
   6. Disable author enumeration
   ═══════════════════════════════════════════ */

function tma_block_author_enum() {
if ( ! is_admin() && isset( $_GET['author'] ) ) {
wp_safe_redirect( home_url(), 301 );
exit;
}
}
add_action( 'template_redirect', 'tma_block_author_enum' );

// Block user enumeration via REST API (already handled by #2 above).
function tma_block_user_rest_endpoint( $endpoints ) {
if ( ! is_user_logged_in() ) {
unset( $endpoints['/wp/v2/users'] );
unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
}
return $endpoints;
}
add_filter( 'rest_endpoints', 'tma_block_user_rest_endpoint' );

/* ═══════════════════════════════════════════
   7. Security headers (supplement to Traefik)
   ═══════════════════════════════════════════ */

function tma_security_headers() {
if ( is_admin() ) {
return;
}
header( 'X-Content-Type-Options: nosniff' );
header( 'X-Frame-Options: SAMEORIGIN' );
header( 'Referrer-Policy: strict-origin-when-cross-origin' );
header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'tma_security_headers' );

/* ═══════════════════════════════════════════
   8. Disable application passwords for non-admin
   ═══════════════════════════════════════════ */

function tma_restrict_app_passwords( $available, $user ) {
if ( ! user_can( $user, 'manage_options' ) ) {
return false;
}
return $available;
}
add_filter( 'wp_is_application_passwords_available_for_user', 'tma_restrict_app_passwords', 10, 2 );

/* ═══════════════════════════════════════════
   9. Limit login attempts (basic)
   ═══════════════════════════════════════════ */

function tma_limit_login_attempts( $user, $password ) {
$ip        = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
$transient = 'tma_login_attempts_' . md5( $ip );
$attempts  = (int) get_transient( $transient );

if ( $attempts >= 5 ) {
return new WP_Error(
'too_many_attempts',
__( 'Too many login attempts. Try again in 15 minutes.', 'thormetalart' )
);
}

return $user;
}
add_filter( 'authenticate', 'tma_limit_login_attempts', 30, 2 );

function tma_track_failed_login( $username ) {
$ip        = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
$transient = 'tma_login_attempts_' . md5( $ip );
$attempts  = (int) get_transient( $transient );
set_transient( $transient, $attempts + 1, 15 * MINUTE_IN_SECONDS );
}
add_action( 'wp_login_failed', 'tma_track_failed_login' );

function tma_reset_login_attempts( $username ) {
$ip        = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
$transient = 'tma_login_attempts_' . md5( $ip );
delete_transient( $transient );
}
add_action( 'wp_login', 'tma_reset_login_attempts' );
