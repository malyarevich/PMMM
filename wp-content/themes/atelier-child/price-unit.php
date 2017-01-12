<?php
/**
 * Single Product Price per Unit
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanized/Templates
 * @version     1.0
 */
//Original:\plugins\woocommerce-germanized\templates\single-product\price-unit.php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
?>

<?php if ( wc_gzd_get_gzd_product( $product )->has_unit() ) : ?>
	<?php $min_value = apply_filters( 'woocommerce_quantity_input_min', '', $product ); ?>
	<?php if ( 1 < $min_value ): ?>
		<p class="entspricht"><?php echo __( 'entspricht einem Flaschenpreis von: ', 'swiftframework' ), wc_price( $product->price ); ?></p>
		<p class="price price-unit smaller<?php echo ( ! is_product() ) ? ' preis' : ' related-preis'; ?> ">
			<?php
			$unit_html = wc_gzd_get_gzd_product( $product )->get_unit_html();
			$unit_html = str_replace( '(', '(' . __( 'Preis / Liter: ', 'swiftframework' ), $unit_html );
			if ( is_product_category() )
			{
				$unit_html = preg_replace( '#<del><span class="amount">.*</span></del>#', '', $unit_html );
			}
			echo $unit_html;
			?>
		</p>
		<?php else: ?>
	<p class="price price-unit smaller"><?php echo wc_gzd_get_gzd_product( $product )->get_unit_html(); ?></p>
		<?php endif; ?>
<?php endif; ?>