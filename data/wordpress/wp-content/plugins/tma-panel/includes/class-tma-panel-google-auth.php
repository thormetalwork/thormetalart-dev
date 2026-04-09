<?php
/**
 * TMA Panel — Google OAuth2 Token Manager
 *
 * Handles OAuth2 refresh token → access token exchange for Google APIs.
 * Uses WordPress transients to cache the access token (expires ~55 min).
 *
 * Required constants (defined in wp-config.php from env):
 *   GOOGLE_OAUTH_CLIENT_ID
 *   GOOGLE_OAUTH_CLIENT_SECRET
 *   GOOGLE_OAUTH_REFRESH_TOKEN
 *
 * @package ThorMetalArt\Panel
 * @since   0.5.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Google_Auth {

	/**
	 * Google OAuth2 token endpoint.
	 */
	private const TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';

	/**
	 * Transient key for cached access token.
	 */
	private const TRANSIENT_KEY = 'tma_google_access_token';

	/**
	 * Check if OAuth2 credentials are configured.
	 *
	 * @return bool True if all three constants are non-empty.
	 */
	public static function is_configured(): bool {
		return '' !== self::get_constant( 'GOOGLE_OAUTH_CLIENT_ID' )
			&& '' !== self::get_constant( 'GOOGLE_OAUTH_CLIENT_SECRET' )
			&& '' !== self::get_constant( 'GOOGLE_OAUTH_REFRESH_TOKEN' );
	}

	/**
	 * Get a valid access token. Uses transient cache; refreshes only when expired.
	 *
	 * @return string|WP_Error Access token string, or WP_Error on failure.
	 */
	public static function get_access_token() {
		$cached = get_transient( self::TRANSIENT_KEY );
		if ( false !== $cached && is_string( $cached ) ) {
			return $cached;
		}

		return self::refresh_access_token();
	}

	/**
	 * Force-refresh the access token from Google.
	 *
	 * @return string|WP_Error New access token, or WP_Error on failure.
	 */
	public static function refresh_access_token() {
		if ( ! self::is_configured() ) {
			return new WP_Error(
				'tma_oauth_not_configured',
				'Google OAuth2 credentials not configured.'
			);
		}

		$response = wp_remote_post(
			self::TOKEN_ENDPOINT,
			array(
				'timeout' => 15,
				'body'    => array(
					'client_id'     => self::get_constant( 'GOOGLE_OAUTH_CLIENT_ID' ),
					'client_secret' => self::get_constant( 'GOOGLE_OAUTH_CLIENT_SECRET' ),
					'refresh_token' => self::get_constant( 'GOOGLE_OAUTH_REFRESH_TOKEN' ),
					'grant_type'    => 'refresh_token',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			error_log( 'TMA Panel ERROR: OAuth2 refresh failed — ' . $response->get_error_message() );
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $code || empty( $body['access_token'] ) ) {
			$error_desc = $body['error_description'] ?? ( $body['error'] ?? 'Unknown error' );
			error_log( sprintf( 'TMA Panel ERROR: OAuth2 token refresh HTTP %d — %s', $code, $error_desc ) );
			return new WP_Error(
				'tma_oauth_refresh_failed',
				sprintf( 'Token refresh failed (HTTP %d): %s', $code, $error_desc )
			);
		}

		$access_token = sanitize_text_field( $body['access_token'] );
		$expires_in   = isset( $body['expires_in'] ) ? (int) $body['expires_in'] : 3600;

		// Cache for 5 minutes less than actual expiry to avoid edge cases.
		$cache_ttl = max( $expires_in - 300, 60 );
		set_transient( self::TRANSIENT_KEY, $access_token, $cache_ttl );

		return $access_token;
	}

	/**
	 * Make an authenticated GET request to a Google API.
	 *
	 * @param string $url     Full API URL.
	 * @param array  $headers Additional headers (merged with Authorization).
	 * @return array|WP_Error Decoded JSON response body, or WP_Error.
	 */
	public static function api_get( string $url, array $headers = array() ) {
		$token = self::get_access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 30,
				'headers' => array_merge(
					array( 'Authorization' => 'Bearer ' . $token ),
					$headers
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $code >= 400 ) {
			$msg = $body['error']['message'] ?? 'API error';
			error_log( sprintf( 'TMA Panel ERROR: Google API GET %s — HTTP %d: %s', $url, $code, $msg ) );
			return new WP_Error( 'tma_google_api_error', sprintf( 'HTTP %d: %s', $code, $msg ) );
		}

		return $body;
	}

	/**
	 * Make an authenticated POST request to a Google API.
	 *
	 * @param string $url  Full API URL.
	 * @param array  $body JSON-serializable request body.
	 * @return array|WP_Error Decoded JSON response body, or WP_Error.
	 */
	public static function api_post( string $url, array $body = array() ) {
		$token = self::get_access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$response = wp_remote_post(
			$url,
			array(
				'timeout' => 30,
				'headers' => array(
					'Authorization' => 'Bearer ' . $token,
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode( $body ),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$resp_body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( $code >= 400 ) {
			$msg = $resp_body['error']['message'] ?? 'API error';
			error_log( sprintf( 'TMA Panel ERROR: Google API POST %s — HTTP %d: %s', $url, $code, $msg ) );
			return new WP_Error( 'tma_google_api_error', sprintf( 'HTTP %d: %s', $code, $msg ) );
		}

		return $resp_body;
	}

	/**
	 * Safely read a constant (handles undefined).
	 *
	 * @param string $name Constant name.
	 * @return string Value or empty string.
	 */
	private static function get_constant( string $name ): string {
		return defined( $name ) ? (string) constant( $name ) : '';
	}
}
