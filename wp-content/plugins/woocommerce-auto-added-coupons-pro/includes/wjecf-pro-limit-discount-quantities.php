<?php

defined('ABSPATH') or die();

class WJECF_Pro_Limit_Discount_Quantities extends Abstract_WJECF_Plugin  {

	public function __construct() {	
		$this->set_plugin_data( array(
			'description' => __( 'Limit discount to only certain products in the cart.', 'woocommerce-jos-autocoupon' ),
			'dependencies' => array(),
			'can_be_disabled' => true
		) );		
	}

    public function init_hook() {
		/* FRONTEND */
		add_filter('woocommerce_coupon_get_discount_amount', array( $this, 'woocommerce_coupon_get_discount_amount' ), 10, 5);
    }

	public function init_admin_hook() {
		add_action( 'woocommerce_coupon_options_usage_limit', array( $this, 'woocommerce_coupon_options_usage_limit' ), 10, 2 );
		add_action( 'woocommerce_process_shop_coupon_meta', array( $this, 'process_shop_coupon_meta' ), 10, 2 );		
	}


// =============== FRONTEND HOOKS ==================== //

	//2.3.1
	/**
	 * Hooked to the filter woocommerce_coupon_get_discount_amount.
	 * Will decrease discount if items are excluded.
	 * @param float $discount 
	 * @param float $discounting_amount 
	 * @param array $cart_item 
	 * @param bool $single 
	 * @param type $coupon 
	 * @return float
	 */
	public function woocommerce_coupon_get_discount_amount ( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

		//Check for is_null because WC versions prior to 2.3.0 could pass null, 
		//also percent discount were handled differently and total values can be different
		if ( WJECF_WC()->coupon_is_type( $coupon, array( 'fixed_cart' ) ) || is_null( $cart_item ) ) {
			return $discount;
		}

		$apply_to = get_post_meta( $coupon->id, '_wjecf_apply_discount_to', true );
		if ( empty( $apply_to ) ) {
			return $discount;
		}

		//REMOVED 2.3.7: this was wrong; why no discount on products if discount type is cart discount?
		// if ( ! $this->coupon_is_valid_for_product( $coupon, $cart_item['data'], $cart_item ) ) {
		// 	return 0;
		// }		

		//echo $cart_item['data']->post->post_title . ' ' . $coupon->code . ' disc: ' .$discount. ' disc_am: ' .$discounting_amount. '<br>';
		// echo "<br>";

		//Number of discounted items on this order line ()
		$orig_discount_qty = $cart_item_qty = is_null( $cart_item ) ? 1 : $cart_item['quantity'];

		//FIX 2.3.3-b3: If limit_usage_to_x_items is 0 it may mean that not all items on the line were discounted
		//Using this trick we recalculate the original quantity of discounted items
		if ( $coupon->limit_usage_to_x_items === 0 ) {
			if (  WJECF_WC()->coupon_is_type( $coupon, array( 'percent_product', 'percent' ) ) ) {
				$expected_discount = $coupon->coupon_amount * ( $discounting_amount / 100 );
				$orig_discount_qty *= $discount / $expected_discount; 
			} elseif ( WJECF_WC()->coupon_is_type( $coupon, 'fixed_product' ) ) {
				$expected_discount = min( $coupon->coupon_amount, $discounting_amount );
				$expected_discount = $single ? $expected_discount : $expected_discount * $cart_item_qty;
				$orig_discount_qty *= $discount / $expected_discount;
			}
			$orig_discount_qty = round( $orig_discount_qty, 2 ); //just in case fractions are used in cart
		}
		$discount_qty = $orig_discount_qty;

		switch ( $apply_to ) {
			default:
				$this->log('WARNING: Unknown value for _wjecf_apply_discount_to: ' . $apply_to);
				break;
			case 'all': 
				//No limitation of discount
				break;
			case 'cheapest_product':
				$discount_qty = $this->get_discount_qty_cheapest_product( $cart_item, $discount_qty, $coupon );
				break;
			case 'cheapest_line':
				$discount_qty = $this->get_discount_qty_cheapest_line( $cart_item, $discount_qty, $coupon );
				break;
			case 'one_per_line':
				$discount_qty = min(1, $discount_qty);
				break;
			case 'every_nth_item':
				$discount_qty = $this->get_discount_qty_every_nth_item( $cart_item, $discount_qty, $coupon );
				break;
		}

		//Changed amount of items to apply discount to?
		if ( $discount_qty != $orig_discount_qty ) {
			$discount = $discount * $discount_qty / $orig_discount_qty;
			//Re-increase limit_usage_to_x_items
			if ( $coupon->limit_usage_to_x_items !== '' ) {
				$coupon->limit_usage_to_x_items += $orig_discount_qty - $discount_qty;
			}
		}

		// WC_ROUNDING_PRECISION does not exist in WC 2.6.3 (will be back in later versions)
		$discount = round( $discount, defined( 'WC_ROUNDING_PRECISION' ) ? WC_ROUNDING_PRECISION : wc_get_price_decimals() + 2 );

		return $discount;
	}


// =============== FUNCTIONALITY ==================== //


