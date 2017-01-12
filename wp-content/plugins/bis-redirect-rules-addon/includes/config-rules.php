<div class="body">

<div class="alert alert-success alert-dismissable bis-message-position" id="alert_success_id"
     xmlns="http://www.w3.org/1999/html">
    <button class="close" id="sucessButtonId" type="button"
            data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong><span id="alert_success_message"> </span></strong>
</div>

<div class="alert alert-danger alert-dismissable bis-message-position" id="alert_failure_id">
    <button class="close" id="failureButtonId" type="button" class="close">&times;</button>
    <strong><span id="alert_failure_message"></span></strong>
</div>

<!-- Nav tabs -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10">
            &nbsp;
        </div>
        <div class="col-md-1 pull-right">
            <a id="bis_help_" class="btn popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE ?>"
                                 data-content='<?php _e("Click here to know more about RulesEngine.", "redirectrules"); ?>' rel="popover"
                                 data-placement="bottom" data-original-title='<?php _e("Help", "redirectrules"); ?>' data-trigger="hover">
                <label style="padding-right: 10px"><?php _e("Help", "redirectrules"); ?></label><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
            </a>
        </div>
        <div class="col-md-1">
        </div>
    </div>
        <div class="row">
            <div class="col-md-12">
                <?php
                 require_once 'redirect-rules-list.php';
                ?>
            </div>
        </div>

    </div>
</div>
<div class="page-loader" style="display:block"><?php _e("Please wait....", "rulesengine"); ?></div>
<script type="text/javascript">

    jQuery(document).ready(function () {

        bis_showRedirectRulesList();

        var alertDivWidth = jQuery(".container-fluid").width() - 40;
        jQuery("#alert_failure_id").width(alertDivWidth);
        jQuery("#alert_success_id").width(alertDivWidth);

        jQuery('.popoverData').popover();

        jQuery("#failureButtonId").click(function () {
            jQuery("#alert_failure_id").hide();
        });

        function adjustModalMaxHeightAndPosition() {
            jQuery('.modal').each(function () {

                if (jQuery(this).hasClass('in') == false) {
                    jQuery(this).show();
                }

                var contentHeight = jQuery(window).height() - 60;
                var headerHeight = jQuery(this).find('.modal-header').outerHeight() || 2;
                var footerHeight = jQuery(this).find('.modal-footer').outerHeight() || 2;

                jQuery(this).find('.modal-content').css({
                    'max-height': function () {
                        return contentHeight;
                    }
                });

                jQuery(this).find('.modal-body').css({
                    'max-height': function () {
                        return (contentHeight - (headerHeight + footerHeight));
                    }
                });

                jQuery(this).find('.modal-dialog').css({
                    'margin-top': function () {
                        return -(jQuery(this).outerHeight() / 2);
                    },
                    'margin-left': function () {
                        return -(jQuery(this).outerWidth() / 2);
                    }
                });
                if (jQuery(this).hasClass('in') == false) {
                    jQuery(this).hide();
                }


            });
        }

        jQuery(window).resize(adjustModalMaxHeightAndPosition).trigger("resize");

    });

</script>
