        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><label><?php _e("New Logical Rule", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> </label></h3>
            </div>
            <div class="panel-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label><?php _e("Rule Name", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> </label>
                            <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                            <a class="popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE_SITE ?>"
                               data-content="<?php _e("Name is required and should be unique.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>" rel="popover"
                               data-placement="bottom" data-trigger="hover">
                                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                            </a>

                            <input type="text" class="form-control" maxlength="50"
                                   id="bis_re_name" name="bis_re_name" autofocus
                                   placeholder="<?php _e("Enter rule name", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>">

                        </div>

                        <div class="form-group col-md-5">
                            <label><?php _e("Action Hook", BIS_RULES_ENGINE_TEXT_DOMAIN); ?> </label>
                            <a class="popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE_SITE ?>"
                               data-content='<?php _e("Hook will be called if rule criteria is satisfied. Before entering action hook, please define a function with action hook.Action hook should not contain any spaces.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                               rel="popover"
                               data-placement="bottom" data-trigger="hover">
                                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                            </a>
                            <input type="text" class="form-control" id="bis_re_hook" name="bis_re_hook"
                                   placeholder="<?php _e("Enter action hook", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>">
                        </div>
                        <div class="form-group col-md-2">
                            <label><?php _e("Status", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></label>

                            <div>
                                <select class="form-control bis-multiselect" id="bis_re_status" style="width:20%">
                                    <option value="active"><?php _e("Active", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                                    <option value="deactive"><?php _e("Deactive", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-10">
                            <label><?php _e("Description", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></label>
                            <textarea rows="2" maxlength="500" class="form-control" name="bis_re_description"
                                      id="bis_re_description"
                                      placeholder="<?php _e("Enter rule description", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>"></textarea>
                        </div>
                        <div class="form-group col-md-2">
                            <label><?php _e("Evaluation Type", "rulesengine"); ?></label>
                            <a class="popoverData" target="_blank" href=""
                               data-content='<?php
                               _e("Evaluation Type is Atleast Once means even if the rule is satisfied atleast once, "
                                       . "the rule will be considered as satisfied for the entire session. Useful for Requested URL subcategory. "
                                       . "Default is Always.", "rulesengine");
                               ?>'
                               rel="popover"
                               data-placement="bottom" data-trigger="hover">
                                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                            </a>
                            <div>
                                <select class="form-control bis-multiselect" name="bis_re_eval_type" 
                                        id="bis_re_eval_type" style="width:20%">
                                    <option value="1"><?php _e("Always", "rulesengine"); ?></option>
                                    <option value="2"><?php _e("Atleast Once", "rulesengine"); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $logical_rule_engine_modal = new bis\repf\model\LogicalRulesEngineModel();
        $bis_re_rule_options = $logical_rule_engine_modal->get_rules_options();
        $bis_re_editable_roles = get_editable_roles();
        ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-5">
                            <span class="panel-title"><?php _e("Add Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></span>
                            <a class="popoverData" style="z-index: 1000"
                               target="_blank" href="<?php echo BIS_RULES_ENGINE_SITE ?>" data-content='<?php _e("All fields are required except operator field. Operator field is used to define more complex rules. Click to know more about how to define simple and complex rules.", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                               rel="popover" data-placement="bottom" data-trigger="hover">
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
                            <th width="5%"><?php _e("Operator", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></th>
                            <th width="15%"><?php _e("Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></th>
                            <th width="15%"><?php _e("Sub Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></th>
                            <th width="13%"><?php _e("Condition", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></th>
                            <th><?php _e("Value", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></th>
                            <th width="15%"><?php _e("Operator", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></th>
                            <th width="2%">
                                <span title="<?php _e("Remove criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>" class="glyphicon glyphicon-remove bis-icon-red"
                                      aria-hidden="true">

                                </span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>

<?php
$rows_count = BIS_RULES_CRITERIA_ROWS_COUNT;
for ($i = 0; $i < $rows_count; $i++) {
    $style = "";

    // Hide other rows
    if ($i > 0) {
        $style = "display:none";
    }
    ?>

                            <tr class="rule_criteria_row" id="criteria_<?php echo $i ?>" style='<?php echo $style ?>'>
                                <td>
                                    <select name="bis_re_left_bracket[]" size="2" id="bis_re_left_bracket_<?php echo $i ?>"
                                            class="bis-multiselect">
                                        <option value="0" selected="selected">&nbsp;</option>
                                        <option value="1">(</option>
                                        <option value="2">((</option>
                                        <option value="3">(((</option>
                                        <option value="4">((((</option>
                                        <option value="5">(((((</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group btn-group">
                                        <select class="bis-multiselect bis_re_rule_option" name="bis_re_rule_option[]"
                                                id="bis_re_rule_option_<?php echo $i ?>" size="2">
    <?php foreach ($bis_re_rule_options as $row) { ?>
                                                <option value="<?php echo $row->id ?>"
                                                        id="<?php echo $row->id ?>"><?php echo __($row->name, 'rulesengine'); ?></option>
    <?php } ?>
                                        </select>
                                    </div>
                                </td>
                                <td><span class="bis_re_sub_option_span"
                                          id="bis_re_sub_option_span_<?php echo $i ?>"></span>
                                </td>
                                <td><span class="bis_re_condition_span" id="bis_re_condition_span_<?php echo $i ?>"></span>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="col-sm-11">
                                            <span id="bis_re_rule_value_span_<?php echo $i ?>"></span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group btn-group">
                                        <span id="bis_re_logical_option_span_<?php echo $i ?>">
                                            <select name="bis_re_right_bracket[]" size="2"
                                                    id="bis_re_right_bracket_<?php echo $i ?>"
                                                    class="bis-multiselect">
                                                <option value="0" selected="selected">&nbsp;</option>
                                                <option value="1">)</option>
                                                <option value="2">))</option>
                                                <option value="3">)))</option>
                                                <option value="4">))))</option>
                                                <option value="5">)))))</option>
                                            </select>
                                            <select name="bis_re_logical_op[]" size="2"
                                                    id="bis_re_logical_op_<?php echo $i ?>"
                                                    class="bis-multiselect logical-operator">
                                                <option value="0" selected="selected">&nbsp;</option>
                                                <option value="1"><?php _e("And", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                                                <option value="2"><?php _e("OR", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></option>
                                            </select>
                                        </span>

                                        <span class="bis_re_value_type_id_span"
                                              id="bis_re_value_type_id_span_<?php echo $i ?>"> </span>

                                    </div>

                                </td>
                                <td>
                                    <span>
    <?php if ($i != 0) { ?>
                                            <a href="#" class="bis-remove-icon bis_remove_criteria"
                                               id="bis_remove_criteria_<?php echo $i ?>" title="Remove criteria">
                                                <span
                                                    class="glyphicon glyphicon-remove bis-icon-red"
                                                    aria-hidden="true"></span></a>
    <?php } ?>
                                    </span>
                                </td>
                            </tr>
<?php } ?>
                    </tbody>

                </table>

            </div>
        </div>

<script>

    // wait for the DOM to be loaded
    jQuery(document).ready(function () {

        jQuery("#wpfooter").css("position", "relative");

        jQuery('.popoverData').popover();
        criteria = 0;
        subCriteria = 0;

        jQuery('#bis_re_operator').multiselect({
            nonSelectedText: '<?php _e("More Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
        });


        jQuery('.logical-operator').multiselect({
            nonSelectedText: '<?php _e("None", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>',
            buttonWidth: '65px'
        });


        jQuery('#bis_re_operator_1').multiselect({
            nonSelectedText: '<b class="glyphicon glyphicon-plus"></b><?php _e("More Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?></span>'
        });

        jQuery(".input-group-remove").click(function () {
            jQuery(this.parentElement).remove();
        });

        jQuery('.bis_re_rule_option').multiselect({
            nonSelectedText: '<?php _e("Select Criteria", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>',
            enableCaseInsensitiveFiltering: true,
            maxHeight: 220,
            onChange: function (element, checked) {
                bis_showSubOptions(element[0]);

                //Remove child dependent values
                var siblings = element.closest("td").nextAll();
                jQuery(siblings[1].children).html('<?php _e("None Selected", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
                jQuery(siblings[2].children).children().children().html("");
            }

        });


        jQuery('#bis_re_status').multiselect();

        jQuery('.bis-multiselect').multiselect({
        });

        var options = {
            success: showResponse,
            url: BISAjax.ajaxurl,
            beforeSubmit: bis_validateAddRulesForm,
            data: {
                action: 'bis_create_logical_rule',
                bis_rules_engine_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

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

        jQuery('#bis-addlogicalruleform').ajaxForm(options);


        // Function called on change of Criteria drop donwn.
        function bis_showSubOptions(element) {
            criteria = element.value;
            var rowIndex = jQuery(element).closest("tr").index();
            jQuery.post(
                    BISAjax.ajaxurl,
                    {
                        action: 'bis_get_sub_options',
                        optionId: criteria
                    },
            function (response) {

                var data = {
                    compId: 'bis_re_sub_option_' + rowIndex,
                    compName: 'bis_re_sub_option[]',
                    suboptions: response
                };

                var source = jQuery("#bis-selectComponent").html();
                var template = Handlebars.compile(source);
                var logicalRulesContent = template(data);
                var subOptObj = jQuery("#bis_re_sub_option_span_" + rowIndex);
                subOptObj.html(logicalRulesContent);

                jQuery('.bis-multiselect').multiselect({
                    nonSelectedText: '<?php _e("None Selected", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>'
                });

                addDropdownDeleteEvent();

                // Add change event for sub Option

                addSubOptionChangeEvent(jQuery("#" + data.compId));


            }
            );
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
        function addSubOptionChangeEvent(subOptObj) {

            subOptObj.on("change", function () {
                var rowIndex = jQuery(this).closest("tr").index();
                subCriteria = this.value;

                //Remove child dependent values
                var siblings = jQuery(this).closest("td").nextAll();
                jQuery(siblings[1].children).children().children().html("");

                // Componenent Id should be dynamic
                jQuery.post(
                        BISAjax.ajaxurl,
                        {
                            action: 'bis_get_conditions',
                            optionId: this.value
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
            });

        }

        function addSelectBox(rowIndex) {

            var jqXHR = jQuery.get(ajaxurl,
                    {
                        action: "bis_re_get_rule_values",
                        bis_nonce: BISAjax.bis_rules_engine_nonce,
                        bis_sub_criteria: subCriteria,
                        bis_criteria: criteria,
                        bis_condition: condition
                    });

            jqXHR.done(function (response) {

                var data = {
                    compId: 'bis_re_rule_value_' + rowIndex,
                    compName: 'bis_re_rule_value[]',
                    suboptions: response
                };

                source = jQuery("#bis-selectComponent").html();

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
                    "&criteria=" + criteria + "&subcriteria=" + subCriteria + "&condition=" + condition, {
                        theme: "facebook",
                        minChars: 2,
                        method: "POST",
                        onAdd: function (item) {
                            jQuery("#bis_re_rule_value_" + rowIndex).val(JSON.stringify(this.tokenInput("get")));
                        }
                    });

        }

        jQuery(".bis_remove_criteria").click(function () {

            var ctr = jQuery(this).closest("tr").filter(":visible");

            var pretr = ctr.prevUntil().filter(":visible").attr("id");
            var preIndex = getRowIndex(pretr);

            var siblings = ctr.children();

            var crowlen = Number(jQuery(".rule_criteria_row").filter(":visible").length);

            // Removes the dropdown logical operation value.
            crowlen = crowlen - 1;
            var cIndex = Number(preIndex) + 1;

            if (crowlen === cIndex) {
                jQuery("#bis_re_logical_op_" + preIndex).multiselect("deselect", jQuery("#bis_re_logical_op_" + preIndex).val());
                jQuery("#bis_re_logical_op_" + preIndex).multiselect("select", '0');
            }

            // Remove the values before hiding the criteria
            jQuery(siblings[2].children).html('<?php _e("None Selected", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
            jQuery(siblings[3].children).html('<?php _e("None Selected", BIS_RULES_ENGINE_TEXT_DOMAIN); ?>');
            jQuery(siblings[4].children).children().children().html("");

            var criteriaOption = jQuery(siblings[1]).children().children();
            if (criteriaOption.val()) {
                jQuery("#" + criteriaOption.attr("id")).multiselect("deselect", criteriaOption.val());
            }

            // Hide the next row
            jQuery(this).closest("tr").hide();


        });

        /**
         This method will show and hide the next criteria based on Operator value.
         */
        jQuery(".logical-operator").change(function () {
            jQuery(this).closest("tr").next().show();
        });


        function split(val) {
            return val.split(/,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }


        function getCriteria(val) {
            var ele = jQuery(val).closest("td").prevAll();
            criteria = jQuery(ele[2]).children()[0].value;
            return criteria;
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

    });
</script>