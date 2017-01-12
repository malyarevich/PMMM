<?php
/**
 * Invoice Header
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php echo implode( '<br/>', $invoice->get_sender_address( 'detail' ) ); ?>