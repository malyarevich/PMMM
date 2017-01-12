<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	global $prdctfltr_global;

	if ( isset( $prdctfltr_global['curr_options'] ) ) {
		$curr_override = $prdctfltr_global['curr_options']['wc_settings_prdctfltr_noproducts'];
	}
	else {
		$curr_options = WC_Prdctfltr::prdctfltr_get_settings();
		$curr_override = isset( $prdctfltr_global['curr_options']['wc_settings_prdctfltr_noproducts'] ) ? $prdctfltr_global['curr_options']['wc_settings_prdctfltr_noproducts'] : '';
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