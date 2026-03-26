<?php
/**
 * Plugin Name: TMA Panel
 * Plugin URI:  https://panel.thormetalart.com
 * Description: Panel ejecutivo para Thor Metal Art — dashboard, documentos, leads y notas.
 * Version:     0.1.0
 * Author:      Thor Metal Art Dev
 * Text Domain: thormetalart
 * Requires PHP: 8.1
 * Requires at least: 6.5
 * License:     Proprietary
 */

defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════════════════════════════
   Constants
   ═══════════════════════════════════════════════════════════════════ */

define( 'TMA_PANEL_VERSION', '0.1.0' );
define( 'TMA_PANEL_PATH', plugin_dir_path( __FILE__ ) );
define( 'TMA_PANEL_URL', plugin_dir_url( __FILE__ ) );
define( 'TMA_PANEL_HOST', 'panel.thormetalart.com' );

/* ═══════════════════════════════════════════════════════════════════
   Includes
   ═══════════════════════════════════════════════════════════════════ */

require_once TMA_PANEL_PATH . 'includes/class-tma-panel-router.php';

/* ═══════════════════════════════════════════════════════════════════
   Init — Router intercepts panel domain before WP query resolution
   ═══════════════════════════════════════════════════════════════════ */

add_action( 'init', function (): void {
	if ( ! isset( $_SERVER['HTTP_HOST'] ) || $_SERVER['HTTP_HOST'] !== TMA_PANEL_HOST ) {
		return;
	}

	$request_uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
	$request_uri = rtrim( $request_uri, '/' );

	// Allow REST API and WordPress core paths to pass through.
	if ( str_starts_with( $request_uri, '/wp-json' )
		|| str_starts_with( $request_uri, '/wp-admin' )
		|| str_starts_with( $request_uri, '/wp-login' ) ) {
		return;
	}

	// Not logged in → redirect to login page.
	if ( ! is_user_logged_in() ) {
		if ( $request_uri === '/login' ) {
			TMA_Panel_Router::send_security_headers();
			require_once TMA_PANEL_PATH . 'templates/login.php';
			exit;
		}
		wp_safe_redirect( 'https://' . TMA_PANEL_HOST . '/login' );
		exit;
	}

	// Logged in on /login → redirect to panel root.
	if ( $request_uri === '/login' ) {
		wp_safe_redirect( 'https://' . TMA_PANEL_HOST . '/' );
		exit;
	}

	// Verify role.
	$user        = wp_get_current_user();
	$valid_roles = array( 'tma_admin', 'tma_client', 'administrator' );
	if ( ! array_intersect( $valid_roles, $user->roles ) ) {
		wp_die(
			esc_html__( 'Tu cuenta no tiene permisos para acceder al panel.', 'thormetalart' ),
			esc_html__( 'Acceso denegado', 'thormetalart' ),
			array( 'response' => 403 )
		);
	}

	// Serve panel SPA shell.
	TMA_Panel_Router::send_security_headers();
	require_once TMA_PANEL_PATH . 'templates/panel.php';
	exit;
}, 1 );

/* ═══════════════════════════════════════════════════════════════════
   Disable canonical redirect on panel domain
   ═══════════════════════════════════════════════════════════════════ */

add_filter( 'redirect_canonical', function ( $redirect_url ) {
	if ( isset( $_SERVER['HTTP_HOST'] ) && $_SERVER['HTTP_HOST'] === TMA_PANEL_HOST ) {
		return false;
	}
	return $redirect_url;
} );

/* ═══════════════════════════════════════════════════════════════════
   Allow panel domain as safe redirect target
   ═══════════════════════════════════════════════════════════════════ */

add_filter( 'allowed_redirect_hosts', function ( array $hosts ): array {
	$hosts[] = TMA_PANEL_HOST;
	return $hosts;
} );
