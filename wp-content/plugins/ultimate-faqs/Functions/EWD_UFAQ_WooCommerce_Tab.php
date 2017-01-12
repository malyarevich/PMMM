<?php

add_filter( 'woocommerce_product_tabs', 'EWD_UFAQ_Woo_FAQ_Tab' );
function EWD_UFAQ_Woo_FAQ_Tab( $tabs ) {
	global $product;

	$Use_Product = get_option("EWD_UFAQ_Use_Product");

	$WooCommerce_FAQs = get_option("EWD_UFAQ_WooCommerce_FAQs");

	if ($Use_Product == "Yes" and is_object($product)) {$Product_Post = get_post($product->get_id());}
	else {$Product_Post = get_post(get_the_id());}

	$UFAQ_Product_Category = get_term_by('name', $Product_Post->post_title, 'ufaq-category');

	$WC_Cats = get_the_terms($Product_Post, 'product_cat');
	$UFAQ_WCCat_Category = false;
	if ($WC_Cats) {
		foreach ($WC_Cats as $WC_Cat) {
			if (get_term_by('name', $WC_Cat->name, 'ufaq-category')) {$UFAQ_WCCat_Category = true;}
		}
	}
	
	if (($UFAQ_Product_Category or $UFAQ_WCCat_Category) and $WooCommerce_FAQs == "Yes") {
		$tabs['faq_tab'] = array(
			'title' 	=> __( 'FAQs', 'EWD_UFAQ' ),
			'priority' 	=> 50,
			'callback' 	=> 'EWD_UFAQ_Woo_FAQ_Tab_Content'
		);
	
		return $tabs;
	}

}

function EWD_UFAQ_Woo_FAQ_Tab_Content() {
	global $product;

	$Use_Product = get_option("EWD_UFAQ_Use_Product");
	
	if ($Use_Product == "Yes") {$Product_Post = get_post($product->get_id());}
	else {$Product_Post = get_post(get_the_id());}
	$UFAQ_Product_Category = get_term_by('name', $Product_Post->post_title, 'ufaq-category');

	$WC_Cats = get_the_terms($Product_Post, 'product_cat');
	$UFAQ_WC_Category_List = "";
	if ($WC_Cats) {
		foreach ($WC_Cats as $WC_Cat) {
			$UFAQ_WC_Category = get_term_by('name', $WC_Cat->name, 'ufaq-category');
			if ($UFAQ_WC_Category) {$UFAQ_WC_Category_List .= "," . $UFAQ_WC_Category->slug;}
		}
	}

	echo '<h2>FAQs</h2>';
	echo do_shortcode("[ultimate-faqs include_category='". $UFAQ_Product_Category->slug . $UFAQ_WC_Category_List . "']");;
	
}

?>