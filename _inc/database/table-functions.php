<?php
function create_otp_verification_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'lr_verification_otp_codes';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            phone_number varchar(20) NOT NULL,
            verification_code varchar(6) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            expires_at datetime NOT NULL,
            is_used tinyint(1) DEFAULT 0 COMMENT '0:unused 1:used',
            PRIMARY KEY (id),
            KEY phone_number (phone_number),
            KEY verification_code (verification_code)
        ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function delete_otp_verification_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'lr_verification_otp_codes';
    $sql = "DROP TABLE IF EXISTS `$table_name`";
    $wpdb->query($sql);
}