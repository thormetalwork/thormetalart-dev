<?php
/**
 * Migration 001 — Initial schema + seed data
 *
 * Creates: panel_leads, panel_notes, panel_kpis, panel_audit, panel_docs
 * Seeds:   12 portal documents + 6 months of demo KPI data
 *
 * Variables available: $wpdb, $prefix, $charset_collate
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════════════════════════════
   TABLE SCHEMAS
   ═══════════════════════════════════════════════════════════════════ */

$sql = array();

// ── Leads ─────────────────────────────────────────────────────
$sql[] = "CREATE TABLE {$prefix}panel_leads (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	name varchar(200) NOT NULL DEFAULT '',
	email varchar(200) NOT NULL DEFAULT '',
	phone varchar(50) NOT NULL DEFAULT '',
	source varchar(100) NOT NULL DEFAULT '',
	status varchar(50) NOT NULL DEFAULT 'new',
	notes text NOT NULL,
	assigned_to bigint(20) unsigned NOT NULL DEFAULT 0,
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_status (status),
	KEY idx_created (created_at)
) $charset_collate;";

// ── Notes ─────────────────────────────────────────────────────
$sql[] = "CREATE TABLE {$prefix}panel_notes (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	user_id bigint(20) unsigned NOT NULL DEFAULT 0,
	title varchar(200) NOT NULL DEFAULT '',
	content longtext NOT NULL,
	visibility varchar(20) NOT NULL DEFAULT 'internal',
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_user (user_id),
	KEY idx_visibility (visibility)
) $charset_collate;";

// ── KPIs ──────────────────────────────────────────────────────
$sql[] = "CREATE TABLE {$prefix}panel_kpis (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	metric varchar(100) NOT NULL DEFAULT '',
	value decimal(12,2) NOT NULL DEFAULT 0.00,
	period varchar(20) NOT NULL DEFAULT '',
	category varchar(100) NOT NULL DEFAULT '',
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_metric_period (metric, period),
	KEY idx_category (category)
) $charset_collate;";

// ── Audit log ─────────────────────────────────────────────────
$sql[] = "CREATE TABLE {$prefix}panel_audit (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	user_id bigint(20) unsigned NOT NULL DEFAULT 0,
	action varchar(100) NOT NULL DEFAULT '',
	entity_type varchar(50) NOT NULL DEFAULT '',
	entity_id bigint(20) unsigned NOT NULL DEFAULT 0,
	details text NOT NULL,
	ip_address varchar(45) NOT NULL DEFAULT '',
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	KEY idx_user (user_id),
	KEY idx_entity (entity_type, entity_id),
	KEY idx_created (created_at)
) $charset_collate;";

// ── Documents ─────────────────────────────────────────────────
$sql[] = "CREATE TABLE {$prefix}panel_docs (
	id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(200) NOT NULL DEFAULT '',
	slug varchar(200) NOT NULL DEFAULT '',
	doc_order smallint(5) unsigned NOT NULL DEFAULT 0,
	status varchar(50) NOT NULL DEFAULT 'pending',
	file_url varchar(500) NOT NULL DEFAULT '',
	visibility varchar(20) NOT NULL DEFAULT 'client',
	created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
	updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (id),
	UNIQUE KEY idx_slug (slug),
	KEY idx_status (status),
	KEY idx_order (doc_order)
) $charset_collate;";

// Run dbDelta for all tables.
dbDelta( implode( "\n", $sql ) );

/* ═══════════════════════════════════════════════════════════════════
   SEED DATA — 12 Portal Documents
   ═══════════════════════════════════════════════════════════════════ */

$docs_table = $prefix . 'panel_docs';
$doc_count  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$docs_table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

if ( 0 === $doc_count ) {
	$documents = array(
		array( 1, 'Metodología Maestra',            'metodologia_maestra',           'pending' ),
		array( 2, 'Diagnóstico & Auditoría',         'diagnostico_auditoria',         'pending' ),
		array( 3, 'Brief de Posicionamiento',        'brief_posicionamiento',         'pending' ),
		array( 4, 'Plan de Proyecto & Checklists',   'plan_proyecto_checklists',      'pending' ),
		array( 5, 'Checklist Maestro',               'checklist_maestro',             'pending' ),
		array( 6, 'Reporte Mensual',                 'reporte_mensual',               'pending' ),
		array( 7, 'Propuesta de Consultoría',        'propuesta_consultoria',         'pending' ),
		array( 8, 'Scripts de Comunicación',         'scripts_comunicacion',          'pending' ),
		array( 9, 'Guía de Fotografía',              'guia_fotografia',               'pending' ),
		array( 10, 'Copys del Sitio Web',            'copys_sitio_web',               'pending' ),
		array( 11, 'Tracker de Leads',               'tracker_leads',                 'pending' ),
		array( 12, 'Dashboard & Arquitectura',       'dashboard_arquitectura',        'pending' ),
		array( 13, 'Thor Kickoff Deck',              'thor_kickoff_deck',             'pending' ),
	);

	foreach ( $documents as $doc ) {
		$wpdb->insert(
			$docs_table,
			array(
				'doc_order'  => $doc[0],
				'title'      => $doc[1],
				'slug'       => $doc[2],
				'status'     => $doc[3],
				'visibility' => 'client',
			),
			array( '%d', '%s', '%s', '%s', '%s' )
		);
	}
}

/* ═══════════════════════════════════════════════════════════════════
   SEED DATA — Demo KPIs (Sep 2025 – Feb 2026)
   ═══════════════════════════════════════════════════════════════════ */

$kpis_table = $prefix . 'panel_kpis';
$kpi_count  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$kpis_table}`" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

if ( 0 === $kpi_count ) {
	$periods = array( '2025-09', '2025-10', '2025-11', '2025-12', '2026-01', '2026-02' );
	$kpis    = array(
		array( 'revenue',         'ingresos',    array( 12500, 14200, 15800, 18500, 16200, 19800 ) ),
		array( 'leads',           'marketing',   array( 8, 12, 15, 22, 18, 25 ) ),
		array( 'conversion_rate', 'marketing',   array( 12.5, 16.7, 20.0, 22.7, 16.7, 24.0 ) ),
		array( 'projects_active', 'operaciones', array( 3, 4, 5, 6, 5, 7 ) ),
		array( 'avg_ticket',      'ingresos',    array( 4166, 3550, 3160, 3083, 3240, 2828 ) ),
		array( 'satisfaction',    'calidad',     array( 4.5, 4.6, 4.7, 4.8, 4.7, 4.9 ) ),
	);

	foreach ( $kpis as $kpi ) {
		foreach ( $periods as $i => $period ) {
			$wpdb->insert(
				$kpis_table,
				array(
					'metric'   => $kpi[0],
					'category' => $kpi[1],
					'value'    => $kpi[2][ $i ],
					'period'   => $period,
				),
				array( '%s', '%s', '%f', '%s' )
			);
		}
	}
}
