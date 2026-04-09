<?php
/**
 * TMA Panel — External KPI Sync Cron
 *
 * Schedules and runs periodic sync tasks for GA4, Search Console, GBP and Instagram.
 * Uses OAuth2 via TMA_Panel_Google_Auth for GA4 Data API and Search Console API.
 * If credentials are missing, logs warnings and skips source without failing.
 *
 * @package ThorMetalArt\Panel
 * @since   0.5.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Cron {

	/**
	 * Cron hook name.
	 */
	private const EVENT_HOOK = 'tma_panel_sync_external_kpis';

	/**
	 * GA4 Data API base URL.
	 */
	private const GA4_API_BASE = 'https://analyticsdata.googleapis.com/v1beta';

	/**
	 * Search Console API base URL.
	 */
	private const GSC_API_BASE = 'https://www.googleapis.com/webmasters/v3';

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
		self::sync_source( 'ga4' );
		self::sync_source( 'gsc' );
		self::sync_source( 'gbp' );
		self::sync_source( 'instagram' );
	}

	/**
	 * Sync one source into panel_kpis table.
	 *
	 * @param string $source Source key: ga4|gsc|gbp|instagram.
	 * @return bool True if inserted data, false when skipped.
	 */
	public static function sync_source( string $source ): bool {
		$data = array();

		switch ( $source ) {
			case 'ga4':
				$data = self::fetch_ga4_data();
				break;
			case 'gsc':
				$data = self::fetch_gsc_data();
				break;
			case 'gbp':
				$data = self::fetch_gbp_data();
				break;
			case 'instagram':
				$data = self::fetch_instagram_data();
				break;
			default:
				error_log( sprintf( 'TMA Panel WARN: Unknown source %s', $source ) );
				return false;
		}

		if ( empty( $data ) ) {
			return false;
		}

		return self::store_kpis( $data, $source );
	}

	/* ═══════════════════════════════════════════════════════════════
	   GA4 DATA API (Real)
	   ═══════════════════════════════════════════════════════════════ */

	/**
	 * Fetch real GA4 metrics via GA4 Data API (v1beta).
	 *
	 * @return array<string,float> Metric => value pairs, or empty on failure.
	 */
	private static function fetch_ga4_data(): array {
		if ( ! class_exists( 'TMA_Panel_Google_Auth' ) || ! TMA_Panel_Google_Auth::is_configured() ) {
			error_log( 'TMA Panel WARN: Google OAuth2 not configured — skipping GA4 sync.' );
			return array();
		}

		$property_id = defined( 'GA4_PROPERTY_ID' ) ? GA4_PROPERTY_ID : '';
		if ( '' === $property_id ) {
			error_log( 'TMA Panel WARN: GA4_PROPERTY_ID not defined — skipping GA4 sync.' );
			return array();
		}

		// Query last 30 days of aggregated metrics.
		$url  = self::GA4_API_BASE . '/' . $property_id . ':runReport';
		$body = array(
			'dateRanges' => array(
				array(
					'startDate' => '30daysAgo',
					'endDate'   => 'yesterday',
				),
			),
			'metrics'    => array(
				array( 'name' => 'sessions' ),
				array( 'name' => 'totalUsers' ),
				array( 'name' => 'screenPageViews' ),
				array( 'name' => 'averageSessionDuration' ),
				array( 'name' => 'conversions' ),
				array( 'name' => 'bounceRate' ),
			),
		);

		$result = TMA_Panel_Google_Auth::api_post( $url, $body );
		if ( is_wp_error( $result ) ) {
			error_log( 'TMA Panel ERROR: GA4 runReport failed — ' . $result->get_error_message() );
			return array();
		}

		$data = array();
		if ( ! empty( $result['rows'][0]['metricValues'] ) ) {
			$values = $result['rows'][0]['metricValues'];
			$data['sessions']          = (float) ( $values[0]['value'] ?? 0 );
			$data['users']             = (float) ( $values[1]['value'] ?? 0 );
			$data['pageviews']         = (float) ( $values[2]['value'] ?? 0 );
			$data['avg_session_dur']   = round( (float) ( $values[3]['value'] ?? 0 ), 1 );
			$data['conversions']       = (float) ( $values[4]['value'] ?? 0 );
			$data['bounce_rate']       = round( (float) ( $values[5]['value'] ?? 0 ) * 100, 1 );
		}

		// Also fetch top pages.
		$top_pages = self::fetch_ga4_top_pages( $property_id );
		if ( ! empty( $top_pages ) ) {
			// Store top pages as JSON in a special metric.
			$data['top_pages_json'] = $top_pages;
		}

		if ( ! empty( $data ) ) {
			error_log( sprintf( 'TMA Panel: GA4 sync OK — %d metrics fetched.', count( $data ) ) );
		}

		return $data;
	}

	/**
	 * Fetch top 10 pages from GA4.
	 *
	 * @param string $property_id GA4 property ID.
	 * @return string JSON-encoded array of top pages, or empty string.
	 */
	private static function fetch_ga4_top_pages( string $property_id ): string {
		$url  = self::GA4_API_BASE . '/' . $property_id . ':runReport';
		$body = array(
			'dateRanges' => array(
				array(
					'startDate' => '30daysAgo',
					'endDate'   => 'yesterday',
				),
			),
			'dimensions' => array(
				array( 'name' => 'pagePath' ),
			),
			'metrics'    => array(
				array( 'name' => 'sessions' ),
			),
			'orderBys'   => array(
				array(
					'metric' => array( 'metricName' => 'sessions' ),
					'desc'   => true,
				),
			),
			'limit'      => 10,
		);

		$result = TMA_Panel_Google_Auth::api_post( $url, $body );
		if ( is_wp_error( $result ) || empty( $result['rows'] ) ) {
			return '';
		}

		$pages = array();
		foreach ( $result['rows'] as $row ) {
			$pages[] = array(
				'path'     => $row['dimensionValues'][0]['value'] ?? '/',
				'sessions' => (int) ( $row['metricValues'][0]['value'] ?? 0 ),
			);
		}

		return wp_json_encode( $pages );
	}

	/* ═══════════════════════════════════════════════════════════════
	   SEARCH CONSOLE API (Real)
	   ═══════════════════════════════════════════════════════════════ */

	/**
	 * Fetch real Search Console metrics via Search Console API (v3).
	 *
	 * @return array<string,float> Metric => value pairs, or empty on failure.
	 */
	private static function fetch_gsc_data(): array {
		if ( ! class_exists( 'TMA_Panel_Google_Auth' ) || ! TMA_Panel_Google_Auth::is_configured() ) {
			error_log( 'TMA Panel WARN: Google OAuth2 not configured — skipping GSC sync.' );
			return array();
		}

		$site_url = defined( 'GSC_SITE_URL' ) ? GSC_SITE_URL : '';
		if ( '' === $site_url ) {
			error_log( 'TMA Panel WARN: GSC_SITE_URL not defined — skipping GSC sync.' );
			return array();
		}

		$encoded_site = rawurlencode( $site_url );
		$url = self::GSC_API_BASE . '/sites/' . $encoded_site . '/searchAnalytics/query';

		// Last 28 days aggregate.
		$body = array(
			'startDate'  => wp_date( 'Y-m-d', strtotime( '-28 days' ) ),
			'endDate'    => wp_date( 'Y-m-d', strtotime( '-1 day' ) ),
			'dimensions' => array(),
			'rowLimit'   => 1,
		);

		$result = TMA_Panel_Google_Auth::api_post( $url, $body );
		if ( is_wp_error( $result ) ) {
			error_log( 'TMA Panel ERROR: GSC searchAnalytics failed — ' . $result->get_error_message() );
			return array();
		}

		$data = array();
		if ( ! empty( $result['rows'][0] ) ) {
			$row = $result['rows'][0];
			$data['clicks']       = (float) ( $row['clicks'] ?? 0 );
			$data['impressions']  = (float) ( $row['impressions'] ?? 0 );
			$data['ctr']          = round( (float) ( $row['ctr'] ?? 0 ) * 100, 2 );
			$data['avg_position'] = round( (float) ( $row['position'] ?? 0 ), 1 );
		}

		// Fetch top 10 queries.
		$top_queries = self::fetch_gsc_top_queries( $encoded_site );
		if ( ! empty( $top_queries ) ) {
			$data['top_queries_json'] = $top_queries;
		}

		// Fetch top 10 pages.
		$top_pages = self::fetch_gsc_top_pages( $encoded_site );
		if ( ! empty( $top_pages ) ) {
			$data['gsc_top_pages_json'] = $top_pages;
		}

		if ( ! empty( $data ) ) {
			error_log( sprintf( 'TMA Panel: GSC sync OK — %d metrics fetched.', count( $data ) ) );
		}

		return $data;
	}

	/**
	 * Fetch top 10 search queries from Search Console.
	 *
	 * @param string $encoded_site URL-encoded site identifier.
	 * @return string JSON-encoded array of top queries, or empty string.
	 */
	private static function fetch_gsc_top_queries( string $encoded_site ): string {
		$url  = self::GSC_API_BASE . '/sites/' . $encoded_site . '/searchAnalytics/query';
		$body = array(
			'startDate'  => wp_date( 'Y-m-d', strtotime( '-28 days' ) ),
			'endDate'    => wp_date( 'Y-m-d', strtotime( '-1 day' ) ),
			'dimensions' => array( 'query' ),
			'rowLimit'   => 10,
		);

		$result = TMA_Panel_Google_Auth::api_post( $url, $body );
		if ( is_wp_error( $result ) || empty( $result['rows'] ) ) {
			return '';
		}

		$queries = array();
		foreach ( $result['rows'] as $row ) {
			$queries[] = array(
				'query'       => $row['keys'][0] ?? '',
				'clicks'      => (int) ( $row['clicks'] ?? 0 ),
				'impressions' => (int) ( $row['impressions'] ?? 0 ),
				'ctr'         => round( (float) ( $row['ctr'] ?? 0 ) * 100, 2 ),
				'position'    => round( (float) ( $row['position'] ?? 0 ), 1 ),
			);
		}

		return wp_json_encode( $queries );
	}

	/**
	 * Fetch top 10 pages from Search Console.
	 *
	 * @param string $encoded_site URL-encoded site identifier.
	 * @return string JSON-encoded array of top pages, or empty string.
	 */
	private static function fetch_gsc_top_pages( string $encoded_site ): string {
		$url  = self::GSC_API_BASE . '/sites/' . $encoded_site . '/searchAnalytics/query';
		$body = array(
			'startDate'  => wp_date( 'Y-m-d', strtotime( '-28 days' ) ),
			'endDate'    => wp_date( 'Y-m-d', strtotime( '-1 day' ) ),
			'dimensions' => array( 'page' ),
			'rowLimit'   => 10,
		);

		$result = TMA_Panel_Google_Auth::api_post( $url, $body );
		if ( is_wp_error( $result ) || empty( $result['rows'] ) ) {
			return '';
		}

		$pages = array();
		foreach ( $result['rows'] as $row ) {
			$pages[] = array(
				'page'        => $row['keys'][0] ?? '',
				'clicks'      => (int) ( $row['clicks'] ?? 0 ),
				'impressions' => (int) ( $row['impressions'] ?? 0 ),
				'ctr'         => round( (float) ( $row['ctr'] ?? 0 ) * 100, 2 ),
				'position'    => round( (float) ( $row['position'] ?? 0 ), 1 ),
			);
		}

		return wp_json_encode( $pages );
	}

	/* ═══════════════════════════════════════════════════════════════
	   GBP (Placeholder — waiting API quota approval)
	   ═══════════════════════════════════════════════════════════════ */

	/**
	 * Fetch GBP data — currently returns empty (quota not approved yet).
	 *
	 * @return array<string,float>
	 */
	private static function fetch_gbp_data(): array {
		$key = self::get_env_value( 'GBP_API_KEY' );
		if ( '' === $key ) {
			error_log( 'TMA Panel INFO: GBP API not configured — skipping (pending quota approval).' );
			return array();
		}

		// TODO: Implement real GBP API calls once quota is approved.
		return array();
	}

	/* ═══════════════════════════════════════════════════════════════
	   INSTAGRAM (Placeholder — needs Facebook App)
	   ═══════════════════════════════════════════════════════════════ */

	/**
	 * Fetch Instagram data — currently returns empty (needs Facebook App setup).
	 *
	 * @return array<string,float>
	 */
	private static function fetch_instagram_data(): array {
		$token = self::get_env_value( 'IG_ACCESS_TOKEN' );
		if ( '' === $token ) {
			error_log( 'TMA Panel INFO: Instagram API not configured — skipping.' );
			return array();
		}

		// TODO: Implement real Instagram Graph API calls.
		return array();
	}

	/* ═══════════════════════════════════════════════════════════════
	   STORAGE
	   ═══════════════════════════════════════════════════════════════ */

	/**
	 * Store KPI data in the database, using UPSERT (replace existing period data).
	 *
	 * @param array  $data     Metric => value pairs.
	 * @param string $category Source category (ga4, gsc, gbp, instagram).
	 * @return bool True if data was stored.
	 */
	private static function store_kpis( array $data, string $category ): bool {
		global $wpdb;
		$table  = $wpdb->prefix . 'panel_kpis';
		$period = wp_date( 'Y-m' );

		foreach ( $data as $metric => $value ) {
			// For JSON metrics, store the raw string; for numeric, store float.
			$is_json     = str_ends_with( $metric, '_json' );
			$store_value = $is_json ? 0.0 : (float) $value;

			// Check if row exists for this metric+period+category.
			$existing_id = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT id FROM {$table} WHERE metric = %s AND period = %s AND category = %s LIMIT 1",
					$metric,
					$period,
					$category
				)
			);

			if ( $existing_id ) {
				// Update existing row.
				$wpdb->update(
					$table,
					array( 'value' => $store_value ),
					array( 'id' => (int) $existing_id ),
					array( '%f' ),
					array( '%d' )
				);
			} else {
				// Insert new row.
				$wpdb->insert(
					$table,
					array(
						'metric'   => $metric,
						'value'    => $store_value,
						'period'   => $period,
						'category' => $category,
					),
					array( '%s', '%f', '%s', '%s' )
				);
			}

			// For JSON metrics, store the JSON string in a dedicated option (transient).
			if ( $is_json && is_string( $value ) ) {
				set_transient( 'tma_kpi_' . $category . '_' . $metric, $value, DAY_IN_SECONDS );
			}
		}

		return true;
	}

	/* ═══════════════════════════════════════════════════════════════
	   HELPERS
	   ═══════════════════════════════════════════════════════════════ */

	/**
	 * Resolve env value: env var first, then defined constant.
	 *
	 * @param string $name Variable / constant name.
	 * @return string Value or empty string.
	 */
	private static function get_env_value( string $name ): string {
		$val = getenv( $name );
		if ( false !== $val && '' !== $val ) {
			return (string) $val;
		}
		return defined( $name ) ? (string) constant( $name ) : '';
	}
}
