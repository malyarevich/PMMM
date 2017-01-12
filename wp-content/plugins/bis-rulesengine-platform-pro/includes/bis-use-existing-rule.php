<div class="row">
    <div class="form-group col-md-3">
        <label><?php _e("Logical Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></label>
        <span class="glyphicon"></span>

        <div>
            <?php
            use bis\repf\common\RulesEngineCacheWrapper;
            
            $logicalRulesEngineModal = new bis\repf\model\LogicalRulesEngineModel();  
            $ruleId = RulesEngineCacheWrapper::get_session_attribute(BIS_LOGICAL_RULE_ID);
            $rules = $logicalRulesEngineModal->get_active_rules();
            $description  = "";
            ?>

            <select name="bis_re_ex_rule_id"
                    id="bis_re_ex_rule_id" size="2">
                <?php foreach ($rules as $rule) { 
                    $selected = "";
                    if($ruleId == $rule->ruleId) {
                          $selected = "selected";
                          $description = $rule->description;
                    }
                    ?>
                <option <?php echo $selected ?> value="<?php echo $rule->ruleId ?>"
                        id="<?php echo $rule->ruleId ?>"><?php echo $rule->name ?></option>
                        <?php } ?>
            </select>
        </div>
    </div>
</div>  
<div class="row">
    <div class="form-group col-md-9">
        <label for="description"><?php _e("Description", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></label>
        <textarea readonly rows="2" maxlength="500" class="form-control" name="bis_re_rule_description"
                  id="bis_re_rule_description" placeholder='<?php _e("Enter rule description", BIS_RULES_ENGINE_TEXT_DOMAIN) ?>'> 
        <?php echo $description; ?>
        </textarea>
    </div>
    <input type="hidden" id="bis_re_name" name="bis_re_name" />
</div>
  

<script lang="javascript">
     // wait for the DOM to be loaded
    jQuery(document).ready(function () {
        
        jQuery('.popoverData').popover();
        
        var options = {
            success: showResponse,
            url: BISAjax.ajaxurl,
            data: {
                action: 'bis_create_logical_rule_new',
                bis_rules_engine_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        jQuery('#bis_re_ex_rule_id').change(function() {
            
            var jqXHR = jQuery.get(ajaxurl,
            {
                    action: 'bis_re_logical_rule',
                    rule_id: this.value,
                    bis_nonce: BISAjax.bis_rules_engine_nonce
            });

            jqXHR.done(function (response) {
                jQuery("#bis_re_rule_description").val(response['data'].description);
                jQuery("#bis_re_name").val(response['data'].name);
                
            });
        });

        jQuery('#bis_re_ex_rule_id').multiselect({
            includeSelectAllOption: true,
            enableCaseInsensitiveFiltering: true,
            maxHeight: 300,
            selectedClass: null,
            buttonWidth: '250px',
            nonSelectedText: '<?php _e("Select Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
        });
        
    });
</script>    
