<?php
/**
 * Migration 003 — Add missing schema columns.
 *
 * Adds columns previously added at runtime via ensure_*() guards:
 * - panel_docs:  approved_by, approved_at, change_notes
 * - panel_notes: module, item_id
 * - panel_leads: lead_value
 *
 * Variables available from runner: $wpdb, $prefix, $charset_collate
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $wpdb, $prefix ) ) {
	return;
}

// ── panel_docs: approval metadata ─────────────────────────────
$columns = $wpdb->get_col( "SHOW COLUMNS FROM {$prefix}panel_docs", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

if ( ! in_array( 'approved_by', $columns, true ) ) {
	$wpdb->query( "ALTER TABLE {$prefix}panel_docs ADD COLUMN approved_by bigint(20) unsigned NOT NULL DEFAULT 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}
if ( ! in_array( 'approved_at', $columns, true ) ) {
	$wpdb->query( "ALTER TABLE {$prefix}panel_docs ADD COLUMN approved_at datetime NULL" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}
if ( ! in_array( 'change_notes', $columns, true ) ) {
	$wpdb->query( "ALTER TABLE {$prefix}panel_docs ADD COLUMN change_notes text NULL" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

// ── panel_notes: contextual notes ─────────────────────────────
$columns = $wpdb->get_col( "SHOW COLUMNS FROM {$prefix}panel_notes", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

if ( ! in_array( 'module', $columns, true ) ) {
	$wpdb->query( "ALTER TABLE {$prefix}panel_notes ADD COLUMN module varchar(50) NOT NULL DEFAULT 'general'" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}
if ( ! in_array( 'item_id', $columns, true ) ) {
	$wpdb->query( "ALTER TABLE {$prefix}panel_notes ADD COLUMN item_id bigint(20) unsigned NOT NULL DEFAULT 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

// ── panel_leads: pipeline value ───────────────────────────────
$columns = $wpdb->get_col( "SHOW COLUMNS FROM {$prefix}panel_leads", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

if ( ! in_array( 'lead_value', $columns, true ) ) {
	$wpdb->query( "ALTER TABLE {$prefix}panel_leads ADD COLUMN lead_value decimal(12,2) NOT NULL DEFAULT 0.00" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}
