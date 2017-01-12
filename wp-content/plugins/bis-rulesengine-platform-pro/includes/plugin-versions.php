<div class="container-fluid">
    
<div class="panel panel-default">
        <div class="panel-heading">
           <h3 class="panel-title"><?php _e("RulesEngine Platform", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></h3>
        </div>
        <div class="panel-body">
           <h5>Version : <?php echo number_format(floatval($installed_version), 1); ?></h5>
        </div>
</div>

    <?php
    $bis_addons = RulesEngineUtil::get_option(BIS_RULESENGINE_ADDONS_CONST);

    if ($bis_addons != null) {
        $bis_active_rules_json = json_decode($bis_addons);
        foreach ($bis_active_rules_json as $ac_rule) {
            if ($ac_rule->status == '1') {
               
            ?>
    <div id="p_<?php echo $ac_rule->id ?>" class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php _e($ac_rule->displayName, BIS_RULES_ENGINE_TEXT_DOMAIN); ?> </h3>
        </div>
        <div class="panel-body">
            <div class="alert alert-success alert-dismissable bis-message-position" 
                 id="<?php echo $ac_rule->id ?>_sId">
                <button class="close" id="<?php echo $ac_rule->id ?>_close_sId" type="button"
                                    data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong><span id="<?php echo $ac_rule->id ?>_smessage"> </span></strong>
            </div>
            <div class="alert alert-danger alert-dismissable bis-message-position" id="<?php echo $ac_rule->id ?>_fId">
                <button class="close" id="<?php echo $ac_rule->id ?>_close_fId" type="button" class="close">&times;</button>
                <strong><span id="<?php echo $ac_rule->id ?>_fmessage"></span></strong>
            </div>
           <h5>Version :   <?php echo number_format(floatval($ac_rule->version), 1); ?></h5>
       
            <h5><?php _e("Item Purchase Code:", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                <a class="popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE_SITE ?>"
                   data-content='<?php _e("Item purchase code is required.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>' rel="popover"
                   data-placement="bottom" data-trigger="hover">
                    <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                </a>
            </h5>
           <input value="<?php echo RulesEngineUtil::get_purchase_code($ac_rule->id); ?>" type="text" class="form-control" maxlength="150"
                  id="pc_<?php echo $ac_rule->id ?>" name="pc_<?php echo $ac_rule->id ?>" placeholder="<?php _e("Enter item purchase code", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>">

            <div class="bis-verify-lic-div">
                <button onclick="bis_verify_pcode('<?php echo $ac_rule->id ?>', '<?php echo $ac_rule->apiKey ?>')" id="bis_re_pc_<?php echo $ac_rule->id ?>"  class="btn btn-primary pcode-button">
                <i class="glyphicon glyphicon-ok-sign bis-glyphi-position"></i><?php _e("Activate", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </button>
            </div>    
        </div>
    </div>
                <?php
            }     
            
            }
    }
    ?>
    
</div>