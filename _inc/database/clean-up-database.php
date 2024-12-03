<?php
// Delete expired and used OTP records periodically
function cleanup_expired_verification_codes(): void
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_verification_otp_codes';
    $retention_period = HOUR_IN_SECONDS*12;

    $wpdb->query("
        DELETE FROM $table
        WHERE expires_at < DATE_SUB(NOW(), INTERVAL $retention_period SECOND)
    ");
}

// Delete expired and used recovery password links periodically
function cleanup_expired_recovery_password_token(): void
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_recovery_password_token';
    $retention_period = HOUR_IN_SECONDS*12;

    $wpdb->query("
        DELETE FROM $table
        WHERE expires_at < DATE_SUB(NOW(), INTERVAL $retention_period SECOND)
    ");
}
add_action('wp_scheduled_delete', 'cleanup_expired_verification_codes');
add_action('wp_scheduled_delete', 'cleanup_expired_recovery_password_token');