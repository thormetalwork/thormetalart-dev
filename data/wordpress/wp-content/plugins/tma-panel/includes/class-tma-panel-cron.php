<?php
/**
 * TMA Panel — External KPI Sync Cron
 *
 * Schedules and runs periodic sync tasks for GBP, GA4 and Instagram.
 * If API keys are missing, logs warnings and skips source without failing.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Cron {

	/**
	 * Cron hook name.
	 */
	private const EVENT_HOOK = 'tma_panel_sync_external_kpis';

	/**
	 * Ensure daily cron event exists.
	 */
	public static function schedule_event(): void {
		if ( ! wp_next_scheduled( self::EVENT_HOOK ) ) {
			wp_schedule_event( time() + 60, 'daily', self::EVENT_HOOK );
		}
	}

	/**
	 * Remove scheduled cron event.
	 */
	public static function unschedule_event(): void {
		$timestamp = wp_next_scheduled( self::EVENT_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::EVENT_HOOK );
		}
	}

	/**
	 * Sync all external sources.
	 */
	public static function sync_all_sources(): void {
		self::sync_source( 'gbp' );
		self::sync_source( 'ga4' );
		self::sync_source( 'instagram' );
	}

	/**
	 * Sync one source into panel_kpis table.
	 *
	 * @param string $source Source key: gbp|ga4|instagram.
	 * @return bool True if inserted data, false when skipped.
	 */
	public static function sync_source( string $source ): bool {
		$key = self::get_api_key( $source );
		if ( '' === $key ) {
			error_log( sprintf( 'TMA Panel WARN: Missing API key for source %s', $source ) );
			return false;
		}

		$data = self::get_mock_payload( $source );
		if ( empty( $data ) ) {
			return false;
		}

		global $wpdb;
		$table  = $wpdb->prefix . 'panel_kpis';
		$period = wp_date( 'Y-m' );

		foreach ( $data as $metric => $value ) {
			$wpdb->insert(
				$table,
				array(
					'metric'   => $metric,
					'value'    => (float) $value,
					'period'   => $period,
					'category' => $source,
				),
				array( '%s', '%f', '%s', '%s' )
			);
		}

		return true;
	}

	/**
	 * Resolve API key for given source.
	 *
	 * @param string $source Source key.
	 * @return string
	 */
	private static function get_api_key( string $source ): string {
		if ( 'gbp' === $source ) {
			return (string) ( getenv( 'GBP_API_KEY' ) ?: ( defined( 'GBP_API_KEY' ) ? GBP_API_KEY : '' ) );
		}

		if ( 'ga4' === $source ) {
			return (string) ( getenv( 'GA4_API_KEY' ) ?: ( defined( 'GA4_API_KEY' ) ? GA4_API_KEY : '' ) );
		}

		if ( 'instagram' === $source ) {
			return (string) ( getenv( 'IG_ACCESS_TOKEN' ) ?: ( defined( 'IG_ACCESS_TOKEN' ) ? IG_ACCESS_TOKEN : '' ) );
		}

		return '';
	}

	/**
	 * Demo payload for each source.
	 *
	 * @param string $source Source key.
	 * @return array<string,float>
	 */
	private static function get_mock_payload( string $source ): array {
		if ( 'gbp' === $source ) {
			return array(
				'reviews'     => 34,
				'impressions' => 6400,
				'actions'     => 112,
			);
		}

		if ( 'ga4' === $source ) {
			return array(
				'sessions'        => 760,
				'users'           => 540,
				'conversion_rate' => 3.8,
			);
		}

		if ( 'instagram' === $source ) {
			return array(
				'followers'       => 1240,
				'reach'           => 5200,
				'engagement_rate' => 4.2,
			);
		}

		return array();
	}
}
