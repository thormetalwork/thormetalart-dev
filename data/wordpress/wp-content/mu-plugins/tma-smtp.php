<?php

/**
 * TMA SMTP Configuration
 *
 * Configures WordPress to send email via external SMTP (Hostinger).
 * Credentials are loaded from environment variables — never hardcoded.
 *
 * @package ThorMetalArt
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

add_action('phpmailer_init', 'tma_configure_smtp');

/**
 * Configure PHPMailer to use SMTP with Hostinger credentials.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance (passed by reference).
 */
function tma_configure_smtp($phpmailer)
{
    $host     = getenv('SMTP_HOST');
    $port     = (int) getenv('SMTP_PORT');
    $user     = getenv('SMTP_USER');
    $pass     = getenv('SMTP_PASS');
    $from     = getenv('SMTP_FROM');
    $from_name = getenv('SMTP_FROM_NAME');

    // Abort silently if credentials are not configured.
    if (! $host || ! $user || ! $pass) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = $host;
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Username   = $user;
    $phpmailer->Password   = $pass;
    $phpmailer->Port       = $port ?: 465;
    $phpmailer->SMTPSecure = ($phpmailer->Port === 587)
        ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS
        : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;

    if ($from) {
        $phpmailer->From     = $from;
        $phpmailer->FromName = $from_name ?: 'Thor Metal Art';
    }
}

/**
 * Capture development email metadata and report success without delivery.
 *
 * @param null|bool $return Short-circuit value.
 * @param array     $atts   Mail arguments.
 * @return null|bool
 */
function tma_capture_development_mail($return, $atts)
{
    if (function_exists('wp_get_environment_type') && wp_get_environment_type() === 'development') {
        $captures   = get_option('tma_dev_mail_capture', []);
        $captures   = is_array($captures) ? $captures : [];
        $captures[] = [
            'recipient_count' => count(array_filter((array) ($atts['to'] ?? []))),
            'subject_hash' => hash('sha256', (string) ($atts['subject'] ?? '')),
            'message_hash' => hash('sha256', (string) ($atts['message'] ?? '')),
            'captured_at'  => gmdate('c'),
        ];
        update_option('tma_dev_mail_capture', array_slice($captures, -20), false);
        return true;
    }

    return $return;
}
add_filter('pre_wp_mail', 'tma_capture_development_mail', 10, 2);

/**
 * Log only a sanitized transport error code when WordPress mail fails.
 *
 * @param WP_Error $error Mail failure.
 */
function tma_log_mail_failure($error)
{
    if ($error instanceof WP_Error) {
        error_log('[TMA Mail] wp_mail_failed: ' . sanitize_key($error->get_error_code()));
    }
}
add_action('wp_mail_failed', 'tma_log_mail_failure');

/**
 * Override the default wp_mail From address.
 */

add_filter('wp_mail_from', function ($original) {
    $from = getenv('SMTP_FROM');
    return $from ?: $original;
});

add_filter('wp_mail_from_name', function ($original) {
    $name = getenv('SMTP_FROM_NAME');
    return $name ?: $original;
});
