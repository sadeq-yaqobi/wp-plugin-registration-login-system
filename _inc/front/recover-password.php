<?php
add_action('wp_ajax_nopriv_lr_recover_password','lr_recover_password');
function lr_recover_password(): void
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


    $recover_email_link = lr_generate_recovery_link_email($user_email);
    $add_link=lr_add_recovery_password_link_to_db($user_email, $recover_email_link);
    if (!$add_link) {
        wp_send_json([
            'error' => true,
            'message' => 'در فرایند ایجاد لینک بازیابی خطایی رخ داده است. مجدد امتحان کنید.'
        ], 500);
    }
    lr_mail_recovery_password_link($user_email, $recover_email_link);

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

function lr_generate_recovery_link_email($email): string
{
    $token = date('Ymd') . md5($email) . rand(100000, 999999);
//    return site_url('recovery-password') . '?recoverToken=' . $token;
    return add_query_arg(['recoverToken' => $token], site_url('recovery-password/'));

}
