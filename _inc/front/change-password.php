<?php
add_action('wp_ajax_nopriv_lr_set_new_password', 'lr_set_new_password');
function lr_set_new_password(): void
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }
    $new_password = sanitize_text_field($_POST['newPassword']);
    $repeated_new_password = sanitize_text_field($_POST['newPasswordRepeated']);
    $token = sanitize_text_field($_POST['recoveryToken']);
    $validated_token = lr_validate_recovery_password_token($token);
    if (!$validated_token) {
        wp_send_json([
            'error' => true,
            'message' => 'لینک بازیابی کلمه عبور شما معتبر نیست یا منقضی شده است.'
        ], 400);
    }
    lr_validate_new_password($new_password, $repeated_new_password);
    $user_data = lr_get_user_data_by_token($token);
    $user_id = $user_data['ID'];
    $user_login = $user_data['user_login'];
    $change_password = wp_set_password($new_password, $user_id);
    if (is_wp_error($change_password)) {
        wp_send_json([
            'error' => true,
            'message' => 'در فرایند تغییر کلمه عبور خطایی رخ داده است. دوباره تلاش کنید.'
        ], 500);
    }
    lr_change_token_status_to_used($token);

    //login user after successful change password
    $credentials = [
        'user_login' => $user_login,
        'user_password' => $new_password
    ];
    $user_log_in = wp_signon($credentials, false);

    if (is_wp_error($user_log_in)) {
        // Send error response for invalid credentials
        wp_send_json([
            'error' => true,
            'message' => 'تغییر کلمه عبور با موفقیت انجام شد. لطفا از صفحه ورود وارد حساب خود شوید.',
        ], 401);
    }
    // Set the current user
    wp_set_current_user($user_log_in->ID, $user_log_in->user_login);

    wp_send_json([
        'success' => true,
        'message' => 'تغییر کلمه عبور با موفقیت انجام شد. در حال انتقال به سایت'
    ], 200);



}


function lr_validate_new_password($new_password, $repeated_new_password): void
{
    if ($new_password == '' || $repeated_new_password == '') {
        wp_send_json([
            'error' => true,
            'message' => 'کلمه عبور و تکرار آن را وارد نمایید.'
        ], 400);
    }
    if ($new_password != $repeated_new_password) {
        wp_send_json([
            'error' => true,
            'message' => 'کلمه عبور وارد شده و تکرار آن یکسان نیست.'
        ], 400);
    }
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/', $new_password)) {
        wp_send_json([
            'error' => true,
            'message' => 'کلمه عبور باید شامل حداقل هشت کاراکتر و ترکیبی از حروف کوچک و بزرگ، عدد و کاراکترهای ویژه باشد.'
        ], 400);
    }
}

