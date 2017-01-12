<?php
/**
 * Invoice Table
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.5
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
			<?php foreach ( $invoice->items as $item ) : 
			
				$_product  = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
				$item_meta = wc_gzdp_get_order_meta( $_product, $item );
				$item_meta_print = '';
				if ( $item_meta->meta )
					$item_meta_print = $item_meta->display( true, true, '_', ", " );

			?>
				<tr class="data" nobr="true">
					<td class="first" width="<?php echo $first_width; ?>">
						<?php echo $item[ 'name' ]; ?> 
						<?php if ( $invoice->get_option( 'show_sku' ) === 'yes' && is_object( $_product ) && $_product->get_sku() ) : ?>
							<small>[<?php echo _x( 'SKU', 'invoices', 'woocommerce-germanized-pro' );?>: <?php echo $_product->get_sku(); ?>]</small>
						<?php endif; ?>
						<?php if ( $invoice->get_option( 'show_variation_attributes' ) == 'yes' && ! empty( $item_meta_print ) ) : ?>
							<p><small><?php echo $item_meta->display( true, true, '_', ", " ); ?></small></p>
						<?php endif; ?>
						<?php if ( $invoice->get_option( 'show_product_units' ) == 'yes' && isset( $item[ 'units' ] ) ) : ?>
							<p><small><?php echo wc_gzd_cart_product_units( '', $item ); ?></small></p>
						<?php endif; ?>
						<?php if ( $invoice->get_option( 'show_item_desc' ) == 'yes' && isset( $item[ 'item_desc' ] ) ) : ?>
							<?php echo wpautop( $item[ 'item_desc' ] ); ?>
						<?php endif; ?>
					</td>
					<td width="<?php echo $column_width; ?>"><?php echo $item[ 'qty' ]; ?></td>
					<?php if( get_option( 'woocommerce_gzdp_invoice_show_tax_rate' ) === 'yes' ) : ?>
						<td width="<?php echo $column_width; ?>">
							<?php echo wc_gzdp_get_order_item_tax_rate( $item, $order ); ?>
						</td>
					<?php endif; ?>
					<td width="<?php echo $column_width; ?>">
						<?php
							if ( isset( $item['line_total'] ) ) {
								if ( isset( $item['line_subtotal'] ) && $item['line_subtotal'] != $item['line_total'] ) {
									echo '<del>' . wc_price( $order->get_item_subtotal( $item, false, true ), array( 'currency' => $invoice->currency ) ) . '</del> ';
								}
								echo wc_price( $order->get_item_total( $item, false, true ), array( 'currency' => $invoice->currency ) );
							}
						?>
						<?php if ( isset( $item[ 'unit_price_excl' ] ) ) : ?>
							<p><small><?php echo wc_gzdp_get_invoice_unit_price_excl( $item ); ?></small></p>
						<?php endif; ?>
					</td>
					<td class="last" width="<?php echo $column_width; ?>">
						<?php echo wc_price( $item[ 'line_total' ], array( 'currency' => $invoice->currency ) ); ?>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
<table class="main">
	<tr nobr="true">
		<td>
			<table class="main">
				<tr class="footer footer-spacing">
					<th colspan="3"></th>
					<td></td>
				</tr>
				<tr class="footer footer-first">
					<th colspan="3"><?php _e( 'Subtotal', 'woocommerce-germanized-pro' ); ?></th>
					<td><?php echo wc_price( $totals[ 'subtotal' ], array( 'currency' => $invoice->currency ) ); ?></td>
				</tr>
				<?php if ( ! empty( $totals[ 'discount' ] ) ) : ?>
					<tr class="footer">
						<th colspan="3"><?php _e( 'Discount', 'woocommerce-germanized-pro' ); ?></th>
						<td><?php echo wc_price( $totals[ 'discount' ], array( 'currency' => $invoice->currency ) ); ?></td>
					</tr>
				<?php endif; ?>
				<tr class="footer">
					<th colspan="3"><?php _e( 'Shipping', 'woocommerce-germanized-pro' ); ?></th>
					<td><?php echo wc_price( $totals[ 'shipping' ] ); ?></td>
				</tr>
				<?php if ( $invoice->fee_totals ) : ?>
					<?php foreach ( $invoice->fee_totals as $fee ) : ?>
						<tr class="footer">
							<th colspan="3"><?php echo $fee[ 'name' ]; ?></th>
							<td><?php echo wc_price( $fee[ 'line_total' ], array( 'currency' => $invoice->currency ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ( ! empty( $totals[ 'refunded' ] ) ) : ?>
					<tr class="footer">
						<th colspan="3"><?php _e( 'Refund', 'woocommerce-germanized-pro' ); ?></th>
						<td><?php echo wc_price( $totals[ 'refunded' ], array( 'currency' => $invoice->currency ) ); ?></td>
					</tr>
				<?php endif; ?>
				<?php if ( $invoice->tax_totals ) : ?>
					<?php foreach ( $invoice->tax_totals as $code => $tax ) : ?>
						<tr class="footer">
							<th colspan="3"><?php echo wc_gzdp_get_tax_label( $tax->rate_id ) ?></th>
							<td><?php echo wc_price( $tax->amount, array( 'currency' => $invoice->currency ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				<tr class="footer footer-total">
					<th colspan="3"><?php _e( 'Total', 'woocommerce-germanized-pro' ); ?></th>
					<td><?php echo wc_price( $totals[ 'total' ], array( 'currency' => $invoice->currency ) ); ?></td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?php if ( $invoice->get_static_pdf_text( 'after_table' ) ) : ?>
	<div class="static">
		<?php echo $invoice->get_static_pdf_text( 'after_table' ); ?>
	</div>
<?php endif; ?>