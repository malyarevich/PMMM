/**
 * Limit Date Range
 */
jQuery( document ).on( 'gform_post_render', gfp_limit_date_range );

function gfp_limit_date_range() {
	jQuery( '.datepicker' ).each(
		function () {
			var datepicker = jQuery( this );
			if ( gfp_datepicker_has_limit( datepicker ) ) {
				gfp_datepicker_apply_limit( datepicker );
			}
		} );
}

function gfp_datepicker_has_limit( datepicker ) {
	var has_limit = false;

	var field_id = gfp_get_datepicker_field_id( datepicker );
	if ( 'undefined' !== typeof( gfp_limit_date_range_vars.fields[ field_id ] ) ) {
		has_limit = true;
	}

	return has_limit;
}

function gfp_datepicker_apply_limit( datepicker ) {
	var field_id = gfp_get_datepicker_field_id( datepicker );
	var format = gfp_date_range_get_format( gfp_limit_date_range_vars.fields[ field_id ]['format'] );

	if ( 0 < gfp_limit_date_range_vars.fields[ field_id ]['min'].length ) {
		var date_vars = gfp_date_range_get_date_vars( gfp_limit_date_range_vars.fields[ field_id ]['min'], format );
		datepicker.datepicker( 'option', 'minDate', new Date( date_vars['year'], date_vars['month'], date_vars['day'] ) );
	}

	if ( 0 < gfp_limit_date_range_vars.fields[ field_id ]['max'].length ) {
		var date_vars = gfp_date_range_get_date_vars( gfp_limit_date_range_vars.fields[ field_id ]['max'], format );
		datepicker.datepicker( 'option', 'maxDate', new Date( date_vars['year'], date_vars['month'], date_vars['day'] ) );
	}
}

function gfp_get_datepicker_field_id( datepicker ) {
	var field_name = datepicker.attr( 'name' );
	var field_name_split = field_name.split( '_' );

	return field_name_split[1];
}

function gfp_date_range_get_format( format_class ) {
	var format = [];

	switch ( format_class ) {
		case 'mdy':
			format['separator'] = '/';
			format[0] = 'month';
			format[1] = 'day';
			format[2] = 'year';
			break;
		case 'dmy':
			format['separator'] = '/';
			format[0] = 'day';
			format[1] = 'month';
			format[2] = 'year';
			break;
		case 'dmy_dash':
			format['separator'] = '-';
			format[0] = 'day';
			format[1] = 'month';
			format[2] = 'year';
			break;
		case 'dmy_dot':
			format['separator'] = '.';
			format[0] = 'day';
			format[1] = 'month';
			format[2] = 'year';
			break;
		case 'ymd_slash':
			format['separator'] = '/';
			format[0] = 'year';
			format[1] = 'month';
			format[2] = 'day';
			break;
		case 'ymd_dash':
			format['separator'] = '-';
			format[0] = 'year';
			format[1] = 'month';
			format[2] = 'day';
			break;
		case 'ymd_dot':
			format['separator'] = '.';
			format[0] = 'year';
			format[1] = 'month';
			format[2] = 'day';
			break;
	}

	return format;
}

function gfp_date_range_get_date_vars( date_string, format ) {
	var date_vars = [];
	var parsed_date = date_string.split( format['separator'] );
	for ( var i = 0; i < parsed_date.length; i++ ) {
		switch ( format[i] ) {
			case 'year':
				date_vars['year'] = parsed_date[i];
				break;
			case 'month':
				date_vars['month'] = parsed_date[i] - 1;
				break;
			case 'day':
				date_vars['day'] = parsed_date[i];
				break;
		}
	}

	return date_vars;
}