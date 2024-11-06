<?php
//add otp code in database
function lr_add_verification_code_to_db($phone_number, $verification_code): bool
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_verification_otp_codes';
    //check OTP request limit
    $request_limit = 5;
    $request_time_frame = MINUTE_IN_SECONDS * 10;

    $existing_records = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table 
        WHERE phone_number = %s
        AND created_at >= DATE_SUB(NOW(), INTERVAL %d SECOND)
        ORDER BY created_at DESC",
        $phone_number,
        $request_time_frame
    ));
    if (count($existing_records) >= $request_limit) {
        wp_send_json([
            'error' => true,
            'message' => 'تعداد درخواست‌ها از حد مجاز بیشتر است. لطفا بعدا امتحان کنید.'
        ], 429);
    }
    //add to otp code to database
    $expires_at = date('Y-m-d H:i:s', strtotime('+3 minutes'));
    $data = [
        'phone_number' => $phone_number,
        'verification_code' => $verification_code,
        'expires_at' => $expires_at,
        'is_used' => 0
    ];
    $format = ['%s', '%s', '%s', '%d'];
    $stmt = $wpdb->insert($table, $data, $format);

    if (!$stmt) {
        return false;
    }
    return true;
}

// validate an OTP
function lr_validate_verification_code($phone_number, $verification_code): bool
{
    global $wpdb;
    $table = $wpdb->prefix . 'lr_verification_otp_codes';
    $result = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table 
        WHERE phone_number = %s
        AND verification_code = %s
        AND expires_at > NOW()
        AND is_used = 0
        ORDER BY created_at DESC
        LIMIT 1",
        $phone_number,
        $verification_code
    ));
    if ($result && time() < strtotime($result->expires_at)) {
        $data = ['is_used' => 1];
        $where = ['id' => $result->id];
        $format = ['%d'];
        $where_format = ['%d'];
        $wpdb->update($table, $data, $where, $format, $where_format);
        return true;
    }
    return false;
}