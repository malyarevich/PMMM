<?php
$bis_delete_db = RulesEngineUtil::get_option(BIS_RULES_ENGINE_DELETE_DB);
$bis_cache_installed = RulesEngineUtil::get_option(BIS_RULES_ENGINE_CACHE_INSTALLED);
$bis_plugin_force_delete = RulesEngineUtil::get_option(BIS_RULES_ENGINE_PLUGIN_FORCE_DELETE);
$bis_capture_analytics = RulesEngineUtil::get_option(BIS_CAPTURE_ANALYTICS_DATA);
$installed_version = RulesEngineUtil::get_option(BIS_RULES_ENGINE_VERSION_CONST);
$cat_addon_installed_version = RulesEngineUtil::get_option("BIS_CATEGORY_CONTROLLER_VERSION");
$allowable_tags = RulesEngineUtil::get_option(BIS_RULES_ENGINE_ALLOWABLE_TAGS_CONST);
$bis_geoname_user = RulesEngineUtil::get_option(BIS_GEO_NAME_USER);
$bis_config_tab = "active in";

$bis_activate_plugins = "";
$bis_config_ac_tab = "active";
$bis_plugin_ac_tab = "deactive";

$bis_geo_lookup_type = RulesEngineUtil::get_option(BIS_GEO_LOOKUP_TYPE);
$bis_geo_lookup_webservice_type = RulesEngineUtil::get_option(BIS_GEO_LOOKUP_WEBSERVICE_TYPE);
$bis_geo_maxmind_db_file =  RulesEngineUtil::get_option(BIS_GEO_MAXMIND_DB_FILE);

if(isset($_GET["stab"])) {
    $bis_activate_plugins = "active in";
    $bis_config_tab = "";
    $bis_config_ac_tab = "deactive";
    $bis_plugin_ac_tab = "active";
}

$checked = '';
if($bis_delete_db === "true") {
    $checked = "checked";
}

$plugin_force_delete_checked = '';

if($bis_plugin_force_delete === "true") {
    $plugin_force_delete_checked = "checked";
}
$bis_re_enable_analytics = '';

if ($bis_capture_analytics === "true") {
    $bis_re_enable_analytics = "checked";
}
$bis_re_cache_enabled = '';

if ($bis_cache_installed === "true") {
    $bis_re_cache_enabled = "checked";
}

?>
<span id="bis_setting_rule_tab">
<span id="setting-rules-list-content">
        <!-- Search Panel -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4">
                            <h3 class="panel-title"><label><?php _e("Rules Engine Settings", BIS_RULES_ENGINE_TEXT_DOMAIN);?></label></h3>
                        </div>

                    </div>
                </div>
            </div>
            <div class="panel-body">
                <ul role="tablist" class="nav nav-tabs" id="myTabs">
                    <li class="<?php echo $bis_config_ac_tab ?>" role="presentation">
                        <a aria-expanded="false" aria-controls="installed" data-toggle="tab" 
                           role="tab" id="config-tab" href="#installed"><?php _e("Configuration", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></a>
                    </li>
                    <li class="<?php echo $bis_plugin_ac_tab ?>" role="presentation">
                        <a aria-expanded="false" aria-controls="active-plugins" data-toggle="tab" 
                           role="tab" id="active-plugins-tab" href="#active-plugins"><?php _e("Activate Plugins", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></a>
                    </li>
                    <li class="deactive" role="presentation">
                        <a aria-expanded="false" aria-controls="trouble-shoot" data-toggle="tab" 
                           role="tab" id="trouble-shoot-tab" href="#trouble-shoot"><?php _e("Trouble Shoot", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent" style="margin-top: 20px;">
                    <div aria-labelledby="config-tab" id="installed" 
                         class="tab-pane fade <?php echo $bis_config_tab; ?>" role="tabpanel">
                        <?php include 'configuration.php'; ?>
                    </div>
                    <div aria-labelledby="active-plugins-tab" id="active-plugins" 
                         class="tab-pane fade <?php echo $bis_activate_plugins ?>" role="tabpanel">
                        <div class="row" style="margin-top: 20px;">
                           <?php include 'plugin-versions.php'; ?>
                        </div>
                    </div>    
                    <div aria-labelledby="trouble-shoot-tab" id="trouble-shoot" 
                         class="tab-pane fade" role="tabpanel">
                        <div class="row" style="margin-top: 20px;">
                           <?php include 'bis-troubleshoot.php'; ?>
                        </div>
                    </div>    
                </div>              
            </div>
        </div>
    </form>
</span>

<script>

    // wait for the DOM to be loaded
    jQuery(document).ready(function () {

        var options = {
            success: bis_showSettingResponse,
            beforeSubmit: bis_validateRulesSettingsForm,
            url: BISAjax.ajaxurl,

            data: {
                action: 'bis_re_save_settings',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        jQuery('#rulesSettingsForm').ajaxForm(options);

        function bis_showSettingResponse(responseText, statusText, xhr, $form) {
            if (responseText["status"] === "success") {
                if(responseText["data"] === "true") {
                    jQuery("#bis_re_delete_db").attr("checked", "checked");
                } else {
                    jQuery("#bis_re_delete_db").removeAttr("checked")
                }

                bis_showSuccessMessage('<?php _e("Plugin settings saved successfully", BIS_RULES_ENGINE_TEXT_DOMAIN);?>');
            } else {
                if(responseText['message_key'] === "bis_invalid_database_file") {
                    bis_showErrorMessage('<?php _e("Invalid MaxMind local database file", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
                } else {     
                    bis_showErrorMessage('<?php _e("Error occured while saving Plugin settings", BIS_RULES_ENGINE_TEXT_DOMAIN);?>');
                }        
            }
        }
        
         jQuery('#bis_geo_maxmind').click(function() {            
            if (jQuery(this).is(':checked')) {
                jQuery("#bis_geo_maxmind_db_file").show();
            } else {
                jQuery("#bis_geo_maxmind_db_file").hide();
            }
        });     
    });
</script>