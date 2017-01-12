jQuery( function( $ ) {

	if ( 'amazon_payments_advanced' === $( 'input[name=payment_method]:checked' ).val() ) {

		$( '#amazon_customer_details .col2-set .col-2' ).appendTo( '#order-payment' );
		$( '#amazon_customer_details' ).appendTo( '#customer_details' );
		$( '#customer_details > .col-1' ).hide();
		$( '#customer_details > .col-2' ).hide();
		$( '#order-payment > .col-2 > h3' ).hide();

		$( 'body' ).on( 'updated_checkout', function() {
			$( '.woocommerce-gzpd-checkout-verify-data .addresses address' ).text( amazon_helper.managed_by );
		});

	}

});