<?php
use bis\repf\common\RulesEngineCacheWrapper;

$logical_rule_engine_modal = new bis\repf\model\LogicalRulesEngineModel();

$ruleIdParent = RulesEngineCacheWrapper::get_session_attribute(BIS_LOGICAL_RULE_ID);
// Get the rule details.
$bis_rule_parent = $logical_rule_engine_modal->get_logical_rule($ruleIdParent);

$add_rule_type = $bis_rule_parent['data']->add_rule_type;
?>
<div id="bis_editlogicalrule">
    <form id="bis-editlogicalruleform" method="post" name="bis-editlogicalruleform">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <label id="bis_re_basic_add_rule">Edit Rule </label>
                </h3>
            </div>

            <div id="bis_re_basic_lr_body" class="panel-body">
                <div class="well">
                    <label class="radio-inline">
                        <input type="radio" name="bis_add_rule_type" id="bis_add_rule_type_1" value="1"> 
                         <?php _e("Standard Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="bis_add_rule_type" id="bis_add_rule_type_2" value="2">
                        <?php _e("Advanced Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="bis_add_rule_type" id="bis_add_rule_type_3" value="3">
                         <?php _e("Use Existing Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                    </label>
                </div>    

                <div id="bis-logical-rule-define">
                    <?php
                    $pluginPath = RulesEngineUtil::getIncludesDirPath();
                    switch($add_rule_type) {
                        case 1: 
                            require_once $pluginPath . "logical-rules-basic-edit-body.php";
                            break;
                        
                        case 2:
                            require_once $pluginPath . "logical-rules-advance-edit-body.php";
                            break;
                        
                        case 3:
                            require_once $pluginPath . "bis-use-existing-rule.php";
                            break;
                    }    
        ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>

    // wait for the DOM to be loaded
    jQuery(document).ready(function () {
        
        var selectedOption = '<?php echo $add_rule_type ?>';
        jQuery("#bis_add_rule_type_"+selectedOption).attr("checked", "checked");
        var req_action = "bis_re_advance_rule_include";
        
        jQuery('[name = "bis_add_rule_type"]').click(function() {
            var loadPage = true;
            switch(this.value) {
                case  '1' :
                        req_action = 'bis_re_basic_rule_edit_include';
                        options.data.action = 'bis_re_update_rule_new';  
                        if(selectedOption === '2') {
                           loadPage = false;
                           jQuery("#bis_add_rule_type_"+selectedOption).attr("checked", "checked");
                           bis_confirm("<h4><?php _e('By converting an advance rule to a standard rule you may loose some conditions. Do you want to continue ?') ?></h4>", function (result) {
                                if (result) {
                                   jQuery("#bis_add_rule_type_1").attr("checked", "checked");
                                   bis_showLogicalRuleCriteria();
                                }    
                            }); 
                        }
                        break;
                case  '2' : 
                        req_action = 'bis_re_advance_rule_edit_include';
                        options.data.action = 'bis_re_update_rule_new';
                        break;
                case  '3' : 
                        req_action = 'bis_re_exising_rule_include';
                        options.data.action = 'bis_re_use_exising_rule';
                        break;        
            }
           
            if(loadPage) {
              bis_showLogicalRuleCriteria();
            }
        });
        
        function bis_showLogicalRuleCriteria() {
                var jqXHR = jQuery.get(ajaxurl,
                    {
                        action: req_action,
                        bis_nonce: BISAjax.bis_rules_engine_nonce
                    });

                jqXHR.done(function (data) {
                        jQuery("#bis-logical-rule-define").html(data);
                });
        }
        
       
        
        var options = {
            success: showResponse,
            url: BISAjax.ajaxurl,
            beforeSubmit: bis_validateAddRulesForm,

            data: {
                action: 'bis_re_update_rule_new',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        var edit_options = {
            url: BISAjax.ajaxurl,
            data: {
                action: jQuery("#bis_child_update_rule").val(),
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        function showResponse(responseText, statusText, xhr, $form) {
            if (responseText["status"] === "success") {
                var callback = jQuery("#bis_child_list_callback").val();
                window[callback]();
            } else {
                if (responseText["status"] === "error") {
                    if (responseText["message_key"] === "duplicate_entry") {
                        bis_showErrorMessage('<?php _e("Duplicate rule name, Name should be unique.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
                    } else if (responseText["message_key"] === "no_method_found") {
                        bis_showErrorMessage('<?php _e("Action hook method does not exist, Please define method with name", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                        +" \""+ jQuery("#bis_re_hook").val() + "\".");
                    } else {
                        bis_showErrorMessage('<?php _e("Error occurred while updating rule.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
                    }
                }
            }
        }
        jQuery('#bis-editlogicalruleform').ajaxForm(options);

    });
</script>