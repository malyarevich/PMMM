<?php 
/*
 Plugin Name: WooCommerce Checkout Field Manager
Plugin URI: http://www.najeebmedia.com
Description: This plugn allow you to edit/add field on checkout page
Version: 4.9
Domain: nm-cofm
Author: Najeeb Ahmad
Author URI: http://www.najeebmedia.com/

	
*/

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    // 'http://wordpresspoets.com/wp-update-server/?action=get_metadata&slug=nm-woocommerce-checkout-field-manager',
    'http://www.wordpresspoets.com/release-json/cfom.json',
    __FILE__,
    'nm-woocommerce-checkout-field-manager'
);

/*
 * Lets start from here
*/

/*
 * loading plugin config file
 */
$_config = dirname(__FILE__).'/config.php';
if( file_exists($_config))
	include_once($_config);
else
	die('Reen, Reen, BUMP! not found '.$_config);


/* ======= the plugin main class =========== */
$_plugin = dirname(__FILE__).'/classes/plugin.class.php';
if( file_exists($_plugin))
	include_once($_plugin);
else
	die('Reen, Reen, BUMP! not found '.$_plugin);

/*
 * [1]
 * TODO: just replace class name with your plugin
 */
$cofm = NM_Checkout_Field_Manager::get_instance();
NM_Checkout_Field_Manager::init();


if( is_admin() ){

	$_admin = dirname(__FILE__).'/classes/admin.class.php';
	if( file_exists($_admin))
		include_once($_admin );
	else
		die('file not found! '.$_admin);

	$cofm_admin = new NM_Checkout_Field_Manager_Admin();
}

/*
 * activation/install the plugin data
*/
register_activation_hook( __FILE__, array('NM_Checkout_Field_Manager', 'activate_plugin'));
register_deactivation_hook( __FILE__, array('NM_Checkout_Field_Manager', 'deactivate_plugin'));


