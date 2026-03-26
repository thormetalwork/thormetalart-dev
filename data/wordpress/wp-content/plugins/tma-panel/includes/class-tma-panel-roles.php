<?php
/**
 * TMA Panel — Roles & Capabilities
 *
 * Registers tma_admin and tma_client roles with granular capabilities
 * for controlling access to panel modules.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Roles {

	/**
	 * All panel capabilities.
	 */
	private const ALL_CAPS = array(
		'tma_view_panel',
		'tma_manage_docs',
		'tma_manage_leads',
		'tma_manage_notes',
		'tma_view_audit',
		'tma_export',
		'tma_manage_kpis',
		'tma_toggle_visibility',
	);

	/**
	 * Capabilities restricted from tma_client role.
	 */
	private const CLIENT_RESTRICTED = array(
		'tma_view_audit',
		'tma_toggle_visibility',
		'tma_manage_kpis',
	);

	/**
	 * Register roles on plugin activation.
	 */
	public static function activate(): void {
		// tma_admin — full panel access + basic WP caps.
		$admin_caps = array(
			'read'           => true,
			'upload_files'   => true,
			'edit_posts'     => true,
		);
		foreach ( self::ALL_CAPS as $cap ) {
			$admin_caps[ $cap ] = true;
		}
		add_role( 'tma_admin', 'TMA Admin', $admin_caps );

		// tma_client — restricted panel access.
		$client_caps = array(
			'read' => true,
		);
		foreach ( self::ALL_CAPS as $cap ) {
			$client_caps[ $cap ] = ! in_array( $cap, self::CLIENT_RESTRICTED, true );
		}
		add_role( 'tma_client', 'TMA Client', $client_caps );

		// Grant all panel caps to administrator role.
		$admin_role = get_role( 'administrator' );
		if ( $admin_role ) {
			foreach ( self::ALL_CAPS as $cap ) {
				$admin_role->add_cap( $cap );
			}
		}
	}

	/**
	 * Remove roles on plugin deactivation.
	 */
	public static function deactivate(): void {
		remove_role( 'tma_admin' );
		remove_role( 'tma_client' );

		// Remove panel caps from administrator.
		$admin_role = get_role( 'administrator' );
		if ( $admin_role ) {
			foreach ( self::ALL_CAPS as $cap ) {
				$admin_role->remove_cap( $cap );
			}
		}
	}
}
