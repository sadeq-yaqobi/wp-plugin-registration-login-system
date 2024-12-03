<?php
//add recovery password link to the database
function lr_add_recovery_password_token_to_db($email, $token): mysqli_result|bool|int|null
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_recovery_password_token';
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
        'recovery_password_token' => $token,
        'expires_at' => $expires_at,
        'is_used' => 0
    ];
    $format = ['%s', '%s', '%s', '%d'];
    return $wpdb->insert($table, $data, $format);
}

//validate recovery password link to the database
function lr_validate_recovery_password_token($token): bool
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_recovery_password_token';
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table
        WHERE recovery_password_token=%s
        AND expires_at > NOW()
        AND is_used = 0
        ORDER BY created_at DESC LIMIT 1",
        $token
    ));

    if ($result && time() < strtotime($result->expires_at)) {
        return true;
    }
    return false;
}


function lr_get_user_data_by_token($token): array
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_recovery_password_token';
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT user_email FROM $table
        WHERE recovery_password_token = '%s'
        ",
        $token));

    $user = get_user_by_email($result->user_email);
    return [
        'ID'=>$user->ID,
        'user_login'=>$user->user_login
        ];
}

function lr_change_token_status_to_used($token):void
{
global $wpdb;
    $table = $wpdb->prefix . 'lr_recovery_password_token';
    $data = ['is_used'=>1];
    $where=['recovery_password_token'=>$token];
    $format=['%d'];
    $where_format=['%s'];
    $wpdb->update($table, $data, $where, $format, $where_format);
}