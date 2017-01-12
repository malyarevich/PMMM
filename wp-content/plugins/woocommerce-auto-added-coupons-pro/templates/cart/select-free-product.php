<?php
/**
 * Select Free Product on Cart page
 * 
 * This template can be overridden by copying it to yourtheme/woocommerce-auto-added-coupons/cart/select-free-product.php
 * 
 * @version     2.3.4
 */

defined('ABSPATH') or die();

//This should already be set by the calling function, but hey...
if ( ! isset( $free_gift_coupons ) ) {
	$free_gift_coupons = WJECF_API()->get_applied_select_free_product_coupons();
}

?>
<tr class="wjecf-fragment-cart-select-free-product">
	<td colspan="6" class="wjecf-select-free-products">
		<?php
		foreach( $free_gift_coupons as $coupon ):
		    $free_product_ids = WJECF_API()->get_coupon_free_product_ids( $coupon );
		    $field_name = 'wjecf_free_sel_' . esc_attr( $coupon->code );
		    $checked_field = WJECF_API()->get_session_selected_product( $coupon->code );
			?>
				<h3><?php echo WJECF_API()->get_select_free_product_message( $coupon ); ?></h3>
				<ul class="wjecf-cols cols-4">
				<?php
				    foreach ( $free_product_ids as $free_product_id ) {
				        $product = wc_get_product( $free_product_id );
				        echo '<li>';
				        echo '<input type="radio" name="' . $field_name . '" value="' . $free_product_id . '"' . ( $checked_field == $free_product_id ? ' checked="checked"' : '') . '>';
				        echo esc_html__( $product->get_title(), 'woocommerce' ) . '<br>' . $product->get_image();
				        echo '</li>';
				    }
				?>
				</ul>
			<?php
		endforeach;
		?>
	</td>
</tr>
