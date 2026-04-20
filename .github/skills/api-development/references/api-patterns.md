# API Patterns — TMA Panel

## Existing Endpoint Patterns

### GET Collection (with filtering)

From `get_leads()`:
```php
public function get_leads( WP_REST_Request $request ) {
    global $wpdb;

    $status = $request->get_param( 'status' );
    $where  = '';
    $params = array();

    if ( $status ) {
        $where    = 'WHERE status = %s';
        $params[] = $status;
    }

    $query = "SELECT * FROM {$wpdb->prefix}panel_leads $where ORDER BY created_at DESC";

    $results = empty( $params )
        ? $wpdb->get_results( $query )
        : $wpdb->get_results( $wpdb->prepare( $query, $params ) );

    return new WP_REST_Response( $results, 200 );
}
```

### GET Single (with path parameter)

```php
register_rest_route(
    'tma-panel/v1',
    '/leads/(?P<id>\d+)/history',
    array(
        'methods'             => 'GET',
        'callback'            => array( $this, 'get_lead_history' ),
        'permission_callback' => function () {
            return is_user_logged_in() && current_user_can( 'tma_view_panel' );
        },
        'args'                => array(
            'id' => array(
                'required'          => true,
                'validate_callback' => function ( $value ) {
                    return is_numeric( $value ) && intval( $value ) > 0;
                },
                'sanitize_callback' => 'absint',
            ),
        ),
    )
);
```

### POST Update (with audit logging)

From `update_lead()`:
```php
public function update_lead( WP_REST_Request $request ) {
    global $wpdb;

    $id     = absint( $request->get_param( 'id' ) );
    $status = sanitize_text_field( $request->get_param( 'status' ) );
    $value  = floatval( $request->get_param( 'lead_value' ) );

    // Validate status
    $valid_statuses = array( 'new', 'contacted', 'quoted', 'won', 'lost' );
    if ( ! in_array( $status, $valid_statuses, true ) ) {
        return new WP_REST_Response(
            array( 'message' => 'Invalid status' ),
            400
        );
    }

    // Get current state for audit
    $current = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT status FROM {$wpdb->prefix}panel_leads WHERE id = %d",
            $id
        )
    );

    if ( ! $current ) {
        return new WP_REST_Response( array( 'message' => 'Lead not found' ), 404 );
    }

    // Update
    $wpdb->update(
        "{$wpdb->prefix}panel_leads",
        array(
            'status'     => $status,
            'lead_value' => $value,
            'updated_at' => current_time( 'mysql' ),
        ),
        array( 'id' => $id ),
        array( '%s', '%f', '%s' ),
        array( '%d' )
    );

    // Audit trail
    if ( $current->status !== $status ) {
        $wpdb->insert(
            "{$wpdb->prefix}panel_lead_history",
            array(
                'lead_id'    => $id,
                'user_id'    => get_current_user_id(),
                'action'     => 'status_change',
                'old_status' => $current->status,
                'new_status' => $status,
                'created_at' => current_time( 'mysql' ),
            ),
            array( '%d', '%d', '%s', '%s', '%s', '%s' )
        );
    }

    // High-value alert
    if ( $value >= 5000 ) {
        do_action( 'tma_high_value_lead', $id, $value );
    }

    return new WP_REST_Response( array( 'updated' => true ), 200 );
}
```

## Permission Patterns

```php
// Admin-only (audit, kpis, visibility)
'permission_callback' => function () {
    return is_user_logged_in() && current_user_can( 'tma_view_audit' );
}

// Both roles (view, leads, docs, notes)
'permission_callback' => function () {
    return is_user_logged_in() && current_user_can( 'tma_view_panel' );
}

// Write operations (notes)
'permission_callback' => function () {
    return is_user_logged_in() && current_user_can( 'tma_manage_notes' );
}
```

## Error Response Patterns

```php
// 400 Bad Request — invalid input
return new WP_REST_Response( array( 'message' => 'Invalid parameter: status' ), 400 );

// 401 Unauthorized — not logged in (handled by permission_callback)

// 403 Forbidden — logged in but lacks capability (handled by permission_callback)

// 404 Not Found — resource doesn't exist
return new WP_REST_Response( array( 'message' => 'Lead not found' ), 404 );

// 500 Internal Server Error — database failure
return new WP_REST_Response( array( 'message' => 'Database error' ), 500 );
```
