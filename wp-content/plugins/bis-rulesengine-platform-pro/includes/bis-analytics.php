<?php
$bis_delete_db = RulesEngineUtil::get_option(BIS_RULES_ENGINE_DELETE_DB);
$installed_version = RulesEngineUtil::get_option(BIS_RULES_ENGINE_VERSION_CONST);
$cat_addon_installed_version = RulesEngineUtil::get_option("BIS_CATEGORY_CONTROLLER_VERSION");
$allowable_tags = RulesEngineUtil::get_option(BIS_RULES_ENGINE_ALLOWABLE_TAGS_CONST);
$bis_geoname_user = RulesEngineUtil::get_option(BIS_GEO_NAME_USER);

$checked = '';
if ($bis_delete_db === "true") {
    $checked = "checked";
}
?>
<form name="bisAnalyticsForm" id="bisAnalyticsForm" method="POST">
    <span id="bis_setting_rule_tab">
        <span id="setting-rules-list-content">
            <!-- Search Pannel -->
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-4">
                                <h3 class="panel-title"><label><?php _e("Rules Engine Analytics", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></label></h3>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="nav nav-tabs  nav-justified" id="bis_re_analytics">
                        <li class="active">
                            <a href="bis_country_requests" data-target="#bis_country_requests" 
                               class="media_node  span" id="bis_country_requests_tab" data-toggle="tabajax" 
                               rel="tooltip"> <?php _e("Requests by Country", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> 
                            </a>
                        </li>
                        <li><a href="bis_device_requests" data-target="#bis_device_requests" 
                               class="media_node span" id="bis_device_requests_tab" data-toggle="tabajax" rel="tooltip">
                                <?php _e("Requests by Device", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></a>
                            </a>
                        </li>
                        <li><a href="bis_redirect_analytics" data-target="#bis_redirect_analytics" 
                               class="media_node span" id="bis_redirect_analytics_tab" data-toggle="tabajax" 
                               rel="tooltip"><?php _e("Redirects by Device", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                            </a>
                        </li>
                        <li><a href="bis_redirect_device_type" data-target="#bis_redirect_device_type" 
                               class="media_node span" id="bis_redirect_device_type_tab" data-toggle="tabajax" 
                               rel="tooltip"><?php _e("Redirects by Device Type", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                            </a>
                        </li>
                        <li><a href="bis_page_views" data-target="#bis_page_views" 
                               class="media_node span" id="bis_page_views_tab" data-toggle="tabajax" 
                               rel="tooltip"><?php _e("Page Views", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                            </a>
                        </li>
                        <li><a href="bis_unique_visitor" data-target="#bis_unique_visitor" 
                               class="media_node span" id="bis_unique_visitor_tab" data-toggle="tabajax" 
                               rel="tooltip"><?php _e("Unique Visitors", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content" style="margin-top: 20px;">
                        <div class="tab-panel active" id="bis_country_requests">
                            <?php include 'bis-country-analytics.php'; ?>
                        </div>
                        <div class="tab-panel" id="bis_device_requests">

                        </div>
                        <div class="tab-panel" id="bis_redirect_analytics">
                        </div>

                        <div class="tab-panel" id="bis_redirect_device_type">
                        </div>

                        <div class="tab-panel" id="bis_page_views">
                        </div>

                        <div class="tab-panel" id="bis_unique_visitor">

                        </div>
                    </div>
                </div> 
            </div>
        </span> 
    </span>
</form>
<script>
    jQuery(document).ready(function () {

        // Load default tab.

        jQuery('[data-toggle="tabajax"]').click(function (e) {

            var loadurl = jQuery(this).attr('href');
            var targ = jQuery(this).attr('data-target');

            jQuery(".tab-panel").html("");
            
            var jqXHR = jQuery.get(BISAjax.ajaxurl,
                    {
                        action: loadurl,
                        bis_nonce: BISAjax.bis_rules_engine_nonce
                    });

            jqXHR.done(function (data) {
                jQuery(targ).html(data);
            });

            jQuery(this).tab('show');
            return false;
        });
    });

</script>    