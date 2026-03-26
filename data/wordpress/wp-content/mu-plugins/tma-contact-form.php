<?php
/**
 * Plugin Name: TMA Contact Form & Lead Tracking
 * Description: Formulario de contacto con tracking de leads para Thor Metal Art.
 * Version: 1.0.0
 * Author: Thor Metal Art Dev
 *
 * Shortcode: [tma_contact_form]
 * Features: Honeypot anti-spam, nonce CSRF, rate limiting, UTM tracking,
 *           admin leads table, email notification, bilingual EN/ES.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─── 1. Custom Table Creation ─────────────────────────────────── */

function tma_leads_create_table() {
    global $wpdb;
    $table   = $wpdb->prefix . 'tma_leads';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        full_name VARCHAR(200) NOT NULL,
        email VARCHAR(200) NOT NULL,
        phone VARCHAR(50) DEFAULT '',
        service VARCHAR(200) DEFAULT '',
        message TEXT NOT NULL,
        page_url VARCHAR(500) DEFAULT '',
        referrer VARCHAR(500) DEFAULT '',
        utm_source VARCHAR(200) DEFAULT '',
        utm_medium VARCHAR(200) DEFAULT '',
        utm_campaign VARCHAR(200) DEFAULT '',
        ip_hash VARCHAR(64) DEFAULT '',
        locale VARCHAR(10) DEFAULT 'es',
        status VARCHAR(20) DEFAULT 'new',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_status (status),
        KEY idx_created (created_at)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
    update_option( 'tma_leads_db_version', '1.0' );
}

// Run on first load if table doesn't exist.
add_action( 'init', function () {
    if ( get_option( 'tma_leads_db_version' ) !== '1.0' ) {
        tma_leads_create_table();
    }
} );

/* ─── 2. Shortcode ─────────────────────────────────────────────── */

add_shortcode( 'tma_contact_form', 'tma_render_contact_form' );

function tma_render_contact_form( $atts ) {
    $atts = shortcode_atts( [ 'lang' => 'es' ], $atts, 'tma_contact_form' );
    $lang = in_array( $atts['lang'], [ 'en', 'es' ], true ) ? $atts['lang'] : 'es';

    $labels = tma_form_labels( $lang );

    // Enqueue inline styles & JS.
    tma_enqueue_form_assets();

    $nonce = wp_nonce_field( 'tma_contact_submit', '_tma_nonce', true, false );

    $services = [
        'custom-gates'    => [ 'es' => 'Puertas personalizadas', 'en' => 'Custom Gates' ],
        'railings'        => [ 'es' => 'Barandas y pasamanos',   'en' => 'Railings & Handrails' ],
        'fences'          => [ 'es' => 'Cercas ornamentales',    'en' => 'Ornamental Fences' ],
        'furniture'       => [ 'es' => 'Mobiliario metálico',    'en' => 'Metal Furniture' ],
        'metal-art'       => [ 'es' => 'Arte en metal',          'en' => 'Metal Art & Sculptures' ],
        'other'           => [ 'es' => 'Otro',                   'en' => 'Other' ],
    ];

    $options_html = '<option value="">' . esc_html( $labels['select_service'] ) . '</option>';
    foreach ( $services as $value => $names ) {
        $options_html .= '<option value="' . esc_attr( $value ) . '">' . esc_html( $names[ $lang ] ) . '</option>';
    }

    ob_start();
    ?>
    <div id="tma-contact-form-wrap" class="tma-cf-wrap">
        <form id="tma-contact-form" method="post" novalidate>
            <?php echo $nonce; ?>
            <input type="hidden" name="action" value="tma_submit_lead" />
            <input type="hidden" name="tma_page_url" value="" />
            <input type="hidden" name="tma_referrer" value="" />
            <input type="hidden" name="tma_utm_source" value="" />
            <input type="hidden" name="tma_utm_medium" value="" />
            <input type="hidden" name="tma_utm_campaign" value="" />
            <input type="hidden" name="tma_locale" value="<?php echo esc_attr( $lang ); ?>" />

            <!-- Honeypot — hidden from humans -->
            <div style="position:absolute;left:-9999px;" aria-hidden="true">
                <label for="tma_website">Website</label>
                <input type="text" name="tma_website" id="tma_website" tabindex="-1" autocomplete="off" />
            </div>

            <div class="tma-cf-field">
                <label for="tma_name"><?php echo esc_html( $labels['name'] ); ?> *</label>
                <input type="text" id="tma_name" name="tma_name" required maxlength="200"
                       placeholder="<?php echo esc_attr( $labels['name_ph'] ); ?>" />
            </div>

            <div class="tma-cf-row">
                <div class="tma-cf-field">
                    <label for="tma_email"><?php echo esc_html( $labels['email'] ); ?> *</label>
                    <input type="email" id="tma_email" name="tma_email" required maxlength="200"
                           placeholder="<?php echo esc_attr( $labels['email_ph'] ); ?>" />
                </div>
                <div class="tma-cf-field">
                    <label for="tma_phone"><?php echo esc_html( $labels['phone'] ); ?></label>
                    <input type="tel" id="tma_phone" name="tma_phone" maxlength="50"
                           placeholder="<?php echo esc_attr( $labels['phone_ph'] ); ?>" />
                </div>
            </div>

            <div class="tma-cf-field">
                <label for="tma_service"><?php echo esc_html( $labels['service'] ); ?></label>
                <select id="tma_service" name="tma_service">
                    <?php echo $options_html; ?>
                </select>
            </div>

            <div class="tma-cf-field">
                <label for="tma_message"><?php echo esc_html( $labels['message'] ); ?> *</label>
                <textarea id="tma_message" name="tma_message" rows="5" required maxlength="2000"
                          placeholder="<?php echo esc_attr( $labels['message_ph'] ); ?>"></textarea>
            </div>

            <div class="tma-cf-submit">
                <button type="submit" id="tma-cf-btn">
                    <?php echo esc_html( $labels['submit'] ); ?>
                </button>
            </div>

            <div id="tma-cf-feedback" class="tma-cf-feedback" role="alert" aria-live="polite"></div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}