	private function get_discount_qty_every_nth_item( $cart_item, $discount_qty, $coupon ) {
		//nth is the amount entered in 'minimum qty of matching products' or 2
		$nth = intval( get_post_meta( $coupon->id, '_wjecf_min_matching_product_qty', true ) );
		if ( empty( $nth ) ) $nth = 2; //default to every second item

		//Discountable items; expensive to cheap
		$discountable_cart_items = $this->get_discountable_cart_items( $coupon );
		uasort($discountable_cart_items, array( $this, 'sort_by_product_price') );
		$discountable_cart_items = array_reverse( $discountable_cart_items, true ); 

		$from_idx = 1;
		foreach( $discountable_cart_items as $key => $discountable_cart_item ) {
			if ( $discountable_cart_item == $cart_item ) {
				$to_idx = $from_idx + $discountable_cart_item['quantity'] - 1;						
				//We know the first and last index of the products in this orderline (in the expensive--->cheap array)
				//Now we can calculate howmany products in this order line are the nth item
				$orig_discount_qty = $discount_qty;				
				$discount_qty = min($discount_qty, floor($to_idx/$nth) - floor(($from_idx-1)/$nth));

				//$this->log(sprintf('%s: discounting %s of %s idx: %d...%d  %s', $coupon->code, $discount_qty, $orig_discount_qty, $from_idx, $to_idx, $cart_item['data']->post->post_title ));
				break;
			} else {
				$from_idx += $discountable_cart_item['quantity'];
			}
		}

		return $discount_qty;
	}

	private function get_discount_qty_cheapest_line( $cart_item, $discount_qty, $coupon ) {
		$discountable_cart_items = $this->get_discountable_cart_items( $coupon );
		uasort($discountable_cart_items, array( $this, 'sort_by_line_price') );

		if ( $cart_item === reset( $discountable_cart_items ) ) {
			//Yes, it's the cheapest item; discount it!
			return $discount_qty;
		} else {
			return 0;
		}
	}

	private function get_discount_qty_cheapest_product( $cart_item, $discount_qty, $coupon ) {
		$discountable_cart_items = $this->get_discountable_cart_items( $coupon );
		uasort($discountable_cart_items, array( $this, 'sort_by_line_price') );

		if ( $cart_item === reset( $discountable_cart_items ) ) {
			//Yes, it's the cheapest item; discount it only once!
			return min(1, $discount_qty);
		} else {
			return 0;
		}
	}	


