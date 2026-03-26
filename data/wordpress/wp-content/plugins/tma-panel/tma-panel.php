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
require_once TMA_PANEL_PATH . 'includes/class-tma-panel-roles.php';
require_once TMA_PANEL_PATH . 'includes/class-tma-panel-data.php';
require_once TMA_PANEL_PATH . 'includes/class-tma-panel-api.php';
require_once TMA_PANEL_PATH . 'includes/class-tma-panel-audit.php';

/* ═══════════════════════════════════════════════════════════════════
   Activation / Deactivation
   ═══════════════════════════════════════════════════════════════════ */

register_activation_hook( __FILE__, function (): void {
	TMA_Panel_Roles::activate();
	TMA_Panel_Data::maybe_migrate();
	TMA_Panel_Audit::schedule_cleanup();
} );
register_deactivation_hook( __FILE__, function (): void {
	TMA_Panel_Roles::deactivate();
	TMA_Panel_Audit::unschedule_cleanup();
} );

/* ═══════════════════════════════════════════════════════════════════
   Auto-migrate on admin_init (for updates without reactivation)
   ═══════════════════════════════════════════════════════════════════ */

add_action( 'admin_init', array( 'TMA_Panel_Data', 'maybe_migrate' ) );

/* ═══════════════════════════════════════════════════════════════════
   Audit cron scheduling + hook
   ═══════════════════════════════════════════════════════════════════ */

add_action( 'init', array( 'TMA_Panel_Audit', 'schedule_cleanup' ) );
add_action( 'tma_panel_audit_cleanup', array( 'TMA_Panel_Audit', 'cleanup' ) );

/* ═══════════════════════════════════════════════════════════════════
   REST API — Register tma-panel/v1 endpoints
   ═══════════════════════════════════════════════════════════════════ */

add_action( 'rest_api_init', array( 'TMA_Panel_API', 'register_routes' ) );

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

	// Not logged in → public routes.
	if ( ! is_user_logged_in() ) {
		if ( $request_uri === '/login' ) {
			TMA_Panel_Router::send_security_headers();
			require_once TMA_PANEL_PATH . 'templates/login.php';
			exit;
		}
		if ( $request_uri === '/forgot-password' ) {
			TMA_Panel_Router::send_security_headers();
			require_once TMA_PANEL_PATH . 'templates/forgot-password.php';
			exit;
		}
		if ( $request_uri === '/reset-password' ) {
			TMA_Panel_Router::send_security_headers();
			require_once TMA_PANEL_PATH . 'templates/reset-password.php';
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

/* ═══════════════════════════════════════════════════════════════════
   AJAX Login handler
   ═══════════════════════════════════════════════════════════════════ */

add_action( 'wp_ajax_nopriv_tma_panel_login', 'tma_panel_handle_login' );
add_action( 'wp_ajax_tma_panel_login', 'tma_panel_handle_login' );

function tma_panel_handle_login(): void {
	if ( ! isset( $_POST['tma_login_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tma_login_nonce'] ) ), 'tma_panel_login' ) ) {
		wp_send_json_error( 'Nonce inválido.', 403 );
	}

	// Rate limiting: 5 attempts per 15 minutes per IP.
	$ip            = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0' );
	$transient_key = 'tma_panel_login_attempts_' . md5( $ip );
	$attempts      = (int) get_transient( $transient_key );

	if ( $attempts >= 5 ) {
		wp_send_json_error( 'Demasiados intentos. Intenta de nuevo en 15 minutos.' );
	}

	$log      = sanitize_text_field( wp_unslash( $_POST['log'] ?? '' ) );
	$pwd      = $_POST['pwd'] ?? '';
	$remember = ! empty( $_POST['rememberme'] );

	$user = wp_signon( array(
		'user_login'    => $log,
		'user_password' => $pwd,
		'remember'      => $remember,
	), is_ssl() );

	if ( is_wp_error( $user ) ) {
		set_transient( $transient_key, $attempts + 1, 15 * MINUTE_IN_SECONDS );
		wp_send_json_error( 'Credenciales inválidas.' );
	}

	$valid_roles = array( 'tma_admin', 'tma_client', 'administrator' );
	if ( ! array_intersect( $valid_roles, $user->roles ) ) {
		wp_destroy_current_session();
		set_transient( $transient_key, $attempts + 1, 15 * MINUTE_IN_SECONDS );
		wp_send_json_error( 'Tu cuenta no tiene permisos para acceder al panel.' );
	}

	// Clear rate limiter on success.
	delete_transient( $transient_key );

	wp_send_json_success( array( 'redirect' => 'https://' . TMA_PANEL_HOST . '/' ) );
}
