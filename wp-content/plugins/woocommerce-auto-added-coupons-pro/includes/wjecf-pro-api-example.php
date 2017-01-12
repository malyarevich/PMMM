<?php

add_action( 'wp_footer', 'WJECF_API_Test', 5, 0);

function WJECF_API_Test() {
	$all = WJECF_API()->get_all_auto_coupons();

	foreach( $all as $code => $coupon ) {
		$cp = $coupon->id;
		echo "<h3>" . $code . "</h3>";
		echo "get_quantity_of_matching_products: " . WJECF_API()->get_quantity_of_matching_products( $cp ) . "<br>";
		echo "get_subtotal_of_matching_products: " . WJECF_API()->get_subtotal_of_matching_products( $cp ) . "<br>";
		echo "get_coupon_shipping_method_ids: " . print_r( WJECF_API()->get_coupon_shipping_method_ids( $cp ), true ) . "<br>";
		echo "get_coupon_payment_method_ids: " . print_r( WJECF_API()->get_coupon_payment_method_ids( $cp ), true ) . "<br>";
		echo "get_coupon_customer_ids: " . print_r (WJECF_API()->get_coupon_customer_ids( $cp ), true ) . "<br>";
		echo "get_coupon_customer_roles: " . print_r( WJECF_API()->get_coupon_customer_roles( $cp ), true ) . "<br>";
		echo "get_coupon_excluded_customer_roles: " . print_r( WJECF_API()->get_coupon_excluded_customer_roles( $cp ), true ) . "<br>";
		echo "get_coupon_free_product_ids: " . print_r( WJECF_API()->get_coupon_free_product_ids( $cp ), true ) . "<br>";
	}
	
}