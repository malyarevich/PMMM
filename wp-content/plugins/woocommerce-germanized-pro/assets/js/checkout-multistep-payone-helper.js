jQuery( function( $ ) {

	// Unbind checkout_place_order event to prevent Payone from checking fields in step 1
	$( document ).ajaxComplete(function(event, xhr, settings) {

		if ( $( '.step-wrapper-1' ).is( ':visible' ) ) {

			$( 'form.checkout' ).unbind( 'checkout_place_order' );

		}

	});

	// Before Payone script init: Remove data-payonecw-attached attribute to reinit
	$( document.body ).bind( 'updated_checkout', function() {

		$( 'form.checkout' ).unbind( 'checkout_place_order' );
		$( 'form.checkout' ).removeAttr( 'data-payonecw-attached' );

	});

	// Unbind event for back to step 1 button
	$( document.body ).bind( 'wc_gzdp_show_step', function( event, step ) {
		
		if ( $( step ).hasClass( 'step-1' ) ) {
			$( 'form.checkout' ).unbind( 'checkout_place_order' );
		}

		$( 'form.checkout' ).removeAttr( 'data-payonecw-attached' );

	});

});