	/**
	 * Get all matching cart items (for product discount) or all cart items (for cart discount)
	 * @param WC_Coupon $coupon 
	 * @return array [ 'cart_item_key' => cart_item, ... ]
	 */
	public function get_discountable_cart_items( $coupon ) {
		$cart = WC()->cart->get_cart();

		if ( ! WJECF_WC()->coupon_is_type( $coupon, array( 'fixed_product', 'percent_product' ) ) ) {
			return $cart; // return all if it's a cart discount
		}

		//discountable_cart_items: Either the matching cart items (product discount) or all cart items (cart discount)
		$discountable_cart_items = array();
		foreach ( $cart as $cart_item_key => $cart_item ) {
			if ( WJECF()->coupon_is_valid_for_product( $coupon, $cart_item['data'], $cart_item ) ) {
				$discountable_cart_items[$cart_item_key] = $cart_item;
			}
		}

		return $discountable_cart_items;
	}

	/**
	 * Use with uasort() to sort an array of cart_items by the single product price
	 * @param type $cart_item_a 
	 * @param type $cart_item_b 
	 * @return type
	 */
	private function sort_by_product_price( $cart_item_a, $cart_item_b ) {
		$price_a = $cart_item_a['data']->get_price();
		$price_b = $cart_item_b['data']->get_price();
		if ($price_a == $price_b) {
			return 0;
		}
		return ($price_a < $price_b) ? -1 : 1;
	}

	/**
	 * Use with uasort() to sort an array of cart_items by the line subtotal
	 * @param type $cart_item_a 
	 * @param type $cart_item_b 
	 * @return type
	 */
	private function sort_by_line_price( $cart_item_a, $cart_item_b ) {
		$price_a = isset( $cart_item_a['line_subtotal'] ) ? $cart_item_a['line_subtotal'] : $cart_item_a['data']->get_price() * $cart_item_a['quantity'];
		$price_b = isset( $cart_item_b['line_subtotal'] ) ? $cart_item_b['line_subtotal'] : $cart_item_b['data']->get_price() * $cart_item_b['quantity'];
		if ($price_a == $price_b) {
			return 0;
		}
		return ($price_a < $price_b) ? -1 : 1;
	}		


// =============== ADMIN ==================== //

	//2.3.3-b3
	public function woocommerce_coupon_options_usage_limit() {
		global $thepostid, $post;
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;

		echo '<div class="options_group wjecf_hide_on_fixed_cart_discount">';
		echo '<h3>' . __( 'Limit discount to', 'woocommerce-jos-autocoupon') . '</h3>';
		echo '<p>' . __( 'Here you can exclude certain products from being discounted (Only applies to Cart % Discount, Product Discount, Product % Discount)', 'woocommerce-jos-autocoupon') . '</p>';

		//2.3.1
		woocommerce_wp_select( array( 
			'id' => '_wjecf_apply_discount_to', 
			'label' => __( 'Limit discount to', 'woocommerce' ), 
			'options' => array( 
				'all' => __( '(default)', 'woocommerce-jos-autocoupon'), 
				'one_per_line' => __( 'One item per order line', 'woocommerce-jos-autocoupon'), 
				'cheapest_product' => __( 'Lowest priced product (single item)', 'woocommerce-jos-autocoupon'), 
				'cheapest_line' => __( 'Lowest priced order line (all items)', 'woocommerce-jos-autocoupon'),
				'every_nth_item' => __( 'Every nth item. n = min qty of matching products (or 2 if not supplied)', 'woocommerce-jos-autocoupon'), 
			),
			'description' => __( 'Please note that when the discount type is \'Product discount\' (see \'General\'-tab), the discount will only be applied to <em>matching</em> products.', 'woocommerce-jos-autocoupon' ),
			'desc_tip' => true
		) );


        echo '</div>';

	}	

	public function process_shop_coupon_meta( $post_id, $post ) {
		//2.3.1
		$wjecf_apply_discount_to = wc_clean( $_POST['_wjecf_apply_discount_to'] );
		update_post_meta( $post_id, '_wjecf_apply_discount_to', $wjecf_apply_discount_to );
	}	
}