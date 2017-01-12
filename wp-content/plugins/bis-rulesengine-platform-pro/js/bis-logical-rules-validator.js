
/**
 * This method validates the Add Logical Rules Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateAddRulesForm(arr, form, options) {
    
    if (!bis_isValidRulesForm(arr, form, options)) {
        return false;
    }

    return bis_isValidCriteria();

}

/**
 * This method validates the Edit Logical Rules Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateEditRulesForm(arr, form, options) {

    if (jQuery("#bis_re_tokenInput").val() === "tokenInput") {
        jqForm[0].bis_re_criteria_value_json.value = JSON.stringify(jQuery("#bis_re_rule_value").tokenInput("get"));
    }

    if (!bis_isValidRulesForm(arr, form, options)) {
        return false;
    }

    var crows = jQuery(".rule_criteria_row").filter(":visible");

    for (var i = 0; i < crows.length; i++) {
        // Get the row index
        var rindex = getRowIndex(crows[i].getAttribute("id"));

        if (jQuery("#bis_re_rule_value_" + rindex).val() === null || jQuery("#bis_re_rule_value_" + rindex).val() === ""
            || jQuery("#bis_re_rule_value_" + rindex).val() === "[]") {
            bis_alert(breCV.enter_criteria_value);
            return false;
        } else if (jQuery("#bis_re_suboption_id_" + rindex).val() === "6" &&
            (jQuery("#bis_re_condition_id_" + rindex).val() === "1" ||
            jQuery("#bis_re_condition_id_" + rindex).val() === "2")) {

            // Condition for url validation
            if (!bis_validate_url(jQuery("#bis_re_rule_value_" + rindex).val())) {
                return false;
            }
        }
    }

    return true;
}

/**
 * This method will validates the logical rules form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_isValidRulesForm(arr, form, options) {

    var ruleName = jQuery.trim(jQuery("#bis_re_name").val());
    var logicalRuleId = null;
    
    if (jQuery("#bis_re_ex_rule_id") != null && jQuery("#bis_re_ex_rule_id").val()) {
        logicalRuleId = jQuery("#bis_re_ex_rule_id").val();
        jQuery("#bis_re_rule_id").val(logicalRuleId);
    }
    
    if( jQuery('#bis_re_ex_rule_id') != null && 
            jQuery('#bis_re_ex_rule_id').length > 0) {
        logicalRuleId = jQuery("#bis_re_ex_rule_id").val();
        if(logicalRuleId == null) {
            bis_alert(breCV.select_logical_rule); 
            return false;
        }
    } else { 
    
        if (ruleName === "") {
            bis_alert(breCV.enter_rule_name);
            return false;
        } else {
            if (bis_isContainsSpecialChars(ruleName)) {
                bis_alert(breCV.rule_name_special_char);
                return false;
            }
        }
    }    

    var hook = jQuery.trim(jQuery("#bis_re_hook").val());

    if (hook !== "") {
        var status = checkWords(hook);
        if (status) {
            bis_alert(breCV.hook_space_rule);
            return false;
        } else {
            if (bis_isContainsSpecialChars(hook)) {
                bis_alert(breCV.hook_special_char);
                return false;
            }
        }
    }

    return true;

}

/**
 * This method check for any words in contained string.
 * @param str
 *
 * @returns {boolean} : True is param contains string.
 */
function checkWords(str) {

    var wordCount = str.replace(/[^\w ]/g, "").split(/\s+/).length;

    return wordCount > 1;


}

/**
 * This method return row index.
 *
 * @param eid
 * @returns row index Id.
 *
 */
function getRowIndex(eid) {
    var spArray = eid.split("_");
    var clen = spArray.length;
    return Number(spArray[clen - 1]);
}

/**
 * Validates criteria rules.
 *
 * @returns {boolean}
 */
