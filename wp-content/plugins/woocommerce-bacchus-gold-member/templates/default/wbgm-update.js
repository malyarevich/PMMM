/**
 * Created by Reno on 12/16/2016.
 */

jQuery(document).ready(function() {

    jQuery.ajax({
        type: 'POST',
        url: window.wp_data.ajax_url,
        data: {
            action: 'wbgm_update_frontend'
        },
        success: function( data ) {
            console.log(data);
            if (data['is_show_bonus_btn']) {
                jQuery('#wbgm-add-to-cart-bonus-form').show();
            } else {
                jQuery('#wbgm-add-to-cart-bonus-form').hide();
            }

            jQuery('.wbgm-top-balance').html(data['balance']);
        }
    });
});