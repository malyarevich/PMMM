<?php
/**
 * Packing SLip Table
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$totals = $invoice->totals;

$order = $invoice->get_order();

$total_width = $total_width - 5;
$columns = 2;
$first_width = $total_width * 0.8;
$total_width_left = $total_width - $first_width;
$column_width = $total_width_left;

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
			<th class="last" width="<?php echo $column_width; ?>"><?php _e( 'Quantity', 'woocommerce-germanized-pro' ); ?></th>
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
						<?php if ( $invoice->get_option( 'show_sku' ) && is_object( $_product ) && $_product->get_sku() ) : ?>
							<small>[<?php echo _x( 'SKU', 'invoices', 'woocommerce-germanized-pro' );?>: <?php echo $_product->get_sku(); ?>]</small>
						<?php endif; ?>
						<?php if ( $invoice->get_option( 'show_variation_attributes' ) == 'yes' && ! empty( $item_meta_print ) ) : ?>
							<p><small><?php echo $item_meta->display( true, true, '_', ", " ); ?></small></p>
						<?php endif; ?>
						
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
					</td>
					<td class="last" width="<?php echo $column_width; ?>"><?php echo $item[ 'qty' ]; ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<?php if ( $invoice->get_static_pdf_text( 'after_table' ) ) : ?>
	<div class="static">
		<?php echo $invoice->get_static_pdf_text( 'after_table' ); ?>
	</div>
<?php endif; ?>