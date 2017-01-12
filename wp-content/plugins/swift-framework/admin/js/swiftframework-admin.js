/*global jQuery */

(function( $ ) {
	'use strict';

	jQuery(document).ready( function() {

	 	var hash = window.location.hash,
        token = hash.substring(14),
        id = token.split('.')[0];
	    
	    //If there's a hash then autofill the token and id
	    if (hash) {
	    	$('#access_token').val(token);
	    	$('#user_id').val(id);
	        $('#sf_instagram_info').append('<div class="notice"><p><span style="color: red;">Important:</span> please make sure you press <b>"Save Changes"</b> to store the authentication information.</p></div>');
	    }
	});

	jQuery(window).load( function() {

	});


})( jQuery );