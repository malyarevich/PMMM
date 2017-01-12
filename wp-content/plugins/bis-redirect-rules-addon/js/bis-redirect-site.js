// wait for the DOM to be loaded
jQuery(document).ready(function () {

    var redCookie = jQuery.cookie("bis_red_cookie");
    var is404 = false;
    
    if (jQuery(".error404").length) {
        is404 = true;
    }

    //if (redCookie == null) {
        jQuery.ajax({
            type: "GET",
            url: jQuery("#bis_re_site_url").val(),
            data: {
                bis_re_action: "bis_re_redirect",
                bis_re_cache_post_id: jQuery("#bis_re_cache_post_id").val(),
                bis_re_cache_cat_id: jQuery("#bis_re_cache_cat_id").val(),
                bis_re_cache_reffer_path: jQuery("#bis_re_cache_reffer_path").val(),
                bis_re_cache_404: is404,
                bis_nonce: BISAjax.bis_rules_engine_nonce},
            success: function (response) {
                if (response.data && response.status !== "success_with_no_data") {
                    if (response.data.popup_data) {
                        if(redCookie == null) {
                            bis_show_redirect_popup(response);
                        } else {
                            if(redCookie === "redirect") {
                                window.location.href = response.data.popup_data.buttonOneUrl;
                            } 
                        }    
                    } else {
                        var action = jQuery.parseJSON(response.data.action);
                        window.location.href = action.target_url;
                    }
                }
            }
        });
    //}

    function bis_meta_redirect(response, targetUrl) {
        var source = jQuery("#bis_redirect_meta_template").html();
        
        if (!source) {
            templateContent = response.data.popup_data.metaTemplate;
            jQuery("body").append('<span id="bis_re_rd_meta_span"></span>');
            jQuery("body").append(templateContent);
            source = jQuery("#bis_redirect_meta_template").html()
        }
        
        var template = Handlebars.compile(source);

        if (targetUrl == null) {
            targetUrl = jQuery.parseJSON(response.data.action);
        }

        var redirectAction = template(targetUrl);
        jQuery("#bis_re_rd_meta_span").html(redirectAction);
    }

    function bis_show_redirect_popup(response) {
        var source = jQuery("#bis_redirect_popup_template").html();
        
        if(!source) {
            templateContent = response.data.popup_data.popupTemplate;
            jQuery("body").append('<div id="bis_re_modal_div"></div>');
            jQuery("body").append(templateContent);
            source = jQuery("#bis_redirect_popup_template").html()
        }
        
        var template = Handlebars.compile(source);
        var redirectPopUp = template(response['data']);
        
        var options = {
            closeOnOutsideClick: true,
            closeOnEscape: true
        }

        var popUp = response['data']['popup_data']

        jQuery("#bis_re_modal_div").html(redirectPopUp);

        if (popUp.popUpBackgroundColor && popUp.popUpBackgroundColor != '') {
            jQuery("#bis_re_redirect_modal").attr("Style", "background-color:" + popUp.popUpBackgroundColor);
        }

        if (popUp.headingOneClass && popUp.headingOneClass != '') {
            jQuery(".redirect-popup-heading").addClass(popUp.headingOneClass);
            jQuery(".redirect-popup-header").removeClass("redirect-popup-heading");
        }

        if (popUp.headingTwoClass && popUp.headingTwoClass != '') {
            jQuery(".redirect-popup-subheading").addClass(popUp.headingTwoClass);
            jQuery(".redirect-popup-subheading").removeClass("redirect-popup-subheading");
        }

        if (popUp.buttonOneClass && popUp.buttonOneClass != '') {
            jQuery(".remodal-confirm").addClass(popUp.buttonOneClass);
            // Uncommment below link to remove completely existing class
            //jQuery(".remodal-confirm").removeClass("remodal-confirm");
        }

        if (popUp.buttonTwoClass && popUp.buttonTwoClass != '') {
            jQuery(".remodal-cancel").addClass(popUp.buttonTwoClass);
            // Uncommment below link to remove completely existing class
            //jQuery(".remodal-cancel").removeClass("remodal-cancel");
        }

        if (popUp.titleClass && popUp.titleClass != '') {
            jQuery(".redirect-popup-title").addClass(popUp.titleClass);
            jQuery(".redirect-popup-title").removeClass("redirect-popup-title");
        }

        var bis_remodal = jQuery('#bis_re_redirect_modal').remodal(options);
        bis_remodal.open();

        var secInterval;

        if (popUp.autoCloseTime !== 0) {
            var counter = Number(popUp.autoCloseTime) / 1000;
            jQuery("#bis_stimer").text(counter);

            secInterval = setInterval(function () {
                counter = counter - 1;
                if (counter <= 0) {
                    window.location.href = popUp.buttonOneUrl;
                } else {
                    jQuery("#bis_stimer").text(counter)
                }
            }, 1000);
        }

        jQuery("#bis_re_cancel_red").click(function () {
            if (jQuery("#bis_red_remem").is(':checked')) {
                bis_setCookie("bis_red_cookie", "cancel", 7);
            }
            clearInterval(secInterval);
        });

        jQuery("#bis_confirm_redirect").click(function () {
          
            if (jQuery("#bis_red_remem").is(':checked')) {
                bis_setCookie("bis_red_cookie", "redirect", 7);
            }
            window.location.href = popUp.buttonOneUrl;
        });
    }

});