<?php
// Delete expired and used OTP records periodically
function cleanup_expired_verification_codes() {
    global $wpdb;
    $table = $wpdb->prefix . 'lr_verification_otp_codes';
    $retention_period = HOUR_IN_SECONDS*12;

    $wpdb->query("
        DELETE FROM $table
        WHERE expires_at < DATE_SUB(NOW(), INTERVAL $retention_period SECOND)
    ");
}
add_action('wp_scheduled_delete', 'cleanup_expired_verification_codes');