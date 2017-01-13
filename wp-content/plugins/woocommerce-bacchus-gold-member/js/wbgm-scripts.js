/**
 * @file wbgm-scripts.js
 *
 * Frontend core script for Woocommerce Bacchus Gold Member plugin.
 *
 * Copyright (c) 2016, Yevgen <yevgen.slyuzkin@gmail.com>
 */
jQuery(document).ready(function() {
	setTimeout( function() {
		jQuery('.wbgm-popup, .wbgm-overlay').fadeIn(1300);
	}, 700);

	jQuery('.wbgm-no-thanks').click(function(e) {
		e.preventDefault();
		jQuery('.wbgm-popup, .wbgm-overlay').fadeOut(500, function() {
			jQuery(this).remove();
		});
	});

	jQuery('.wbgm-add-gifts').click(function(e) {
		e.preventDefault();
		var form = jQuery(this).closest('form');
		jQuery.ajax({
			type: 'POST',
			url: form.attr('action'),
			data: form.serialize(),
			success: function( response ) {
				window.location.reload();
			}
		});
	});

	jQuery('.wbgm-gold-member-add-to-cart').click(function(e) {

		if(/*jQuery( "#NoticeAddGold" ).length*/ true)   {

            /*jQuery("#main-container > div > div > div > div.woo-aux-options.clearfix").*/
		} else {
        	/*<ul class="woocommerce-message">
                <li>1 x <a href="https://www.bacchus.de/shop/dornfelder-traubenlikoer-18-vol/">Dornfelder Traubenlikör 18 % Vol.</a> wurde als Gold Artikel hinzugefügt.</li>
            </ul>*/
		}

		e.preventDefault();
		var form = jQuery(this).closest('form');
        var $btn = jQuery(this);
        $btn.button('loading');
		jQuery.ajax({
			type: 'POST',
            url: form.attr('action'),
			data: form.serialize(),
			success: function( response ) {
                /*console.log('BTN_Add-to-cart');*/
			},
			complete: function (data) {
                /**For refresh fragment, look in atelier-child-js-function
                * find: "jQuery(document).on("change", ".wbgm-gold-member-add-to-cart", function ()"
                **/
                jQuery('.wbgm-gold-member-add-to-cart').change();
                $btn.button('reset');
            }
		});

	});

	var wbgmCheckboxes = jQuery('.wbgm-checkbox');
	wbgmCheckboxes.click(function() {
		if(  WBGM_SPECIFIC.gifts_allowed <= 0 ) {
			return;
		}

		if( jQuery('.wbgm-checkbox:checked').length >= WBGM_SPECIFIC.gifts_allowed ) {
			wbgmCheckboxes.not('.wbgm-checkbox:checked').attr('disabled', 'disabled').parent().addClass( "opaque" );
		} else {
			wbgmCheckboxes.removeAttr('disabled').parent().removeClass( "opaque" );
		}
	});

    jQuery('.wbgm-fixed-notice-remove').click(function() {
        jQuery(this).closest('.wbgm-fixed-notice').fadeOut(1000);
	});

    jQuery( '<div class="col-sm-6 tb-left"><div class="tb-text"></div></div>' ).insertBefore( ".mobile-header-opts.opts-left" );
    function getfragment() {
        var $fragment_refresh = {
            url: wc_cart_fragments_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'get_refreshed_fragments' ),
            type: 'POST',
            success: function( data ) {
                if ( data && data.fragments ) {

                    jQuery.each( data.fragments, function( key, value ) {
                        jQuery( key ).replaceWith( value );
                    });

                    if ( jQuery.supports_html5_storage ) {
                        sessionStorage.setItem( wc_cart_fragments_params.fragment_name, JSON.stringify( data.fragments ) );
                        set_cart_hash( data.cart_hash );

                        if ( data.cart_hash ) {
                            set_cart_creation_timestamp();
                        }
                    }

                    jQuery( document.body ).trigger( 'wc_fragments_refreshed' );
                }
            }
        };
    jQuery.ajax( $fragment_refresh );
    }

});

/* use as handler for resize*/
jQuery(window).resize(wbgmAdjustLayout);

function wbgmAdjustLayout(){
    jQuery('.wbgm-popup').css({
        position:'fixed',
        left: (jQuery(window).width() - jQuery('.wbgm-popup').outerWidth())/2,
        top: (jQuery(window).height() - jQuery('.wbgm-popup').outerHeight())/2
    });

}