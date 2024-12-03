<?php
/**
 * WordPress AJAX Authentication Handler
 * Handles login requests for non-logged-in users with security checks
 */

// Hook for handling AJAX login requests from non-logged-in users
add_action('wp_ajax_nopriv_lr_auth_login', 'lr_auth_login');

/**
 * Main login authentication function
 * Processes login requests, validates input, and handles user authentication
 *
 * @return void Sends JSON response
 */
function lr_auth_login(): void
{
    // Security: Verify nonce token
    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'])) {
        wp_send_json([
            'error' => true,
            'message' => 'access denied',
        ], 403);
    }

    // Sanitize and prepare user input
    $email = sanitize_text_field($_POST['email']);
    $password = sanitize_text_field($_POST['password']);
    $remember_me = rest_sanitize_boolean($_POST['rememberMe']);

    // Validate user input
    lr_validate_input($email, $password);

    // Get username from email (WordPress uses username for authentication)
    $user_login = sanitize_user(get_user_by_email($email)->user_login);

    // Prepare credentials array for wp_signon
    $credentials = [
        'user_login' => $user_login,
        'user_password' => $password,
        'remember' => $remember_me
    ];

    // Attempt user authentication
    $user = wp_signon($credentials, false);
    // Debug line (commented out)
    // DebugHelper::dump($user);

    // Handle authentication result
    if (!is_wp_error($user)) {
        // Optional: Clear existing auth cookies
        // wp_clear_auth_cookie();

        // Set the current user
        wp_set_current_user($user->ID, $user->user_login);

        // Optional: Manually set auth cookie
        // wp_set_auth_cookie($user->ID, $remember_me);

        // Send success response
        wp_send_json([
            'success' => true,
            'message' => 'ورود با موفقیت انجام شد. در حال انتقال...' // Success message in Persian
        ], 200);
    } else {
        // Send error response for invalid credentials
        wp_send_json([
            'error' => true,
            'message' => 'نام کاربری یا کلمه عبور اشتباه است.', // Invalid credentials message in Persian
        ], 401);
    }
}

/**
 * Validates user input for login
 * Checks for empty fields and valid email format
 *
 * @param string $email    User's email address
 * @param string $password User's password
 * @return void Sends JSON response if validation fails
 */
function lr_validate_input(string $email, string $password): void
{
    // Check for empty fields
    if (empty($email) or empty($password)) {
        wp_send_json([
            'error' => true,
            'message' => 'لطفا ایمیل و کلمه عبور خود را وارد نمایید.', // Empty fields message in Persian
        ], 400);
    }
    // Validate email format
    elseif (!is_email($email)) {
        wp_send_json([
            'error' => true,
            'message' => 'لطفا ایمیل معتبر وارد نمایید.', // Invalid email message in Persian
        ], 400);
    }
}

/**
 * Sets custom expiration time for authentication cookie
 *
 * @param int $expiration Default expiration time
 * @return int Modified expiration time in seconds
 */
add_filter('auth_cookie_expiration', 'lr_expiration_auth_cookie', 10, 3);
function lr_expiration_auth_cookie(int $expiration): float|int
{
    // Set cookie expiration to 10 days
    return DAY_IN_SECONDS * 10;
}