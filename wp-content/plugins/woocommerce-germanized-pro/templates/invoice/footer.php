<?php
/**
 * Invoice Footer
 *
 * @author 		Vendidero
 * @package 	WooCommerceGermanizedPro/Templates
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<table class="footer">
	<tr>
		<td colspan="2"></td>
	</tr>
	<tr>
		<td>
			<?php echo implode( '<br/>', $invoice->get_sender_address() ); ?>
		</td>
		<td>
			<?php echo implode( '<br/>', $invoice->get_sender_address( 'detail' ) ); ?>
		</td>
	</tr>
</table>