/* ─── 3. Bilingual Labels ──────────────────────────────────────── */

function tma_form_labels( $lang = 'es' ) {
    $labels = [
        'es' => [
            'name'           => 'Nombre completo',
            'name_ph'        => 'Tu nombre',
            'email'          => 'Correo electrónico',
            'email_ph'       => 'tu@email.com',
            'phone'          => 'Teléfono',
            'phone_ph'       => '(305) 555-0000',
            'service'        => 'Servicio de interés',
            'select_service' => '— Selecciona un servicio —',
            'message'        => 'Mensaje',
            'message_ph'     => 'Cuéntanos sobre tu proyecto...',
            'submit'         => 'Enviar mensaje',
            'success'        => '¡Gracias! Hemos recibido tu mensaje. Te contactaremos pronto.',
            'error'          => 'Hubo un error al enviar tu mensaje. Inténtalo de nuevo.',
            'rate_limit'     => 'Ya enviaste un mensaje recientemente. Espera unos minutos.',
            'spam'           => 'Envío no válido.',
        ],
        'en' => [
            'name'           => 'Full name',
            'name_ph'        => 'Your name',
            'email'          => 'Email address',
            'email_ph'       => 'you@email.com',
            'phone'          => 'Phone',
            'phone_ph'       => '(305) 555-0000',
            'service'        => 'Service of interest',
            'select_service' => '— Select a service —',
            'message'        => 'Message',
            'message_ph'     => 'Tell us about your project...',
            'submit'         => 'Send message',
            'success'        => 'Thank you! We received your message. We will contact you soon.',
            'error'          => 'There was an error sending your message. Please try again.',
            'rate_limit'     => 'You already sent a message recently. Please wait a few minutes.',
            'spam'           => 'Invalid submission.',
        ],
    ];
    return $labels[ $lang ] ?? $labels['es'];
}

/* ─── 4. AJAX Handler ──────────────────────────────────────────── */

add_action( 'wp_ajax_tma_submit_lead',        'tma_handle_lead_submission' );
add_action( 'wp_ajax_nopriv_tma_submit_lead',  'tma_handle_lead_submission' );

