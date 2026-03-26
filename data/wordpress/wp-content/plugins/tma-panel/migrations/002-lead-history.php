<?php
/**
 * Migration 002 — Lead history timeline table.
 *
 * Creates panel_lead_history to store status change events per lead.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $wpdb, $prefix, $charset_collate ) ) {
	return;
}

$sql = "CREATE TABLE {$prefix}panel_lead_history (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	lead_id bigint(20) unsigned NOT NULL,
	user_id bigint(20) unsigned NOT NULL DEFAULT 0,
	action varchar(200) NOT NULL DEFAULT '',
	old_status varchar(50) NOT NULL DEFAULT '',
	new_status varchar(50) NOT NULL DEFAULT '',
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_lead_id (lead_id),
	KEY idx_created_at (created_at)
) {$charset_collate};";

dbDelta( $sql );
