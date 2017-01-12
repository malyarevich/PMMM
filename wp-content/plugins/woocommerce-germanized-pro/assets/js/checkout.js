jQuery( function( $ ) {

	$( document ).on( 'change', '#billing_vat_id', function() {
		$( '.woocommerce-error, .woocommerce-message' ).remove();
		$( 'body' ).trigger( 'update_checkout' );
	});

});