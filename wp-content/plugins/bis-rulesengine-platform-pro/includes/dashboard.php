<div class="panel panel-default">
    <div class="panel-heading">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-2">
                    <h3 class="panel-title"><label><?php _e("Dashboard", BIS_RULES_ENGINE_TEXT_DOMAIN);?></label></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
    <div class="container-fluid">
    <div>
        <ul role="tablist" class="nav nav-tabs" id="myTabs">
            <li class="active" role="presentation">
                <a aria-expanded="false" aria-controls="installed" data-toggle="tab" 
                   role="tab" id="home-tab" href="#installed">
                       <?php _e("Active", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </a>
            </li>
            <li class="deactive" role="presentation">
                <a aria-expanded="false" aria-controls="inactive" data-toggle="tab" 
                   role="tab" id="inactive-tab" href="#inactive">
                       <?php _e("Inactive", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </a>
            </li>
            <li role="presentation" class="">
                <a aria-controls="available" data-toggle="tab" id="profile-tab" role="tab" 
                   href="#available" aria-expanded="true">
                       <?php _e("Available Plugins", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </a>
            </li>
            <?php if (is_network_admin()) { ?>
            <li role="presentation" class="">
                <a aria-controls="settings" data-toggle="tab" id="profile-tab" role="tab" 
                   href="#settings" aria-expanded="true"> 
                       <?php _e("Settings", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </a>
            </li>
            <?php } ?>

        </ul>
        <div class="tab-content" id="myTabContent">
            <div aria-labelledby="home-tab" id="installed" class="tab-pane fade active in" role="tabpanel">
                <div class="row" style="margin-top: 20px;">
                    <?php if (!is_network_admin()) { ?>
                    <a href="admin.php?page=bis_pg_rulesengine">
                        <div class="col-md-3">
                            <div class="icon-placeholder">
                                <span class="logical-rule-icon"></span>
                                <h3><?php _e("Logical Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define your business rules.", BIS_RULES_ENGINE_TEXT_DOMAIN) ?></P>
                            </div>
                        </div>
                    </a>
                    <?php } else { ?>
                        <div class="col-md-3">
                            <div class="icon-placeholder">
                                <span class="logical-rule-icon"></span>
                                <h3><?php _e("Logical Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define your business rules.", BIS_RULES_ENGINE_TEXT_DOMAIN) ?></P>
                            </div>
                        </div>
                    <?php } 
                    $bis_addons  = RulesEngineUtil::get_option(BIS_RULESENGINE_ADDONS_CONST);
                    
                    if($bis_addons != null) {
                        $bis_active_rules_json = json_decode($bis_addons);
                        foreach ($bis_active_rules_json as $ac_rule) {
                            if($ac_rule->status == '1') {
                             
                            if(!is_network_admin()) {    
                        ?>
                            <a href="<?php echo $ac_rule->path ?>">
                                <div class="col-md-3">
                                    <div class="icon-placeholder">    
                                        <span class="<?php echo $ac_rule->cssClass ?>"></span>
                                        <h3><?php _e($ac_rule->displayName, BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                        <p><?php _e($ac_rule->description, BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                                    </div>
                                </div>
                            </a>
                        <?php   
                            } else { // End of Network Admin If ?>
                            <div class="col-md-3">
                                <div class="icon-placeholder">    
                                    <span class="<?php echo $ac_rule->cssClass ?>"></span>
                                    <h3><?php _e($ac_rule->displayName, BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                    <p><?php _e($ac_rule->description, BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                                </div>
                            </div>
                        <?php } // End of Network admin else 
                            }// If condition                    
                            } // For loop
                            } // Bis Addons
                    
                     ?>
                    
                </div> 
            </div>
            <div aria-labelledby="inactive-tab" id="inactive" class="tab-pane fade" role="tabpanel">
                <div class="row" style="margin-top: 20px;">
                    <?php
                    $bis_addons  = RulesEngineUtil::get_option(BIS_RULESENGINE_ADDONS_CONST);
                    $no_inactive = true;
                 
                    if($bis_addons != null) {
                        $bis_active_rules_json = json_decode($bis_addons);
                        foreach ($bis_active_rules_json as $ac_rule) {
                            if($ac_rule->status == '0') {
                            $no_inactive = false;      
                            
                    ?>
                        <div class="col-md-3">
                            <div class="icon-placeholder-disable">    
                                <span class="<?php echo $ac_rule->cssClass ?>"></span>
                                <h3><?php _e($ac_rule->displayName, BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e($ac_rule->description, BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    <?php
                            } // If condition                    
                        } // For loop
                    } // Bis Addons
                    
                    if($no_inactive) { ?>
                    
                    <div class="alert alert-info" role="alert">No inactive plugins.</div>
                    <?php } ?>

                </div> 
            </div>
            <div aria-labelledby="available-tab" id="available" class="tab-pane fade" role="tabpanel">
                <div class="row" style="margin-top: 20px;">
                    <a target="_blank" href="http://rulesengine.in/page-and-menu-controller-2/">
                        <div class="col-md-3">
                            <div class="icon-placeholder">    
                                <span class="page-rule-icon"></span>
                                <h3><?php _e("Page Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define rules for hiding, appending and replacing content for pages.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    </a>

                    <a target="_blank" href="http://wiki-rulesengine.wc.lt/post-rules/">
                        <div class="col-md-3">
                            <div class="icon-placeholder">   
                                <span class="post-rule-icon"></span>
                                <h3><?php _e("Post Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define rules for hiding, appending and replacing content for posts.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    </a>
                    <a target="_blank" href="http://rulesengine.in/category-controller/">
                        <div class="col-md-3">
                            <div class="icon-placeholder">
                                <span class="category-rule-icon"></span>
                                <h3><?php _e("Category Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define rules for show or hide categories.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    </a>
                    <a target="_blank" href="http://wiki-rulesengine.wc.lt/widget-rules/" >
                        <div class="col-md-3">
                            <div class="icon-placeholder">
                                <span class="widget-rule-icon"></span>
                                <h3><?php _e("Widget Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define rules for show or hide widgets.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    </a>
                    <a target="_blank" href="http://wiki-rulesengine.wc.lt/theme-rules/">
                        <div class="col-md-3">
                            <div class="icon-placeholder">
                                <span class="theme-rule-icon"></span>
                                <h3><?php _e("Theme Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define rules for switching themes.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    </a>
                    <a target="_blank" href="http://rulesengine.in/language-switcher/">
                        <div class="col-md-3">
                            <div class="icon-placeholder">
                                <span class="language-rule-icon"></span>
                                <h3><?php _e("Language Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define rules for switching languages.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    </a>            
                    <a target="_blank" href="http://rulesengine.in/redirect-rules/" >
                        <div class="col-md-3">
                            <div class="icon-placeholder">
                                <span class="redirect-rule-icon"></span>
                                <h3><?php _e("Redirect Rules", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Define rules for url redirection.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div aria-labelledby="settings-tab" id="settings" class="tab-pane fade" role="tabpanel">
                <div class="row" style="margin-top: 20px;">
                    <a href="admin.php?page=bis_pg_settings">
                        <div class="col-md-3">
                            <div class="icon-placeholder">
                                <span class="settings-rule-icon"></span>
                                <h3><?php _e("Settings", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
                                <p><?php _e("Configure settings for Rules Engine.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></P>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>        


           
               
            
