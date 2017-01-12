<?php
?>
<li class="limit_date_range_enable_setting gfp_date_range_setting field_setting">
	<input type="checkbox" name="field_limit_date_range" id="field_limit_date_range"
		   onclick="SetFieldProperty( 'limitDateRange', this.checked ); ToggleDateRangeLimitSettings( this.checked ); "/>
	<label for="field_limit_date_range" class="inline">
		<?php _e( 'Limit Date Range', 'gfp-limit-date-range' ); ?>
		<?php gform_tooltip( 'form_field_limit_date_range' ) ?>
	</label> <br/>

	<div id="field_gfp_limit_date_range_container" style="display:none; padding-top:10px;">
		<span id="gfp_limit_date_range_datedropdown_instruction" class="instruction" style="margin-left:0;"><?php _e( 'Enter 4-digit year e.g. 2001', 'gfp-limit-dtate-range' ); ?></span>
		<table>
			<tbody>
			<tr></tr>
			<tr>
				<td><label for="gfp_limit_date_range_min_date" class="inline"><?php _e( 'Minimum', 'gfp-limit-date-range' ) ?></label></td>
				<td><input type="text" value="" id="gfp_limit_date_range_min_date" class="gfp_limit_date_range"
						   onchange="SetFieldProperty( 'limitDateRangeMinDate', this.value );"></td>
			</tr>
			<tr>
				<td><label for="gfp_limit_date_range_max_date" class="inline"><?php _e( 'Maximum', 'gfp-limit-date-range' ) ?></label></td>
				<td><input type="text" value="" id="gfp_limit_date_range_max_date" class="gfp_limit_date_range"
						   onchange="SetFieldProperty( 'limitDateRangeMaxDate', this.value );"></td>
			</tr>
			<tr></tr>
			</tbody>
		</table>
	</div>

</li>