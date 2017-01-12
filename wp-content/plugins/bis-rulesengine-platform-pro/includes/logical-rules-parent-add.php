<form name="bis-addlogicalruleform" id="bis-addlogicalruleform"
      method="POST">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <label id="bis_re_basic_add_rule">Add Rule </label>
            </h3>
        </div>
        
        <div id="bis_re_basic_lr_body" class="panel-body">
            <div class="well">
                <label class="radio-inline">
                    <input type="radio" checked="checked" name="bis_add_rule_type" id="inlineRadio1" value="1"> 
                      <?php _e("Standard Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="bis_add_rule_type" id="inlineRadio2" value="2">
                     <?php _e("Advanced Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </label>
                <label class="radio-inline">
                    <input type="radio" name="bis_add_rule_type" id="inlineRadio3" value="3">
                     <?php _e("Use Existing Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>
                </label>
            </div>    
                
                <div id="bis-logical-rule-define">
                <?php
                $pluginPath = RulesEngineUtil::getIncludesDirPath();
                require_once $pluginPath . "logical-rules-basic-add-body.php";
                ?>
                </div>
           
        </div>
    </div>

</form>
<script>

    // wait for the DOM to be loaded
    jQuery(document).ready(function () {

        var options = {
            success: showResponse,
            url: BISAjax.ajaxurl,
            beforeSubmit: bis_validateAddRulesForm,
            data: {
                action: 'bis_create_logical_rule_new',
                bis_rules_engine_nonce: BISAjax.bis_rules_engine_nonce
            }
        };
        
        jQuery('[name = "bis_add_rule_type"]').click(function() {
            
            var req_action = "bis_re_advance_rule_include";
            switch(this.value) {
                case  '1' :
                        req_action = 'bis_re_basic_rule_include';
                        options.data.action = 'bis_create_logical_rule_new';
                        break;
                case  '2' : 
                        req_action = 'bis_re_advance_rule_include';
                        options.data.action = 'bis_create_logical_rule_new';
                        break;
                case  '3' : 
                        req_action = 'bis_re_exising_rule_include';
                        options.data.action = 'bis_re_use_exising_rule';
                        break;        
            }
            
            var jqXHR = jQuery.get(ajaxurl,
                {
                    action: req_action,
                    bis_nonce: BISAjax.bis_rules_engine_nonce
            });

            jqXHR.done(function (data) {
                jQuery("#bis-logical-rule-define").html(data);
            });
           
        });
        function showResponse(responseText, statusText, xhr, $form) {

            if (responseText["status"] === "success") {
                var callback = jQuery("#bis_child_list_callback").val();
                window[callback]();
            } else {
                if (responseText["status"] === "error") {
                    if (responseText["message_key"] === "duplicate_entry") {
                        bis_showErrorMessage('<?php _e("Duplicate rule name, Name should be unique.", "rulesengine"); ?>');
                    } else if (responseText["message_key"] === "no_method_found") {
                        bis_showErrorMessage('<?php _e("Action hook method does not exist, Please define method with name", "rulesengine"); ?>'
                                + " \"" + jQuery("#bis_re_hook").val() + "\".");
                    } else {
                        bis_showErrorMessage("<?php _e("Error occurred while creating rule.", "rulesengine"); ?>");
                    }
                }
            }

        }

        jQuery('#bis-addlogicalruleform').ajaxForm(options);
       
    });

</script>    