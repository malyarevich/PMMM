function bis_showRedirectResponse(responseText, statusText, xhr, $form) {
    bis_showRedirectRulesView(responseText);
}


function bis_deleteRedirectRule() {
    var ruleId = jQuery(this).closest("div").children()[0].value;

    var rule_name = jQuery(jQuery(this).closest("td").children()[0]).html();

    bis_confirm("<h4>Do want to delete rule \"" + rule_name + "\" ?</h4>", function (result) {
        if (result) {
            var jqXHR = jQuery.get(ajaxurl,
                    {
                        action: "bis_re_redirect_delete_rule",
                        ruleId: ruleId,
                        bis_nonce: BISAjax.bis_rules_engine_nonce
                    });

            jqXHR.done(function (response) {
                bis_showRedirectRulesView(response);
                bis_setRedirectSearchButtonWidth();
                bis_redirectFieldsReset();
            });
        }

    });
}

function bis_showEditRedirectRule() {

    var ruleId = jQuery(this).closest("div").children()[0].value;

    var jqXHR = jQuery.get(ajaxurl,
            {
                action: "bis_re_show_edit_redirect_rule",
                ruleId: ruleId,
                bis_nonce: BISAjax.bis_rules_engine_nonce
            });

    jqXHR.done(function (data) {
        jQuery("#redirect_rules_child_content").html(data);
        jQuery("#redirect_rules_list_content").hide();
        jQuery("#redirect_rules_child_content").show();
        bis_setRedirectSearchButtonWidth();
        bis_redirectFieldsReset();

    });

}

function bis_showRedirectRulesList() {

    jQuery.get(
            BISAjax.ajaxurl,
            {
                action: 'bis_re_redirect_rules_list',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            },
    function (response) {
        bis_showRedirectRulesView(response);
        bis_request_complete();
    });
}

function bis_showRedirectRulesView(response) {

    jQuery("#redirect_rules_list_content").show();
    jQuery("#redirect_rules_child_content").html("");
    jQuery("#redirect_rules_parent_content").html("");
    var status = response["status"];

    if (status === "success_with_no_data") {

        jQuery("#bis-redirect-rules-table").hide();
        jQuery("#bis-redirect-no-result").children().html('Redirect rules not found');
        jQuery("#bis-redirect-no-result").show();
        jQuery(".bis-rows-no").html(0);

    } else {
        var data = {
            redirectrules: response["data"]
        };

        if (status === "error") {
            bis_showErrorMessage("Error occurred while retrieving redirect rules.");
        }

        jQuery(".bis-rows-no").html(response["data"].length);
        jQuery("#bis-redirect-rules-table").show();
        jQuery("#bis-redirect-no-result").hide();
        var source = jQuery("#bis-redirectRulesListTemplate").html();
        var template = Handlebars.compile(source);
        var redirectRulesListContent = template(data);
        jQuery("#bis-redirectRulesListContent").html(redirectRulesListContent);
        jQuery(".bis-redirect-rule-delete").on("click", bis_deleteRedirectRule);
        jQuery(".bis-redirect-rule-edit").on("click", bis_showEditRedirectRule);
        jQuery(".glyphicon-thumbs-down").parent().parent().addClass("warning");
    }
    // stop loading icon
	bis_request_complete();
}


function bis_redirectFieldsReset() {

    jQuery("#bis_re_redirect_search_value").val("");
    jQuery("#bis_re_redirect_search_status").multiselect({});
    jQuery('#bis_re_redirect_search_status').multiselect('select', 'all');
    jQuery('#bis_re_redirect_search_status').multiselect('deselect', ['1', '0']);
    jQuery("#bis_re_redirect_search_by").multiselect('select', 'name');
    jQuery("#bis_re_redirect_search_by").multiselect('deselect', 'description');
}

function bis_setRedirectSearchButtonWidth() {
    jQuery('#bis_re_redirect_search_by').multiselect({
        buttonWidth: '200px'
    });

    jQuery('#bis_re_redirect_search_status').multiselect({
        buttonWidth: '200px'
    });

}

/**
 * This method validates the Edit Redirect Rules Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateEditRedirectRulesForm(arr, form, options) {
    var ruleName = jQuery.trim(jQuery("#bis_re_redirect_name").val());
    
    if (!bis_validateRulesName(ruleName)) {
        return false;
    }

    var targetURL = jQuery.trim(jQuery("#bis_re_target_url").val());

    if (targetURL === "") {
        bis_alert(breCV.enter_target_url);
        return false;
    }

    if(bis_validate_url(targetURL) === false) {
        return false;
    }
    
    if(jQuery("#bis_re_show_popup").is(':checked')) {
        if(jQuery("#bis_re_red_btn_label").val() === "") {
            bis_alert(redirectreCV.enter_redirect_button_label);
            return false;
        }
        if (jQuery("#bis_re_cancel_button").val() === "") {
            bis_alert(redirectreCV.enter_cancel_button_label);
            return false;
        }
    }
    
    return bis_validateAddRulesForm(arr, form, options);
    
}

/**
 * This method validates the Add Redirect Rules Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateAddRedirectRulesForm(arr, form, options) {
    
    
    var ruleName = jQuery.trim(jQuery("#bis_re_redirect_name").val());
    jQuery("#bis_re_name").val(ruleName+" Redirect");
    
    jQuery("#bis_re_description").val(jQuery.trim(jQuery("#bis_re_redirect_description").val()));
    
    if (!bis_validateRulesName(ruleName)) {
        return false;
    }

    var targetURL = jQuery.trim(jQuery("#bis_re_target_url").val());

    if (targetURL === "") {
        bis_alert(breCV.enter_target_url);
        return false;
    }
    if(!bis_validate_url(targetURL)) {
        return false;
    }
    
    return bis_validateAddRulesForm(arr, form, options);
}