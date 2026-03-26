<?php
/**
 * TMA Panel — Data Layer & Migration Runner
 *
 * Manages custom database tables and versioned migrations.
 * Tables use WordPress prefix: {prefix}panel_leads, {prefix}panel_notes, etc.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Data {

	/**
	 * Current database schema version.
	 * Increment when adding new migration files.
	 */
	private const DB_VERSION = 2;

	/**
	 * Option key for tracking applied migration version.
	 */
	private const VERSION_KEY = 'tma_panel_db_version';

	/**
	 * Run migrations if the DB version is behind.
	 * Called on plugin activation and on admin_init.
	 */
	public static function maybe_migrate(): void {
		$current = (int) get_option( self::VERSION_KEY, 0 );
		if ( $current >= self::DB_VERSION ) {
			return;
		}
		self::run_migrations( $current );
	}

	/**
	 * Execute all pending migration files sequentially.
	 *
	 * Migration files must be named NNN-description.php (e.g., 001-initial.php)
	 * and return the version number they represent.
	 *
	 * @param int $from_version Current DB version (already applied).
	 */
	public static function run_migrations( int $from_version ): void {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$prefix          = $wpdb->prefix;

		$migrations_dir = TMA_PANEL_PATH . 'migrations/';
		$files          = glob( $migrations_dir . '*.php' );
		if ( ! $files ) {
			return;
		}

		sort( $files );

		foreach ( $files as $file ) {
			$basename = basename( $file, '.php' );
			// Extract version number from filename (e.g., "001" from "001-initial").
			$parts   = explode( '-', $basename, 2 );
			$version = (int) $parts[0];

			if ( $version <= $from_version ) {
				continue;
			}

			// Migration file receives $wpdb, $prefix, $charset_collate.
			require $file;

			update_option( self::VERSION_KEY, $version );
		}
	}

	/**
	 * Drop all custom tables. Used on uninstall.
	 */
	public static function drop_tables(): void {
		global $wpdb;
		$tables = array(
			$wpdb->prefix . 'panel_leads',
			$wpdb->prefix . 'panel_lead_history',
			$wpdb->prefix . 'panel_notes',
			$wpdb->prefix . 'panel_kpis',
			$wpdb->prefix . 'panel_audit',
			$wpdb->prefix . 'panel_docs',
		);
		foreach ( $tables as $table ) {
			$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
		delete_option( self::VERSION_KEY );
	}
}