function tma_handle_lead_submission() {
    // Nonce check.
    if ( ! isset( $_POST['_tma_nonce'] ) || ! wp_verify_nonce( $_POST['_tma_nonce'], 'tma_contact_submit' ) ) {
        wp_send_json_error( [ 'message' => 'Security check failed.' ], 403 );
    }

    $locale = isset( $_POST['tma_locale'] ) && $_POST['tma_locale'] === 'en' ? 'en' : 'es';
    $labels = tma_form_labels( $locale );

    // Honeypot check.
    if ( ! empty( $_POST['tma_website'] ) ) {
        wp_send_json_error( [ 'message' => $labels['spam'] ], 403 );
    }

    // Rate limiting — 1 submission per IP per 3 minutes.
    $ip_raw     = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $ip_hash    = hash( 'sha256', $ip_raw . wp_salt( 'nonce' ) );
    $rate_key   = 'tma_lead_rate_' . substr( $ip_hash, 0, 16 );

    if ( get_transient( $rate_key ) ) {
        wp_send_json_error( [ 'message' => $labels['rate_limit'] ], 429 );
    }

    // Sanitize inputs.
    $name    = sanitize_text_field( wp_unslash( $_POST['tma_name'] ?? '' ) );
    $email   = sanitize_email( wp_unslash( $_POST['tma_email'] ?? '' ) );
    $phone   = sanitize_text_field( wp_unslash( $_POST['tma_phone'] ?? '' ) );
    $service = sanitize_text_field( wp_unslash( $_POST['tma_service'] ?? '' ) );
    $message = sanitize_textarea_field( wp_unslash( $_POST['tma_message'] ?? '' ) );

    // Validate required fields.
    if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( [ 'message' => $labels['error'] ], 400 );
    }
    if ( ! is_email( $email ) ) {
        wp_send_json_error( [ 'message' => $labels['error'] ], 400 );
    }

    // Tracking data.
    $page_url     = esc_url_raw( wp_unslash( $_POST['tma_page_url'] ?? '' ) );
    $referrer     = esc_url_raw( wp_unslash( $_POST['tma_referrer'] ?? '' ) );
    $utm_source   = sanitize_text_field( wp_unslash( $_POST['tma_utm_source'] ?? '' ) );
    $utm_medium   = sanitize_text_field( wp_unslash( $_POST['tma_utm_medium'] ?? '' ) );
    $utm_campaign = sanitize_text_field( wp_unslash( $_POST['tma_utm_campaign'] ?? '' ) );

    // Insert into DB.
    global $wpdb;
    $table  = $wpdb->prefix . 'tma_leads';
    $result = $wpdb->insert(
        $table,
        [
            'full_name'    => $name,
            'email'        => $email,
            'phone'        => $phone,
            'service'      => $service,
            'message'      => $message,
            'page_url'     => $page_url,
            'referrer'     => $referrer,
            'utm_source'   => $utm_source,
            'utm_medium'   => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'ip_hash'      => $ip_hash,
            'locale'       => $locale,
            'status'       => 'new',
        ],
        [ '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ]
    );

    if ( false === $result ) {
        wp_send_json_error( [ 'message' => $labels['error'] ], 500 );
    }

    do_action(
        'tma_panel_create_lead',
        [
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'source'  => $utm_source ?: 'web',
            'message' => $message,
        ]
    );

    // Set rate limit transient (3 minutes).
    set_transient( $rate_key, 1, 3 * MINUTE_IN_SECONDS );

    // Send admin notification.
    tma_send_lead_notification( $name, $email, $phone, $service, $message, $page_url );

    wp_send_json_success( [ 'message' => $labels['success'] ] );
}

/* ─── 5. Email Notification ────────────────────────────────────── */

function tma_send_lead_notification( $name, $email, $phone, $service, $message, $page_url ) {
    $to      = get_option( 'admin_email' );
    $subject = sprintf( '[Thor Metal Art] Nuevo lead: %s', $name );
    $body    = implode( "\n", [
        "Nuevo contacto recibido:",
        "",
        "Nombre:   {$name}",
        "Email:    {$email}",
        "Teléfono: {$phone}",
        "Servicio: {$service}",
        "Página:   {$page_url}",
        "",
        "Mensaje:",
        $message,
        "",
        "---",
        "Gestiona los leads en: " . admin_url( 'admin.php?page=tma-leads' ),
    ] );

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$name} <{$email}>",
    ];

    wp_mail( $to, $subject, $body, $headers );
}

/* ─── 6. Admin Page — Leads List ───────────────────────────────── */

add_action( 'admin_menu', function () {
    add_menu_page(
        'TMA Leads',
        'Leads',
        'manage_options',
        'tma-leads',
        'tma_render_leads_page',
        'dashicons-megaphone',
        30
    );
} );

