<?php
add_action('wp_ajax_nopriv_lr_auth_send_sms_verification_code', 'lr_auth_send_sms_verification_code');
add_action('wp_ajax_nopriv_lr_auth_validate_verification_code', 'lr_auth_validate_verification_code');
add_action('wp_ajax_nopriv_lr_register_user', 'lr_register_user');
function lr_auth_send_sms_verification_code(): void
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }

    $user_phone = sanitize_text_field($_POST['userPhone']);
    lr_is_phone_exist($user_phone);
    lr_validate_phone($user_phone);
    $verification_code = generate_verification_code();
    $add_code = lr_add_verification_code_to_db($user_phone, $verification_code);

    //send verification code to user by SMS
    $bodyID = get_option('_lr_bodyID_registration_otp_code');
    lr_send_sms($verification_code, $user_phone, $bodyID);

    if (!$add_code) {
        wp_send_json([
            'error' => true,
            'message' => 'خطایی رخ داده است. مجدد تلاش کنید',
        ], 500);
    }
    wp_send_json([
        'success' => true,
        'message' => 'کد اعتبارسنجی ارسال شد.',
        'phone_number' => $user_phone
    ], 200);

}

function lr_auth_validate_verification_code(): void
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }

    $verification_code_value = sanitize_text_field($_POST['verificationCodeValue']);
    $user_phone = sanitize_text_field($_POST['userPhone']);
    lr_validate_verification_code_value($verification_code_value);
    $valid_code = lr_validate_verification_code($user_phone, $verification_code_value);
    if (!$valid_code['status']) {
        wp_send_json([
            'error' => true,
            'message' => 'کد اعتبارسنجی اشتباه است یا منقضی شده است.',
        ], 401);
    }
    $_SESSION['user_valid_phone'] = $valid_code['valid_phone']; //setting valid user phone number as a session
    wp_send_json([
        'success' => true,
        'message' => 'کد اعتبارسنجی درست است.اعتبارسنجی انجام شد.',
    ], 200);
}

function lr_register_user(): void
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }
    lr_validate_registration_user_data($_POST);
    $user_valid_phone = $_SESSION['user_valid_phone'];
    $user_password = sanitize_text_field($_POST['password']);
    $user_display_name = sanitize_text_field($_POST['displayName']);
    $user_email = sanitize_text_field($_POST['email']);
    //generate user login
    $user_login = explode('@', $user_email);
    $user_login = $user_login[0] . rand(10, 99);

    $userdata = [
        'user_login' => apply_filters('pre_user_login', $user_login),
        'user_pass' => apply_filters('pre_user_pass', $user_password),
        'display_name' => apply_filters('pre_user_display_name', $user_display_name),
        'user_email' => apply_filters('pre_user_email', $user_email),
        'meta_input' => ['_lr_user_phone' => $user_valid_phone]
    ];
    $user_ID = wp_insert_user($userdata);

    if (is_wp_error($user_ID)) {
        wp_send_json([
            'error' => true,
            'message' => 'خطایی در ثبت نام رخ داده است.'
        ], 409);
    }

    $bodyID = get_option('_lr_bodyID_registration_welcome_message');
    lr_send_sms("{$user_display_name};{$user_login};{$user_password}", $user_valid_phone, $bodyID);
    unset($_SESSION['user_valid_phone']);

    wp_send_json([
        'success' => true,
        'message' => 'ثبت نام با موفقیت انجام شد.'
    ], 200);

}


function lr_is_phone_exist($user_phone): void
{
    $args = [
        'meta_key' => '_lr_user_phone',
        'meta_value' => $user_phone,
        'compare' => '='
    ];
    $user_query = new WP_User_Query($args);
    if ($user_query->get_results()) {
        wp_send_json([
            'error' => true,
            'message' => 'شماره موبایل وارد شده قبلا ثبت شده است.'
        ], 400);
    }
}

function lr_validate_phone($phone_number): void
{
    if (!preg_match('/^(00|09|\+)[0-9]{8,12}$/', $phone_number)) {

        wp_send_json([
            'error' => true,
            'message' => 'لطفا شماره موبایل معتبر وارد کنید.'
        ], 403);
    }
}

function lr_validate_verification_code_value($verification_code_value): void
{
    if ($verification_code_value == '') {
        wp_send_json([
            'error' => true,
            'message' => 'کد اعتبارسنجی را وارد کنید.'
        ], 400);
    }
    if (strlen($verification_code_value) != 6) {
        wp_send_json([
            'error' => true,
            'message' => 'کد تایید شما باید شامل ۶ رقم باشد.'
        ], 400);
    }
}

function lr_validate_registration_user_data($data): void
{
    $display_name = $data['displayName'];
    $email = $data['email'];
    $password = $data['password'];
    if ($display_name == '') {
        wp_send_json([
            'error' => true,
            'message' => 'نام و نام خانوادگی خود را وارد نمایید.'
        ], 400);
    }
    if ($email == '') {
        wp_send_json([
            'error' => true,
            'message' => 'ایمیل خود را وارد نمایید.'
        ], 400);
    }
    if (!is_email($email)) {
        wp_send_json([
            'error' => true,
            'message' => 'ایمیل معتبر وارد نمایید.'
        ], 400);
    }
    if (email_exists($email)) {
        wp_send_json([
            'error' => true,
            'message' => 'ایمیل وارد شده قبلا ثبت شده است.'
        ], 400);
    }
    if ($password == '') {
        wp_send_json([
            'error' => true,
            'message' => 'یک کلمه عبور وارد نمایید.'
        ], 400);
    }
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/', $password)) {
        wp_send_json([
            'error' => true,
            'message' => 'کلمه عبور باید شامل حداقل هشت کاراکتر و ترکیبی از حروف کوچک و بزرگ، عدد و کاراکترهای ویژه باشد.'
        ], 400);
    }
}

