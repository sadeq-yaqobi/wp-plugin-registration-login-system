<?php
add_action('wp_ajax_nopriv_lr_auth_send_sms_verification_code', 'lr_auth_send_sms_verification_code');
add_action('wp_ajax_nopriv_lr_auth_validate_verification_code', 'lr_auth_validate_verification_code');
add_action('wp_ajax_nopriv_lr_register_user', 'lr_register_user');
function lr_auth_send_sms_verification_code()
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }

    $phone_number = sanitize_text_field($_POST['userPhone']);
    lr_validate_phone($phone_number);
    $verification_code = generate_verification_code();
    $add_code = lr_add_verification_code_to_db($phone_number, $verification_code);
    //    $bodyID = get_option('_lr_bodyID_registration_otp_code');
//    ls_send_sms($otp_code, $user_phone, $bodyID);
    if (!$add_code) {
        wp_send_json([
            'error' => true,
            'message' => 'خطایی رخ داده است. مجدد تلاش کنید',
        ], 500);
    }
    wp_send_json([
        'success' => true,
        'message' => 'کد اعتبارسنجی ارسال شد.',
        'phone_number'=>$phone_number
    ], 200);

}

function lr_auth_validate_verification_code()
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }
    $verification_code_value = sanitize_text_field($_POST['verificationCodeValue']);
    $valid_phone_number = sanitize_text_field($_POST['validPhoneNumber']);
    lr_validate_verification_code_value($verification_code_value);
    $validation_code=lr_validate_verification_code($valid_phone_number, $verification_code_value);
    if (!$validation_code) {
        wp_send_json([
            'error' => true,
            'message' => 'کد اعتبارسنجی اشتباه است یا منقضی شده است.',
        ], 401);
    }

    wp_send_json([
        'success' => true,
        'message' => 'کد اعتبارسنجی درست است.اعتبارسنجی انجام شد.',
    ], 200);
}

function lr_register_user()
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }
    lr_validate_registration_user_data($_POST);
    $user_login = explode('@', $_POST['email']);
    $user_login = $user_login[0] . rand(10, 99);
    $userdata = [
        'user_login' => apply_filters('pre_user_login', sanitize_text_field($user_login)),
        'user_pass' => apply_filters('pre_user_pass', sanitize_text_field($_POST['password'])),
        'display_name' => apply_filters('pre_user_display_name', sanitize_text_field($_POST['displayName'])),
        'user_email' => apply_filters('pre_user_email', sanitize_text_field($_POST['email'])),
        'meta_input' => ['_lr_user_phone' => sanitize_text_field($_POST['validPhoneNumber'])]
    ];
    $user_ID = wp_insert_user($userdata);
    if (is_wp_error($user_ID)) {
        wp_send_json([
            'error' => true,
            'message' => 'خطایی در ثبت نام رخ داده است.'
        ], 409);
    }
    wp_send_json([
        'success' => true,
        'message' => 'ثبت نام با موفقیت انجام شد.'
    ], 200);

}


function lr_validate_phone($phone_number)
{
    if (!preg_match('/^(00|09|\+)[0-9]{8,12}$/', $phone_number)) {

        wp_send_json([
            'error' => true,
            'message' => 'لطفا شماره موبایل معتبر وارد کنید.'
        ], 403);
    }
}

function lr_validate_verification_code_value($verification_code_value )
{
    if ($verification_code_value == '') {
        wp_send_json([
            'error' => true,
            'message' => 'کد اعتبارسنجی را وارد کنید.'
        ], 400);
    }
    if (strlen($verification_code_value)!=6) {
        wp_send_json([
            'error' => true,
            'message' =>'کد تایید شما باید شامل ۶ رقم باشد.'
        ], 400);
    }
}

function lr_validate_registration_user_data($data)
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
    if ( $email== '') {
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
            'error'=>true,
            'message'=>'ایمیل وارد شده قبلا ثبت شده است.'
        ],400);
    }
    if ($password == '') {
        wp_send_json([
            'error' => true,
            'message' => 'یک کلمه عبور وارد نمایید.'
        ], 400);
    }
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/',$password)) {
        wp_send_json([
            'error' => true,
            'message' => 'کلمه عبور باید شامل حداقل هشت کاراکتر و ترکیبی از حروف کوچک و بزرگ، عدد و کاراکترهای ویژه باشد.'
        ], 400);
    }
}

