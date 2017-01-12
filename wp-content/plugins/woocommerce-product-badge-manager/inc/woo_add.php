<?php
// add badges on wocommerce single product page
$product_badge_show = vp_option('woo_badge_man_opt.show_badge_single_page');
if ($product_badge_show == 1) {
	function add_woo_pro_badges_on_woo() {
		$marginTop = vp_option('woo_badge_man_opt.single_product_badge_top');
		$marginBottom = vp_option('woo_badge_man_opt.single_product_badge_bottom');
		$badge_size = vp_option('woo_badge_man_opt.single_product_badge_width');
		$tooltip = vp_option('woo_badge_man_opt.single_product_show_tooltip');
		$tooltip_animation = vp_option('woo_badge_man_opt.single_product_show_tooltip_animation');
		$badge_description = vp_option('woo_badge_man_opt.single_product_badge_page_link');
		// Execute Shortcode
		echo do_shortcode('[woo_pro_badges margintop="'.$marginTop.'" marginbottom="'.$marginBottom.'" size="'.$badge_size.'" tooltip="'.$tooltip.'" tooltip_animation="'.$tooltip_animation.'" badge_description_page="'.$badge_description.'"]');
	}

	// retrive value from options
	$badge_position = vp_option('woo_badge_man_opt.single_product_badge_position');
	// add action
	add_action( 'woocommerce_single_product_summary', 'add_woo_pro_badges_on_woo', $badge_position );
}