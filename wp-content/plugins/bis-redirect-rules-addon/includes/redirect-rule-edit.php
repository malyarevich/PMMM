<?php

use bis\repf\common\RulesEngineCacheWrapper;

$re_rules_engine_modal = new RedirectRulesEngineModel();
$rule_id = $_GET["ruleId"];
$rr_details = $re_rules_engine_modal->get_redirect_applied_rule($rule_id);
$action_values = json_decode($rr_details->action);
$showPopUp = $rr_details->showpopup;

$showPopupStyle = "";
$countryCheckbox = "";
$showCountry = null;
$popUpVO = null;

if($showPopUp === "1") {
    $popUpVO = json_decode($rr_details->popupvo);
    $showPopupStyle = "checked=checked";
    $showCountry = $popUpVO->checkBoxOne;
}


if($showCountry === "on") {
    $countryCheckbox = "checked=checked";
}


RulesEngineCacheWrapper::set_session_attribute(BIS_LOGICAL_RULE_ID, $rr_details->rule_id);

$bis_re_rule_id = $rr_details->rule_id;
?>
<form name="bis_re_addredirectruleform" id="bis_re_addredirectruleform"
      method="POST">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="container-fluid">
                <div class="row">
                    <h3 class="panel-title"><label><?php _e("Edit Redirect Rule", "redirectrules");?></label></h3>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="form-group col-md-9">
                    <label><?php _e("Rule Name", "redirectrules");?></label>
                    <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                    <a class="popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE ?>"
                       data-content='<?php _e("Name is required and should be unique.", "redirectrules");?>' rel="popover"
                       data-placement="bottom" data-trigger="hover">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    </a>
                    <input type="text" class="form-control" maxlength="50"
                           value="<?php echo $rr_details->redirect_rule_name; ?>"
                           id="bis_re_redirect_name" name="bis_re_redirect_name" placeholder="Enter rule name">
                </div>
                <div class="form-group col-md-3">
                    <label><?php _e("Status", "redirectrules"); ?></label>

                    <div>
                        <select name="bis_re_rule_status" size="2" class="form-control" id="bis_re_rule_status">
                            <option value="1"><?php _e("Active", "redirectrules"); ?></option>
                            <option value="0"><?php _e("Deactive", "redirectrules"); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-9">
                    <label><?php _e("Target URL", "redirectrules");?></label>
                    <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                    <a class="popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE ?>"
                       data-content='<?php _e("Use /** at the end of the URL, to copy the parameters from source URL. i.e http://site.com/page1/**", "redirectrules");?>'
                       rel="popover"
                       data-placement="bottom" data-trigger="hover">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    </a>
                    <input type="text" class="form-control" value="<?php echo $action_values->target_url; ?>"
                           placeholder='<?php _e("Enter Redirect URL", "redirectrules");?>' id="bis_re_target_url"
                                                     name="bis_re_target_url">
                </div>
                <div class="form-group col-md-3">
                    <label><?php _e("Redirect Type", "redirectrules"); ?></label>
                    <div>
                        <select name="bis_re_redirect_type" size="2" class="form-control" id="bis_re_redirect_type">
                            <option value="301">301</option>
                            <option value="302">302</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-9">
                    <label><?php _e("Description", "redirectrules"); ?></label>
                    <textarea rows="1" maxlength="500" class="form-control" name="bis_re_redirect_description"
                              id="bis_re_redirect_description"
                              placeholder='<?php _e("Enter rule description", "redirectrules") ?>'><?php echo stripslashes($rr_details->description) ?></textarea>
                </div>
            </div>
            <?php include_once 'redirect-popup-include.php' ?>
        </div>
    </div>
    </div>

    <input type="hidden" id="bis_re_rule_id" name="bis_re_rule_id" value="<?php echo $bis_re_rule_id; ?>"/>
    <input type="hidden" id="bis_re_edit_detail_id" name="bis_re_edit_detail_id" value="<?php echo $rule_id; ?>"/>
</form>
<?php include_once(ABSPATH . "wp-content/plugins/" . BIS_REDIRECT_RULESENGINE_PLATFORM . "/includes/logical-rules-parent-edit.php"); ?>
    <div class="row">
        <div class="form-group col-md-5">
            <button type="button" id="bis_re_show_redirect_rules_list" class="btn btn-primary">
                <i class="glyphicon glyphicon-chevron-left bis-glyphi-position"></i><?php _e("Go Back", "redirectrules"); ?>
            </button>
        </div>
        <div class="form-group col-md-2 col-md-offset-5" align="right">
            <button id="bis_re_redirect_update"  type="button" class="btn btn-primary">
                <?php _e(" Update Rule", "rulesengine"); ?>
            </button>
        </div>
    </div>
