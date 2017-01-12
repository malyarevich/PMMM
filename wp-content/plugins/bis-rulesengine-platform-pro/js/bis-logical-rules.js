function bis_setLogicalSearchButtonWidth() {
    jQuery('#bis_re_search_by').multiselect({
        buttonWidth: '200px'
    });

    jQuery('#bis_re_search_status').multiselect({
        buttonWidth: '200px'
    });
}

function bis_logicalResetFields() {
    
    jQuery('#bis_re_search_status').multiselect('select', 'all');
    jQuery('#bis_re_search_status').multiselect('deselect', ['1', '0']);
    jQuery('#bis_re_search_by').multiselect('select', 'name');
    jQuery('#bis_re_search_by').multiselect('deselect', 'description');   
    jQuery("#bis_re_search_value").val("");
}

function bis_showLogicalRulesList() {

    jQuery.get(
        BISAjax.ajaxurl,
        {
            action: 'bis_get_logical_rules'
        },
        function (response) {

            bis_setLogicalSearchButtonWidth();
            bis_showLogicalRules(response);
            bis_logicalResetFields();

        }
    );

}

function bis_showLogicalRules(response) {

    jQuery("#logical_rules_list_content").show();
    jQuery("#logical_rules_child_content").html("");

    if (response["status"] === "success") {

        var data = {
            logicalrules: response["data"]
        };

        var source = jQuery("#bis-logicalRulesTemplate").html();
        var template = Handlebars.compile(source);
        var logicalRulesContent = template(data);

        jQuery(".bis-rows-no").html(response["data"].length);
        jQuery("#bis-logicalRulesContent").html(logicalRulesContent);

        jQuery(".bis-rule-delete").on("click", deleteRule);
        jQuery(".bis-rule-edit").on("click", bis_edit_rule);
        jQuery("#bis-logical-rules-table").show();
        jQuery("#bis-logical-no-result").hide();

    } else {
        jQuery("#bis-logical-rules-table").hide();
        jQuery(".bis-rows-no").html(0);
        jQuery("#bis-logical-no-result").children().html(response["data"]);
        jQuery("#bis-logical-no-result").show();
    }

}


function bis_edit_rule() {

    var ruleId = jQuery(this).closest("div").children()[0].value;

    var jqXHR = jQuery.get(ajaxurl,
        {
            action: "bis_re_edit_rule",
            ruleId: ruleId,
            bis_nonce: BISAjax.bis_rules_engine_nonce
        });

    jqXHR.done(function (data) {
        jQuery("#logical_rules_child_content").html(data);
        jQuery("#logical_rules_list_content").hide();
        jQuery("#logical_rules_child_content").show();
    });

}

function deleteRule() {
    var ruleId = jQuery(this).closest("div").children()[0].value;
    var rule_name = jQuery(jQuery(this).closest("td").children()[0]).html();
    
    bis_confirm("<h4>"+breCV.delete_confirm+" \"" + rule_name + "\" ?</h4>", function (result) {
        if (result) {
            var jqXHR = jQuery.get(ajaxurl,
                {
                    action: "bis_re_delete_rule",
                    ruleId: ruleId,
                    bis_nonce: BISAjax.bis_rules_engine_nonce
                });

            jqXHR.done(function (data) {
                if (data["status"] !== "error") {
                    bis_showLogicalRules(data["data"]);
                } else {
                    bis_showErrorMessage(data["data"]);
                }

            });
        }

    });

}


function showResponse(responseText, statusText, xhr, $form) {
    bis_showLogicalRules(responseText);
}


