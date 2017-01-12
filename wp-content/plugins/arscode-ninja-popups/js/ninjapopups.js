/*!
 * Ninja Popups for WordPress
 * http://codecanyon.net/item/ninja-popups-for-wordpress/3476479?ref=arscode
 *
 * Copyright 2015, ARSCode
 */
var snp_timer;
var snp_timer_o;
var snp_is_internal_link;
function snp_ga(category, action, label, value)
{
    if (!snp_enable_analytics_events || typeof ga !== "function")
    {
        return;
    }
    ga('send', 'event', category, action, label, value);
}
function snp_set_cookie(name, value, expires)
{
    if (expires == -2)
    {
        return
    }
    if (expires != -1)
    {
        expires = expires * 1;
        var args = {path: '/', expires: expires};
    }
    else
    {
        var args = {path: '/'};
    }
    if (snp_ignore_cookies === undefined || snp_ignore_cookies == false)
    {
        jQuery.cookie(snp_cookie_prefix + '' +name, value, args);
    }
}
function snp_close()
{
    if (jQuery.fancybox2 !== undefined && jQuery.fancybox2.isOpen)
    {
        jQuery.fancybox2.close();
    }
    else
    {
        var popup = jQuery('#snp_popup').val();
        if (snp_f[popup + '-open'] !== undefined)
        {
            snp_f[popup + '-close']();
            snp_onclose_popup();
        }
    }
}
function snp_onsubmit(form)
{
    var popup = jQuery('#snp_popup').val();
    var popup_ID = parseInt(jQuery('#snp_popup_id').val());
    if (!popup_ID)
    {
        popup_ID = form.parents('.snppopup').find('.snp_popup_id').val();
    }
    var ab_ID = form.parents('.snppopup').find('.snp_popup_ab_id').val();
    if (ab_ID === undefined)
    {
        ab_ID = false;
    }
    var ninja_popups_submit = jQuery.event.trigger({
        type: "ninja_popups_submit",
        popup_id: popup_ID
    });
    if (ninja_popups_submit === false)
    {
        return false;
    }
    var snp_optin_redirect_url = form.parents('.snppopup').find('.snp_optin_redirect_url').val();
    if (form.attr('action') == '#')
    {
        var submit_button = jQuery('input[type="submit"], button[type="submit"]', form);
        var submit_button_width = submit_button.outerWidth();
        var text_loading = submit_button.data('loading');
        var text_success = submit_button.data('success');
        var nextstep = submit_button.data('nextstep');
        var bld_nextstep = submit_button.data('step');
        var text_submit = submit_button.html() ? submit_button.html() : submit_button.val();
        if (text_loading)
        {
            if (!submit_button.hasClass('snp-nomw'))
            {
                submit_button.css('min-width', submit_button_width);
            }
            //text_loading = $('<div/>').html(text_loading).text();
            submit_button.html(text_loading);
            submit_button.val(text_loading);
        }
        var data = {};
        data['action'] = 'snp_popup_submit';
        data['popup_ID'] = popup_ID;
        jQuery('input, select, textarea', form).each(function (key)
        {
            data[this.name] = this.value;
        });
        jQuery.ajax({
            url: snp_ajax_url,
            type: 'POST',
            dataType: 'json',
            data: data,
            success: function (data) {
                jQuery("input, textarea, select", form).removeClass('snp-error');
                if (data.Ok == true)
                {
                    snp_onconvert('optin', popup_ID, ab_ID, (!nextstep && !bld_nextstep ? true : false));
                    jQuery.event.trigger({
                        type: "ninja_popups_submit_success",
                        popup_id: popup_ID
                    });
                    if (snp_optin_redirect_url)
                    {
                        document.location.href = snp_optin_redirect_url;
                    }
                    if (bld_nextstep)
                    {
                        snp_bld_gotostep(popup_ID, bld_nextstep);
                    }
                    else if (nextstep)
                    {
                        var p = submit_button.parents('.snp-fb');
                        p.find('.snp-step-show').fadeOut(function () {
                            jQuery(this).removeClass('snp-step-show');
                            p.find('.snp-step-' + nextstep).fadeIn(function () {
                                jQuery(this).addClass('snp-step-show');
                            });
                        });
                    }
                    else if (text_success)
                    {
                        submit_button.text(text_success);
                        submit_button.val(text_success);
                        setTimeout("snp_close();", 800);
                    }
                    else
                    {
                        snp_close();
                    }
                }
                else
                {
                    if (data.Errors)
                    {
                        jQuery.each(data.Errors, function (index, value) {
                            jQuery("input[name='" + index + "'], textarea[name='" + index + "'], select[name='" + index + "']", form).addClass('snp-error');
                        });
                    }
                    if (text_loading)
                    {
                        submit_button.html(text_submit);
                        submit_button.val(text_submit);
                    }
                    jQuery.event.trigger({
                        type: "ninja_popups_submit_error",
                        popup_id: popup_ID
                    });
                }
            }
        });
        return false;
    }
    else
    {
        var Error = 0;
        jQuery('input[type="text"]', form).each(function (key)
        {
            if (!this.value)
            {
                Error = 1;
                jQuery(this).addClass('snp-error');
            }
            else
            {
                jQuery(this).removeClass('snp-error');
            }
        });
        if (Error == 1)
        {
            return false;
        }
        if (form.attr('target') == '_blank')
        {
            snp_close();
        }
        if (snp_optin_redirect_url)
        {
            document.location.href = snp_optin_redirect_url;
        }
        snp_onconvert('optin', popup_ID, ab_ID);
        return true;
    }
}
function snp_onconvert(type, popup_ID, ab_ID, close)
{
    var popup = jQuery('#snp_popup').val();
    if (!popup_ID)
    {
        var popup_ID = parseInt(jQuery('#snp_popup_id').val());
    }
    if (popup)
    {
        var sufix = '';
        if(snp_separate_cookies)
        {
            sufix = popup_ID;
        }
        var cookie_conversion = jQuery('#' + popup + ' .snp_cookie_conversion').val();
        if (!cookie_conversion)
        {
            cookie_conversion = 30;
        }
        snp_set_cookie('snp_' + popup + sufix, '1', cookie_conversion);
    }
    jQuery.post(
            snp_ajax_url,
            {
                'action': 'snp_popup_stats',
                'type': type,
                'popup_ID': popup_ID,
                'ab_ID': ab_ID
            });
    if (type != 'optin')
    {
        var snp_optin_redirect_url = jQuery('#' + popup).find('.snp_optin_redirect_url').val();
        if (snp_optin_redirect_url)
        {
            document.location.href = snp_optin_redirect_url;
        }
    }
    snp_ga('popup', 'subscribe', popup_ID);
    if (close == true)
    {
        snp_close();
    }
}
function snp_onshare_li()
{
    snp_onconvert('li', 0, false, true);
}
function snp_onshare_gp()
{
    snp_onconvert('gp', 0, false, true);
}
function snp_onclose_popup()
{
    var popup = jQuery('#snp_popup').val();
    var popup_ID = parseInt(jQuery('#snp_popup_id').val());
    var sufix = '';
    if(snp_separate_cookies)
    {
        sufix = popup_ID;
    }
    if (jQuery('#snp_popup').val())
    {
        var cookie_close = jQuery('#' + jQuery('#snp_popup').val() + ' .snp_cookie_close').val();
    }
    else
    {
        cookie_close = -1;
    }
    if (!jQuery.cookie(snp_cookie_prefix + 'snp_' + popup + sufix))
    {
        if (!cookie_close)
        {
            cookie_close = -1;
        }
        snp_set_cookie('snp_' + popup + sufix, '1', cookie_close);
    }
    if (jQuery('#snp_exithref').val())
    {
        // exit popup
        //if(jQuery('#snp_exittarget').val()=='_blank')
        //{
        //	window.open(jQuery('#snp_exithref').val());	
        //}
        //else
        //{
        document.location.href = jQuery('#snp_exithref').val();
        //}
    }
    jQuery.event.trigger({
        type: "ninja_popups_close",
        popup_id: jQuery('#snp_popup_id').val()
    });
    jQuery('.fancybox-overlay').removeClass('snp-pop-' + jQuery('#snp_popup_id').val() + '-overlay');
    jQuery('.snp-wrap').removeClass('snp-pop-' + jQuery('#snp_popup_id').val() + '-wrap');
    jQuery('#snp_popup_theme').val('');
    jQuery('#snp_popup').val('');
    jQuery('#snp_popup_id').val('');
    jQuery('#snp_exithref').val('');
    jQuery('#snp_exittarget').val('');
}
function snp_onstart_popup()
{
    jQuery('.fancybox-overlay').addClass('snp-pop-' + jQuery('#snp_popup_id').val() + '-overlay');
    jQuery('.snp-wrap').addClass('snp-pop-' + jQuery('#snp_popup_id').val() + '-wrap');
    jQuery('.snp-wrap').addClass('snp-pop-' + jQuery('#snp_popup_theme').val() + '-wrap');
    var ab_ID = jQuery('.snp-pop-' + jQuery('#snp_popup_id').val()).find('.snp_popup_ab_id').val();
    if (ab_ID === undefined)
    {
        ab_ID = false;
    }
    jQuery.post(
            snp_ajax_url,
            {
                'action': 'snp_popup_stats',
                'type': 'view',
                'popup_ID': jQuery('#snp_popup_id').val(),
                'ab_ID': ab_ID,
            });
}
function snp_open_popup(href, target, popup, type)
{
    if (jQuery.fancybox2 !== undefined && jQuery.fancybox2.isOpen)
    {
        return;
    }
    if (snp_enable_mobile == false && type != 'content' && jQuery.browser.mobile == true)
    {
        return;
    }
    if ((snp_ignore_cookies !== undefined && snp_ignore_cookies == true) || type == 'content')
    {
    }
    else
    {
        var sufix = '';
        if(snp_separate_cookies)
        {
            sufix = jQuery('#' + popup + ' >  .snp_popup_id').val();
        }
        if (jQuery.cookie(snp_cookie_prefix + 'snp_' + popup + sufix) == 1) {
            return true;
        }
    }
    var snp_autoclose = parseInt(jQuery('#' + popup + ' .snp_autoclose').val());
    var snp_show_cb_button = jQuery('#' + popup + ' .snp_show_cb_button').val();
    if (snp_autoclose)
    {
        snp_timer = setTimeout("snp_close()", snp_autoclose * 1000);
        jQuery('#' + popup + ' input').focus(function () {
            clearTimeout(snp_timer);
        });
    }
    var snp_overlay = jQuery('#' + popup + ' .snp_overlay').val();
    jQuery('#snp_popup').val(popup);
    jQuery('#snp_popup_id').val(jQuery('#' + popup + ' >  .snp_popup_id').val());
    jQuery('#snp_popup_theme').val(jQuery('#' + popup + ' >  .snp_popup_theme').val());
    jQuery('#snp_exithref').val(href);
    jQuery('#snp_exittarget').val(target);
    snp_ga('popup', 'open', jQuery('#snp_popup_id').val());
    if (snp_f[popup + '-open'] !== undefined)
    {
        jQuery('#' + popup).appendTo("body");
        snp_f[popup + '-open']();
        snp_onstart_popup();
    }
    else
    {
        var overlay_css = {};
        if (snp_overlay == 'disabled')
        {
            overlay_css.background = 'none';
        }
        jQuery.fancybox2({
            'href': '#' + popup,
            'helpers': {
                'overlay': {
                    'locked': false,
                    'closeClick': false,
                    'showEarly': false,
                    'speedOut': 5,
                    'css': overlay_css
                }
            },
            'padding': 0,
            'autoCenter': jQuery.browser.mobile == true ? false : true,
            'autoDimensions': true,
            'titleShow': false,
            //'openEffect': 'none',
            'closeBtn': (snp_show_cb_button == 'yes' ? true : false),
            'keys': {
                'close': (snp_show_cb_button == 'yes' ? [27] : '')
            },
            'showNavArrows': false,
            'wrapCSS': 'snp-wrap',
            'afterClose': function () {
                return snp_onclose_popup()
            },
            'beforeShow': function () {
                return snp_onstart_popup()
            }
        });
    }
    if (jQuery('#' + popup + ' .snp-subscribe-social').length > 0)
    {
        if (typeof FB != 'undefined') {
            FB.Event.subscribe('edge.create', function () {
                snp_onconvert('fb', 0, false, true);
            });
        }
        if (typeof twttr != 'undefined') {
            twttr.events.bind('tweet', function (event) {
                snp_onconvert('tw_t', 0, false, true);
            });
            twttr.events.bind('follow', function (event) {
                snp_onconvert('tw_f', 0, false, true);
            });
        }
        jQuery("#" + popup + " a.pin-it-button").click(function () {
            snp_onconvert('pi', 0, false, true);
        });
    }
    jQuery.event.trigger({
        type: "ninja_popups_open",
        popup_id: jQuery('#snp_popup_id').val()
    });
    return false;
}
function snp_bld_gotostep(popup_id, nextstep)
{
    //var popup = jQuery('#snp_popup').val();
    var p = jQuery('.snp-pop-' + popup_id).find('.snp-builder');
    var cur_step = p.find('.snp-bld-showme');
    var next_step = p.find('.snp-bld-step-' + nextstep);
                                                                            //tutaj
    if (cur_step.data('animation-close') !== undefined)
    {
        cur_step.removeClass('animated ' + cur_step.attr('data-animation'));
        cur_step.addClass('animated ' + cur_step.attr('data-animation-close')).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            jQuery(this).removeClass('animated ' + jQuery(this).attr('data-animation-close'));
            jQuery(this).removeClass('snp-bld-showme');
        });
    }
    else
    {
        cur_step.removeClass('snp-bld-showme');
    }
    if (next_step.attr('data-animation') !== undefined)
    {
        next_step.addClass('animated ' + next_step.attr('data-animation')).addClass('snp-bld-showme').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            jQuery(this).removeClass('animated ' + jQuery(this).attr('data-animation'));
        });
    }
    else
    {
        next_step.addClass('snp-bld-showme');
    }
    snp_resp(nextstep);
    if (next_step.attr('data-overlay') == 'disabled')
    {
        p.next('.snp-overlay').removeClass('snp-overlay-show');
    }
    else
    {
        p.next('.snp-overlay').addClass('snp-overlay-show');
    }
    if (jQuery("#snp-bld-step-bg-" + nextstep).length > 0)
    {
        jQuery("#snp-bld-step-bg-" + nextstep).mb_YTPlayer();
    }
    snp_stop_video(cur_step);
    snp_start_video(next_step);
    snp_init_map(next_step);
    snp_ga('popup', 'step' + nextstep, jQuery('#snp_popup_id').val());
}
function snp_start_video(obj)
{
    obj.find('.snp-bld-video').each(function(){
            var url = jQuery(this).attr('data-src') + jQuery(this).attr('data-autoplay');
            jQuery(this).attr('src', url);
        });
}
function snp_stop_video(obj)
{
    obj.find('.snp-bld-video').each(function(){
            var url = jQuery(this).attr('data-src');
            jQuery(this).attr('src', url);
        });
}
function snp_init_map(obj)
{
    obj.find('.snp-bld-googleMap').each(function(){
        jQuery(this).height( jQuery(this).parent().height() );
        jQuery(this).width( jQuery(this).parent().width() );
        var mapType;
        switch(jQuery(this).attr('data-mapType')){
            case 'ROADMAP':
                mapType = google.maps.MapTypeId.ROADMAP;
                break;
            case 'SATELLITE':
                mapType = google.maps.MapTypeId.SATELLITE;
                break;
            case 'HYBRID':
                mapType = google.maps.MapTypeId.HYBRID;
                break;
            case 'TERRAIN':
                mapType = google.maps.MapTypeId.TERRAIN;
                break;
        }
        var mapProp = {
            center: new google.maps.LatLng(parseFloat(jQuery(this).attr('data-coordx')),parseFloat(jQuery(this).attr('data-coordy'))),
            zoom: parseInt(jQuery(this).attr('data-zoom')),
            mapTypeId: mapType,
        };
        var element = jQuery(this);
        var map = new google.maps.Map(element[0], mapProp);
        var point = new google.maps.LatLng(parseFloat(jQuery(this).attr('data-coordx')), parseFloat(jQuery(this).attr('data-coordy')));
        var opts = {
            position: point,
            map: map,
            icon: jQuery(this).attr('data-icon'),
        };
        var marker = new google.maps.Marker(opts);
    });
}
jQuery(document).ready(function ($) {
    jQuery(window).resize(function(){snp_resp(false);});
    jQuery(".snp_nothanks, .snp_closelink, .snp-close-link").click(function () {
        snp_close();
        return false;
    });
    jQuery(".snp_subscribeform").submit(function () {
        return snp_onsubmit(jQuery(this));
    });
    if (jQuery('#snppopup-welcome').length > 0)
    {
        var snp_open = jQuery('#snppopup-welcome .snp_open').val();
        var snp_open_after = jQuery('#snppopup-welcome .snp_open_after').val();
        var snp_open_inactivity = jQuery('#snppopup-welcome .snp_open_inactivity').val();
        var snp_open_scroll = jQuery('#snppopup-welcome .snp_open_scroll').val();
        var snp_op_welcome = false;
        if (snp_open === 'inactivity')
        {
            var snp_idletime = 0;
            function snp_timerIncrement()
            {
                snp_idletime++;
                if (snp_idletime > snp_open_inactivity)
                {
                    window.clearTimeout(snp_idleInterval);
                    snp_open_popup('', '', 'snppopup-welcome', 'welcome');
                }
            }
            var snp_idleInterval = setInterval(snp_timerIncrement, 1000);
            jQuery(this).mousemove(function (e) {
                snp_idletime = 0;
            });
            jQuery(this).keypress(function (e) {
                snp_idletime = 0;
            });
        }
        else if (snp_open === 'scroll')
        {
            jQuery(window).scroll(function () {
                var h = jQuery(document).height() - jQuery(window).height();
                var sp = jQuery(window).scrollTop();
                var p = parseInt(sp / h * 100);
                if (p >= snp_open_scroll && snp_op_welcome == false) {
                    snp_open_popup('', '', 'snppopup-welcome', 'welcome');
                    snp_op_welcome = true;
                }
            });
        }
        else
        {
            if (snp_open_after)
            {
                snp_timer_o = setTimeout("snp_open_popup('','','snppopup-welcome','welcome');", snp_open_after * 1000);
            }
            else
            {
                snp_open_popup('', '', 'snppopup-welcome', 'welcome');
            }
        }
    }
    if (jQuery('#snppopup-exit').length > 0)
    {
        var snp_show_on_exit = jQuery('#snppopup-exit .snp_show_on_exit').val();
        if (snp_show_on_exit == 2)
        {
            jQuery("a").click(function () {
                if(jQuery(this).hasClass('noexitpopup'))
                {
                    snp_is_internal_link = true;
                }
                else if (jQuery(this).hasClass('snppopup'))
                {
                    return snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                }
                else
                {
                    var url = jQuery(this).attr("href");
                    if(url === undefined)
                    {
                        snp_is_internal_link = true;
                    }
                    else
                    { 
                        if (url.slice(0, 1) == "#")
                        {
                            return;
                        }
                        if (url.length > 0 && !snp_hostname.test(url) && snp_http.test(url))
                        {
                            if (jQuery.inArray(url, snp_excluded_urls) == -1)
                            {
                                snp_is_internal_link = false;
                            }
                            else
                            {
                                snp_is_internal_link = true;
                            }
                        }
                        else
                        {
                            snp_is_internal_link = true;
                        }
                    }
                }
            });
            jQuery(window).bind('beforeunload', function (e) {
                if (jQuery.cookie(snp_cookie_prefix + 'snp_snppopup-exit') == 1 && snp_ignore_cookies == false)
                    return;
                if (jQuery.fancybox2 !== undefined && jQuery.fancybox2.isOpen)
                    return;
                if (snp_is_internal_link == true)
                    return;
                setTimeout("snp_open_popup(jQuery(this).attr('href'),jQuery(this).attr('target'),'snppopup-exit','exit');", 1000);
                var e = e || window.event;
                if (e) {
                    e.returnValue = jQuery('#snppopup-exit .snp_exit_js_alert_text').val().replace(/\\n/g, "\n");
                }
                return jQuery('#snppopup-exit .snp_exit_js_alert_text').val().replace(/\\n/g, "\n");
            });
        }
        else if (snp_show_on_exit == 3)
        {
            var snp_op_exit = false;
            jQuery(document).bind('mouseleave', function (e) {
                var rightD = jQuery(window).width() - e.pageX;
                if (snp_op_exit == false && rightD > 20) {
                    snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                    snp_op_exit = true;
                }
            });
        }
        else
        {
            if (snp_use_in_all)
            {
                jQuery("a:not(.snppopup)").click(function () {
                    if (jQuery(this).hasClass('snppopup'))
                    {
                        return snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                    }
                    else
                    {
                        var url = jQuery(this).attr("href");
                        if (!snp_hostname.test(url) && url.slice(0, 1) != "#" && snp_http.test(url))
                        {
                            if (jQuery.inArray(url, snp_excluded_urls) == -1)
                            {
                                return snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
                            }
                        }
                    }
                });
            }
            jQuery("a.snppopup").click(function () {
                return snp_open_popup(jQuery(this).attr('href'), jQuery(this).attr('target'), 'snppopup-exit', 'exit');
            });
        }
    }
    jQuery('.snp-submit').click(function () {
        $(this).blur();
    });
    jQuery("a.snppopup-content, a[href^='#ninja-popup-']").click(function () {
        var id = jQuery(this).attr('rel');
        if (!id)
        {
            id = jQuery(this).attr('href').replace('#ninja-popup-', '');
        }
        if (id)
        {
            return snp_open_popup('', '', 'snppopup-content-' + id, 'content');
        }
    });
    jQuery('.snp_nextstep').click(function () {
        var nextstep = $(this).data('nextstep');
        var p = $(this).parents('.snp-fb');
        p.find('.snp-step-show').fadeOut(function () {
            jQuery(this).removeClass('snp-step-show');
            p.find('.snp-step-' + nextstep).fadeIn(function () {
                jQuery(this).addClass('snp-step-show');
            })
        });
        snp_ga('popup', 'step' + nextstep, jQuery('#snp_popup_id').val());
        return false;
    });
    jQuery('.snp-overlay').click(function () {
        if ($(this).attr('data-close') == 'yes')
        {
            snp_close();
            //return false;
        }
    });
    jQuery('.snp-bld-gotostep').click(function () {
        var nextstep = $(this).data('step');
        var popup_ID = $(this).parents('.snppopup').find('.snp_popup_id').val();
        snp_bld_gotostep(popup_ID, nextstep);
        return false;
    });
});
function snp_resp(nextstep)
{
    var popup = jQuery('#snp_popup').val();
    if (popup !== undefined)
    {
        var p = jQuery('#' + popup).find('.snp-builder');
        if (nextstep === false)
        {
            var cur_step = p.find('.snp-bld-showme');
        }
        else
        {
            var cur_step = p.find('#snp-bld-step-' + nextstep);
        }
        var maxHeight = jQuery(window).height();
        var maxWidth = jQuery(window).width();
        var scaleX = maxWidth / cur_step.data('width');
        var scaleY = maxHeight / cur_step.data('height');
        var scale = ((scaleX > scaleY) ? scaleY : scaleX) - 0.01;
        if (scale > 1)
            scale = 1;
        var parent = cur_step.parent('.snp-bld-step-cont');
        if (scale < 1)
        {
            parent.css('transform', 'translateX(-50%) translateY(-50%) scale(' + scale + ')');
            parent.css('-webkit-transform', 'translateX(-50%) translateY(-50%) scale(' + scale + ')');
            parent.css('-moz-transform', 'translateX(-50%) translateY(-50%) scale(' + scale + ')');
            parent.css('-ms-transform', 'translateX(-50%) translateY(-50%) scale(' + scale + ')');
            parent.css('top', '50%');
            parent.css('left', '50%');
            parent.css('right', 'auto');
            parent.css('bottom', 'auto');
        }
        else
        {
            parent.css('transform', "");
            parent.css('-webkit-transform', "");
            parent.css('-moz-transform', "");
            parent.css('-ms-transform', "");
            parent.css('top', "");
            parent.css('left', "");
            parent.css('right', "");
            parent.css('bottom', "");
        }
    }
}
function _snp_bld_open(ID)
{
    var step1 = jQuery('.snp-pop-' + ID + ' .snp-builder').not('.snp-pos-static').addClass('snp-bld-showme').find('.snp-bld-step-1');
    step1.addClass('snp-bld-showme');
    snp_start_video(step1);
    snp_init_map(step1);
    if (step1.attr('data-overlay') != 'disabled')
    {
        jQuery('.snp-pop-' + ID + ' .snp-overlay').addClass('snp-overlay-show');
    }
    if (jQuery("#snp-bld-step-bg-1").length > 0)
    {
        jQuery("#snp-bld-step-bg-1").mb_YTPlayer();
    }
    snp_resp(1);
}
function _snp_bld_close(ID)
{
    var p = jQuery('.snp-pop-' + ID + ' .snp-builder').not('.snp-pos-static');
    var cur_step = p.find('.snp-bld-showme');
    snp_stop_video(cur_step);
    if (cur_step.data('animation-close') !== undefined)
    {
        cur_step.removeClass('animated ' + cur_step.attr('data-animation'));
        cur_step.addClass('animated ' + cur_step.attr('data-animation-close')).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
            jQuery(this).removeClass('animated ' + jQuery(this).attr('data-animation-close'));
            jQuery(this).removeClass('snp-bld-showme');
        });
    }
    else
    {
        cur_step.removeClass('snp-bld-showme');
    }
    p.removeClass('snp-bld-showme');
    jQuery('.snp-pop-' + ID + ' .snp-overlay').removeClass('snp-overlay-show');
}
(function (a) {
    (jQuery.browser = jQuery.browser || {}).mobile = /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))
})(navigator.userAgent || navigator.vendor || window.opera);