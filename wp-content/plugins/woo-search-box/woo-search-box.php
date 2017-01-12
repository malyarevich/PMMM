<?php
/*
Plugin Name:       Woo Search Box
Plugin URI:        http://guaven.com/woo-search-box
Description:       Ultimate WordPress plugin which turns a simple search box of your Woo Store to the powerful multifunctional magic box which can help you to sell more products.
Version:           1.5.1
Author:            Guaven Labs
Author URI:        http://guaven.com
Text Domain:       guaven_woo_search
Domain Path:       /languages
*/

if (!defined('ABSPATH')) {
    die;
}

define('GUAVEN_WOO_SEARCH_PLUGIN_PATH', plugin_dir_path(__FILE__));
require_once GUAVEN_WOO_SEARCH_PLUGIN_PATH.'admin/class-admin-settings.php';
require_once GUAVEN_WOO_SEARCH_PLUGIN_PATH.'admin/class-search-analytics.php';
require_once GUAVEN_WOO_SEARCH_PLUGIN_PATH.'public/class-front.php';

$guaven_woo_search_front = new Guaven_woo_search_front();
$guaven_woo_search_admin = new Guaven_woo_search_admin();
$guaven_woo_search_analytics = new Guaven_woo_search_analytics();
$guaven_woos_active_plugins = apply_filters('active_plugins', get_option('active_plugins'));

$guaven_woo_search_admin->woo_activeness = in_array('woocommerce/woocommerce.php', $guaven_woos_active_plugins) ? 1 : 0;
if ($guaven_woo_search_admin->woo_activeness == 0) {
    $guaven_woos_active_plugins_ms = get_site_option('active_sitewide_plugins');
    $guaven_woo_search_admin->woo_activeness = (is_array($guaven_woos_active_plugins_ms) and !empty($guaven_woos_active_plugins_ms['woocommerce/woocommerce.php'])) ? 1 : 0;
}

$guaven_woo_search_front->wpml_existence = in_array('wpml-string-translation/plugin.php', $guaven_woos_active_plugins) ? 1 : 0;

$guaven_woo_search_admin->argv1 = isset($argv[1]) ? $argv[1] : '';
if (!empty($guaven_woo_search_admin->argv1)) {
    add_action('init', array($guaven_woo_search_admin, 'cache_rebuild_ajax_callback'));
}

add_action('admin_menu', array(
    $guaven_woo_search_admin,
    'admin_menu',
));

add_action('admin_menu', array(
    $guaven_woo_search_analytics,
    'admin_menu',
));

add_action('admin_enqueue_scripts', array(
    $guaven_woo_search_analytics,
    'enqueue',
), 100);

add_action('edit_post', array(
    $guaven_woo_search_admin,
    'edit_hook_rebuilder',
));

add_action('admin_footer', array(
    $guaven_woo_search_admin,
    'do_rebuilder_at_footer',
));

if (empty($guaven_woo_search_admin->guaven_woos_firstrun) and $guaven_woo_search_admin->woo_activeness == 1) {
    add_action('admin_bar_menu', array($guaven_woo_search_admin, 'woos_rebuild_top_button'), 999);

    add_action('wp_enqueue_scripts', array(
        $guaven_woo_search_front,
        'enqueue',
    ), 100);

    add_action('wp_footer', array(
        $guaven_woo_search_front,
        'inline_js',
    ), 100);

    add_action('script_loader_tag', array(
        $guaven_woo_search_front,
        'add_async_attribute',
    ), 10, 2);

    add_action('wp_ajax_cache_rebuild_ajax', array(
        $guaven_woo_search_admin,
        'cache_rebuild_ajax_callback',
    ));

    add_action('wp', array(
        $guaven_woo_search_front,
        'personal_interest_collector',
    ));

    add_action('posts_where', array(
        $guaven_woo_search_front,
        'backend_search_filter',
    ), 100);

    add_action('wp_ajax_guaven_woos_tracker', array(
        $guaven_woo_search_front,
        'guaven_woos_tracker_callback',
    ));
    add_action('wp_ajax_nopriv_guaven_woos_tracker', array(
        $guaven_woo_search_front,
        'guaven_woos_tracker_callback',
    ));
}
