/**
 *
 */
jQuery( document ).ready( function ( jQuery ) {
	fieldSettings['date'] += ', .gfp_date_range_setting';

	jQuery( document ).bind( 'gform_load_field_settings', function ( event, field, form ) {
		if ( ( 'date' == field.type ) && ( 'datepicker' == field['dateType'] || 'datedropdown' == field['dateType'] ) ) {
			var enable_date_range_limit = jQuery( '#field_limit_date_range' );
			var date_range_limit_container = jQuery( '#field_gfp_limit_date_range_container' );

			enable_date_range_limit.prop( 'checked', field['limitDateRange'] == true );
			date_range_limit_container.hide();

			var min_date = jQuery( '#gfp_limit_date_range_min_date' );
			var max_date = jQuery( '#gfp_limit_date_range_max_date' );

			if ( 'datepicker' == field['dateType'] ) {
				gfp_date_range_add_datepicker( min_date );
				gfp_date_range_add_datepicker( max_date );
			}

			min_date.val( field['limitDateRangeMinDate'] );
			max_date.val( field['limitDateRangeMaxDate'] );

			if ( false !== enable_date_range_limit.prop( 'checked' ) ) {

				date_range_limit_container.show();

				if ( 'datepicker' == field['dateType'] ) {
					jQuery( '#field_date_format' ).change( gfp_date_range_reformat_date );
					jQuery( '#gfp_limit_date_range_datedropdown_instruction' ).hide();
				}
				else {
					min_date.on( 'keyup', { range_field: 'min' }, gfp_date_range_set_value );
					max_date.on( 'keyup', { range_field: 'max' }, gfp_date_range_set_value );
				}
			}

			jQuery( '#field_date_input_type' ).change( gfp_date_range_toggle_settings );
		}
		else {
			jQuery( '.limit_date_range_setting' ).hide();
		}
	} );
} );

function gfp_date_range_toggle_settings() {
	var input_type = jQuery( this ).val();
	var enable_date_range_limit = jQuery( '#field_limit_date_range' );

	enable_date_range_limit.prop( 'checked', false );
	SetFieldProperty( 'limitDateRange', enable_date_range_limit.checked );

	if ( 'datepicker' === input_type ) {
		var min_date = jQuery( '#gfp_limit_date_range_min_date' );
		var max_date = jQuery( '#gfp_limit_date_range_max_date' );

		gfp_date_range_reset();

		gfp_date_range_add_datepicker( min_date );
		gfp_date_range_add_datepicker( max_date );

		jQuery( '#gfp_limit_date_range_datedropdown_instruction' ).hide();
		jQuery( '#field_gfp_limit_date_range_container' ).hide();
		jQuery( '.limit_date_range_enable_setting' ).show();
	}
	else if ( 'datedropdown' === input_type ) {
		var min_date = jQuery( '#gfp_limit_date_range_min_date' );
		var max_date = jQuery( '#gfp_limit_date_range_max_date' );

		min_date.datepicker( 'destroy' );
		max_date.datepicker( 'destroy' );

		gfp_date_range_reset();

		jQuery( '#gfp_limit_date_range_datedropdown_instruction' ).show();
		jQuery( '#field_gfp_limit_date_range_container' ).hide();
		jQuery( '.limit_date_range_enable_setting' ).show();
	}
	else {
		jQuery( '.limit_date_range_enable_setting' ).hide();
	}
}

function gfp_date_range_add_datepicker( element ) {
	element.datepicker( { yearRange: '-100:+20',
							showOn: 'both',
							buttonText: '<span class="dashicons dashicons-calendar"></span>',
							dateFormat: gfp_date_range_get_format( jQuery( '#field_date_format' ).val() ),
							changeMonth: true,
							changeYear: true } );

}

function gfp_date_range_set_value( event ) {
	if ( 'min' === event.data.range_field ) {
		SetFieldProperty( 'limitDateRangeMinDate', jQuery( this ).val() );
	}
	else if ( 'max' === event.data.range_field ) {
		SetFieldProperty( 'limitDateRangeMaxDate', jQuery( this ).val() );
	}
}

function gfp_date_range_get_format( format_class ) {
	var format = 'mm/dd/yy';

	switch ( format_class ) {
		case 'mdy':
			format = 'mm/dd/yy';
			break;
		case 'dmy':
			format = 'dd/mm/yy';
			break;
		case 'dmy_dash':
			format = 'dd-mm-yy';
			break;
		case 'dmy_dot':
			format = 'dd.mm.yy';
			break;
		case 'ymd_slash':
			format = 'yy/mm/dd';
			break;
		case 'ymd_dash':
			format = 'yy-mm-dd';
			break;
		case 'ymd_dot':
			format = 'yy.mm.dd';
			break;
	}

	return format;
}

function ToggleDateRangeLimitSettings( limit_date_range_enabled ) {
	if ( false !== limit_date_range_enabled ) {
		jQuery( '#field_gfp_limit_date_range_container' ).show( 'slow' );
	}
	else {
		jQuery( '#field_gfp_limit_date_range_container' ).hide( 'slow' );
		gfp_date_range_reset();
	}
}

function gfp_date_range_reformat_date() {
	var new_format_class = jQuery( this ).val();
	var new_format = gfp_date_range_get_format( new_format_class );

	var min_date = jQuery( '#gfp_limit_date_range_min_date' );
	var max_date = jQuery( '#gfp_limit_date_range_max_date' );

	min_date.datepicker( 'option', 'dateFormat', new_format );
	max_date.datepicker( 'option', 'dateFormat', new_format );

	SetFieldProperty( 'limitDateRangeMinDate', min_date.val() );
	SetFieldProperty( 'limitDateRangeMaxDate', max_date.val() );
}

function gfp_date_range_reset() {
	jQuery( '.gfp_limit_date_range' ).val( '' );

	SetFieldProperty( 'limitDateRangeMinDate', jQuery( '#gfp_limit_date_range_min_date' ).val() );
	SetFieldProperty( 'limitDateRangeMaxDate', jQuery( '#gfp_limit_date_range_max_date' ).val() );
}