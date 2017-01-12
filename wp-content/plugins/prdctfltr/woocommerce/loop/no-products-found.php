<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	global $prdctfltr_global;

	if ( isset( WC_Prdctfltr::$settings['instance'] ) ) {
		$curr_override = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_noproducts'];
	}
	else {
		$curr_override = '';
	}

	$curr_class = ( WC_Prdctfltr::$settings['wc_settings_prdctfltr_ajax_class'] == '' ? 'products' : WC_Prdctfltr::$settings['wc_settings_prdctfltr_ajax_class'] );

	echo '<div class="' . $curr_class . '">';

	if ( $curr_override == '' ) {
		wc_get_template( 'loop/no-products-found.php' );
	}
	else {
		echo do_shortcode( $curr_override );
	}

	echo '</div>';

?>