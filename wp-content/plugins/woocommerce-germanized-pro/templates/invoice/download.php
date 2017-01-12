<?php
/**
 * Single Product Price per Unit
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanized/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $invoices;
?>
<div class="woocommerce-gzdp-invoice">
	<h3><?php echo _x( 'Download Invoices', 'invoices', 'woocommerce-germanized-pro' );?></h3>
	<?php foreach ( $invoices as $invoice ) : ?>
		<a class="button button-invoice-download" href="<?php echo wc_gzdp_get_invoice_download_url( $invoice->id );?>" target="_blank"><?php printf( _x( 'Download %s', 'invoices', 'woocommerce-germanized-pro' ), $invoice->get_title() ); ?></a>
	<?php endforeach; ?>
</div>