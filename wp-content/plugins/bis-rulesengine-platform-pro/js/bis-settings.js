function bis_verify_pcode(productId, apikey) {
  
    jQuery("#bis_re_pc_" + productId).html("<i class=\"glyphicon glyphicon-time bis-glyphi-position\"></i>" + breCV.pc_button_please_wait);
    jQuery("#bis_re_pc_" + productId).attr("disabled", "true")
    jQuery("#pc_" + productId).attr("readonly", "true");

    var pCode = jQuery("#pc_" + productId).val();

    var mpURL = "https://marketplace.envato.com/api/v3/rules4wp/" + apikey + "/verify-purchase:" + pCode + ".json";
    jQuery.get(mpURL, function (data) {

        var licDivWidth = jQuery(".bis-verify-lic-div").width() - 52;

        jQuery("#" + productId + "_sId").width(licDivWidth);

        var vData = data["verify-purchase"];
        var buyer = vData["buyer"];

        if (buyer != null) {
            bis_save_success_ver(vData, productId, pCode);
        } else {
            bis_licver_showErrorMessage(productId, breCV.invalid_purchase_code);
            bis_save_fail_ver(productId, pCode);
        }

    }).fail(function () {
        bis_licver_showErrorMessage(productId, breCV.error_verify_pcode);
        bis_save_fail_ver(productId, pCode);

    });
}

function bis_reset_activate_button(productId) {
    
    jQuery("#bis_re_pc_" + productId).html("<i class=\"glyphicon glyphicon-ok-sign bis-glyphi-position\"></i>" + breCV.pc_button_activate);
    jQuery("#bis_re_pc_" + productId).removeAttr("disabled");
    jQuery("#pc_" + productId).removeAttr("readonly");
    
}

function bis_validate_dates() {
    var fromDate = jQuery.trim(jQuery("#bis_re_from_date").val());
    var toDate = jQuery.trim(jQuery("#bis_re_to_date").val());

    if (fromDate === "") {
        bis_alert(breCV.bis_select_from_date);
        return false;
    }
    
    if (toDate === "") {
        bis_alert(breCV.bis_select_to_date);
        return false;
    }
    
    if(fromDate > toDate) {
        bis_alert(breCV.bis_from_greater_to_date);
        return false;
    }
}

function bis_save_success_ver(vData, productId, pCode) {

    jQuery.post(
            BISAjax.ajaxurl,
            {
                action: 'bis_re_activate_plugin',
                bis_nonce: BISAjax.bis_rules_engine_nonce,
                item_name: vData["item_name"],
                item_id: vData["item_id"],
                buyer: vData["buyer"],
                licence_type: vData["licence"],
                product_id: productId,
                purchase_date: vData['created_at'],
                pur_code: pCode
            },
        function (data) {
            jQuery("#" + productId + "_smessage").html(breCV.pc_plugin_act);
            jQuery("#" + productId + "_sId").show();

            jQuery("#" + productId + "_close_sId").click(function () {
                jQuery("#" + productId + "_sId").hide();
            });

        }).always(function () {
            bis_reset_activate_button(productId);
        });

}

function bis_save_fail_ver(productId, pCode) {
    jQuery.post(
            BISAjax.ajaxurl,
            {action: 'bis_re_acplg_error',
                bis_nonce: BISAjax.bis_rules_engine_nonce,
                product_id: productId,
                pur_code: pCode
            },
    function (data) {
        // do nothing
    }).always(function () {
        bis_reset_activate_button(productId);
    });

}

function bis_licver_showErrorMessage(productId, message) {
    var licDivWidth = jQuery(".bis-verify-lic-div").width() - 52;
    jQuery("#" + productId + "_fId").width(licDivWidth);
    jQuery("#" + productId + "_fmessage").html(message);
    jQuery("#" + productId + "_fId").show();

    jQuery("#" + productId + "_close_fId").click(function () {
        jQuery("#" + productId + "_fId").hide();
    });
}