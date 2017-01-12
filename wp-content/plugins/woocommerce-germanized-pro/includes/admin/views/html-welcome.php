<?php
/**
 * Admin View: Generator Editor
 */

if ( ! defined( 'ABSPATH' ) )
	exit;
?>

<div id="message" class="updated woocommerce-gzd-message wc-connect">
	<h3><?php _e( 'Thank you for Upgrading to WooCommerce Germanized Pro', 'woocommerce-germanized-pro' );?></h3>
	<p><?php printf( __( 'Congratulations. Your WooCommerce Germanized Pro installation was successful. To generate invoices please grant <a href="%s" target="_blank">writing permissions</a> to the following folder and it\'s subfolders:', 'woocommerce-germanized-pro' ), 'https://vendidero.de/dokument/dateirechte-fuer-das-rechnungsarchiv-vergeben' );?></p>
	<p>wp-content/uploads/wc-gzdp</p>
	<p><a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=germanized' );?>"><?php _e( 'Start configuration', 'woocommerce-germanized-pro' );?></a></p>
</div>