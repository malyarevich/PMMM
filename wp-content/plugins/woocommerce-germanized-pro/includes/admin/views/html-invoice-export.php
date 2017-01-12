<?php
/**
 * Admin View: Invoice Export
 */

if ( ! defined( 'ABSPATH' ) )
	exit;
?>

<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function($){
 		var form = $('#export-filters'),
 			filters = form.find('.export-filters');
 		filters.hide();
 		form.find('input:radio').change(function() {
			switch ( $(this).val() ) {
				case 'invoice': $('#invoice-filters').slideDown(); break;
			}
 		});
	});
//]]>
</script>
<ul id="invoice-filters" class="export-filters">
	<li>
		<label><?php _e( 'Date range:' ); ?></label>
		<select name="invoice_start_date">
			<option value="0"><?php echo _x( 'Start Date', 'invoices', 'woocommerce-germanized-pro' ); ?></option>
			<?php export_date_options( 'invoice' ); ?>
		</select>
		<select name="invoice_end_date">
			<option value="0"><?php echo _x( 'End Date', 'invoices', 'woocommerce-germanized-pro' ); ?></option>
			<?php export_date_options( 'invoice' ); ?>
		</select>
	</li>
	<li>
		<label><?php echo _x( 'Typ:', 'invoices', 'woocommerce-germanized-pro' ); ?></label>
		<select name="invoice_type">
			<option value="0"><?php echo _x( 'All', 'invoices', 'woocommerce-germanized-pro' ); ?></option>
			<?php foreach( wc_gzdp_get_invoice_types() as $type => $label ) : ?>
				<option value="<?php echo $type;?>"><?php echo $label[ 'title' ]; ?></option>
			<?php endforeach; ?>
		</select>
	</li>
	<li>
		<label><?php echo _x( 'Status:', 'invoices', 'woocommerce-germanized-pro' ); ?></label>
		<select name="invoice_status">
			<option value="0"><?php echo _x( 'All', 'invoices', 'woocommerce-germanized-pro' ); ?></option>
			<?php foreach( wc_gzdp_get_invoice_statuses() as $status => $label ) : ?>
				<option value="<?php echo $status;?>"><?php echo $label; ?></option>
			<?php endforeach; ?>
		</select>
	</li>
	<li>
		<label><?php echo _x( 'Export Format:', 'invoices', 'woocommerce-germanized-pro' ); ?></label>
		<select name="invoice_export_format">
			<option value="csv"><?php echo _x( 'CSV', 'invoices', 'woocommerce-germanized-pro' ); ?></option>
			<option value="zip"><?php echo _x( 'ZIP (PDF)', 'invoices', 'woocommerce-germanized-pro' ); ?></option>
		</select>
	</li>
</ul>