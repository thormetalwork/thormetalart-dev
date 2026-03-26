<?php
/**
 * TMA Panel — Export
 *
 * Generates a plain-text project summary for clipboard export.
 * Includes: documents status, leads pipeline, KPIs, notes.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_Export {

	/**
	 * Generate a consolidated text summary of the project state.
	 *
	 * @return string Plain-text summary.
	 */
	public static function generate_summary(): string {
		global $wpdb;

		$lines   = array();
		$lines[] = '══════════════════════════════════════════════';
		$lines[] = '  THOR METAL ART — RESUMEN DEL PROYECTO';
		$lines[] = '  Fecha: ' . wp_date( 'Y-m-d H:i' );
		$lines[] = '══════════════════════════════════════════════';
		$lines[] = '';

		// ── Documents ─────────────────────────────────────────
		$lines[] = '📄 DOCUMENTOS';
		$lines[] = '──────────────────────────────────────────────';

		$docs = $wpdb->get_results(
			"SELECT title, status FROM {$wpdb->prefix}panel_docs ORDER BY doc_order ASC"
		);
		if ( $docs ) {
			$approved = 0;
			$pending  = 0;
			foreach ( $docs as $doc ) {
				$icon    = 'approved' === $doc->status ? '✅' : '⏳';
				$lines[] = "  {$icon} {$doc->title} [{$doc->status}]";
				if ( 'approved' === $doc->status ) {
					++$approved;
				} else {
					++$pending;
				}
			}
			$lines[] = "  Total: {$approved} aprobados, {$pending} pendientes";
		} else {
			$lines[] = '  (sin documentos)';
		}
		$lines[] = '';

		// ── Leads ─────────────────────────────────────────────
		$lines[] = '🎯 LEADS PIPELINE';
		$lines[] = '──────────────────────────────────────────────';

		$leads = $wpdb->get_results(
			"SELECT name, status, email FROM {$wpdb->prefix}panel_leads ORDER BY created_at DESC"
		);
		if ( $leads ) {
			$statuses = array();
			foreach ( $leads as $lead ) {
				$lines[] = "  • {$lead->name} ({$lead->email}) [{$lead->status}]";
				$statuses[ $lead->status ] = ( $statuses[ $lead->status ] ?? 0 ) + 1;
			}
			$lines[] = '  Resumen: ' . implode( ', ', array_map(
				function ( $s, $c ) {
					return "{$c} {$s}";
				},
				array_keys( $statuses ),
				array_values( $statuses )
			) );
		} else {
			$lines[] = '  (sin leads)';
		}
		$lines[] = '';

		// ── KPIs ──────────────────────────────────────────────
		$lines[] = '📊 KPIs (ÚLTIMOS DATOS)';
		$lines[] = '──────────────────────────────────────────────';

		$kpis = $wpdb->get_results(
			"SELECT k1.metric, k1.value, k1.period, k1.category
			 FROM {$wpdb->prefix}panel_kpis k1
			 INNER JOIN (
			   SELECT metric, MAX(period) AS max_period
			   FROM {$wpdb->prefix}panel_kpis
			   GROUP BY metric
			 ) k2 ON k1.metric = k2.metric AND k1.period = k2.max_period
			 ORDER BY k1.category, k1.metric"
		);
		if ( $kpis ) {
			foreach ( $kpis as $kpi ) {
				$lines[] = "  • {$kpi->metric}: {$kpi->value} ({$kpi->period})";
			}
		} else {
			$lines[] = '  (sin KPIs)';
		}
		$lines[] = '';

		// ── Notes ─────────────────────────────────────────────
		$lines[] = '📝 NOTAS RECIENTES';
		$lines[] = '──────────────────────────────────────────────';

		$notes = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT title, content, created_at FROM {$wpdb->prefix}panel_notes ORDER BY created_at DESC LIMIT %d",
				10
			)
		);
		if ( $notes ) {
			foreach ( $notes as $note ) {
				$excerpt  = wp_trim_words( wp_strip_all_tags( $note->content ), 20, '…' );
				$lines[] = "  • [{$note->created_at}] {$note->title}: {$excerpt}";
			}
		} else {
			$lines[] = '  (sin notas)';
		}
		$lines[] = '';
		$lines[] = '══════════════════════════════════════════════';
		$lines[] = '  Generado por TMA Panel v' . TMA_PANEL_VERSION;
		$lines[] = '══════════════════════════════════════════════';

		return implode( "\n", $lines );
	}
}
