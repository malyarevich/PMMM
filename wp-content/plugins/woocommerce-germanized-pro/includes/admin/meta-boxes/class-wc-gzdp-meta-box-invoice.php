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
class WC_GZDP_Meta_Box_Invoice {

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
				<?php if ( ! $invoice->is_new() ) : ?>
					<p class="invoice_summary"><?php echo $invoice->get_summary();?></p>
				<?php endif; ?>
				<div class="order_data_column_container invoice_data_column_container">
					<div class="order_data_column invoice_data_column">
						<h4><?php _e( 'General Details', 'woocommerce-germanized-pro' ); ?></h4>
						<?php if ( ! $invoice->is_type( 'cancellation' ) ) : ?>
							<p class="form-field form-field-wide"><label for="invoice_status"><?php _e( 'Invoice status:', 'woocommerce-germanized-pro' ) ?></label>
							<select id="invoice_status_<?php echo $invoice->id;?>" name="invoice_status_<?php echo $invoice->id;?>" class="chosen_select">
								<?php
									$statuses = wc_gzdp_get_invoice_statuses();
									$default = ( $invoice->get_status() == "auto-draft" ? wc_gzdp_get_default_invoice_status() : $invoice->get_status() );
									foreach ( $statuses as $status => $status_name ) {
										echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status, $default, false ) . '>' . esc_html( $status_name ) . '</option>';
									}
								?>
							</select></p>
						<?php endif; ?>
						<p class="form-field form-field-wide"><label for="invoice_date"><?php _e( 'Invoice date:', 'woocommerce-germanized-pro' ) ?></label>
							<input type="text" class="date-picker-field" name="invoice_date_<?php echo $invoice->id;?>" id="invoice_date_<?php echo $invoice->id;?>" maxlength="10" value="<?php echo ( $invoice->is_new() ? '' : $invoice->get_date( 'Y-m-d' ) );?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
						</p>
						<?php if ( $invoice->is_new() ) : ?>
							<p><?php _e( 'Leave blank to use current date.', 'woocommerce-germanized-pro' ); ?></p>
						<?php endif; ?>
						<?php if ( ! $invoice->is_new() ) : ?>
							<p class="form-field form-field-wide"><label for="invoice_delivery_status"><?php _e( 'Invoice delivery status:', 'woocommerce-germanized-pro' ) ?></label>
								<span class="delivery_status <?php echo ( $invoice->is_delivered() ? 'is_delivered' : 'not_delivered' );?>"><?php echo ( $invoice->is_delivered() ? sprintf( __( 'Delivered @ %s', 'woocommerce-germanized-pro' ), $invoice->get_delivery_date() ) : __( 'Not yet delivered', 'woocommerce-germanized-pro' ) );?></span>
							</p>
						<?php endif; ?>
					</div>
					<div class="order_data_column invoice_data_column invoice-submit-area">
						<h4><?php _e( 'Actions', 'woocommerce-germanized-pro' ); ?></h4>
						<input type="hidden" name="invoice[]" value="<?php echo $invoice->id;?>" />
						<?php if ( $invoice->is_cancellation() ) : ?>
							<input type="hidden" name="invoice_parent_<?php echo $invoice->id;?>" value="<?php echo $invoice->parent_id; ?>" />
						<?php endif; ?>
						<?php if ( ! $invoice->is_locked() ) : ?>
							<p class="form-field form-field-wide"><label for="invoice_generate"><?php _e( 'This will (re)generate the PDF invoice.', 'woocommerce-germanized-pro' ) ?></label>
								<input type="hidden" name="invoice_generate_<?php echo $invoice->id;?>" value="" class="invoice_generate invoice_generate_<?php echo $invoice->id;?>" />
								<button type="submit" class="button <?php echo ( $invoice->is_new() ? 'button-primary' : 'button-secondary' ); ?> button-invoice" data-invoice="<?php echo $invoice->id;?>" data-action="generate"><?php echo $invoice->get_submit_button_text();?></button>
							</p>
						<?php endif; ?>
						<?php if ( $invoice->has_attachment() ) : ?>
							<p class="form-field form-field-wide"><label for="invoice_date"><?php _e( 'Sends the invoice to the customer by email. Invoice will be locked after sending.', 'woocommerce-germanized-pro' ) ?></label>
								<input type="hidden" name="invoice_send_<?php echo $invoice->id;?>" value="" class="invoice_send invoice_send_<?php echo $invoice->id;?>" />
								<button type="submit" class="button <?php echo ( ! $invoice->is_delivered() ? 'button-primary' : 'button-secondary' ); ?> button-invoice" data-invoice="<?php echo $invoice->id;?>" data-action="send"><?php echo ( $invoice->is_delivered() ? __( 'Resend to Customer', 'woocommerce-germanized-pro' ) : __( 'Send to Customer', 'woocommerce-germanized-pro' ) );?></button>
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
						<?php _e( 'Deleting an invoice can lead to data inconsistencies and taxation problems.', 'woocommerce-germanized-pro' ); ?>
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
