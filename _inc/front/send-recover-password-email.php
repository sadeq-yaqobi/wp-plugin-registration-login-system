<?php
add_action('wp_ajax_nopriv_lr_send_recovery_password_email','lr_send_recovery_password_email');
function lr_send_recovery_password_email(): void
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }

    $user_email = sanitize_text_field($_POST['userEmail']);
    lr_validate_email($user_email);

    $recovery_link_and_token = lr_generate_recovery_link_email($user_email);
    $add_token=lr_add_recovery_password_token_to_db($user_email, $recovery_link_and_token['token']);
    if (!$add_token) {
        wp_send_json([
            'error' => true,
            'message' => 'در فرایند ایجاد لینک بازیابی خطایی رخ داده است. مجدد امتحان کنید.'
        ], 500);
    }
    lr_mail_recovery_password_link($user_email, $recovery_link_and_token['recovery_link']);

}

function lr_validate_email($email): void
{
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
    if (!email_exists($email)) {
        wp_send_json([
            'error' => true,
            'message' => 'کاربری با ایمیل مورد نظر یافت نشد.'
        ], 400);
    }
}

function lr_generate_recovery_link_email($email): array
{
    $token = date('Ymd') . md5($email) . rand(100000, 999999);
//    return site_url('recovery-password') . '?recoverToken=' . $token;
    $link= add_query_arg(['recoverToken' => $token], site_url('recovery-password/'));
    return [
        'token'=>$token,
        'recovery_link'=>$link
    ];

}