function bis_isValidCriteria() {
    var crows = jQuery(".rule_criteria_row").filter(":visible");

    var left_bracket_sum = 0;
    var right_bracket_sum = 0;
    var subOpt = null;
    var option = null;
    var options = null;
    
    for (var i = 0; i < crows.length; i++) {
        
        // Get the row index
        var rindex = getRowIndex(crows[i].getAttribute("id"));
        option = jQuery("#bis_re_rule_option_" + rindex).val();
        subOpt = jQuery("#bis_re_sub_option_" + rindex).val();
        
        // jQuery('[name = "bis_add_rule_type"]')
        if (jQuery('[name = "bis_add_rule_type"]').val()) {
            if (jQuery("#bis_re_sub_option_" + rindex).val()) {
                subOpt = jQuery("#bis_re_sub_option_" + rindex).val()
                if (subOpt.indexOf("_") >= 0) {
                    options = subOpt.split("_");
                    option = options[0];
                    subOpt = options[1];
                }
            } else {
               bis_alert(breCV.select_criteria);
               return false;
            }
        } 

        left_bracket_sum = Number(jQuery("#bis_re_left_bracket_" + rindex).val()) + left_bracket_sum;
        right_bracket_sum = Number(jQuery("#bis_re_right_bracket_" + rindex).val()) + right_bracket_sum;

        if (rindex < (crows.length-1)) {

            if (jQuery("#bis_re_logical_op_" + rindex).val() === "0" || jQuery("#bis_re_logical_op_" + rindex).val() === 0) {
                bis_alert(breCV.select_operator_value+(rindex+1));
                return false;
            }
        }

        if (option === null || option === "") {
            bis_alert(breCV.select_criteria);
            return false;
        }

        if (subOpt === null || subOpt === "") {
            bis_alert(breCV.select_sub_criteria);
            return false;
        }


        if (jQuery("#bis_re_condition_" + rindex).val() === null || jQuery("#bis_re_condition_" + rindex).val() === "") {
            bis_alert(breCV.select_condition);
            return false;
        }

        if (jQuery("#bis_re_rule_value_" + rindex).val() === null || jQuery("#bis_re_rule_value_" + rindex).val() === ""
            || jQuery("#bis_re_rule_value_" + rindex).val() === "[]") {
            bis_alert(breCV.select_criteria_value);
            return false;

        }

        if (subOpt === "6" &&
            (jQuery("#bis_re_condition_" + rindex).val() === "1" ||
            jQuery("#bis_re_condition_" + rindex).val() === "2")) {

            // Condition for url validation
            if (!bis_validate_url(jQuery("#bis_re_rule_value_" + rindex).val())) {
                return false;
            }
        }
       
        if (subOpt === "6" &&
            jQuery("#bis_re_condition_" + rindex).val() === "13") {
         
            if(!(jQuery("#bis_re_rule_value_" + rindex).val().indexOf("/**") >= 0)) {
                bis_alert(breCV.pattern_not_found);
                return false;
            }
        }
        
        if (subOpt === "15") {

            var inputVal = jQuery("#bis_re_rule_value_" + rindex).val();

            if (jQuery("#bis_re_condition_" + rindex).val() === "1" ||
                    jQuery("#bis_re_condition_" + rindex).val() === "2") {
                // Condition for url validation
                if (!bis_validateIPaddress(inputVal)) {
                    return false;
                }
            } else if (jQuery("#bis_re_condition_" + rindex).val() === "10") {
                var ipList = inputVal.split(",");
                for (i = 0; i < ipList.length; i++) {
                    if (!bis_validateIPaddress(jQuery.trim(ipList[i]))) {
                        return false;
                    }
                }
            } else {
                // Need to work on more specific ip format
                var characterReg = /([0-9]$)/;
                if (!characterReg.test(inputVal)) {
                    bis_alert(breCV.ipaddress_invalid_characters);
                    return false;
                }
            }
        }

    }


    if (right_bracket_sum < left_bracket_sum) {
        bis_alert(breCV.select_right_brackets);
        return false;
    }

    if (left_bracket_sum < right_bracket_sum) {
        bis_alert(breCV.select_left_brackets);
        return false;
    }

    return true;
}


function bis_validateIPaddress(inputText)
{
    var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
    
    if (!inputText.match(ipformat))
    {
        bis_alert(breCV.ipaddress_invalid);
        return false;
    }
    
    return true;
    
}  
/**
 * Generic method to check special characters.
 *
 * @param inputVal
 * @returns {boolean}
 */
function bis_isContainsSpecialChars(inputVal) {
    var characterReg = /[@!#$%^&*()+=}{:;<>?]/;
    return characterReg.test(inputVal);

}
