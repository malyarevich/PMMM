<form name="rulesSettingsForm" id="rulesSettingsForm"
      method="POST">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <label id="bis_re_basic_add_rule">Geolocation configuration </label>
                        </h3>
                    </div>

                    <div id="bis_re_basic_lr_body" class="panel-body">
                        <div class="well">
                            <div class="row">
                                <div class="col-md-4" >
                                    <label>
                                        <?php
                                       
                                        $bis_geo_plugin_checked = "";
                                        $bis_geo_max_checked = "";
                                        $bis_geo_db_text_style = "display:none";

                                        if ($bis_geo_lookup_webservice_type == 1) {
                                            $bis_geo_plugin_checked = "checked=\"checked\"";
                                        }
                                        if ($bis_geo_lookup_type == 1) {
                                            $bis_geo_max_checked = "checked=\"checked\"";
                                            $bis_geo_db_text_style = "display:block";
                                        }
                                        ?>
                                        <input type="checkbox" <?php echo $bis_geo_max_checked; ?> name="bis_geolocation_db" id="bis_geo_maxmind" value="1"> 
                                        <?php _e("Use MaxMind local database", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                                    </label>
                                    <input <?php echo $bis_geo_db_text_style; ?> value="<?php echo $bis_geo_maxmind_db_file ?>" class="form-control" type="text" id="bis_geo_maxmind_db_file" 
                                                                                 name="bis_geo_maxmind_db_file"    placeholder='<?php _e("Enter MaxMind database file name", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'>
                                </div>
                                <div class="col-md-6">
                                    <label>
                                        <input <?php echo $bis_geo_plugin_checked; ?> type="checkbox" name="bis_geolocation_ws" id="bis_geo_geoloc" value="1">
                                        <?php _e("Use Geoplugin webservice", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                                    </label>
                                </div> 
                            </div>
                            <div class="row">
                                <div class="col-lg-12"> &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    This product includes GeoLite2 data created by MaxMind, available from
                                    <a href="http://www.maxmind.com">http://www.maxmind.com</a>.
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>    
                <!-- /input-group -->
            </div>
        </div>
        <div class="well">
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input <?php echo $bis_re_cache_enabled ?> type="checkbox" 
                                                                       name="bis_re_cache_enabled" id="bis_re_cache_enabled"/>
                        </span>
                        <label class="form-control" style="cursor: default;">
                            <?php _e("Cache enabled for this site", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12"> &nbsp;
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input <?php echo $plugin_force_delete_checked ?> type="checkbox" name="bis_re_del_plugin" id="bis_re_del_plugin"/>
                        </span>
                        <label class="form-control" style="cursor: default;">
                            <?php _e("Force delete Rules Engine Platform plug-in. Plug-in will be deleted without checking any dependent child plug-ins. Use only in rare cases.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                        </label>
                    </div>
                    <!-- /input-group -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12"> &nbsp;
                </div>
            </div>
            <?php 
            if(BIS_RULES_ENGINE_PLATFORM_TYPE === "BIS_RE_PROF_EDITION") {?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input <?php echo $bis_re_enable_analytics ?> type="checkbox" name="bis_re_enable_analytics" id="bis_re_del_plugin"/>
                        </span>
                        <label class="form-control" style="cursor: default;">
                            <?php _e("Capture data for analytics. If unchecked no data will stored for analytics purpose.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                        </label>
                    </div>
                    <!-- /input-group -->
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12"> &nbsp;
                </div>
            </div>
            <?php } ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <span class="input-group-addon">
                            <input <?php echo $checked ?> type="checkbox" name="bis_re_delete_db" id="bis_re_delete_db"/>
                        </span>
                        <label class="form-control" style="cursor: default;">
                            <?php _e("Delete plugin-in tables on plugin uninstall or delete. Uncheck checkbox before plugin-in upgrade.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- /.row -->
        <div class="row">
            <div class="form-group col-md-12">
                <label for="description"><?php _e("Allowable tags", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></label>
                <strong><textarea rows="2" maxlength="500" class="form-control" name="bis_re_allowable_tags"
                                  id="bis_re_description" placeholder='<?php _e("Enter allowable tags", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'><?php echo $allowable_tags ?></textarea>
                </strong>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="description"><?php _e("Geoname User", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></label>
                <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                <strong>
                    <input value="<?php echo $bis_geoname_user ?>" class="form-control" type="text" id="geonameuser" 
                           name="geonameuser"    placeholder='<?php _e("Enter user name", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'>
                </strong>
                <div style="padding-top: 5px;">
                    <a target="_blank" href="http://www.geonames.org/">
                        <span style="font-size: 12px;"><?php _e("Credits", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>: geonames.org</span>
                    </a>
                </div>
            </div>
            <div class="col-lg-4">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 pull-left">
            <button id="bis_re_save_settings" type="submit" class="btn btn-primary">
                <i class="glyphicon glyphicon-ok-sign bis-glyphi-position"></i><?php _e("Save Settings", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
            </button>
        </div>
    </div>
</form>