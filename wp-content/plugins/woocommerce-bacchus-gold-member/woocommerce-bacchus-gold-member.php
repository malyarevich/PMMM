<?php
/***
 Plugin Name: Woocommerce Bacchus Gold Loyalty Program Plugin
 Plugin URI:
 Description: WooCommerce plugin special for bacchus.de.
 Version: 0.0.0
 Author: Yevgen
 Author URI: http://bacchus.de/
 Text Domain: woocommerce-bacchus-gold-member
 Domain Path: /languages

 Copyright (c) 2016, Yevgen <yevgen.slyuzkin@gmail.com>.
*/

//Avoid direct calls to this file
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die( 'Access Forbidden' );
}

define( 'PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

include 'lib/helpers/WBGM_Common_Helper.class.php';
include 'lib/helpers/WBGM_Settings_Helper.class.php';
include 'lib/helpers/WBGM_Product_Helper.class.php';
include 'lib/helpers/WBGM_Criteria_Helper.class.php';
include 'lib/admin/WBGM_Admin.class.php';
include 'lib/admin/WBGM_Single_Gift.class.php';
include 'lib/WBGM_Frontend.class.php';
include 'lib/Woocommerce_Bacchus_Gold_Member.class.php';

//plugin activation hook
register_activation_hook( __FILE__ , array( 'Woocommerce_Bacchus_Gold_Member', 'wbgm_activate' ) );

/** Initialize the awesome */
new Woocommerce_Bacchus_Gold_Member();
