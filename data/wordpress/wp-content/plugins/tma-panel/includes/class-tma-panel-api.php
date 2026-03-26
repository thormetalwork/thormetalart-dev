<?php
/**
 * TMA Panel — REST API
 *
 * Registers /tma-panel/v1 namespace with endpoints for:
 * dashboard, documents, leads, notes, audit, export.
 *
 * All endpoints require authentication + tma_view_panel capability.
 * Audit endpoint additionally requires tma_view_audit.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

class TMA_Panel_API {

	private const NAMESPACE = 'tma-panel/v1';

	/**
	 * Register all routes on rest_api_init.
	 */
	public static function register_routes(): void {
		// ── Dashboard (aggregated stats) ──
		register_rest_route(
			self::NAMESPACE,
			'/dashboard',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_dashboard' ),
				'permission_callback' => array( __CLASS__, 'check_panel_access' ),
			)
		);

		// ── Documents ──
		register_rest_route(
			self::NAMESPACE,
			'/documents',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_documents' ),
				'permission_callback' => array( __CLASS__, 'check_panel_access' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/documents/(?P<code>[a-zA-Z0-9_-]+)/content',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_document_content' ),
				'permission_callback' => array( __CLASS__, 'check_panel_access' ),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/documents/(?P<id>\\d+)/status',
			array(
				'methods'             => 'POST',
				'callback'            => array( __CLASS__, 'update_document_status' ),
				'permission_callback' => array( __CLASS__, 'check_panel_access' ),
				'args'                => array(
					'status' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'notes'  => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// ── Leads ──
		register_rest_route(
			self::NAMESPACE,
			'/leads',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_leads' ),
				'permission_callback' => array( __CLASS__, 'check_panel_access' ),
			)
		);

		// ── Notes (GET + POST) ──
		register_rest_route(
			self::NAMESPACE,
			'/notes',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_notes' ),
					'permission_callback' => array( __CLASS__, 'check_panel_access' ),
				),
				array(
					'methods'             => 'POST',
					'callback'            => array( __CLASS__, 'create_note' ),
					'permission_callback' => array( __CLASS__, 'check_notes_access' ),
					'args'                => array(
						'title'   => array(
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'content' => array(
							'required'          => true,
							'sanitize_callback' => 'wp_kses_post',
						),
						'visibility' => array(
							'default'           => 'internal',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'module' => array(
							'default'           => 'general',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'item_id' => array(
							'default'           => 0,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);

		// ── Audit (admin-only) ──
		register_rest_route(
			self::NAMESPACE,
			'/audit',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_audit' ),
				'permission_callback' => array( __CLASS__, 'check_audit_access' ),
			)
		);

		// ── Export ──
		register_rest_route(
			self::NAMESPACE,
			'/export',
			array(
				'methods'             => 'GET',
				'callback'            => array( __CLASS__, 'get_export' ),
				'permission_callback' => array( __CLASS__, 'check_export_access' ),
			)
		);
	}

	/* ═══════════════════════════════════════════════════════════════
	   PERMISSION CALLBACKS
	   ═══════════════════════════════════════════════════════════════ */

	/**
	 * Base panel access — requires authentication + tma_view_panel.
	 */
	public static function check_panel_access(): bool|WP_Error {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_not_logged_in',
				__( 'Authentication required.', 'thormetalart' ),
				array( 'status' => 401 )
			);
		}
		if ( ! current_user_can( 'tma_view_panel' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Insufficient permissions.', 'thormetalart' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Notes create — requires tma_manage_notes.
	 */
	public static function check_notes_access(): bool|WP_Error {
		$base = self::check_panel_access();
		if ( is_wp_error( $base ) ) {
			return $base;
		}
		if ( ! current_user_can( 'tma_manage_notes' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Cannot manage notes.', 'thormetalart' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Audit access — requires tma_view_audit (admin-only).
	 */
	public static function check_audit_access(): bool|WP_Error {
		$base = self::check_panel_access();
		if ( is_wp_error( $base ) ) {
			return $base;
		}
		if ( ! current_user_can( 'tma_view_audit' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Audit access requires admin role.', 'thormetalart' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/**
	 * Export access — requires tma_export.
	 */
	public static function check_export_access(): bool|WP_Error {
		$base = self::check_panel_access();
		if ( is_wp_error( $base ) ) {
			return $base;
		}
		if ( ! current_user_can( 'tma_export' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Export permission required.', 'thormetalart' ),
				array( 'status' => 403 )
			);
		}
		return true;
	}

	/* ═══════════════════════════════════════════════════════════════
	   ENDPOINT CALLBACKS
	   ═══════════════════════════════════════════════════════════════ */

	/**
	 * GET /dashboard — aggregated KPIs, lead count, doc count.
	 */
	public static function get_dashboard( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;

		$kpi_rows = $wpdb->get_results(
			"SELECT metric, value, period FROM {$wpdb->prefix}panel_kpis ORDER BY period ASC"
		);

		$series_by_metric = array();
		$periods          = array();
		foreach ( $kpi_rows as $row ) {
			if ( ! isset( $series_by_metric[ $row->metric ] ) ) {
				$series_by_metric[ $row->metric ] = array();
			}
			$series_by_metric[ $row->metric ][] = array(
				'period' => $row->period,
				'value'  => (float) $row->value,
			);
			$periods[ $row->period ] = true;
		}

		$get_latest_pair = static function ( array $series ): array {
			$count = count( $series );
			if ( 0 === $count ) {
				return array( 'latest' => 0.0, 'previous' => 0.0, 'trend' => 'neutral' );
			}
			$latest   = (float) $series[ $count - 1 ]['value'];
			$previous = $count > 1 ? (float) $series[ $count - 2 ]['value'] : $latest;
			$trend    = 'neutral';
			if ( $latest > $previous ) {
				$trend = 'up';
			} elseif ( $latest < $previous ) {
				$trend = 'down';
			}
			return array(
				'latest'   => $latest,
				'previous' => $previous,
				'trend'    => $trend,
			);
		};

		$leads_total = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->prefix}panel_leads"
		);

		$lead_sources_rows = $wpdb->get_results(
			"SELECT COALESCE(NULLIF(source,''), 'unknown') AS source, COUNT(*) AS total
			 FROM {$wpdb->prefix}panel_leads
			 GROUP BY source
			 ORDER BY total DESC"
		);
		$lead_sources = array();
		foreach ( $lead_sources_rows as $src ) {
			$lead_sources[] = array(
				'label' => $src->source,
				'value' => (int) $src->total,
			);
		}

		$kpi_map = array(
			'reviews'     => $series_by_metric['reviews'] ?? array(),
			'impressions' => $series_by_metric['impressions'] ?? array(),
			'sessions'    => $series_by_metric['sessions'] ?? array(),
			'leads'       => $series_by_metric['leads'] ?? array(),
		);

		$has_real_dashboard_kpis = ! empty( $kpi_map['impressions'] ) || ! empty( $kpi_map['sessions'] ) || ! empty( $kpi_map['reviews'] );

		if ( ! $has_real_dashboard_kpis ) {
			$demo_periods = array( '2025-09', '2025-10', '2025-11', '2025-12', '2026-01', '2026-02' );
			$kpi_map      = array(
				'reviews'     => array_map(
					static fn( $p, $v ) => array( 'period' => $p, 'value' => $v ),
					$demo_periods,
					array( 18, 21, 24, 27, 30, 34 )
				),
				'impressions' => array_map(
					static fn( $p, $v ) => array( 'period' => $p, 'value' => $v ),
					$demo_periods,
					array( 3200, 3800, 4200, 5100, 5900, 6400 )
				),
				'sessions'    => array_map(
					static fn( $p, $v ) => array( 'period' => $p, 'value' => $v ),
					$demo_periods,
					array( 420, 470, 510, 620, 700, 760 )
				),
				'leads'       => array_map(
					static fn( $p, $v ) => array( 'period' => $p, 'value' => $v ),
					$demo_periods,
					array( 8, 11, 13, 17, 19, 24 )
				),
			);
			if ( empty( $lead_sources ) ) {
				$lead_sources = array(
					array( 'label' => 'google', 'value' => 9 ),
					array( 'label' => 'instagram', 'value' => 6 ),
					array( 'label' => 'referral', 'value' => 4 ),
					array( 'label' => 'website', 'value' => 5 ),
				);
			}
		}

		$cards = array(
			'reviews'     => $get_latest_pair( $kpi_map['reviews'] ),
			'impressions' => $get_latest_pair( $kpi_map['impressions'] ),
			'sessions'    => $get_latest_pair( $kpi_map['sessions'] ),
			'leads'       => $get_latest_pair( $kpi_map['leads'] ),
		);

		$history = array(
			'impressions' => $kpi_map['impressions'],
			'leads'       => $kpi_map['leads'],
		);

		$actions_pair = $get_latest_pair( $series_by_metric['actions'] ?? array() );
		$impressions_split = array();
		foreach ( $kpi_map['impressions'] as $point ) {
			$total = (float) $point['value'];
			$impressions_split[] = array(
				'period'             => $point['period'],
				'impressions_search' => (int) round( $total * 0.7 ),
				'impressions_maps'   => (int) round( $total * 0.3 ),
			);
		}

		$gbp = array(
			'rating'            => 4.8,
			'reviews'           => (int) $cards['reviews']['latest'],
			'impressions'       => (int) $cards['impressions']['latest'],
			'actions'           => (int) $actions_pair['latest'],
			'impressions_split' => $impressions_split,
		);

		$users_pair = $get_latest_pair( $series_by_metric['users'] ?? array() );
		$conv_pair  = $get_latest_pair( $series_by_metric['conversion_rate'] ?? array() );
		$forms_pair = $get_latest_pair( $series_by_metric['forms_submitted'] ?? array() );
		$avg_pair   = $get_latest_pair( $series_by_metric['avg_time'] ?? array() );

		$web = array(
			'sessions'         => (int) $cards['sessions']['latest'],
			'users'            => (int) $users_pair['latest'],
			'conversion_rate'  => (float) $conv_pair['latest'],
			'forms_submitted'  => (int) $forms_pair['latest'],
			'avg_time'         => (int) $avg_pair['latest'],
			'sessions_history' => $kpi_map['sessions'],
			'top_pages'        => array(
				array( 'path' => '/custom-metal-gates-miami', 'sessions' => 220 ),
				array( 'path' => '/metal-railings-miami', 'sessions' => 180 ),
				array( 'path' => '/contact', 'sessions' => 145 ),
				array( 'path' => '/portfolio', 'sessions' => 120 ),
				array( 'path' => '/art-commissions', 'sessions' => 95 ),
			),
		);

		$followers_pair = $get_latest_pair( $series_by_metric['followers'] ?? array() );
		$reach_pair     = $get_latest_pair( $series_by_metric['reach'] ?? array() );
		$eng_pair       = $get_latest_pair( $series_by_metric['engagement_rate'] ?? array() );

		$instagram = array(
			'followers'     => (int) $followers_pair['latest'],
			'reach'         => (int) $reach_pair['latest'],
			'engagement'    => (float) $eng_pair['latest'],
			'reach_history' => $series_by_metric['reach'] ?? array(),
		);

		$docs_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}panel_docs" );
		$notes_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}panel_notes" );

		return new WP_REST_Response(
			array(
				'counts'       => array(
					'reviews'     => (int) $cards['reviews']['latest'],
					'impressions' => (int) $cards['impressions']['latest'],
					'sessions'    => (int) $cards['sessions']['latest'],
					'leads'       => $leads_total > 0 ? $leads_total : (int) $cards['leads']['latest'],
					'documents'   => $docs_count,
					'notes'       => $notes_count,
					'kpis'        => count( $kpi_rows ),
				),
				'kpis'         => $cards,
				'history'      => $history,
				'lead_sources' => $lead_sources,
				'gbp'          => $gbp,
				'web'          => $web,
				'instagram'    => $instagram,
				'is_demo'      => ! $has_real_dashboard_kpis,
				'periods'      => array_keys( $periods ),
			),
			200
		);
	}

	/**
	 * GET /documents — list all documents.
	 */
	public static function get_documents( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;
		self::ensure_docs_approval_columns();

		$rows = $wpdb->get_results(
			"SELECT id, title, slug, doc_order, status, visibility, file_url, approved_by, approved_at, change_notes, created_at, updated_at
			 FROM {$wpdb->prefix}panel_docs
			 ORDER BY doc_order ASC"
		);

		$docs = array();
		foreach ( $rows as $row ) {
			$docs[] = array(
				'id'         => (int) $row->id,
				'title'      => $row->title,
				'slug'       => $row->slug,
				'order'      => (int) $row->doc_order,
				'status'     => $row->status,
				'visibility' => $row->visibility,
				'file_url'   => $row->file_url,
				'approved_by'=> (int) $row->approved_by,
				'approved_at'=> $row->approved_at,
				'notes'      => $row->change_notes,
				'created_at' => $row->created_at,
				'updated_at' => $row->updated_at,
			);
		}

		return new WP_REST_Response( $docs, 200 );
	}

	/**
	 * GET /documents/{code}/content — secure HTML content from cache.
	 */
	public static function get_document_content( WP_REST_Request $request ): WP_REST_Response {
		$code    = sanitize_text_field( $request->get_param( 'code' ) );
		$content = TMA_Panel_Docs::get_document_content( $code );

		if ( is_wp_error( $content ) ) {
			return new WP_REST_Response(
				array( 'message' => $content->get_error_message() ),
				(int) ( $content->get_error_data()['status'] ?? 500 )
			);
		}

		return new WP_REST_Response( $content, 200 );
	}

	/**
	 * POST /documents/{id}/status — update approval status.
	 */
	public static function update_document_status( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;
		self::ensure_docs_approval_columns();

		$doc_id = (int) $request->get_param( 'id' );
		$status = sanitize_text_field( (string) $request->get_param( 'status' ) );
		$notes  = sanitize_text_field( (string) $request->get_param( 'notes' ) );

		$allowed = array( 'pending', 'approved', 'changes_requested' );
		if ( ! in_array( $status, $allowed, true ) ) {
			return new WP_REST_Response( array( 'message' => __( 'Invalid status.', 'thormetalart' ) ), 400 );
		}

		if ( 'changes_requested' === $status && strlen( trim( $notes ) ) < 10 ) {
			return new WP_REST_Response( array( 'message' => __( 'Notes must be at least 10 characters.', 'thormetalart' ) ), 400 );
		}

		$updated = $wpdb->update(
			$wpdb->prefix . 'panel_docs',
			array(
				'status'       => $status,
				'approved_by'  => get_current_user_id(),
				'approved_at'  => current_time( 'mysql' ),
				'change_notes' => $notes,
			),
			array( 'id' => $doc_id ),
			array( '%s', '%d', '%s', '%s' ),
			array( '%d' )
		);

		if ( false === $updated ) {
			return new WP_REST_Response( array( 'message' => __( 'Could not update document.', 'thormetalart' ) ), 500 );
		}

		return new WP_REST_Response(
			array(
				'id'          => $doc_id,
				'status'      => $status,
				'approved_by' => get_current_user_id(),
				'approved_at' => current_time( 'mysql' ),
				'notes'       => $notes,
			),
			200
		);
	}

	/**
	 * Ensure approval metadata columns exist in panel_docs.
	 */
	private static function ensure_docs_approval_columns(): void {
		global $wpdb;
		$table = $wpdb->prefix . 'panel_docs';

		$columns = $wpdb->get_col( "SHOW COLUMNS FROM {$table}", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( ! in_array( 'approved_by', $columns, true ) ) {
			$wpdb->query( "ALTER TABLE {$table} ADD COLUMN approved_by bigint(20) unsigned NOT NULL DEFAULT 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
		if ( ! in_array( 'approved_at', $columns, true ) ) {
			$wpdb->query( "ALTER TABLE {$table} ADD COLUMN approved_at datetime NULL" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
		if ( ! in_array( 'change_notes', $columns, true ) ) {
			$wpdb->query( "ALTER TABLE {$table} ADD COLUMN change_notes text NULL" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
	}

	/**
	 * GET /leads — list all leads.
	 */
	public static function get_leads( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;

		$rows = $wpdb->get_results(
			"SELECT id, name, email, phone, source, status, notes, assigned_to, created_at, updated_at
			 FROM {$wpdb->prefix}panel_leads
			 ORDER BY created_at DESC"
		);

		$leads = array();
		foreach ( $rows as $row ) {
			$leads[] = array(
				'id'          => (int) $row->id,
				'name'        => $row->name,
				'email'       => $row->email,
				'phone'       => $row->phone,
				'source'      => $row->source,
				'status'      => $row->status,
				'notes'       => $row->notes,
				'assigned_to' => (int) $row->assigned_to,
				'created_at'  => $row->created_at,
				'updated_at'  => $row->updated_at,
			);
		}

		return new WP_REST_Response( $leads, 200 );
	}

	/**
	 * GET /notes — list notes (filtered by visibility for clients).
	 */
	public static function get_notes( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;
		self::ensure_notes_context_columns();

		$is_admin = current_user_can( 'tma_view_audit' );

		if ( $is_admin ) {
			$rows = $wpdb->get_results(
				"SELECT id, user_id, title, content, visibility, module, item_id, created_at, updated_at
				 FROM {$wpdb->prefix}panel_notes
				 ORDER BY created_at DESC"
			);
		} else {
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, user_id, title, content, visibility, module, item_id, created_at, updated_at
					 FROM {$wpdb->prefix}panel_notes
					 WHERE visibility = %s OR user_id = %d
					 ORDER BY created_at DESC",
					'client',
					get_current_user_id()
				)
			);
		}

		$notes = array();
		foreach ( $rows as $row ) {
			$notes[] = array(
				'id'         => (int) $row->id,
				'user_id'    => (int) $row->user_id,
				'title'      => $row->title,
				'content'    => $row->content,
				'visibility' => $row->visibility,
				'module'     => $row->module,
				'item_id'    => (int) $row->item_id,
				'created_at' => $row->created_at,
				'updated_at' => $row->updated_at,
			);
		}

		return new WP_REST_Response( $notes, 200 );
	}

	/**
	 * POST /notes — create a new note.
	 */
	public static function create_note( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;
		self::ensure_notes_context_columns();

		$title      = $request->get_param( 'title' );
		$content    = $request->get_param( 'content' );
		$visibility = $request->get_param( 'visibility' );
		$module     = sanitize_text_field( (string) $request->get_param( 'module' ) );
		$item_id    = (int) $request->get_param( 'item_id' );

		$allowed_vis = array( 'internal', 'client' );
		if ( ! in_array( $visibility, $allowed_vis, true ) ) {
			$visibility = 'internal';
		}

		$inserted = $wpdb->insert(
			$wpdb->prefix . 'panel_notes',
			array(
				'user_id'    => get_current_user_id(),
				'title'      => $title,
				'content'    => $content,
				'visibility' => $visibility,
				'module'     => $module ?: 'general',
				'item_id'    => $item_id,
			),
			array( '%d', '%s', '%s', '%s', '%s', '%d' )
		);

		if ( false === $inserted ) {
			return new WP_REST_Response(
				array( 'message' => __( 'Could not create note.', 'thormetalart' ) ),
				500
			);
		}

		return new WP_REST_Response(
			array(
				'id'      => (int) $wpdb->insert_id,
				'module'  => $module ?: 'general',
				'item_id' => $item_id,
				'message' => __( 'Note created.', 'thormetalart' ),
			),
			201
		);
	}

	/**
	 * Ensure notes table supports contextual notes (module/item_id).
	 */
	private static function ensure_notes_context_columns(): void {
		global $wpdb;
		$table   = $wpdb->prefix . 'panel_notes';
		$columns = $wpdb->get_col( "SHOW COLUMNS FROM {$table}", 0 ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		if ( ! in_array( 'module', $columns, true ) ) {
			$wpdb->query( "ALTER TABLE {$table} ADD COLUMN module varchar(50) NOT NULL DEFAULT 'general'" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		if ( ! in_array( 'item_id', $columns, true ) ) {
			$wpdb->query( "ALTER TABLE {$table} ADD COLUMN item_id bigint(20) unsigned NOT NULL DEFAULT 0" ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
	}

	/**
	 * GET /audit — audit log (admin-only).
	 */
	public static function get_audit( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;

		$rows = $wpdb->get_results(
			"SELECT id, user_id, action, entity_type, entity_id, details, ip_address, created_at
			 FROM {$wpdb->prefix}panel_audit
			 ORDER BY created_at DESC
			 LIMIT 100"
		);

		$entries = array();
		foreach ( $rows as $row ) {
			$entries[] = array(
				'id'          => (int) $row->id,
				'user_id'     => (int) $row->user_id,
				'action'      => $row->action,
				'entity_type' => $row->entity_type,
				'entity_id'   => (int) $row->entity_id,
				'details'     => $row->details,
				'ip_address'  => $row->ip_address,
				'created_at'  => $row->created_at,
			);
		}

		return new WP_REST_Response( $entries, 200 );
	}

	/**
	 * GET /export — exportable data summary.
	 */
	public static function get_export( WP_REST_Request $request ): WP_REST_Response {
		$summary = '';
		if ( class_exists( 'TMA_Panel_Export' ) ) {
			$summary = TMA_Panel_Export::generate_summary();
		}

		return new WP_REST_Response(
			array(
				'summary'      => $summary,
				'generated_at' => wp_date( 'c' ),
			),
			200
		);
	}
}
