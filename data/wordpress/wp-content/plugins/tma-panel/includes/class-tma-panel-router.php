<?php
/**
 * TMA Panel — Router
 *
 * Intercepts requests to panel.thormetalart.com and serves the panel template.
 * Does not interfere with the main site (thormetalart.com).
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Router {

	/**
	 * Check if current request is to the panel domain.
	 *
	 * @return bool
	 */
	public static function is_panel_request(): bool {
		$host = $_SERVER['HTTP_HOST'] ?? '';
		return $host === TMA_PANEL_HOST;
	}

	/**
	 * Send security headers for panel responses.
	 */
	public static function send_security_headers(): void {
		header( 'X-Content-Type-Options: nosniff' );
		header( 'X-Frame-Options: DENY' );
		header( 'Referrer-Policy: strict-origin' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
		header( 'X-Robots-Tag: noindex, nofollow' );
		header( "Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data:; connect-src 'self'" );
	}
}