<script>

    // wait for the DOM to be loaded
    jQuery(document).ready(function () {

        jQuery("#bis_re_redirect_update").click(function() {
            jQuery("#bis_re_addredirectruleform").submit();
        });
        
        jQuery('.popoverData').popover();

        jQuery('#bis_re_rule_status').multiselect({
            buttonWidth: '300px'
        });
        
        jQuery('.bis_re_multiselect').multiselect({
            enableCaseInsensitiveFiltering: true,
            maxHeight: 250
        });

        jQuery('#bis_re_redirect_type').multiselect({
            buttonWidth: '300px'
        });

        var options = {
            success: showResponse,
            beforeSend: bis_before_request_send,
            url: BISAjax.ajaxurl,
            beforeSubmit: bis_validateEditRedirectRulesForm,

            data: {
                action: 'bis_re_update_next_redirect_rule',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        jQuery('#bis_re_addredirectruleform').ajaxForm(options);

        function showResponse(responseText, statusText, xhr, $form) {

            if (responseText["status"] === "error") {
                
                jQuery("#alert_failure_id").show();

                if (responseText["message_key"] === "duplicate_entry") {
                    bis_showErrorMessage('<?php _e("Duplicate Redirect Rule name, Redirect Rule name should be unique.", "redirectrules");?>');
                } else {
                    bis_showErrorMessage('<?php _e("Error occurred while creating Redirect rule", "redirectrules");?>');
                }
                
            } else {
               jQuery("#bis-editlogicalruleform").submit();
            }
        }
        
        if (jQuery("#bis_re_show_popup").is(':checked')) {
            jQuery("#bis_popup_pannel").show();
            jQuery("#bis_re_popup_title").val('<?php echo $popUpVO ? $popUpVO->title : ""  ?>');
            jQuery("#bis_re_popup_color").val('<?php echo $popUpVO ? $popUpVO->popUpBackgroundColor : "" ?>');
            jQuery("#bis_re_heading").val('<?php echo $popUpVO ? $popUpVO->headingOne : "" ?>');
            jQuery("#bis_re_heading_class").val('<?php echo $popUpVO ? $popUpVO->headingOneClass : "" ?>');
            jQuery("#bis_re_sub_heading").val('<?php echo $popUpVO ? $popUpVO->headingTwo : "" ?>');
            jQuery("#bis_re_sub_heading_class").val('<?php echo $popUpVO ? $popUpVO->headingTwoClass : "" ?>');
            jQuery("#bis_re_red_btn_label").val('<?php echo $popUpVO ? $popUpVO->buttonLabelOne : "" ?>');
            jQuery("#bis_re_red_btn_class").val('<?php echo $popUpVO ? $popUpVO->buttonOneClass : "" ?>');
            jQuery("#bis_re_cancel_button").val('<?php echo $popUpVO ? $popUpVO->buttonLabelTwo : "" ?>');
            jQuery("#bis_re_can_btn_class").val('<?php echo $popUpVO ? $popUpVO->buttonTwoClass : "" ?>');
            jQuery("#bis_re_btn_hover").val('<?php echo $popUpVO ? $popUpVO->buttonHoverColor : "" ?>');
            jQuery("#bis_re_popup_title_class").val('<?php echo $popUpVO ? $popUpVO->titleClass : "" ?>');
        }
        
        jQuery("#bis_re_show_popup").click(function () {
            if (jQuery(this).is(':checked')) {
                jQuery("#bis_popup_pannel").show();
                var targetDomain = bis_extractDomain(jQuery("#bis_re_target_url").val());
                targetDomain = bis_capsFirstLetter(targetDomain);
                var currentDomain = bis_extractDomain(window.location.href);
                currentDomain = bis_capsFirstLetter(currentDomain);
                jQuery("#bis_re_popup_title").val(targetDomain);
                jQuery("#bis_re_red_btn_label").val("Go to "+targetDomain);
                jQuery("#bis_re_cancel_button").val("Stay On "+currentDomain);
            } else {
                jQuery("#bis_popup_pannel").hide();    
                jQuery("#bis_re_popup_title").val("");
                jQuery("#bis_re_red_btn_label").val("");
                jQuery("#bis_re_cancel_button").val("")
            }    
        });
        
        jQuery("#bis_re_show_redirect_rules_list").click(function () {
            bis_showRedirectRulesList();
        });

        var image = '<?php echo $popUpVO ? $popUpVO->imageOneId : "" ?>';
        
        if(image !== "") {
            jQuery("#bis_re_image_id").multiselect('select', '<?php echo $popUpVO ? $popUpVO->imageOneId : ""?>');
            jQuery("#bis_re_cont_img_size").multiselect('select', '<?php echo $popUpVO ? $popUpVO->imageOneSize : ""?>');
            jQuery("#bis_image_size").show();
        }
        jQuery("#bis_re_rule_status").multiselect('select', '<?php echo $rr_details->status ?>');
        jQuery("#bis_re_redirect_type").multiselect('select', '<?php echo $action_values->redirect_type ?>');

    });
</script>
