<?php
/**
 * Admin View: Generator Editor
 */

if ( ! defined( 'ABSPATH' ) )
	exit;
?>
<style>
	.updated {
		display: none !important;
	}
</style>
<div class="error inline">
	<h3><?php echo _x( 'Please register WC Germanized Pro', 'generator', 'woocommerce-germanized-pro' ); ?></h3>
	<p><?php echo _x( 'To enable the generator please activate the Vendidero Helper plugin and register WC Germanized Pro.', 'generator', 'woocommerce-germanized-pro' ); ?></p>
	<p><?php echo vendidero_helper_notice( true ); ?></p>
</div>