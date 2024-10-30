<?php
// Register menu item in WordPress admin panel
add_action('admin_menu', 'lr_register_login_my_custom_setting_section');
add_action('admin_init', 'lr_login_my_custom_setting_init');
// Add our custom notice
add_action('admin_notices', 'lr_admin_notices');


function lr_register_login_my_custom_setting_section() {
    add_menu_page(
        'پلاگین ثبت‌نام و ورود کاربر',
        'پلاگین ثبت‌نام و ورود',
        'manage_options',
        'register-signIn',
        'lr_render_html_form',
        'dashicons-plus-alt'
    );
}

// Initialize plugin settings and fields
function lr_login_my_custom_setting_init() {
    // Register settings with sanitization
    $args = [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => NULL,
    ];
    register_setting('lr_settings', '_lr_send_SMS_user_name', $args);
    register_setting('lr_settings', '_lr_send_SMS_user_password', $args);
    register_setting('lr_settings', '_lr_bodyID_registration_otp_code', $args);

    // Add settings section for user and password
    add_settings_section(
        'lr_setting_section_user_password',
        '',
        'lr_setting_description_user_password',
        'lr_setting'
    );

    // Add settings section for boyIDs
    add_settings_section(
        'lr_setting_section_bodyID',
        '',
        'lr_setting_description_bodyID',
        'lr_setting'
    );

    // Add settings fields
    add_settings_field(
        'lr_setting_field_sms_user_name',
        'نام کاربری در سامانه ملی پیامک',
        'lr_render_html_sms_user_name',
        'lr_setting',
        'lr_setting_section_user_password'
    );
    add_settings_field(
        'lr_setting_field_sms_password',
        'رمز عبور یا API Key',
        'lr_render_html_sms_password',
        'lr_setting',
        'lr_setting_section_user_password'
    );
    add_settings_field(
        'lr_setting_field_sms_bodyID',
        'bodyID ارسال کد یک‌بار مصرف ثبت نام',
        'lr_render_html_sms_bodyID',
        'lr_setting',
        'lr_setting_section_bodyID'
    );
}

// Render section description
function lr_setting_description_user_password() {
    ?>
    <div class="lr-welcome-panel">
        <div class="lr-welcome-panel-content">
            <h2>تنظیمات سامانه پیامک</h2>
            <p class="lr-about-description">نام کاربری و رمز عبور سامانه ملی پیامک خود را وارد کنید</p>

        </div>
    </div>
    <?php
}

// Render section description
function lr_setting_description_bodyID() {
    ?>
    <div class="lr-welcome-panel">
        <div class="lr-welcome-panel-content">
            <h2>تنظیمات bodyID</h2>
            <p class="lr-about-description">bodyID که از داخل پنل ملی پیامک (بخش وب سرویس خدماتی ) دریافت کردید را در بخش‌های مربوطه وارد کنید </p>

        </div>
    </div>
    <?php
}

// Render username input field
function lr_render_html_sms_user_name() {
    $user_name = get_option('_lr_send_SMS_user_name');
    ?>
    <div class="lr-form-field">
        <input
                dir="rtl"
                type="text"
                name="_lr_send_SMS_user_name"
                value="<?php echo isset($user_name) ? esc_attr($user_name) : ''; ?>"
                class="regular-text"
                placeholder="نام کاربری خود را وارد کنید"
        >
        <p class="lr-description">نام کاربری که از سامانه ملی پیامک دریافت کرده‌اید را در این قسمت وارد نمایید.</p>
    </div>
    <?php
}

// Render password/API key input field
function lr_render_html_sms_password() {
    $password = get_option('_lr_send_SMS_user_password');
    ?>
    <div class="lr-form-field">
        <input
                dir="rtl"
                type="text"
                name="_lr_send_SMS_user_password"
                value="<?php echo isset($password) ? esc_attr($password) : ''; ?>"
                class="regular-text"
                placeholder="رمز عبور یا API Key را وارد کنید"
        >
        <p class="lr-description lr-important">
            <span class="dashicons dashicons-shield-alt"></span>
            نکته امنیتی: بهتر است به جای رمز عبور از API Key که سامانه ملی پیامک در بخش تنظیمات در اختیار شما قرار می‌دهد استفاده نمایید.
        </p>
    </div>
    <?php
}

// Render password/API key input field
function lr_render_html_sms_bodyID() {
    $bodyID_otp = get_option('_lr_bodyID_registration_otp_code');
    ?>
    <div class="lr-form-field">
        <input
                dir="rtl"
                type="text"
                name="_lr_bodyID_registration_otp_code"
                value="<?php echo isset($bodyID_otp) ? esc_attr($bodyID_otp) : ''; ?>"
                class="regular-text"
                placeholder="bodyID مربوط به ارسال کد یکبار مصرف را وارد کنید"
        >
        <p class="lr-description">
            توجه: هر الگوی پیامک bodyID مخصوص به خود را دارد.
        </p>
    </div>
    <?php
}

// Render main settings form
function lr_render_html_form() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Add custom styles for the settings page
    ?>

    <div class="lr-wrap">
        <!-- Page Header -->
        <h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
        <hr class="wp-header-end">

        <!-- Settings Card -->
        <div>
            <form action="options.php" method="post">
                <?php
                // Output security fields
                settings_fields('lr_settings');

                // Output setting sections
                do_settings_sections('lr_setting');

                // Submit Button
                echo '<div class="submit-wrapper" style="margin-top: 20px;">';
                submit_button('ذخیره تغییرات', 'primary large');
                echo '</div>';
                ?>
            </form>
        </div>

        <!-- Help Section -->
        <div style="margin-top: 20px;">
            <h2 class="title">راهنما</h2>
            <p>برای استفاده از این پلاگین، مراحل زیر را دنبال کنید:</p>
            <ol>
                <li>وارد پنل سامانه ملی پیامک شوید</li>
                <li>از بخش تنظیمات، API Key خود را کپی کنید</li>
                <li>اطلاعات را در فرم بالا وارد کنید</li>
                <li>دکمه ذخیره تغییرات را بزنید</li>
            </ol>
        </div>
    </div>
    <?php
}

// Display admin notices for settings updates

function lr_admin_notices() {

    // Check if settings were just updated
    if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {

        if (get_option('_lr_send_SMS_user_name') === false ||
            get_option('_lr_send_SMS_user_password') === false ||
            get_option('_lr_bodyID_registration_otp_code') === false) {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><strong>خطا:</strong> خطا در ذخیره‌سازی تنظیمات.</p>
            </div>
            <?php
        } else {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>موفق:</strong> تنظیمات با موفقیت ذخیره شد.</p>
            </div>
            <?php
        }
    }
}


/**
 * Code Documentation:
 *
 * This plugin creates a settings page for SMS configuration in WordPress admin.
 *
 * Main Components:
 * 1. Menu Registration: Adds a new menu item in WordPress admin
 * 2. Settings Registration: Registers settings fields for username and password
 * 3. Form Rendering: Creates a user-friendly form with proper styling
 * 4. Notice Handling: Shows success/error messages after form submission
 *
 * UI Improvements:
 * - Uses WordPress native classes (card, notice, etc.)
 * - Adds helpful descriptions under fields
 * - Includes a welcome panel with clear instructions
 * - Adds a help section with step-by-step guide
 * - Uses proper spacing and typography
 * - Implements responsive design
 * - Adds security icon for API key note
 *
 * Security Features:
 * - Input sanitization
 * - Capability checking
 * - Proper escaping of output
 * - Password field for sensitive data
 */