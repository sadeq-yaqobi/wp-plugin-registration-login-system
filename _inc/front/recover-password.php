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


}