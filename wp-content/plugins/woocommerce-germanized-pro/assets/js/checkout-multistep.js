jQuery( function( $ ) {

	// Support other Payment Plugins - just add a wrap around their custom payment wrapper
	if ( $( '#payment-manual' ).length ) {
		$( '#payment-manual' ).wrap( '<div id="order-payment"></div>' );
		$( '#order_payment_heading' ).insertBefore( '#payment-manual' );
	} else {
		$( '#payment' ).wrap( '<div id="order-payment"></div>' );
		$( '#order_payment_heading' ).insertBefore( '#payment' );
	}

	$( '#order_review > #order-payment ~ *' ).wrapAll( '<div id="order-verify"></div>' );

	$( '#order_review_heading' ).prependTo( '#order-verify' );

	$.each( steps, function( index, elem ) {
		
		if ( $( elem.selector ).length )  {
			
			// Wrap selector with step-wrapper
			$( elem.selector ).wrap( '<div class="' + multistep.wrapper + ' ' + elem.wrapper_classes.join( ' ' ) +  '" id="step-wrapper-' + elem.id +  '" data-id="' + elem.id +  '"></div>' );
			
			if ( elem.submit_html )
				$( '#step-wrapper-' + elem.id ).append( elem.submit_html );

		}

	});

	$( '.step-wrapper' ).hide();

	$( document ).on( 'click', '.step, .step-trigger', function() {

		if ( ! $( this ).attr( 'href' ) )
			return false;

		var step = $( this ).data( 'href' );

		$( 'body' ).trigger( 'wc_gzdp_show_step', $( this ) );

		$( '.step-' + step ).trigger( 'change', $( this ) );

	});

	$( '.step' ).bind( 'change', function() {

		var id = $( this ).data( 'href' );

		if ( $( '#step-wrapper-' + id ).length ) {
			
			$( '.woocommerce-error' ).remove();
			$( '.step-nav' ).find( '.active' ).removeClass( 'active' );
			$( this ).parents( 'li' ).addClass( 'active' );
			
			$( this ).attr( 'href', '#step-' + $( this ).data( 'href' ) );
			
			$( '.step-wrapper' ).hide();
			$( '.step-wrapper' ).removeClass( 'step-wrapper-active' );
			$( '#step-wrapper-' + id ).show();
			$( '#step-wrapper-' + id ).addClass( 'step-wrapper-active' );

			$( 'body' ).trigger( 'wc_gzdp_step_changed', $( this ) );

		}

	});

	$( '.step-nav li a.step:first' ).trigger( 'change' );

	$( '.step-wrapper' ).bind( 'refresh', function() {

		if ( $( this ).find( '.step-buttons' ).length ) {

			$( this ).find( '.step-buttons' ).prepend( '<input type="hidden" id="wc-gzdp-step-submit" name="wc_gzdp_step_submit" value="' + $( this ).data( 'id' ) + '" />' );

			$( 'body' ).bind( 'checkout_error', function() {

				$( '#wc-gzdp-step-submit' ).remove();

				$( 'body' ).trigger( 'wc_gzdp_step_refreshed' );

			});

		}

	});

	$( document ).on( 'click', '.next-step-button', function(e) {

		var next = $( this ).data( 'next' );
		var current = $( this ).data( 'current' );	

		if ( $( this ).parents( '.step-wrapper' ).hasClass( 'no-ajax' ) ) {

			$( '.step-' + next ).trigger( 'change' );
			
			// Stop auto ajax reload
			e.preventDefault();
			e.stopPropagation();

		} else {

			$( document.body ).bind( 'updated_checkout', function() {

				if ( $( document ).find( '.woocommerce-checkout-payment .blockUI' ).length )
					$( document ).find( '.woocommerce-checkout-payment' ).unblock();

			});

			$( this ).parents( '.step-wrapper' ).trigger( 'refresh' );

			$( 'body' ).bind( 'wc_gzdp_step_refreshed', function() {

				if ( $( '.woocommerce-error' ).length == 0 ) {

					// next step
					$( '.step-' + next ).trigger( 'change' );

				}

				$( 'body' ).unbind( 'wc_gzdp_step_refreshed' );

			});

		}

	});

});