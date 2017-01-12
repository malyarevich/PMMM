<?php
/**
 * Order Data
 *
 * Functions for displaying the order data meta box.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin/Meta Boxes
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WC_Meta_Box_Order_Data Class
 */
class WC_GZDP_Meta_Box_Invoice_Packing_Slip {

	/**
	 * Output the metabox
	 */
	public static function output( $post, $args ) {
		global $theorder;
		if ( ! is_object( $args[ 'args' ][ 'invoice' ] ) )
			return;
		$invoice = $args[ 'args' ][ 'invoice' ];
		$order = $theorder;
		wp_nonce_field( 'woocommerce_save_data', 'wc_gzdp_invoice_data' );
		?>
		<div class="panel-wrap woocommerce">
			<div id="invoice_data_<?php echo $invoice->id; ?>" class="panel invoice_data">
				<h2><?php echo $invoice->get_title(); ?></h2>
				<div class="order_data_column_container invoice_data_column_container">
					<div class="order_data_column invoice_data_column invoice-submit-area">
						<h4><?php _e( 'Actions', 'woocommerce-germanized-pro' ); ?></h4>
						<input type="hidden" name="invoice[]" value="<?php echo $invoice->id;?>" />
						<input type="hidden" name="invoice_status_<?php echo $invoice->id;?>" value="wc-gzdp-pending" />
						<?php if ( ! $invoice->is_locked() ) : ?>
							<p class="form-field form-field-wide"><label for="invoice_generate"><?php _e( 'This will (re)generate the packing slip.', 'woocommerce-germanized-pro' ) ?></label>
								<input type="hidden" name="invoice_generate_<?php echo $invoice->id;?>" value="" class="invoice_generate invoice_generate_<?php echo $invoice->id;?>" />
								<button type="submit" class="button <?php echo ( $invoice->is_new() ? 'button-primary' : 'button-secondary' ); ?> button-invoice" data-invoice="<?php echo $invoice->id;?>" data-action="generate"><?php echo $invoice->get_submit_button_text();?></button>
							</p>
						<?php endif; ?>
					</div>
				</div>
				<div class="clear"></div>
				<p class="button-wrap">
					<?php if ( ! $invoice->is_new() ) : ?>
						<a class="invoice-delete delete" href="#"><?php _e( 'Delete', 'woocommerce-germanized-pro' ); ?></a> 
					<?php endif; ?>
					<?php if ( $invoice->has_attachment() ) : ?>
						<a class="button button-primary" href="<?php echo $invoice->get_pdf_url();?>" target="_blank"><?php printf( __( 'Download %s', 'woocommerce-germanized-pro' ), $invoice->get_title() ) ;?></a>
					<?php endif; ?>
				</p>
				<div class="invoice-delete-wrapper notice notice-error inline">
					<p>
						<input type="hidden" name="invoice_delete_<?php echo $invoice->id;?>" value="" class="invoice_delete invoice_delete_<?php echo $invoice->id;?>" />
						<button type="submit" class="button button-secondary button-invoice" data-invoice="<?php echo $invoice->id;?>" data-action="delete"><?php _e( 'Delete permanently', 'woocommerce-germanized-pro' );?></button>
						<a class="button button secondary hide-invoice-delete" href="#"><?php _e( 'Stop deletion', 'woocommerce-germanized-pro' ); ?></a>
					</p>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		global $wpdb;
	}
}
