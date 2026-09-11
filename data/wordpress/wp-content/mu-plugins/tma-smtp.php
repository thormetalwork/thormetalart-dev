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
 * Override the default wp_mail From address.
 */
/**
 * In development environments, suppress all outgoing email.
 * wp_mail() will return false without attempting delivery.
 */
add_filter('pre_wp_mail', function ($null) {
    if (function_exists('wp_get_environment_type') && wp_get_environment_type() === 'development') {
        return false;
    }
    return $null;
});

add_filter('wp_mail_from', function ($original) {
    $from = getenv('SMTP_FROM');
    return $from ?: $original;
});

add_filter('wp_mail_from_name', function ($original) {
    $name = getenv('SMTP_FROM_NAME');
    return $name ?: $original;
});
