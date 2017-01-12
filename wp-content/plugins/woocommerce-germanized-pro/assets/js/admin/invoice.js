jQuery( function ( $ ) {

	$( document ).on( 'click', '.button-invoice', function() {
		var action = $(this).data( 'action' );
		var id = parseInt( $(this).data( 'invoice' ) );
		var wrapper = $(this).parents( '.invoice_data' );
		if ( wrapper.find( '.invoice_' + action + '_' + id ).length > 0 )
			wrapper.find( '.invoice_' + action + '_' + id ).val( id );
	});

	$( document ).on( 'click', 'a.invoice-delete, .hide-invoice-delete', function() {
		var wrapper = $( this ).parents( '.invoice_data' );
		wrapper.find( '.invoice-delete-wrapper' ).toggle();
		return false;
	});

});