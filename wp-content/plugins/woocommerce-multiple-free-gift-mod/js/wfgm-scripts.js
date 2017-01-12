/**
 * @file wfgm-scripts.js
 *
 * Frontend core script for woocommerce free gift mod plugin.
 *
 * Copyright (c) 2016, Yevgen <yevgen.slyuzkin@gmail.com>
 */
jQuery(document).ready(function($) {

	$('.wfgm-add-gifts').click(function(e) {
        $('.wfgm-add-gifts').removeClass('wfgm-add-gifts');
        $('.wfgm-save-div').show();
		e.preventDefault();
		var form = $(this).closest('form');
		$.ajax({
			type: 'POST',
			url: form.attr('action'),
			data: form.serialize(),
			success: function( response ) {
				window.location.reload();
			}
		});
	});

	/* Doesnt used*/
	$('.wfgm-no-thanks').click(function(e) {
		e.preventDefault();
		$('.wfgm-popup, .wfgm-overlay').fadeOut(500, function() {
			$(this).remove();
		});
	});
	if( $('.wfgm-popup, .wfgm-overlay').length ) {
		setTimeout( function() {
			$('.wfgm-popup, .wfgm-overlay').fadeIn(1300);
		}, 700);



		var wfgmCheckboxes = $('.wfgm-checkbox');
		wfgmCheckboxes.click(function() {
			if(  WFGM_SPECIFIC.gifts_allowed <= 0 ) {
				return;
			}

			if( $('.wfgm-checkbox:checked').length >= WFGM_SPECIFIC.gifts_allowed ) {
				wfgmCheckboxes.not('.wfgm-checkbox:checked').attr('disabled', 'disabled').parent().addClass( "opaque" );
			} else {
				wfgmCheckboxes.removeAttr('disabled').parent().removeClass( "opaque" );
			}
		})
	}
	if( $('.wfgm-so-popup, .wfgm-overlay').length ) {
		setTimeout( function() {
			$('.wfgm-so-popup, .wfgm-overlay').fadeIn(1300);
		}, 700);

		$('.wfgm-no-thanks').click(function(e) {
			e.preventDefault();
			$('.wfgm-so-popup, .wfgm-overlay').fadeOut(500, function() {
				$(this).remove();
			});
		});
	}

	$('.wfgm-fixed-notice-remove').click(function() {
		$(this).closest('.wfgm-fixed-notice').fadeOut(1000);
	});
});

/* use as handler for resize*/
jQuery(window).resize(wfgmAdjustLayout);
/* call function in ready handler*/
jQuery(document).ready(function(){
    wfgmAdjustLayout();
    /* Resize ma adjust garnay cod sabai yesma haalnay*/
});

function wfgmAdjustLayout(){
	jQuery('.wfgm-popup').css({
		position:'fixed',
		right: (jQuery(window).width() - jQuery('.wfgm-popup').outerWidth())/20,
		top: (jQuery(window).height() - jQuery('.wfgm-popup').outerHeight())/20
	});

}

function wfgmAdjustLayout(){
	jQuery('.wfgm-so-popup').css({
		position:'fixed',
		right: (jQuery(window).width() - jQuery('.wfgm-so-popup').outerWidth())/20,
		top: (jQuery(window).height() - jQuery('.wfgm-so-popup').outerHeight())/20
	});

}