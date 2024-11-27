<?php
//add recovery password link to the database
function lr_add_recovery_password_link_to_db($email, $link): mysqli_result|bool|int|null
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_recovery_password_link';
    //check recovery password link limit
    $request_limit = 1;
    $request_time_frame = MINUTE_IN_SECONDS;
    $existing_records = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table 
        WHERE user_email = %s
        AND created_at >= DATE_SUB(NOW(), INTERVAL %d SECOND)
        ORDER BY created_at DESC",
        $email,
        $request_time_frame
    ));
    if (count($existing_records) >= $request_limit) {
        wp_send_json([
            'error' => true,
            'message' => 'تعداد درخواست‌ها از حد مجاز بیشتر است. لطفا بعدا امتحان کنید.'
        ], 429);
    }
    //add the recovery password link to database
    $expires_at = date('Y-m-d H:i:s', strtotime('+3 hours'));
    $data = [
        'user_email' => $email,
        'recovery_password_link' => $link,
        'expires_at' => $expires_at,
        'is_used' => 0
    ];
    $format = ['%s', '%s', '%s', '%d'];
    return $wpdb->insert($table, $data, $format);
}

//validate recovery password link to the database
function lr_validate_recovery_password_link($link): bool
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_recovery_password_link';
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table
        WHERE recovery_password_link=%s
        AND expires_at > NOW()
        AND is_used = 0
        ORDER BY created_at DESC LIMIT 1",
        $link
    ));

    if ($result && time() < strtotime($result->expires_at)) {
//        $data=['is_used'=>1];
//        $where=['id'=>$result->id];
//        $format=['%d'];
//        $where_format=['%d'];
//        $wpdb->update($table, $data, $where, $format, $where_format);
        return true;
    }
    return false;
}