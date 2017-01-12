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
            <a id="bis_help_" class="btn popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE_SITE ?>"
                                 data-content='<?php _e("Click here to know more about RulesEngine.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>' rel="popover"
                                 data-placement="bottom" data-original-title='<?php _e("Help", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>' data-trigger="hover">
                <label style="padding-right: 10px"><?php _e("Help", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></label><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span>
            </a>
        </div>
        <div class="col-md-1">
        </div>
    </div>
        <div class="row">
            <div class="col-md-12">
                <?php

                $bis_show_rule = $_GET["page"];

                switch ($bis_show_rule) {

                    case "bis_pg_rulesengine":
                        RulesEngineUtil::clear_cached_child_rule();
                        require_once 'logical-rules-list.php';
                        break;

                    case "bis_pg_analytics":
			RulesEngineUtil::clear_cached_child_rule();
                        require_once 'bis-analytics.php';
                        break;
                    
                    case "bis_pg_settings":
                        RulesEngineUtil::clear_cached_child_rule();
			$showError = true;

                        if (isset($_GET["deactive"])) {
                            $showError = $_GET["deactive"];
                        }

                        if ($showError === "false") {
                            require_once 'messages.php';
                        } else {
                            if(is_network_admin() || !is_multisite()) {
                                require_once 'rules-settings.php';
                            }
                        }
                        break;

                    case "bis_pg_dashboard":
                        require_once 'dashboard.php';
                        break;
                    
                    default:
                        require_once 'dashboard.php';
                        break;

                }

                ?>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">

    jQuery(document).ready(function () {

        //First time load for logical rules
        var cpage= '<?php echo $bis_show_rule; ?>';

        switch (cpage) {
            case "bis_pg_rulesengine":
                bis_showLogicalRulesList();
                break;

            case "pagerules":
                bis_showPageRulesList();
                break;

            case "postrules":
                bis_showPostRulesList();
                break;

            case "categoryrules":
                bis_showCategoryRulesList();
                break;

            case "widgetrules":
                bis_showWidgetRulesList();
                break;

            case "themerules":
                bis_showThemeRulesList();
                break;

            case "redirectrules":
                bis_showRedirectRulesList();
                break;

        }


        jQuery("#tabs a").click(function (e) {
            e.preventDefault();
            jQuery(this).tab('show');

            if (this.href.indexOf("logicalruleslist") !== -1) {
                bis_showPageRulesList();
            }

            if (this.href.indexOf("pageruleslist") !== -1) {
                bis_showPageRulesList();
            }

            if (this.href.indexOf("postruleslist") !== -1) {
                bis_showPostRulesList();
            }

            if (this.href.indexOf("categoryruleslist") !== -1) {
                bis_showCategoryRulesList();
            }

            if (this.href.indexOf("widgetruleslist") !== -1) {
                bis_showWidgetRulesList();
            }

            if (this.href.indexOf("themeruleslist") !== -1) {
                bis_showThemeRulesList();
            }

            if (this.href.indexOf("redirectruleslist") !== -1) {
                bis_showRedirectRulesList();
            }

        });

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
