jQuery(document).ready (function () {
    //When window load and change self size to mobile or to desk version swap 'id' for correct linked
    var isMinVer;

    function firstLoad() {
        var SelectID = 'tab-reviews2';
        jQuery('#' + SelectID + ' input, ' +
            '#' + SelectID + ' div, ' +
            '#' + SelectID + ' p, ' +
            '#' + SelectID + ' h2, ' +
            '#' + SelectID + ' h3, ' +
            '#' + SelectID + ' textarea, ' +
            '#' + SelectID + ' select, ' +
            '#' + SelectID + ' li, ' +
            '#' + SelectID + ' a, ' +
            '#' + SelectID + ' form').each(function () {
                if (jQuery(this).attr('id')) {
                    jQuery(this).attr('id', this.id + '2');

                }
        });
    }

    function funcNewID(varOld, varNew) {
        jQuery("#default-accordion #" + varOld).attr('id', varNew);
    }

    function funcSwapIDs(SelectID, SelectID2) {
        var str;
        jQuery('#' + SelectID + ' input, ' +
            '#' + SelectID + ' div, ' +
            '#' + SelectID + ' p, ' +
            '#' + SelectID + ' h2, ' +
            '#' + SelectID + ' h3, ' +
            '#' + SelectID + ' textarea, ' +
            '#' + SelectID + ' select, ' +
            '#' + SelectID + ' li, ' +
            '#' + SelectID + ' a, ' +
            '#' + SelectID + ' form').each(function () {
            if (jQuery(this).attr('id')) {
                str = this.id;
                str = str.substring(0, str.length - 1);
                jQuery(this).attr('id', str);
            }
        });
        jQuery('#' + SelectID2 + ' input, ' +
            '#' + SelectID2 + ' div, ' +
            '#' + SelectID2 + ' p, ' +
            '#' + SelectID2 + ' h2, ' +
            '#' + SelectID2 + ' h3, ' +
            '#' + SelectID2 + ' textarea, ' +
            '#' + SelectID2 + ' select, ' +
            '#' + SelectID2 + ' li, ' +
            '#' + SelectID2 + ' a, ' +
            '#' + SelectID2 + ' form').each(function () {
            if (jQuery(this).attr('id')) {
                jQuery(this).attr('id', this.id + '2');
            }
        });
    }

    function funcSwapID(varNew, varOld) {
        var temp = 'tempid';
        jQuery("#" + varNew).attr('id', temp);
        jQuery("#" + varOld).attr('id', varNew);
        jQuery("#" + temp).attr('id', varOld);
    }

    function funcToMinVersion() {
        isMinVer = true;
        console.log('Swap to Min by child-theme/js/ss.js');
        console.log(jQuery(window).width());
        funcSwapID('tab-reviews', 'tab-reviews2');
        funcSwapIDs('tab-reviews', 'tab-reviews2');
    }

    function funcToMaxVersion() {
        isMinVer = false;
        console.log('Swap to Max by child-theme/js/ss.js');
        console.log(jQuery(window).width());
        funcSwapID('tab-reviews', 'tab-reviews2');
        funcSwapIDs('tab-reviews', 'tab-reviews2');
    }

    if (jQuery(window).width() < 768) {
        firstLoad();
        funcToMinVersion();
    } else {
        firstLoad();
        isMinVer = false;
    }

    jQuery(window).resize(function(){
        if ((jQuery(window).width() < 768) && (!isMinVer)) {
            funcToMinVersion();
        }
        if ((jQuery(window).width() > 768) && (isMinVer)) {
            funcToMaxVersion();
        }
    });

    // When click to stars linked to #tab-reviews
    function funcBefore () {
    }

    function funcName (idata) {
        $.ajax ({
            url: ".php",
            type: "POST",
            data: ({item: idata['item']}),
            dataType: "json",
            success: function(data) {
                //process
            }
        });
    }

    jQuery("div.container.product-main > div.summary.entry-summary > div.product-price-wrap.clearfix > div.star-rating").bind("click", function () {

        var hash = '#tab-reviews';
        var tab;
        if (jQuery(window).width() < 768) {
            tab = '#product-reviews';
        } else {
            tab = '#tab-reviews';
        }

        if (jQuery(window).width() < 768) {//minScreen version
            jQuery("#accordion .panel-collapse.collapse").removeClass('in');
            jQuery("#accordion .panel-collapse.collapse").attr('aria-expanded', false);
            jQuery("#accordion .panel-collapse.collapse").css('height', '0px');

            jQuery(tab).addClass('in');
            jQuery(tab).attr('aria-expanded', true);
            jQuery(tab).css('height', 'auto');
        } else {//maxScreen version
            jQuery("#default-accordion .panel.entry-content.wc-tab").hide();
            jQuery("#default-accordion ul.tabs.wc-tabs > li").removeClass('active');

            jQuery("#default-accordion ul.tabs.wc-tabs > li.reviews_tab").addClass('active');
            jQuery(tab).show();
        }

        //jQuery('#accordion').tabs('select', tab);
        jQuery('html, body').animate({
            scrollTop: jQuery(hash).offset().top
        }, 800, function(){

            // Add hash (#) to URL when done scrolling (default click behavior)
            window.location.hash = hash;
        });
    });

});