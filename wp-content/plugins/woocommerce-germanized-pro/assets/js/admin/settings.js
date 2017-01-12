jQuery( function ( $ ) {

	$( document ).on( 'change', '.wc-gzdp-generator input, .wc-gzdp-generator select', function() {
		$( this ).parents( 'tr' ).trigger( 'check_question' );
	});

	$( '.wc-gzdp-generator .form-table tr' ).bind( 'check_question', function() {
    	$( this ).find( 'input:not(.select2-focusser, .select2-input), select' ).each( function() {
    		$( this ).trigger( 'check_input' )
    	});
    });

    $( '.wc-gzdp-generator .form-table tr input, .wc-gzdp-generator .form-table tr select' ).bind( 'check_input', function() {
    	
    	var checked = false;
    	if ( $( this ).is( ':checked' ) || $( this ).is( ':selected' ) )
    		checked = true;

    	var name_org = $( this ).attr( 'name' );

    	if (typeof name_org === typeof undefined || name_org === false) {
    		return;
    	}

    	var name = name_org.replace( '[]', '' );
    	var elem = $( this );
    	var val = $( this ).val();

    	$( '.wc-gzdp-generator' ).find( 'input[data-show_if_' + name +  '], select[data-show_if_' + name +  ']' ).each( function() {
    		$( this ).trigger( 'check_show_if_' + ( checked ? 'checked' : 'unchecked' ), elem );
    	});

    	$( this ).parents( '.form-table' ).trigger( 'check_table' );
    });

   	$( '.wc-gzdp-generator input, .wc-gzdp-generator select' ).bind( 'check_show_if_checked', function( e, elem ) {
   		var i_val = $( elem ).val();
   		var name = $( elem ).attr( 'name' ).replace( '[]', '' );
   		var cur_val = $( this ).data( 'show_if_' + name );
   		var hide = true;
   		if ( cur_val == 'equal_' + i_val && $( elem ).is( ':visible' ) )
   			$( this ).parents( 'tr' ).show();
   		$( this ).trigger( 'check_input' );
   	});

   	$( '.wc-gzdp-generator input, .wc-gzdp-generator select' ).bind( 'check_show_if_unchecked', function( e, elem ) {
   		var i_val = $( elem ).val();
   		var name = $( elem ).attr( 'name' ).replace( '[]', '' );
   		var cur_val = $( this ).data( 'show_if_' + name );
   		var hide = true;
   		if ( cur_val == 'equal_' + i_val || ! ( $( elem ).is( ':visible' ) ) )
   			$( this ).parents( 'tr' ).hide();
   		$( this ).trigger( 'check_input' );
   	});

	$( '.wc-gzdp-generator .form-table' ).bind( 'check_table', function() {
    	var hide = true;
		var table = $( this ); 
		$( this ).find( 'tr' ).each( function() {
			if ( $( this ).is( ':visible' ) )
				hide = false;
		});
		if ( hide )
			table.hide();
    });

    $( '.wc-gzdp-generator .form-table tr' ).bind( 'desc', function( e, desc ) {
    	if ( desc.length > 0 ) {
    		$( '.wc-gzdp-generator-sidebar .info' ).html( desc ).show();
    		$( '.wc-gzdp-generator-sidebar .info' ).offset( { top: $( this ).offset().top } );
    	}
    });

    $( document ).on( 'mouseover', '.wc-gzdp-generator .form-table tr', function() {
    	if ( $( this ).find( '*[data-custom-desc]:first' ).length > 0 )
    		$( this ).trigger( 'desc', $( this ).find( '*[data-custom-desc]:first' ).data( 'custom-desc' ) );
    });

    /*
    $( document ).on( 'focus', '.wc-gzdp-generator input[type=radio], .wc-gzdp-generator input[type=checkbox], .wc-gzdp-generator input[type=text], .wc-gzdp-generator textarea', function() {
    	if ( $( this ).data( 'custom-desc' ) )
    		$( this ).parents( 'tr' ).trigger( 'desc', $( this ).data( 'custom-desc' ) );
    });
	*/

	// Hide show_if default
	$( '.wc-gzdp-generator .form-table input[type=radio]:checked, .wc-gzdp-generator .form-table input[type=checkbox]:checked, .wc-gzdp-generator .form-table select' ).trigger( 'change' );

	// Loading Generator
	$( '.wc-gzdp-generator' ).delay( 800 ).animate({
        opacity: 1
    }, 150 );

    $( document ).on( 'change', '.wc-gzdp-disclaimer-input', function() {
    	$( this ).parents( '.wc-gzdp-disclaimer' ).removeClass( 'notice-error notice-success' );
    	if ( ! $( this ).is( ':checked' ) ) {
    		$( '.wc-gzdp-generator-submit' ).hide();
			$( this ).parents( '.wc-gzdp-disclaimer' ).addClass( 'notice-error' );
    	} else {
    		$( '.wc-gzdp-generator-submit' ).show();
			$( this ).parents( '.wc-gzdp-disclaimer' ).addClass( 'notice-success' );
    	}
    });

	// Submit
	if ( $( '.wc-gzdp-generator' ).length > 0 ) {
		// Remove hidden fields
		$( document ).on( 'submit', '.woocommerce #mainform', function() {
			if ( ! $( '.wc-gzdp-disclaimer-input' ).is( ':checked' ) ) {
				$( '.wc-gzdp-disclaimer' ).removeClass( 'notice-success' );
				$( '.wc-gzdp-disclaimer' ).addClass( 'notice-error' );
				return false;
			}
			$( '.wc-gzdp-generator tr' ).each( function() {
				if ( ! $( this ).is( ':visible' ) )
					$( this ).remove();
			});
		});
	}

	var _custom_media = true,
	_orig_send_attachment = wp.media.editor.send.attachment;
 
	$( document ).on( 'click', '.button-wc-gzdp-attachment-field', function(e) {
		
		var id = $( this ).data( 'input' );
		var input = $( '#' + id );
		var allow_multiple = $( this ).data( 'multiple' ) == "1" ? true : false;
		var utype = $( this ).data( 'type' );
		var wrapper = $( this ).parents( '.wc-gzdp-attachment-wrapper' );

		if (typeof(custom_file_frame)!=="undefined") {
        	custom_file_frame.close();
      	}
	 
		custom_file_frame = wp.media.frames.customHeader = wp.media({
			//Title of media manager frame
			title: wc_gzdp_attachment_field.title,
			library: {
				type: utype
			},
			button: {
				text: wc_gzdp_attachment_field.insert
			},
			multiple: allow_multiple
		});
	 
		custom_file_frame.on('select', function() {
			var currents = input.val().split( ',' );
			var attachments = custom_file_frame.state().get('selection').toJSON();
			var string = input.val();
			$.each( attachments, function( index, attachment ) {
				if ( $.inArray( String(attachment.id), currents ) === -1 ) {
					string += ( index > 0 ? "," : "" ) + attachment.id;
					wrapper.find( '.current-attachment' ).append(
						'<div class="wc-gzdp-unset-attachment-wrap data-unset-' + attachment.id + '"><a class="unset-attachment" data-unset="' + attachment.id + '" href="#"><i class="fa fa-remove"></i></a> <a href="' + attachment.editLink + '" target="_blank">' + attachment.title + '</a></div>'
					);
				}
			});
	
			input.val(string);
			
		});
	 
		custom_file_frame.open();
	});

	$( document ).on( 'click', '.unset-attachment', function() {
		var delid = $( this ).data( 'unset' );
		var input = $( this ).parents( '.wc-gzdp-attachment-wrapper' ).find( '.wc-gzdp-attachment-id-data' );
		var val = input.val();
		var ids = val.split( ',' );
		var string = "";
		var count = 0;
		$.each( ids, function( index, id ) {
			if ( delid != id )
				string += ( count++ > 0 ? "," : "" ) + id;
		});
		input.val( string );
		$( this ).parents( '.current-attachment' ).find( '.data-unset-' + delid ).remove();
		return false;
	});

	// Settings show hide

	var elements = [ 
		'#woocommerce_gzdp_invoice_cancellation_numbering',
		'#woocommerce_gzdp_invoice_packing_slip_enable_numbering',
		'#woocommerce_gzdp_invoice_auto',
		'#woocommerce_gzdp_invoice_cancellation_auto',
		'#woocommerce_gzdp_invoice_show_page_numbers',
		'#woocommerce_gzdp_invoice_cancellation_table_content',
		'#woocommerce_gzdp_invoice_packing_slip_table_content',
	];

	$( document ).on( 'change', '#woocommerce_gzdp_invoice_auto', function() {
		if ( $( this ).is( ':checked' ) ) {
			$( '#woocommerce_gzdp_invoice_auto_email' ).parents( 'tr' ).show();
			$( '#woocommerce_gzdp_invoice_auto_except_free' ).parents( 'tr' ).show();
		} else {
			$( '#woocommerce_gzdp_invoice_auto_email' ).parents( 'tr' ).hide();
			$( '#woocommerce_gzdp_invoice_auto_except_free' ).parents( 'tr' ).hide();
		}
	});

	$( document ).on( 'change', '#woocommerce_gzdp_invoice_cancellation_auto', function() {
		if ( $( this ).is( ':checked' ) )
			$( '#woocommerce_gzdp_invoice_cancellation_auto_email' ).parents( 'tr' ).show();
		else
			$( '#woocommerce_gzdp_invoice_cancellation_auto_email' ).parents( 'tr' ).hide();
	});

	$( document ).on( 'change', '#woocommerce_gzdp_invoice_show_page_numbers', function() {
		if ( $( this ).is( ':checked' ) )
			$( '#woocommerce_gzdp_invoice_page_numbers_bottom' ).parents( 'tr' ).show();
		else
			$( '#woocommerce_gzdp_invoice_page_numbers_bottom' ).parents( 'tr' ).hide();
	});

	$( document ).on( 'change', '#woocommerce_gzdp_invoice_cancellation_table_content', function() {
		if ( $( this ).is( ':checked' ) )
			$( '#woocommerce_gzdp_invoice_cancellation_text_after_table' ).parents( 'tr' ).show();
		else
			$( '#woocommerce_gzdp_invoice_cancellation_text_after_table' ).parents( 'tr' ).hide();
	});

	$( document ).on( 'change', '#woocommerce_gzdp_invoice_packing_slip_table_content', function() {
		if ( $( this ).is( ':checked' ) )
			$( '#woocommerce_gzdp_invoice_packing_slip_text_after_table' ).parents( 'tr' ).show();
		else
			$( '#woocommerce_gzdp_invoice_packing_slip_text_after_table' ).parents( 'tr' ).hide();
	});

	$.each( elements, function( index, value ) {
		$( document ).on( 'change', value, function() {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).parents( 'tr').next( 'tr' ).show();
			} else {
				$( this ).parents( 'tr').next( 'tr' ).hide();
			}
		});
		$( value ).trigger( 'change' );
	});

});