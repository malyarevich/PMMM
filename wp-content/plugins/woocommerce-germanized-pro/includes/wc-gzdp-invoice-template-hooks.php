<?php

if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Action/filter hooks used for functions/templates
 *
 * @author 		Vendidero
 * @version     1
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Invoice download buttons within order table
add_filter( 'woocommerce_my_account_my_orders_actions', 'wc_gzdp_invoice_download_button', 10, 2 );

// Order details page invoice data
add_action( 'woocommerce_view_order', 'wc_gzdp_invoice_download_html', 10, 1 );

?>