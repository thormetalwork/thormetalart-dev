<?php
/**
 * TMA Panel — Audit Log
 *
 * Records user actions, supports retrieval and automatic cleanup.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Audit {

	const RETENTION_DAYS = 90;
	const CRON_HOOK      = 'tma_panel_audit_cleanup';

	/**
	 * Log an action to the audit table.
	 *
	 * @param string $action      Action name (e.g. 'login', 'view_document').
	 * @param string $entity_type Entity type (e.g. 'user', 'document', 'lead').
	 * @param int    $entity_id   Entity ID.
	 * @param int    $user_id     User performing the action (0 = system).
	 */
	public static function log( string $action, string $entity_type = '', int $entity_id = 0, int $user_id = 0 ): void {
		global $wpdb;

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$details = wp_json_encode( array(
			'ip_address' => self::get_ip(),
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] )
				? sanitize_text_field( substr( $_SERVER['HTTP_USER_AGENT'], 0, 255 ) )
				: '',
		) );

		$wpdb->insert(
			$wpdb->prefix . 'panel_audit',
			array(
				'user_id'     => $user_id,
				'action'      => sanitize_text_field( $action ),
				'entity_type' => sanitize_text_field( $entity_type ),
				'entity_id'   => absint( $entity_id ),
				'details'     => $details,
				'ip_address'  => self::get_ip(),
				'created_at'  => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%d', '%s', '%s', '%s' )
		);
	}

	/**
	 * Get recent audit entries.
	 *
	 * @param int $limit Number of entries to return.
	 * @return array
	 */
	public static function get_entries( int $limit = 50 ): array {
		global $wpdb;

		// Lazy cleanup: if cron missed, clean up on read (max once per hour).
		$last_cleanup = (int) get_transient( 'tma_audit_last_cleanup' );
		if ( ! $last_cleanup ) {
			self::cleanup();
			set_transient( 'tma_audit_last_cleanup', time(), HOUR_IN_SECONDS );
		}

		$table = $wpdb->prefix . 'panel_audit';

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT a.*, u.display_name AS user_name
				 FROM {$table} a
				 LEFT JOIN {$wpdb->users} u ON a.user_id = u.ID
				 ORDER BY a.created_at DESC
				 LIMIT %d",
				$limit
			)
		);
	}

	/**
	 * Remove audit entries older than RETENTION_DAYS.
	 */
	public static function cleanup(): void {
		global $wpdb;

		$table    = $wpdb->prefix . 'panel_audit';
		$cutoff   = gmdate( 'Y-m-d H:i:s', strtotime( '-' . self::RETENTION_DAYS . ' days' ) );

		$wpdb->query(
			$wpdb->prepare( "DELETE FROM {$table} WHERE created_at < %s", $cutoff )
		);
	}

	/**
	 * Schedule the daily cleanup cron.
	 */
	public static function schedule_cleanup(): void {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), 'daily', self::CRON_HOOK );
		}
	}

	/**
	 * Unschedule the cleanup cron.
	 */
	public static function unschedule_cleanup(): void {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
		}
	}

	/**
	 * Get client IP address safely.
	 */
	private static function get_ip(): string {
		$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
		return sanitize_text_field( $ip );
	}
}
