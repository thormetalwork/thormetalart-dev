<?php
/**
 * TMA Panel — Leads Service
 *
 * Handles migration and CRUD helpers for panel leads table.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Leads {

	/**
	 * Migrate legacy tma_leads entries into panel_leads.
	 */
	public static function migrate_from_tma_leads(): void {
		global $wpdb;

		$legacy_table = $wpdb->prefix . 'tma_leads';
		$panel_table  = $wpdb->prefix . 'panel_leads';

		$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $legacy_table ) );
		if ( $exists !== $legacy_table ) {
			return;
		}

		$rows = $wpdb->get_results( "SELECT id, full_name, email, phone, service, utm_source, status, submitted_at FROM {$legacy_table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( empty( $rows ) ) {
			return;
		}

		foreach ( $rows as $row ) {
			$already = (int) $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$panel_table} WHERE email=%s AND name=%s",
					$row->email,
					$row->full_name
				)
			);
			if ( $already > 0 ) {
				continue;
			}

			$wpdb->insert(
				$panel_table,
				array(
					'name'       => $row->full_name,
					'email'      => $row->email,
					'phone'      => $row->phone,
					'source'     => $row->utm_source ?: 'web',
					'status'     => $row->status ?: 'new',
					'notes'      => $row->service,
					'created_at' => $row->submitted_at ?: current_time( 'mysql' ),
				),
				array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
			);
		}
	}

	/**
	 * Create a lead in panel_leads from contact form data.
	 *
	 * @param array $data Lead payload.
	 * @return int Lead ID.
	 */
	public static function create_from_contact( array $data ): int {
		global $wpdb;
		$table = $wpdb->prefix . 'panel_leads';

		$wpdb->insert(
			$table,
			array(
				'name'       => sanitize_text_field( $data['name'] ?? '' ),
				'email'      => sanitize_email( $data['email'] ?? '' ),
				'phone'      => sanitize_text_field( $data['phone'] ?? '' ),
				'source'     => sanitize_text_field( $data['source'] ?? 'web' ),
				'status'     => 'new',
				'notes'      => sanitize_textarea_field( $data['message'] ?? '' ),
				'created_at' => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return (int) $wpdb->insert_id;
	}

	/**
	 * Update lead status/value.
	 *
	 * @param int    $lead_id Lead ID.
	 * @param string $status New status.
	 * @param float  $value  Lead value.
	 * @return bool
	 */
	public static function update_lead( int $lead_id, string $status, float $value ): bool {
		global $wpdb;
		$table = $wpdb->prefix . 'panel_leads';

		self::ensure_value_column();

		$allowed = array( 'new', 'contacted', 'quoted', 'won', 'lost' );
		if ( ! in_array( $status, $allowed, true ) ) {
			return false;
		}

		$updated = $wpdb->update(
			$table,
			array(
				'status'     => $status,
				'lead_value' => $value,
				'updated_at' => current_time( 'mysql' ),
			),
			array( 'id' => $lead_id ),
			array( '%s', '%f', '%s' ),
			array( '%d' )
		);

		return false !== $updated;
	}

	/**
	 * Get pipeline value total.
	 *
	 * @return float
	 */
	public static function get_pipeline_value(): float {
		global $wpdb;
		self::ensure_value_column();

		$table = $wpdb->prefix . 'panel_leads';
		$sum   = $wpdb->get_var( "SELECT COALESCE(SUM(lead_value),0) FROM {$table} WHERE status IN ('new','contacted','quoted','won')" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return (float) $sum;
	}

	/**
	 * Ensure lead_value column exists.
	 */
	private static function ensure_value_column(): void {
		global $wpdb;
		$table   = $wpdb->prefix . 'panel_leads';
		$columns = $wpdb->get_col( "SHOW COLUMNS FROM {$table}", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( ! in_array( 'lead_value', $columns, true ) ) {
			$wpdb->query( "ALTER TABLE {$table} ADD COLUMN lead_value decimal(12,2) NOT NULL DEFAULT 0.00" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
	}
}
