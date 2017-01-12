<?php  
$countryCheckbox = "";
$showPopupStyle = "";
$popUpVO = null;
$re_rules_engine_modal = new RedirectRulesEngineModel();
?>
<form name="bis_re_addredirectruleform" id="bis_re_addredirectruleform" method="POST">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="container-fluid">
                <div class="row">
                    <h3 class="panel-title"><label><?php _e("New Redirect Rule", "redirectrules"); ?></label></h3>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="form-group col-md-9">
                    <label><?php _e("Rule Name", "redirectrules"); ?></label>
                    <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                    <a class="popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE ?>"
                       data-content='<?php _e("Name is required and should be unique.", "redirectrules"); ?>' rel="popover"
                       data-placement="bottom" data-trigger="hover">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    </a>
                    <input type="text"  class="form-control" maxlength="50"
                           id="bis_re_redirect_name" name="bis_re_redirect_name" placeholder="Enter rule name">
                </div>
                <div class="form-group col-md-3">
                    <label><?php _e("Status", "redirectrules"); ?></label>

                    <div>
                        <select name="bis_re_rule_status" class="form-control" id="bis_re_rule_status">
                            <option value="1"><?php _e("Active", "redirectrules"); ?></option>
                            <option value="0"><?php _e("Deactive", "redirectrules"); ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-9">
                    <label><?php _e("Target URL", "redirectrules"); ?></label>
                    <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                    <a class="popoverData" target="_blank" href="<?php echo BIS_RULES_ENGINE ?>"
                       data-content='<?php _e("Use /** at the end of the URL, to copy the parameters from source URL. i.e http://site.com/page1/**", "redirectrules"); ?>'
                       rel="popover"
                       data-placement="bottom" data-trigger="hover">
                        <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                    </a>

                    <input type="text" class="form-control" 
                           placeholder='<?php _e("Enter Redirect URL", "redirectrules"); ?>' id="bis_re_target_url"
                           name="bis_re_target_url">
                </div>
                <div class="form-group col-md-3">

                    <label><?php _e("Redirect Type", "redirectrules"); ?></label>

                    <div>
                        <select name="bis_re_redirect_type" class="form-control" id="bis_re_redirect_type">
                            <option value="301">301</option>
                            <option value="302">302</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-9">
                    <label for="description"><?php _e("Description", "redirectrules"); ?></label>
                    <textarea rows="1" maxlength="500" class="form-control" name="bis_re_redirect_description"
                              id="bis_re_redirect_description" placeholder='<?php _e("Enter rule description", "redirectrules") ?>'> </textarea>
                </div>
            </div>
              <?php include_once 'redirect-popup-include.php' ?>
        </div>    
    </div>
    <input type="hidden" id="bis_re_rd_add" name="bis_re_rd_add" value="true" />   
</form>

<?php include_once(ABSPATH . "wp-content/plugins/" . BIS_REDIRECT_RULESENGINE_PLATFORM . "/includes/logical-rules-parent-add.php"); ?>

<div class = "row">
    <div class = "col-md-10">
        <button type="button" id="bis_re_show_redirect_rules_list" class="btn btn-primary">
            <i class="glyphicon glyphicon-chevron-left bis-glyphi-position"></i>
            <?php _e("Go Back", "redirectrules"); ?>
        </button>
    </div>
    <div class="col-md-2" align="right">
        <button id="bis_re_redirect_submit" type="button" class="btn btn-primary">
            <i class="glyphicon glyphicon-ok-sign bis-glyphi-position"></i><?php _e(" Save Rule", "rulesengine"); ?>
        </button>
    </div>
</div>
<script>

    // wait for the DOM to be loaded
    jQuery(document).ready(function () {

        var options = {
            success: showResponse,
            beforeSend: bis_before_request_send,
            url: BISAjax.ajaxurl,
            beforeSubmit: bis_validateAddRedirectRulesForm,
            data: {
                action: 'bis_re_update_next_redirect_rule',
                bis_nonce: BISAjax.bis_rules_engine_nonce
            }
        };

        jQuery("#bis_re_redirect_submit").click(function () {
            jQuery("#bis_re_addredirectruleform").submit();
        });


        jQuery('#bis_re_rule_status').multiselect({
            buttonWidth: '250px'
        });
        
        jQuery('#bis_re_auto_red').multiselect({
            enableCaseInsensitiveFiltering: true,
            maxHeight: 250,
        });

        jQuery('#bis_re_redirect_type').multiselect({
            buttonWidth: '250px'
        });

        jQuery('#bis_re_addredirectruleform').ajaxForm(options);


        function showCreateRedirectResponse(responseText, statusText, xhr, $form) {


            if (responseText["status"] === "error") {
                jQuery("#alert_failure_id").show();

                if (responseText["message_key"] === "duplicate_entry") {
                    bis_showErrorMessage('<?php _e("Duplicate Redirect Rule name, Redirect Rule name should be unique.", "rulesengine"); ?>');
                } else {
                    bis_showErrorMessage('<?php _e("Error occurred while creating Redirect rule", "rulesengine"); ?>');
                }

            } else {
                jQuery("#redirect_rules_child_content").html("");
                jQuery("#redirect_rules_parent_content").html("")
                bis_showRedirectRulesList();
            }
        }

        function showResponse(data, statusText, xhr, $form) {

            // If Logical rule select then enable below code.

            if (data["status"] === "error") {
                jQuery("#alert_failure_id").show();

                if (data["message_key"] === "duplicate_entry") {
                    bis_showErrorMessage('<?php _e("Duplicate Redirect Rule name, Redirect Rule name should be unique.", "redirectrules"); ?>');
                } else {
                    bis_showErrorMessage('<?php _e("Error occurred while creating Redirect rule", "redirectrules"); ?>');
                }
            } else {
                jQuery("#bis-addlogicalruleform").submit();
            }
        }

        jQuery("#bis_re_show_redirect_rules_list").click(function () {
            bis_showRedirectRulesList();
        });

        jQuery("#bis_redirect_back_add_rules").click(function () {
            jQuery("#redirect_rules_parent_content").hide();
            jQuery("#redirect_rules_child_content").show();
        });

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
        
         jQuery("#bis_re_cont_img_size").multiselect('select', 'thumbnail');

    });
</script>
