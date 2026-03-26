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

		$latest_period = $wpdb->get_var(
			"SELECT MAX(period) FROM {$wpdb->prefix}panel_kpis"
		);

		$kpis = array();
		if ( $latest_period ) {
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT metric, value, category FROM {$wpdb->prefix}panel_kpis WHERE period = %s",
					$latest_period
				)
			);
			foreach ( $rows as $row ) {
				$kpis[] = array(
					'metric'   => $row->metric,
					'value'    => (float) $row->value,
					'category' => $row->category,
				);
			}
		}

		$leads_count = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->prefix}panel_leads"
		);

		$docs_count = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->prefix}panel_docs"
		);

		$notes_count = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->prefix}panel_notes"
		);

		return new WP_REST_Response(
			array(
				'kpis'        => $kpis,
				'leads_count' => $leads_count,
				'docs_count'  => $docs_count,
				'notes_count' => $notes_count,
				'period'      => $latest_period ?? '',
			),
			200
		);
	}

	/**
	 * GET /documents — list all documents.
	 */
	public static function get_documents( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;

		$rows = $wpdb->get_results(
			"SELECT id, title, slug, doc_order, status, visibility, file_url, created_at, updated_at
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
				'created_at' => $row->created_at,
				'updated_at' => $row->updated_at,
			);
		}

		return new WP_REST_Response( $docs, 200 );
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

		$is_admin = current_user_can( 'tma_view_audit' );

		if ( $is_admin ) {
			$rows = $wpdb->get_results(
				"SELECT id, user_id, title, content, visibility, created_at, updated_at
				 FROM {$wpdb->prefix}panel_notes
				 ORDER BY created_at DESC"
			);
		} else {
			$rows = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT id, user_id, title, content, visibility, created_at, updated_at
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

		$title      = $request->get_param( 'title' );
		$content    = $request->get_param( 'content' );
		$visibility = $request->get_param( 'visibility' );

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
			),
			array( '%d', '%s', '%s', '%s' )
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
				'message' => __( 'Note created.', 'thormetalart' ),
			),
			201
		);
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
