/**
 * This method validates the Add Widget Rules Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateEditWidgetRulesForm(arr, form, options) {
    var ruleName = jQuery.trim(jQuery("#bis_re_edit_name").val());

    if (!bis_validateRulesName(ruleName)) {
        return false;
    }

    var widgets = jQuery.trim(jQuery("#bis_re_widget_list").val());

    if (widgets === "") {
        bis_alert(breCV.select_widgets);
        return false;
    }

}

/**
 * This method validates the Add Widget Rules Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateAddWidgetRulesForm(arr, form, options) {
    var ruleName = jQuery.trim(jQuery("#bis_re_widget_rule").val());

    if (!bis_validateRulesName(ruleName)) {
        return false;
    }

    var sidebar = jQuery.trim(jQuery("#bis_re_widget_side_bar").val());

    if (sidebar === "") {
        bis_alert(breCV.select_sidebar);
        return false;
    }



    var widgets = jQuery.trim(jQuery("#bis_re_widget_list").val());

    if (widgets === "") {
        bis_alert(breCV.select_widgets);
        return false;
    }

    var rule = jQuery.trim(jQuery("#bis_re_widget_rule_id").val());

    if (rule === "") {
        bis_alert(breCV.select_logical_rule);
        return false;
    }

    var action = jQuery.trim(jQuery("#bis_re_widget_action").val());

    if (action === "") {
        bis_alert(breCV.select_action);
        return false;
    }

}

/**
 * This method validates the Add Post Rules Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateEditPostRulesForm(arr, form, options) {
    var ruleName = jQuery.trim(jQuery("#bis_re_edit_name").val());

    if (!bis_validateRulesName(ruleName)) {
        return false;
    }

    var postName = jQuery.trim(jQuery("#bis_re_post_edit_option").val());

    if (postName === "" || postName === "[]") {
        bis_alert(breCV.enter_posts);
        return false;
    }

    var action = jQuery("#bis_re_post_edit_action").val();

    if (!(action === "append_image_post" || action === "hide_post")) {

        var content = jQuery.trim(jQuery("#bis_re_post_dc_content").val());

        if (content === "") {
            if(action === "append_existing_scode_post") {
                bis_alert(breCV.enter_shortcode);
            } else {
                bis_alert(breCV.enter_content_body);
            }
            return false;
        }

    }

    var location = jQuery.trim(jQuery("#bis_re_cont_pos").val());

    if (location === "" && !(action === "replace_post_content"
        || action === "hide_post" )) {
        bis_alert(breCV.select_location);
        return false;
    }

    // Validation for image
    if (action.indexOf("image") >= 0) {

        var image = jQuery.trim(jQuery("#bis_re_image_id").val());

        if (image === "") {
            bis_alert(breCV.select_image);
            return false;
        }

        var image_size = jQuery("#bis_re_cont_img_size").val();

        if (image_size === null || image_size === "") {
            bis_alert(breCV.select_image_size);
            return false;
        }

    }

    return true;

}

/**
 * This method validates the Add Post Rules Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateAddPostRulesForm(arr, form, options) {
    var ruleName = jQuery.trim(jQuery("#bis_re_post_rule").val());

    if (!bis_validateRulesName(ruleName)) {
        return false;
    }

    var rule = jQuery.trim(jQuery("#bis_re_post_rule_id").val());

    if (rule === "") {
        bis_alert(breCV.select_logical_rule);
        return false;
    }

    var postName = jQuery.trim(jQuery("#bis_re_post_name").val());

    if (postName === "") {
        bis_alert(breCV.enter_posts);
        return false;
    }

    var action = jQuery.trim(jQuery("#bis_re_post_action").val());

    if (action === "") {
        bis_alert(breCV.select_action);
        return false;
    }

    if (!(action === "append_image_post" || action === "hide_post")) {

        var content = jQuery.trim(jQuery("#bis_re_post_dc_content").val());

        if (content === "") {
            if(action === "append_existing_scode_post") {
                bis_alert(breCV.enter_shortcode);
            } else {
                bis_alert(breCV.enter_content_body);
            }
            return false;
        }
    }

    var location = jQuery.trim(jQuery("#bis_re_cont_pos").val());

    if (location === "" && !(action === "replace_post_content"
        || action === "hide_post" || action === "append_existing_scode_post")) {
        bis_alert(breCV.select_location);
        return false;
    }

    // Validation for image
    if (action.indexOf("image") >= 0) {

        var image = jQuery.trim(jQuery("#bis_re_image_id").val());

        if (image === "") {
            bis_alert(breCV.select_image);
            return false;
        }

        var image_size = jQuery("#bis_re_cont_img_size").val();

        if (image_size === null || image_size === "") {
            bis_alert(breCV.select_image_size);
            return false;
        }

    }

    return true;

}

/**
 * This method validates the Rules Settings Form.
 *
 * @param arr
 * @param form
 * @param options
 * @returns {boolean}
 */
function bis_validateRulesSettingsForm(arr, form, options) {
    var userName = jQuery.trim(jQuery("#geonameuser").val());
    var messages = {};
    messages[0] = breCV.enter_geoname_name;

    if (!bis_validateName(userName, messages)) {
        return false;
    }
    return true;

}