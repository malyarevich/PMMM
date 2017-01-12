<?php
/**
 * Invoice Table
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$totals = $invoice->totals;

$order = $invoice->get_order();

$total_width = $total_width - 5;
$columns = ( get_option( 'woocommerce_gzdp_invoice_show_tax_rate' ) === 'yes' ) ? 4 : 3;
$first_width = $total_width * 0.55;
$total_width_left = $total_width - $first_width;
$column_width = $total_width_left / $columns;

?>

<?php if ( $invoice->get_static_pdf_text( 'before_table' ) ) : ?>
	<div class="static">
		<?php echo $invoice->get_static_pdf_text( 'before_table' ); ?>
	</div>
<?php endif; ?>

<table class="main">
	<thead>
		<tr class="header">
			
			<th class="first" width="<?php echo $first_width; ?>"><?php _e( 'Services', 'woocommerce-germanized-pro' ); ?></th>
			<th width="<?php echo $column_width; ?>"><?php _e( 'Quantity', 'woocommerce-germanized-pro' ); ?></th>
			
			<?php if( get_option( 'woocommerce_gzdp_invoice_show_tax_rate' ) === 'yes' ) : ?>
				<th width="<?php echo $column_width; ?>"><?php _e( 'Tax Rate', 'woocommerce-germanized-pro' ); ?></th>
			<?php endif; ?>
			
			<th width="<?php echo $column_width; ?>"><?php _e( 'Unit Price', 'woocommerce-germanized-pro' ); ?></th>
			<th class="last" width="<?php echo $column_width; ?>"><?php _e( 'Price', 'woocommerce-germanized-pro' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if ( $invoice->items ) : ?>
			
			<?php foreach ( $invoice->items as $item_id => $item ) : 
			
				$_product  = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
			
				$item_meta = wc_gzdp_get_order_meta( $_product, $item );
				$item_meta_print = '';
				
				if ( $item_meta->meta )
					$item_meta_print = $item_meta->display( true, true, '_', ", " );

			?>
				<tr class="data" nobr="true">
					
					<td class="first" width="<?php echo $first_width; ?>">
						<?php
							
							// Product name
							echo apply_filters( 'woocommerce_gzdp_invoice_item_name', $item['name'], $item, false );

							// SKU
							if ( $invoice->get_option( 'show_sku' ) === 'yes' && is_object( $_product ) && $_product->get_sku() ) {
								echo ' (#' . $_product->get_sku() . ')';
							}

							// allow other plugins to add additional product information here
							do_action( 'woocommerce_gzdp_invoice_item_meta_start', $item_id, $item, $order );

							if ( $invoice->get_option( 'show_variation_attributes' ) == 'yes' && ! empty( $item_meta_print ) ) {
								echo '<br/><small>' . $item_meta_print . '</small>';
							}

						?>

						<?php if ( $invoice->get_option( 'show_delivery_time' ) == 'yes' ) : $product_delivery_time = wc_gzd_cart_product_delivery_time( '', $item ); ?>
							
							<?php if ( ! empty( $product_delivery_time ) ) : ?>
								<p><small><?php echo trim( strip_tags( $product_delivery_time ) ); ?></small></p>
							<?php endif; ?>

						<?php endif; ?>

						<?php if ( $invoice->get_option( 'show_product_units' ) == 'yes' ) : $product_units = wc_gzd_cart_product_units( '', $item ); ?>
							
							<?php if ( ! empty( $product_units ) ) : ?>
								<p><small><?php echo strip_tags( $product_units ); ?></small></p>
							<?php endif; ?>

						<?php endif; ?>

						<?php if ( $invoice->get_option( 'show_item_desc' ) == 'yes' ) : $product_desc = wc_gzd_cart_product_item_desc( '', $item ); ?>
							
							<?php if ( ! empty( $product_desc ) ) : ?>
								<?php echo wpautop( $product_desc ); ?>
							<?php endif; ?>
						
						<?php endif; ?>	

						<?php do_action( 'woocommerce_gzdp_invoice_after_column_name', $item, $invoice ); ?>

					</td>
					
					<td width="<?php echo $column_width; ?>">
						<?php echo $item[ 'qty' ]; ?>
						<?php do_action( 'woocommerce_gzdp_invoice_after_column_quantity', $item, $invoice ); ?>
					</td>
					
					<?php if( get_option( 'woocommerce_gzdp_invoice_show_tax_rate' ) === 'yes' ) : ?>

						<td width="<?php echo $column_width; ?>">
							<?php echo wc_gzdp_get_order_item_tax_rate( $item, $order ); ?>
							<?php do_action( 'woocommerce_gzdp_invoice_after_column_tax_rate', $item, $invoice ); ?>
						</td>
					
					<?php endif; ?>
					
					<td width="<?php echo $column_width; ?>">
						
						<?php echo wc_price( $order->get_item_subtotal( $item, true, true ), array( 'currency' => $invoice->currency ) ); ?>
						
						<?php if ( $invoice->get_option( 'show_unit_price' ) == 'yes' ) : $unit_price = wc_gzd_cart_product_unit_price( '', $item ); ?>

							<?php if ( ! empty( $unit_price ) ) : ?>
								<p><small><?php echo $unit_price; ?></small></p>
							<?php endif; ?>

						<?php endif; ?>

						<?php do_action( 'woocommerce_gzdp_invoice_after_column_item_subtotal', $item, $invoice ); ?>
					</td>
					
					<td class="last" width="<?php echo $column_width; ?>">
						<?php echo $order->get_formatted_line_subtotal( $item ); ?> 

						<?php do_action( 'woocommerce_gzdp_invoice_after_column_item_total', $item, $invoice ); ?>
					</td>

				</tr>

			<?php endforeach; ?>

		<?php endif; ?>

	</tbody>
</table>

<?php do_action( 'woocommerce_gzdp_invoice_after_item_table', $invoice ); ?>

<table class="main">

	<tr nobr="true">
		<td>
			<table class="main">
				<tr class="footer footer-spacing">
					<th colspan="3"></th>
					<td></td>
				</tr>

				<?php if ( $order_totals = $invoice->get_totals() ) : $i = 0; ?>
					<?php foreach ( $order_totals as $total ) : $i++; ?>
						<tr class="footer <?php echo ( isset( $total[ 'classes' ] ) ? implode( ' ', $total[ 'classes' ] ) : "" ); ?>">
							<th class="td" scope="row" colspan="3"><?php echo $total['label']; ?></th>
							<td class="td"><?php echo $total['value']; ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>

			</table>
		</td>
	</tr>
</table>

<?php if ( $invoice->get_static_pdf_text( 'after_table' ) ) : ?>
	<div class="static">
		<?php echo $invoice->get_static_pdf_text( 'after_table' ); ?>
	</div>
<?php endif; ?>