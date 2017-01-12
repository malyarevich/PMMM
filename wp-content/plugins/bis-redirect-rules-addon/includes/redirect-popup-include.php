<?php
$images_list = $re_rules_engine_modal->get_images_from_media_library();
$pos_img_sizes = $re_rules_engine_modal->get_rule_values(BIS_CONTENT_IMAGE_SIZE);
?>
<div class="row">
    <div class="col-lg-9">
        <div class="input-group">
            <span class="input-group-addon">
                <input <?php echo $showPopupStyle ?>  type="checkbox" name="bis_re_show_popup" id="bis_re_show_popup"/>
            </span>
            <label class="form-control" style="cursor: default;">
                <?php _e("Show confirm redirect PopUp", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>
            </label>
        </div>
    </div>
</div>
<div class="row"> &nbsp;
</div>
<div class="row" id="bis_popup_pannel" style="display:none">
    <div class="form-group col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="container-fluid">
                    <div class="row">
                        <h3 class="panel-title"><label><?php _e("PopUp configuration", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label></h3>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="description"><?php _e("Title", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>
                        </label>
                        <input type="text"  class="form-control" maxlength="100"
                               id="bis_re_popup_title" name="bis_re_popup_title" placeholder="Enter PopUp title">    
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php _e("Auto Redirect Time", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                        <a class="popoverData" target="_blank" 
                           data-content='<?php _e("Redirect in seconds, Never indicate no auto redirect and user action is must.", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>' rel="popover"
                           data-placement="bottom" data-trigger="hover">
                            <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                        </a>
                        <div>
                            <?php
                            $time_selected = "0";

                            if ($popUpVO != null) {
                                $time_selected = $popUpVO->autoCloseTime;
                            }

                            $redirect_time = RedirectRulesUtil::get_auto_redirect_times();
                            ?>
                            <select name="bis_re_auto_red" class="bis_re_multiselect form-control" id="bis_re_auto_red">
                                <?php
                                foreach ($redirect_time as $value) {
                                    $time_sel_attr = "";

                                    if ($time_selected == $value->id) {
                                        $time_sel_attr = "selected=selected";
                                    }
                                    ?>
                                    <option <?php echo $time_sel_attr ?> value="<?php echo $value->id ?>"><?php echo $value->label ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="description"><?php _e("Heading", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                        <input type="text"  class="form-control" maxlength="500"
                               id="bis_re_heading" name="bis_re_heading" placeholder="Enter heading">    
                    </div>
                    <div class="form-group col-md-6">
                        <label for="description"><?php _e("Sub heading", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                        <input type="text"  class="form-control" maxlength="500"
                               id="bis_re_sub_heading" name="bis_re_sub_heading" placeholder="Enter sub heading">    
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="description"><?php _e("Redirect button label", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>
                            <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                        </label>
                        <input type="text"  class="form-control" maxlength="100"
                               id="bis_re_red_btn_label" name="bis_re_red_btn_label" placeholder="<?php _e("Enter redirect button label", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>">    
                    </div>
                    <div class="form-group col-md-6">
                        <label for="description"><?php _e("Cancel button label", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>
                            <span class="glyphicon glyphicon-asterisk bis-icon-red"></span>
                        </label>
                        <input type="text"  class="form-control" maxlength="100"
                               id="bis_re_cancel_button" name="bis_re_cancel_button" placeholder="<?php _e("Enter cancel button label", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>">    
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label><?php _e("Select image", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                        <div>
                            <select class="form-control" name="bis_re_image_id" id="bis_re_image_id"
                                    size="2">
                                        <?php foreach ($images_list as $image) { ?>
                                    <option value="<?php echo $image->ID ?>"
                                            id="<?php echo $image->ID ?>"><?php echo $image->post_title ?></option>
                                        <?php } ?>
                            </select>
                        </div>    
                    </div>
                    <div class="form-group col-md-6">
                        <span class="form-group" id="bis_image_size" style="display:none;">
                            <label><?php _e("Image Size", "rulesengine"); ?></label>
                            <a class="popoverData" target="_blank"
                               data-content='<?php _e("It is recommended to use Thumbnail or Medium images, Some Full or Large images may not fit in the PopUp.", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>' rel="popover"
                               data-placement="bottom" data-trigger="hover">
                                <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
                            </a>
                            <div>
                                <select class="bis_re_multiselect form-control" name="bis_re_cont_img_size" id="bis_re_cont_img_size"
                                        size="2">
                                            <?php foreach ($pos_img_sizes as $img_size) { ?>
                                        <option value="<?php echo $img_size->value ?>"
                                                id="<?php echo $img_size->value ?>"><?php echo $img_size->name ?></option>
                                            <?php } ?>
                                </select>
                            </div>
                        </span>
                    </div>
                </div>
               
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" name="bis_re_custom_popup" id="bis_re_custom_popup"/>
                                    </span>
                                    <label class="form-control" style="cursor: default;">
                                        <?php _e("Customize redirect PopUp", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body" id="bis_cust_popup_panel_body" style="display:none">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="description"><?php _e("PopUp Background color", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                                <input type="text"  class="form-control" maxlength="100"
                                       id="bis_re_popup_color" name="bis_re_popup_color" placeholder="<?php _e("Enter PopUp background in Hex Color Codes (ex: #80EEEE).", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>">    
                            </div>
                            <div class="form-group col-md-6">
                                <label for="popup title"><?php _e("Title css class", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                                <input type="text"  class="form-control" maxlength="100"
                                       id="bis_re_popup_title_class" name="bis_re_popup_title_class" placeholder="<?php _e("Enter PopUp title css class name", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>">    
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="description"><?php _e("Heading css class", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                                <input type="text"  class="form-control" maxlength="100"
                                       id="bis_re_heading_class" name="bis_re_heading_class" placeholder="<?php _e("Enter heading css class name", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>">    
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description"><?php _e("Sub Heading css class", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                                <input type="text"  class="form-control" maxlength="100"
                                       id="bis_re_sub_heading_class" name="bis_re_sub_heading_class" placeholder="<?php _e("Enter sub heading css class name", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>">    
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="description"><?php _e("Redirect button css class", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                                <input type="text"  class="form-control" maxlength="100"
                                       id="bis_re_red_btn_class" name="bis_re_red_btn_class" placeholder="<?php _e("Enter redirect button css class name", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>">    
                            </div>
                            <div class="form-group col-md-6">
                                <label for="description"><?php _e("Cancel button css class", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?></label>
                                <input type="text"  class="form-control" maxlength="100"
                                       id="bis_re_can_btn_class" name="bis_re_can_btn_class" placeholder="<?php _e("Enter cancel button class name", BIS_REDIRECT_RULES_TEXT_DOMAIN); ?>">    
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

<script>
    // wait for the DOM to be loaded
    jQuery(document).ready(function () {
        
        jQuery("#bis_re_custom_popup").click(function(){
            if(jQuery(this).is(':checked')) {
                jQuery("#bis_cust_popup_panel_body").show();
            } else {
                jQuery("#bis_cust_popup_panel_body").hide();
            }
        });
        
        jQuery('#bis_re_image_id').multiselect({
            enableCaseInsensitiveFiltering: true,
            maxHeight: 250,
            onChange: function (element, checked) {
                jQuery("#bis_image_size").show();
            }
        });
    });
</script>    