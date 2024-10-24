<?php
/*Plugin Name: پلاگین ورود و ثبت نام
Plugin URI: http://siteyar.net/plugins/
Description: پلاگین ورود و ثبت نام
Author: Sadeq Yaqobi
Version: 1.0.0
License: GPLv2 or later
Author URI: http://siteyar.net/sadeq-yaqobi/ */

#for security
defined('ABSPATH') || exit();

//defined required const
define('LR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LR_PLUGIN_URL', plugin_dir_url(__FILE__));
const LR_PLUGIN_INC = LR_PLUGIN_DIR . '_inc/';
const LR_PLUGIN_VIEW = LR_PLUGIN_DIR . 'view/';
const LR_PLUGIN_ASSETS_DIR = LR_PLUGIN_DIR . 'assets/';
const LR_PLUGIN_ASSETS_URL = LR_PLUGIN_URL . 'assets/';

/**
 * Register and enqueue frontend assets
 */
function lr_register_assets_front() {
    // Register and enqueue CSS
    wp_register_style('slick-style',LR_PLUGIN_ASSETS_URL . 'css/front/slick.min.css',[],'1.0.0');
    wp_enqueue_style('slick-style');
    wp_register_style('slick-theme-style',LR_PLUGIN_ASSETS_URL . 'css/front/slick-theme.min.css',[],'1.0.0');
    wp_enqueue_style('slick-theme-style');

    wp_register_style('lr-style',LR_PLUGIN_ASSETS_URL . 'css/front/style.css',[],'1.0.0');
    wp_enqueue_style('lr-style');

    // Register and enqueue JavaScript
    wp_register_script('toast-js', LR_PLUGIN_ASSETS_URL . 'js/front/jquery.toast.min.js', ['jquery'], '1.0.0', ['strategy' => 'async', 'in_footer' => true]);
    wp_enqueue_script('toast-js');
    wp_register_script('slick-js', LR_PLUGIN_ASSETS_URL . 'js/front/slick.min.js', ['jquery'], '1.0.0', ['strategy' => 'async', 'in_footer' => true]);
    wp_enqueue_script('slick-js');

    wp_register_script('lr-main-js',LR_PLUGIN_ASSETS_URL . 'js/front/main.js', ['jquery'], '1.0.0', ['strategy' => 'async', 'in_footer' => true]);
    wp_enqueue_script('lr-main-js');
    wp_register_script('lr-front-ajax',LR_PLUGIN_ASSETS_URL . 'js/front/front-ajax.js', ['jquery'], '1.0.0', ['strategy' => 'async', 'in_footer' => true]);
    wp_enqueue_script('lr-front-ajax');

    // localize script
    wp_localize_script('lr-front-ajax', 'lr_ajax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        '_nonce' => wp_create_nonce()
    ]);
}

function lr_register_assets_admin() {
    // Register and enqueue CSS
    wp_register_style('lr-admin-style',LR_PLUGIN_ASSETS_URL . 'css/admin/admin-style.css',[],'1.0.0');
    wp_enqueue_style('lr-admin-style');

    // Register and enqueue JavaScript
    wp_register_script('lr-admin-js',LR_PLUGIN_ASSETS_URL . 'js/admin/admin-js.js', ['jquery'], '1.0.0', ['strategy' => 'async', 'in_footer' => true]);
    wp_enqueue_script('lr-admin-js');
    wp_register_script('lr-admin-ajax',LR_PLUGIN_ASSETS_URL . 'js/admin/admin-ajax.js', ['jquery'], '1.0.0', ['strategy' => 'async', 'in_footer' => true]);
    wp_enqueue_script('lr-admin-ajax');
}
add_action('wp_enqueue_scripts', 'lr_register_assets_front');
add_action('admin_enqueue_scripts', 'lr_register_assets_admin');

//including files
if (is_admin()) {
    include LR_PLUGIN_INC . 'admin/menus.php';
}
include_once LR_PLUGIN_VIEW . 'front/login.php';
include_once LR_PLUGIN_INC . 'front/login.php';

//activation and deactivation plugin hooks
/*function func1()
{
//    any work that needs to do when the plugin is activated like creating tables on database
}

function func2()
{
    //
}
register_activation_hook(__FILE__,func1());
register_deactivation_hook(__FILE__,func2());*/

