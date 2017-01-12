<?php
use bis\repf\common\RulesEngineCacheWrapper;
use bis\repf\common\RulesEngineLocalization;

$logical_rule_engine_modal = new bis\repf\model\LogicalRulesEngineModel();

$ruleId = RulesEngineCacheWrapper::get_session_attribute(BIS_LOGICAL_RULE_ID);

// Get the rule details.
$bis_rule = $logical_rule_engine_modal->get_rule($ruleId);

// Ge the rule details
$rule = $bis_rule["rule"];

// Get the criteria array from map
$rule_criteria_array = $bis_rule["rule_criteria"];

$bis_re_rule_options = $logical_rule_engine_modal->get_rules_options();
$bis_re_editable_roles = get_editable_roles();
?>


<input type="hidden" name="bis_re_rId" id="bis_re_rId" value='<?php echo $rule->rId; ?>'/>
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <span class="panel-title"><?php _e("Edit Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN);?></span>
                            <a class="popoverData"
                               target="_blank" href="<?php echo BIS_RULES_ENGINE_SITE ?>" data-content="<?php _e("All fields are required except operator field.
                               Operator field is used to define more complex rules. Click to know more about how to define
                               simple and complex rules.", BIS_RULES_ENGINE_TEXT_DOMAIN);?>" rel="popover" data-placement="bottom" data-trigger="hover">
                                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                    <tr>
                        <th width="25%"><?php _e("Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN);?></th>
                        <th width="13%"><?php _e("Condition", BIS_RULES_ENGINE_TEXT_DOMAIN);?></th>
                        <th><?php _e("Value", BIS_RULES_ENGINE_TEXT_DOMAIN);?></th>
                        <th width="10%"></th>
                     </tr>
                    </thead>
                    <tbody>
                    <?php
                        $rows_count = BIS_RULES_CRITERIA_ROWS_COUNT;
                        $criteria_count = count($rule_criteria_array);
                        for ($i = 0; $i < $rows_count; $i++) {
                            $style = "";
                            $rule_criteria = null;
                           
                            // Hide other rows
                            if ($i > $criteria_count - 1) {
                                $style = "display:none";
                            } elseif ($i < $criteria_count) {
                                $rule_criteria = $rule_criteria_array[$i];
                                $oper_ar[$rule_criteria->operId] = "selected=\"selected\"";
                            }
                            ?>

                        <tr class="rule_criteria_row" id="rule_criteria_<?php echo $i ?>" style='<?php echo $style ?>'>
                            <td>
                                    <div class="input-group btn-group">
                                        <select class="bis-multiselect bis_re_sub_option" name="bis_re_sub_option[]"
                                                id="bis_re_sub_option_<?php echo $i ?>" size="2">
                                            <?php
                                            foreach ($bis_re_rule_options as $row) {
                                                $bis_sub_options = $logical_rule_engine_modal->get_rules_sub_options($row->id);
                                                $bis_sub_options = RulesEngineLocalization::get_localized_values($bis_sub_options);
                                                ?>
                                                <optgroup label="<?php echo __($row->name, 'rulesengine'); ?>">
                                                    <?php foreach ($bis_sub_options as $sub_row) { 
                                                        $selected = "";
                                                        if ($sub_row != null && $rule_criteria != null && $sub_row->id == $rule_criteria->subOptId) {
                                                            $selected = "selected=\"selected\"";
                                                        }
                                                                ?>
                                                    <option <?php echo $selected ?> value="<?php echo $row->id . '_' . $sub_row->id ?>"
                                                                id="<?php echo $sub_row->id ?>"><?php echo __($sub_row->name, 'rulesengine'); ?>
                                                        </option>
                                                    <?php } ?>        
                                                </optgroup>
                                                <?php } ?>          
                                        </select>
                                    </div>
                                </td>
                            <td>
                                <span class="bis_re_condition_span" id="bis_re_condition_span_<?php echo $i ?>">
                                <?php if ($i < $criteria_count) {
                                    $rconditions = $logical_rule_engine_modal->get_rules_conditions($rule_criteria->subOptId);
                                    $rules_conditions = RulesEngineLocalization::get_localized_values($rconditions["RuleConditions"]);
                                    ?>
                                    <select size="4" id="bis_re_condition_<?php echo $i ?>" name="bis_re_condition[]" class="bis-multiselect">
                                        <?php foreach ($rules_conditions as $rule_cond) {

                                            $selected = "";
                                            if($rule_criteria != null && $rule_cond->id == $rule_criteria->condId) {
                                                $selected =  "selected=\"selected\"";
                                            }
                                            ?>
                                            <option <?php echo $selected ?> value="<?php echo $rule_cond->id ?>">
                                                <?php echo $rule_cond->name ?>
                                            </option>
                                        <?php } ?>

                                    </select>
                                <?php } ?>
                               </span>
                            </td>
                            <td>
                                <div class="form-group">
                                    <div class="col-sm-11">
                                        <span id="bis_re_rule_value_span_<?php echo $i ?>">
                                        <?php if ($i < $criteria_count) {

                                            $value_type_id = (int)$rule_criteria->valueTypeId;

                                            switch ($value_type_id) {

                                                case 1: // Input Token
                                                    if($rule_criteria->subOptId === "21" || $rule_criteria->subOptId === "22") {
                                                        $pages_json = $logical_rule_engine_modal->get_posts_by_ids(json_decode($rule_criteria->value));
                                                        $rule_criteria->value = json_encode($pages_json);
                                                    } else if($rule_criteria->subOptId === "17") {
                                                        $categories_json = $logical_rule_engine_modal->get_wp_categories_by_ids(json_decode($rule_criteria->value));
                                                        $rule_criteria->value = json_encode($categories_json);
                                                    } else if($rule_criteria->subOptId === "25") {
                                                        $categories_json = $logical_rule_engine_modal->get_woocommerce_categories_by_ids(json_decode($rule_criteria->value));
                                                        $rule_criteria->value = json_encode($categories_json);
                                                    }
                                                    ?>
                                                    <input type="text" class="bis_re_rule_value" autocomplete="off" class="form-control"
                                                           id="bis_re_rule_value_<?php echo $i ?>"
                                                           name="bis_re_rule_value[]"
                                                           placeholder="Enter rule value"/>

                                                    <?php break;
                                                case 2: //SelectBox
                                                    $values = $logical_rule_engine_modal->get_rule_values($rule_criteria->subOptId);
                                                                          
                                                        ?>

                                                    <select class="form-control bis_re_rule_value select" name="bis_re_rule_value[]"
                                                            id="bis_re_rule_value_<?php echo $i ?>" size="2">
                                                        <?php foreach ($values as $value) {
                                                            $selected = "";

                                                            if ($value->id == $rule_criteria->value) {
                                                                $selected = "selected=selected";
                                                            }
                                                            ?>

                                                            <option  <?php echo $selected ?> value="<?php echo $value->id ?>"
                                                                                             id="<?php echo $value->id ?>">
                                                                <?php echo $value->name ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>

                                                    <?php break;
                                                case 3: // Date
                                                    ?>

                                                    <input type="text" autocomplete="off" class="form-control bis_re_rule_value"
                                                           id="bis_re_rule_value_<?php echo $i ?>"
                                                           name="bis_re_rule_value[]"
                                                           placeholder="Enter rule value" value="<?php echo $rule_criteria->value; ?>"/>

                                                    <?php break;
                                                case 4: // Textbox
                                                    $rule_value = $rule_criteria->value;
                                                    

                                                    if ($rule_criteria->optId == 3 || $rule_criteria->subOptId == 32) {
                                                        $rule_value = json_decode($rule_value);
                                                        $rule_value = $rule_value[0]->id;
                                                    }

                                                    ?>

                                                    <input type="text" autocomplete="off" class="form-control"
                                                           id="bis_re_rule_value_<?php echo $i ?>"
                                                           name="bis_re_rule_value[]"
                                                           placeholder="<?php _e("Enter rule value", BIS_RULES_ENGINE_TEXT_DOMAIN);?>" value="<?php echo $rule_value; ?>"/>


                                                    <?php break;

                                            }

                                      } ?>

                                        </span>

                                    </div>
                                </div>

                            </td>
                            <td>
                                <span class="bis_re_value_type_id_span"
                                          id="bis_re_value_type_id_span_<?php echo $i ?>"> 
                                </span>
                                 <?php 
                                    $bis_re_log_style = "";
                                    if ($i < ($criteria_count - 1)) { 
                                        $bis_re_log_style = "style=display:none;";
                                    }   
                                ?>

                                <span>
                                    <a <?php echo $bis_re_log_style; ?> href="#" class="logical-operator"
                                           id="bis_add_criteria_<?php echo $i ?>" title="Add criteria">
                                            <span class="glyphicon glyphicon-plus-sign add-criteria-icon-blue" aria-hidden="true">
                                            </span>
                                        </a>
                                </span>
                                <span>
                                       <?php if ($i != 0) { ?>
                                         <a href="#" class="bis-remove-icon bis_remove_criteria"
                                            id="bis_remove_criteria_<?php echo $i ?>" title="Remove criteria">
                                            <span class="glyphicon glyphicon-remove delete-criteria-icon-red"
                                                     aria-hidden="true">
                                            </span>   
                                         </a>
                                        <?php } ?> 
                                </span>
                            </td>
                            <?php

                            if($rule_criteria != null) {
                                                                                            
                                if($rule_criteria->valueTypeId === "1") {
                                   $rule_criteria->value = stripslashes($rule_criteria->value);
                                }               
                                 
                                ?>

                                <input type="hidden" name="bis_re_rcId[]" id="bis_re_rcId_<?php echo $i ?>"
                                       value="<?php echo $rule_criteria->rcId; ?>"/>
                                <input type="hidden" id="bis_re_tokenInput_<?php echo $i ?>" name="bis_re_tokenInput[]"
                                       value="<?php echo $rule_criteria->valueTypeId; ?>"/>
                                
                                <input type="hidden" class="bis_re_rule_value_json" name="bis_re_rule_value_json[]"
                                       value='<?php echo $rule_criteria->value; ?>'
                                       id="bis_re_rule_value_json_<?php echo $i ?>"/>
                                <input type="hidden" class="bis_re_sub_opt_type_id"
                                       id="bis_re_sub_opt_type_id_<?php echo $i ?>"
                                       name="bis_re_sub_opt_type_id[]" value="<?php echo $value_type_id; ?>"/>
                                <input type="hidden" class="bis_re_delete"
                                       id="bis_re_delete_<?php echo $i ?>"
                                       name="bis_re_delete[]" />
                          <?php  } ?>

                        </tr>
                       <?php } ?>    
                    </tbody>
                </table>
            </div>
            
            <input type="hidden" id="bis_re_name" name="bis_re_name" value="<?php echo $rule->name ?>">
            <input type="hidden" id="bis_re_description" name="bis_re_description" value="<?php echo $rule->description ?>">
            <input type="hidden" id="bis_re_hook" name="bis_re_hook" value="">
            <input type="hidden" id="bis_re_eval_type" name="bis_re_eval_type" value="1">
            <input type="hidden" id="bis_re_status" name="bis_re_status" value="<?php echo $rule->status ?>">

        </div>   
<script>

    // wait for the DOM to be loaded
    jQuery(document).ready(function () {

        jQuery("#wpfooter").css("position", "relative");
        jQuery('.popoverData').popover();
        subCriteria = 0;
        
        jQuery(".input-group-remove").click(function () {
            jQuery(this.parentElement).remove();
        });

        jQuery('.select').multiselect({
            nonSelectedText: '<?php _e("Select Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>',
            enableCaseInsensitiveFiltering: true,
            maxHeight: 220,
            buttonWidth: '350px'
        });  
        
        jQuery('.bis_re_sub_option').multiselect({
            nonSelectedText: '<?php _e("Select Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>',
            enableCaseInsensitiveFiltering: true,
            maxHeight: 220,
            buttonWidth: '350px',
            onChange: function (element, checked) {
                var subCrVal = element[0].value.split("_")[1];
                var rowIndex = jQuery(element[0]).closest("tr").index();
                subCriteria = subCrVal;
                addSubOptionChangeEvent(subCrVal, rowIndex);

                //Remove child dependent values
                var siblings = element.closest("td").nextAll();
                jQuery(siblings[0].children).html('<?php _e("None Selected", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
                jQuery(siblings[1].children).children().children().html("");
                addDropdownDeleteEvent();
                
            }

        });


        jQuery('.bis-multiselect').multiselect({
        });

        function showResponse(responseText, statusText, xhr, $form) {

            if (responseText["status"] === "success") {
                bis_showLogicalRulesList();
            } else {
                if (responseText["status"] === "error") {
                    if (responseText["message_key"] === "duplicate_entry") {
                        bis_showErrorMessage('<?php _e("Duplicate rule name, Name should be unique.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
                    } else if (responseText["message_key"] === "no_method_found") {
                        bis_showErrorMessage('<?php _e("Action hook method does not exist, Please define method with name", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                                + " \"" + jQuery("#bis_re_hook").val() + "\".");
                    } else {
                        bis_showErrorMessage("<?php _e("Error occurred while creating rule.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>");
                    }
                }
            }

        }

        // Adds dropdown delete event
        function addDropdownDeleteEvent() {
            jQuery(".input-group-remove").on("click", function (evt) {
                evt.stopPropagation();
                evt.preventDefault();

                var childCond = jQuery(this).closest("td").nextAll();

                for (var i = 0; i < childCond.length; i++) {
                    jQuery(jQuery(childCond)[i]).find(".btn-group").remove();
                }

                jQuery(this.parentElement).remove();
            });

        }


        /**
         * This method is used to get the sub options of rules based on the optionId
         */
        function addSubOptionChangeEvent(subCrVal, rowIndex) {

                // Componenent Id should be dynamic
                jQuery.post(
                        BISAjax.ajaxurl,
                        {
                            action: 'bis_get_conditions',
                            optionId: subCrVal
                        },
                function (response) {

                    var data = {
                        compId: 'bis_re_condition_' + rowIndex,
                        compName: 'bis_re_condition[]',
                        suboptions: response["RuleConditions"]
                    };

                    var valueTypeId = response["ValueTypeId"];
                    var source = jQuery("#bis-selectComponent").html();
                    var template = Handlebars.compile(source);
                    var logicalRulesContent = template(data);
                    
                    jQuery("#bis_re_condition_span_" + rowIndex).html(logicalRulesContent);
                    
                    jQuery('.bis-multiselect').multiselect({
                        nonSelectedText: '<?php _e("None Selected", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                    });

                    var dataTypeId = {
                        id: 'bis_re_sub_opt_type_id_' + rowIndex,
                        name: 'bis_re_sub_opt_type_id[]',
                        value: valueTypeId
                    };

                    source = jQuery("#bis-logical-hidden-component").html();
                    template = Handlebars.compile(source);
                    var valueTypeIdContent = template(dataTypeId);

                    jQuery("#bis_re_value_type_id_span_" + rowIndex).html(valueTypeIdContent);

                    addDropdownDeleteEvent();
                    addConditionChangeEvent(jQuery("#" + data.compId), valueTypeId);    
                    
                }
                );
        }

        function addSelectBox(rowIndex) {

            var jqXHR = jQuery.get(ajaxurl,
                    {
                    action: "bis_re_get_rule_values",
                    bis_nonce: BISAjax.bis_rules_engine_nonce,
                    bis_sub_criteria: subCriteria
                });

            jqXHR.done(function (response) {

                var data = {
                    compId: 'bis_re_rule_value_' + rowIndex,
                    compName: 'bis_re_rule_value[]',
                    suboptions: response
                };

                var source = jQuery("#bis-selectComponent").html();

                var template = Handlebars.compile(source);
                var logicalRulesContent = template(data);
                var subOptObj = jQuery("#bis_re_rule_value_span_" + rowIndex);

                subOptObj.html(logicalRulesContent);

                jQuery('.bis-multiselect').multiselect({
                    nonSelectedText: '<?php _e("None Selected", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>',
                    maxHeight: 200,
                    enableFiltering: true
                });
            });

        }


        function renderTextBox(data, rowIndex) {
            
            if((subCriteria === 32 || subCriteria === 26 || subCriteria === 27)
                    && (condition === 1 || condition === 2)) {
                data.placeholder = '<?php _e("Enter a value, format name=value", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>';
            }
            
            var source = jQuery("#bis-logical-textbox-component").html();
            var template = Handlebars.compile(source);
            var textbox = template(data);
            var textboxId = jQuery("#bis_re_rule_value_span_" + rowIndex);
            textboxId.html(textbox);

        }

        function addConditionChangeEvent(condOptObj, valueTypeId) {

            condOptObj.on("change", function () {

                condition = Number(jQuery.trim(this.value));
                var rowIndex = jQuery(this).closest("tr").index();
                jQuery("#bis_re_rule_value_" + rowIndex).prev().remove();

                subCriteria = Number(subCriteria);
                valueTypeId = Number(valueTypeId);

                switch (valueTypeId) {

                    case 1: // Token Input
                        addTokenInput(rowIndex);

                        switch (condition) {
                            case 1: // Equal
                            case 2: // Not Equal
                                addTokenInput(rowIndex);
                                break;
                            default:
                                var data = {
                                    id: 'bis_re_rule_value_' + rowIndex,
                                    name: 'bis_re_rule_value[]',
                                    placeholder: '<?php _e("Enter a value", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                                };

                                renderTextBox(data, rowIndex);

                        }
                        break;

                    case 2:  // Select Option
                        addSelectBox(rowIndex);
                        break;

                    case 3: // Calendar Date
                        var data = {
                            id: 'bis_re_rule_value_' + rowIndex,
                            name: 'bis_re_rule_value[]',
                            placeholder: '<?php _e("Enter date", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                        };

                        // Default only date
                        tpicker = false;
                        dformat = 'Y-m-d';
                        dpicker = true;

                        if (subCriteria === 14) { // Date & Time
                            tpicker = true;
                            dformat = 'Y-m-d H:i';
                        } else if (subCriteria === 10) {  // Only Time
                            dpicker = false;
                            dformat = 'H:i';
                            tpicker = true;
                        }

                        renderTextBox(data, rowIndex);

                        jQuery("#bis_re_rule_value_" + rowIndex).datetimepicker({
                            timepicker: tpicker,
                            format: dformat,
                            datepicker: dpicker,
                            closeOnDateSelect: true,
                            mask: true
                        });

                        break;

                    case 4: // Text box
                        var data = {
                            id: 'bis_re_rule_value_' + rowIndex,
                            name: 'bis_re_rule_value[]',
                            placeholder: '<?php _e("Enter a value", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                        };

                        if(condition === 12) {
                          data.placeholder = '<?php _e("Enter values with comma separation (i.e value1, value2, value3)", "rulesengine"); ?>';
                        } else if(condition === 13) {
                          data.placeholder = '<?php _e("Enter URL with /** pattern (ex: http://rulesengine.in/mypath/**)", "rulesengine"); ?>';
                        }  
                        renderTextBox(data, rowIndex);
                        break;
                }
            });
        }


        /**
         Add the tokenInput component with values.
         */
        function addTokenInput(rowIndex) {

            var bis_nonce = BISAjax.bis_rules_engine_nonce;
            jQuery("#bis_re_rule_value_" + rowIndex).prev().remove();

            var data = {
                id: 'bis_re_rule_value_' + rowIndex,
                name: 'bis_re_rule_value[]'
            };

            var source = jQuery("#bis-logical-hidden-component").html();
            var template = Handlebars.compile(source);
            var textbox = template(data);
            var textboxId = jQuery("#bis_re_rule_value_span_" + rowIndex);
            textboxId.html(textbox);

            jQuery("#bis_re_rule_value_" + rowIndex).tokenInput(ajaxurl + "?action=bis_re_get_value&bis_nonce=" + bis_nonce +
                    "&subcriteria=" + subCriteria + "&condition=" + condition, {
                        theme: "facebook",
                        minChars: 2,
                        method: "POST",
                        onAdd: function (item) {
                            jQuery("#bis_re_rule_value_" + rowIndex).val(JSON.stringify(this.tokenInput("get")));
                        }
                    });

        }

        jQuery(".bis_remove_criteria").click(function (event) {
            event.preventDefault();
            var ctr = jQuery(this).closest("tr").filter(":visible");
            var pretr = ctr.prevUntil().filter(":visible").attr("id");
            var preIndex = getRowIndex(pretr);
            var siblings = ctr.children();
            var crowlen = Number(jQuery(".rule_criteria_row").filter(":visible").length);

            // Removes the dropdown logical operation value.
            crowlen = crowlen - 1;
            var cIndex = Number(preIndex) + 1;

            if (crowlen === cIndex) {
                jQuery("#bis_add_criteria_" + preIndex).show();
            }
            
            // Remove the values before hiding the criteria
            jQuery("#bis_re_condition_span_"+cIndex).html('<?php _e("None Selected", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
            jQuery("#bis_re_rule_value_span_"+cIndex).html("");

            var cSubOption =  jQuery("#bis_re_sub_option_"+cIndex);
            cSubOption.multiselect("deselect", cSubOption.val());
            // Hide the next row
            jQuery(this).closest("tr").hide();
            
        });

        /**
         This method will show and hide the next criteria based on Operator value.
         */
        jQuery(".logical-operator").click(function (event) {
            event.preventDefault();
            jQuery(this).closest("tr").next().show();
            var preIndex = getRowIndex(jQuery(this).attr("id"));
            jQuery(this).hide();
            var crowlen = Number(jQuery(".rule_criteria_row").filter(":visible").length);

            // Removes the dropdown logical operation value.
            crowlen = crowlen - 1;
            var nextIndex = Number(preIndex) + 1;
            jQuery("#bis_add_criteria_" + nextIndex).show();
            
        });


        function split(val) {
            return val.split(/,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }

        function getSubCriteria(val) {
            var ele = jQuery(val).closest("td").prevAll();
            subCriteria = jQuery(ele[1]).children().children().children()[1].value;
            return subCriteria;
        }

        function getCondition(val) {

            var ele = jQuery(val).closest("td").prevAll();
            return jQuery(ele[0]).children().children().children()[1].value;
        }

        jQuery("#bis_back_rules_list").click(function () {
            bis_showLogicalRulesList();
        });


        // From edit page

        jQuery("#wpfooter").css("position","relative");
        jQuery('.popoverData').popover();
        jQuery('.bis-multiselect').multiselect();


        jQuery("#bis_re_status").change(function (event) {

            if (jQuery("#bis_re_status").val() === "0") {
                bis_warn('<?php _e("Dependent child rules will become inactive, if logical rule is deactivated", BIS_RULES_ENGINE_TEXT_DOMAIN);?>');
            }
        });

        var rule_values = jQuery(".bis_re_rule_value");
        var bis_nonce = BISAjax.bis_rules_engine_nonce;
        var visible_rows  = jQuery(".rule_criteria_row").filter(":visible").length;

        for (var i = 0; i < rule_values.length; i++) {

            var rule_value = rule_values[i];
            

            var arr = rule_value.id.split("_");
            var uId = arr[arr.length-1];
            var ruleIdObj = jQuery("#" + rule_value.id);
            var optId = jQuery("#bis_re_rule_option_"+uId).val();
            var catSubOptId = jQuery("#bis_re_sub_option_"+uId).val().split("_");
     
            var optId = Number(catSubOptId[0]);
            var subOptId = Number(catSubOptId[1]);        
            var conditionId = Number(jQuery("#bis_re_condition_"+uId).val());
            var rule_value_json = jQuery("#bis_re_rule_value_json_"+uId).val();
            var rule_type_id = Number(jQuery("#bis_re_sub_opt_type_id_"+uId).val());

            switch (rule_type_id) {
                case 1: // Input Token
                    var params = "?action=bis_re_get_value&bis_nonce=" + bis_nonce + "&criteria=" + optId + "&subcriteria=" + subOptId + "&condition=" + conditionId;

                    if (conditionId === 1 || conditionId === 2) {
                        var $tokenInput = jQuery("#" + rule_value.id).tokenInput(ajaxurl + params,
                            {
                                theme: "facebook",
                                prePopulate: jQuery.parseJSON(rule_value_json),
                                minChars: 2,
                                method: "POST",
                                onAdd: function (item) {
                                    jQuery(this).val(JSON.stringify(this.tokenInput("get")));
                                },
                                onDelete: function (item) {
                                    jQuery(this).val(JSON.stringify(this.tokenInput("get")));
                                }
                            });
                        $tokenInput.val(rule_value_json);
                    } else {
                        jQuery(rule_value).val(rule_value_json);
                    }

                    break;

                case 2: // Select Box
                    jQuery(rule_value).multiselect();
                    break;

                case 3:// Date

                    // Default only date
                    tpicker = false;
                    dformat = 'Y-m-d';
                    dpicker = true;

                    if (subOptId === 14) { // Date & Time
                        tpicker = true;
                        dformat = 'Y-m-d H:i';
                    } else if (subOptId === 10) {  // Only Time
                        dpicker = false;
                        dformat = 'H:i';
                        tpicker = true;
                    }

                    jQuery(rule_value).datetimepicker({
                        timepicker: tpicker,
                        format: dformat,
                        datepicker: dpicker,
                        closeOnDateSelect: true,
                        mask: true
                    });

                    break;
            }
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

        jQuery('#bis-editlogicalruleform').ajaxForm(options);

        var edit_options = {
            url: BISAjax.ajaxurl,
            data: {
                action: 'bis_re_update_redirect_rule',
                bis_nonce: BISAjax.bis_rules_engine_nonce

            }
        };

        function showResponse(responseText, statusText, xhr, $form) {
            if (responseText["status"] === "success") {
                jQuery.post(edit_options.url, edit_options.data,
                        function (responseText, status) {
                            if (responseText["status"] === "success") {
                                bis_showRedirectRulesList();
                            } else {
                                bis_showErrorMessage('<?php _e("Error occurred while updating rule.", "rulesengine"); ?>');
                            }
                });
            } else {
                if (responseText["status"] === "error") {
                    if (responseText["message_key"] === "duplicate_entry") {
                        bis_showErrorMessage('<?php _e("Duplicate rule name, Name should be unique.", BIS_RULES_ENGINE_TEXT_DOMAIN);?>');
                    } else if (responseText["message_key"] === "no_method_found") {
                        bis_showErrorMessage('<?php _e("Action hook method does not exist, Please define method with name", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                        +" \""+ jQuery("#bis_re_hook").val() + "\".");
                    } else {
                        bis_showErrorMessage('<?php _e("Error occurred while updating rule.", BIS_RULES_ENGINE_TEXT_DOMAIN);?>');
                    }
                }
            }
        }

        jQuery("#bis_re_show_logical_rules").click(function () {
            bis_showLogicalRulesList();
        });

    });
</script>