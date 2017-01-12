<?php
/***
 Plugin Name: WooCommerce Multiple Free Gift Mod
 Plugin URI:
 Description: WooCommerce plugin special for bacchus.de.
 Version: 0.1
 Author: Evgen
 Author URI: http://bacchus.de/
 Text Domain: woocommerce-multiple-free-gift-mod
 Domain Path: /languages

 Copyright (c) 2015 Yevgen <yevgen.slyuzkin@gmail.com>.
*/

//Avoid direct calls to this file
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die( 'Access Forbidden' );
}

define( 'PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

include 'lib/helpers/WFGM_Common_Helper.class.php';
include 'lib/helpers/WFGM_Settings_Helper.class.php';
include 'lib/helpers/WFGM_Product_Helper.class.php';
include 'lib/helpers/WFGM_Criteria_Helper.class.php';
include 'lib/admin/WFGM_Admin.class.php';
include 'lib/admin/WFGM_Single_Gift.class.php';
include 'lib/WFGM_Frontend.class.php';
include 'lib/Woocommerce_Multiple_Free_Gift_Mod.class.php';

//plugin activation hook
register_activation_hook( __FILE__ , array( 'Woocommerce_Multiple_Free_Gift_Mod', 'wfgm_activate' ) );

/** Initialize the awesome */
new Woocommerce_Multiple_Free_Gift_Mod();