function tma_render_leads_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Unauthorized' );
    }

    global $wpdb;
    $table = $wpdb->prefix . 'tma_leads';

    // Handle status updates.
    if ( isset( $_POST['tma_update_status'], $_POST['_wpnonce'] ) &&
         wp_verify_nonce( $_POST['_wpnonce'], 'tma_lead_status' ) ) {
        $lead_id    = absint( $_POST['lead_id'] );
        $new_status = sanitize_text_field( $_POST['new_status'] );
        $allowed    = [ 'new', 'contacted', 'quoted', 'won', 'lost' ];
        if ( in_array( $new_status, $allowed, true ) && $lead_id > 0 ) {
            $wpdb->update( $table, [ 'status' => $new_status ], [ 'id' => $lead_id ], [ '%s' ], [ '%d' ] );
        }
    }

    // Fetch leads.
    $leads = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 200" );

    $status_colors = [
        'new'       => '#2196F3',
        'contacted' => '#FF9800',
        'quoted'    => '#9C27B0',
        'won'       => '#4CAF50',
        'lost'      => '#F44336',
    ];

    ?>
    <div class="wrap">
        <h1>🔥 Thor Metal Art — Leads</h1>
        <p>Total: <strong><?php echo count( $leads ); ?></strong> leads</p>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Servicio</th>
                    <th>Mensaje</th>
                    <th>Fuente</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            <?php if ( empty( $leads ) ) : ?>
                <tr><td colspan="9" style="text-align:center;">Sin leads todavía.</td></tr>
            <?php else : ?>
                <?php foreach ( $leads as $lead ) : ?>
                <tr>
                    <td><?php echo (int) $lead->id; ?></td>
                    <td><?php echo esc_html( date_i18n( 'M j, Y g:ia', strtotime( $lead->created_at ) ) ); ?></td>
                    <td><strong><?php echo esc_html( $lead->full_name ); ?></strong></td>
                    <td><a href="mailto:<?php echo esc_attr( $lead->email ); ?>"><?php echo esc_html( $lead->email ); ?></a></td>
                    <td><?php echo esc_html( $lead->phone ); ?></td>
                    <td><?php echo esc_html( $lead->service ); ?></td>
                    <td title="<?php echo esc_attr( $lead->message ); ?>"><?php echo esc_html( wp_trim_words( $lead->message, 10 ) ); ?></td>
                    <td>
                        <?php
                        $source_parts = array_filter( [ $lead->utm_source, $lead->utm_medium, $lead->utm_campaign ] );
                        echo $source_parts ? esc_html( implode( ' / ', $source_parts ) ) : '<em>directo</em>';
                        if ( $lead->referrer ) {
                            echo '<br><small>' . esc_html( wp_parse_url( $lead->referrer, PHP_URL_HOST ) ) . '</small>';
                        }
                        ?>
                    </td>
                    <td>
                        <form method="post" style="display:inline;">
                            <?php wp_nonce_field( 'tma_lead_status' ); ?>
                            <input type="hidden" name="lead_id" value="<?php echo (int) $lead->id; ?>" />
                            <input type="hidden" name="tma_update_status" value="1" />
                            <select name="new_status" onchange="this.form.submit()" style="border-left:4px solid <?php echo esc_attr( $status_colors[ $lead->status ] ?? '#999' ); ?>;">
                                <?php foreach ( $status_colors as $key => $color ) : ?>
                                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $lead->status, $key ); ?>><?php echo esc_html( ucfirst( $key ) ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/* ─── 7. Frontend Assets (Inline) ──────────────────────────────── */

function tma_enqueue_form_assets() {
    static $enqueued = false;
    if ( $enqueued ) return;
    $enqueued = true;

    // Register AJAX URL for frontend.
    wp_enqueue_script( 'jquery' );
    wp_add_inline_script( 'jquery', 'var tmaAjax = ' . wp_json_encode( [
        'url'   => admin_url( 'admin-ajax.php' ),
    ] ) . ';' );

    // Inline CSS.
    $css = '
    .tma-cf-wrap { max-width:640px; margin:0 auto; font-family:"DM Sans",sans-serif; }
    .tma-cf-field { margin-bottom:1.2rem; }
    .tma-cf-field label { display:block; font-weight:600; margin-bottom:0.4rem; color:#1A1A1A; font-size:0.95rem; }
    .tma-cf-field input,
    .tma-cf-field select,
    .tma-cf-field textarea { width:100%; padding:0.75rem 1rem; border:1px solid #ccc; border-radius:6px; font-size:1rem; font-family:inherit; transition:border-color 0.2s; box-sizing:border-box; }
    .tma-cf-field input:focus,
    .tma-cf-field select:focus,
    .tma-cf-field textarea:focus { border-color:#B8860B; outline:none; box-shadow:0 0 0 3px rgba(184,134,11,0.15); }
    .tma-cf-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media (max-width:600px) { .tma-cf-row { grid-template-columns:1fr; } }
    .tma-cf-submit { margin-top:1.5rem; }
    #tma-cf-btn { background:#B8860B; color:#fff; border:none; padding:0.9rem 2.5rem; font-size:1.05rem; font-weight:600; border-radius:6px; cursor:pointer; transition:background 0.2s; font-family:inherit; }
    #tma-cf-btn:hover { background:#9a7209; }
    #tma-cf-btn:disabled { opacity:0.6; cursor:wait; }
    .tma-cf-feedback { margin-top:1rem; padding:0; border-radius:6px; font-size:0.95rem; }
    .tma-cf-feedback.success { padding:1rem; background:#e8f5e9; color:#2e7d32; border:1px solid #a5d6a7; }
    .tma-cf-feedback.error { padding:1rem; background:#ffebee; color:#c62828; border:1px solid #ef9a9a; }
    ';
    wp_register_style( 'tma-contact-form', false );
    wp_enqueue_style( 'tma-contact-form' );
    wp_add_inline_style( 'tma-contact-form', $css );

    // Inline JS.
    $js = '
    document.addEventListener("DOMContentLoaded", function() {
        var form = document.getElementById("tma-contact-form");
        if (!form) return;

        /* Populate tracking fields */
        var params = new URLSearchParams(window.location.search);
        form.querySelector("[name=tma_page_url]").value = window.location.href;
        form.querySelector("[name=tma_referrer]").value = document.referrer || "";
        form.querySelector("[name=tma_utm_source]").value = params.get("utm_source") || "";
        form.querySelector("[name=tma_utm_medium]").value = params.get("utm_medium") || "";
        form.querySelector("[name=tma_utm_campaign]").value = params.get("utm_campaign") || "";

        form.addEventListener("submit", function(e) {
            e.preventDefault();
            var btn = document.getElementById("tma-cf-btn");
            var fb  = document.getElementById("tma-cf-feedback");
            btn.disabled = true;
            fb.className = "tma-cf-feedback";
            fb.textContent = "";

            var fd = new FormData(form);
            fetch(tmaAjax.url, { method: "POST", body: fd, credentials: "same-origin" })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        fb.className = "tma-cf-feedback success";
                        fb.textContent = data.data.message;
                        form.reset();
                    } else {
                        fb.className = "tma-cf-feedback error";
                        fb.textContent = data.data && data.data.message ? data.data.message : "Error";
                    }
                })
                .catch(function() {
                    fb.className = "tma-cf-feedback error";
                    fb.textContent = "Error de conexión.";
                })
                .finally(function() { btn.disabled = false; });
        });
    });
    ';
    wp_register_script( 'tma-contact-form-js', false, [ 'jquery' ], '1.0', true );
    wp_enqueue_script( 'tma-contact-form-js' );
    wp_add_inline_script( 'tma-contact-form-js', $js );
}

/* ─── 8. REST Endpoint for Dashboard API ───────────────────────── */

add_action( 'rest_api_init', function () {
    register_rest_route( 'tma/v1', '/leads/stats', [
        'methods'             => 'GET',
        'callback'            => 'tma_leads_stats_endpoint',
        'permission_callback' => function () {
            return current_user_can( 'manage_options' );
        },
    ] );
} );

function tma_leads_stats_endpoint() {
    global $wpdb;
    $table = $wpdb->prefix . 'tma_leads';

    $total     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
    $this_month = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$table} WHERE created_at >= %s",
        gmdate( 'Y-m-01 00:00:00' )
    ) );
    $by_status = $wpdb->get_results(
        "SELECT status, COUNT(*) AS cnt FROM {$table} GROUP BY status",
        OBJECT_K
    );
    $by_source = $wpdb->get_results(
        "SELECT COALESCE(NULLIF(utm_source,''),'directo') AS source, COUNT(*) AS cnt FROM {$table} GROUP BY source ORDER BY cnt DESC LIMIT 10"
    );

    return [
        'total'      => $total,
        'this_month' => $this_month,
        'by_status'  => array_map( function ( $r ) { return (int) $r->cnt; }, (array) $by_status ),
        'by_source'  => $by_source,
    ];
}
