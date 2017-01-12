<?php

/*
Plugin Name: Woocommerce Product Badge Manager
Plugin URI: http://codefairbd.com
Version: 1.3
Description: Woo Product Badge manager is a simple awesome plugin to increase your product reputation and show the difference between product on your shop. You can add badge on your product using Product editing, category ordering and author ordering system.
Author: Robin Islam
Author URI: http://codefairbd.com
*/

// Include ABS path
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Define Plugin PATH & DIRECTORY
define('WOO_PRODUCT_BADGE_MANAGER_DIR', plugin_dir_path(__FILE__));
define('WOO_PRODUCT_BADGE_MANAGER_URI', plugin_dir_url(__FILE__));

// define plugin base name
define('WOO_PRODUCT_BADGE_MANAGER_NAME', plugin_basename(__FILE__));

// include ml file
require_once(WOO_PRODUCT_BADGE_MANAGER_DIR . '/lang.php');

// show admin notice if required plugin isn't activate
if (is_plugin_inactive('woocommerce/woocommerce.php')){
	add_action('all_admin_notices', 'woo_product_badge_manager_required_notice');
	function woo_product_badge_manager_required_notice(){
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="error">
          <p>'.sprintf(__('<strong>%s</strong> You must install <strong><a href="http://goo.gl/04An3t" target="_blank">WooCommerce - excelling eCommerce</a></strong> plugin to use Woo Product Badge Manager.', 'woo_product_badge_manager_txtd'), $plugin_data['Name']).'</p>
        </div>';
	}
}

// check up and load all required file
if (is_plugin_active('woocommerce/woocommerce.php')){
	require_once(WOO_PRODUCT_BADGE_MANAGER_DIR . '/config.php');
	require_once(WOO_PRODUCT_BADGE_MANAGER_DIR . '/script.php');
}