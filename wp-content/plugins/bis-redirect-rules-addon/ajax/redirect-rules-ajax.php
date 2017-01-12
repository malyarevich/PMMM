<?php

/* ######################################################################################

  Copyright (C) 2015 by Ritu.  All rights reserved.  This software
  is an unpublished work and trade secret of Ritu, and distributed only
  under restriction.  This software (or any part of it) may not be used,
  modified, reproduced, stored on a retrieval system, distributed, or
  transmitted without the express written consent of Ritu.  Violation of
  the provisions contained herein may result in severe civil and criminal
  penalties, and any violators will be prosecuted to the maximum extent
  possible under the law.  Further, by using this software you acknowledge and
  agree that if this software is modified by anyone such as you, a third party
  or Ritu, then Ritu will have no obligation to provide support or
  maintenance for this software.

  ##################################################################################### */

use bis\repf\common\RulesEngineCacheWrapper;
use bis\repf\action\RulesEngine;
use bis\repf\vo\PopUpVO;
use bis\repf\vo\CacheVO;
use bis\repf\vo\RulesVO;
use bis\repf\vo\SearchVO;

add_action('wp_ajax_bis_re_show_add_redirect_rule', 'bis_re_show_add_redirect_rule');
add_action('wp_ajax_bis_re_add_redirect_rule', 'bis_re_add_redirect_rule');
add_action('wp_ajax_bis_re_redirect_rules_list', 'bis_re_redirect_rules_list');
add_action('wp_ajax_bis_re_redirect_delete_rule', 'bis_re_redirect_delete_rule');
add_action('wp_ajax_bis_re_show_edit_redirect_rule', 'bis_re_show_edit_redirect_rule');
add_action('wp_ajax_bis_re_update_redirect_rule', 'bis_re_update_redirect_rule');
add_action('wp_ajax_bis_re_redirect_search_rule', 'bis_re_redirect_search_rule');
add_action('wp_ajax_bis_re_create_redirect_rule', 'bis_re_create_redirect_rule');
add_action('wp_ajax_bis_re_update_next_redirect_rule', 'bis_re_update_next_redirect_rule');
add_action('wp_ajax_bis_re_create_redirect_rule_wizard', 'bis_re_create_redirect_rule_wizard');
add_action('wp_ajax_bis_re_redirect', 'bis_re_redirect');

function bis_re_create_redirect_rule_wizard() {

    $nonce = $_POST ['bis_nonce'];

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $rules_vo = RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_RULEVO);

    $re_rules_engine_modal = new RedirectRulesEngineModel();

    $results_map = $re_rules_engine_modal->save_redirect_rule($rules_vo);
    $status = $results_map[BIS_STATUS];

    if ($status == BIS_SUCCESS) {
        RulesEngineCacheWrapper::set_reset_time(BIS_REDIRECT_RULE_RESET);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_SESSION_RULEVO);
    }

    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_redirect_search_rule() {

    $nonce = $_POST ['bis_nonce'];

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }
    $bis_re_search_by = $_POST ["bis_re_redirect_search_by"];
    $bis_re_search_value = $_POST ["bis_re_redirect_search_value"];
    $bis_re_status = $_POST ["bis_re_redirect_search_status"];

    $redirect_rules_engine_modal = new RedirectRulesEngineModel();

    $search_vo = new SearchVO();
    $search_vo->set_search_by($bis_re_search_by);
    $search_vo->set_search_value($bis_re_search_value);
    $search_vo->set_status($bis_re_status);

    $results_map = $redirect_rules_engine_modal->search_child_rules(BIS_REDIRECT_TYPE_RULE, $search_vo);

    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_update_next_redirect_rule() {

    $nonce = $_POST ['bis_nonce'];

    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $re_rules_engine_modal = new RedirectRulesEngineModel();

    $rule_name = $_POST['bis_re_redirect_name'];

    $rdetail_id = 0;

    if (isset($_POST['bis_re_edit_detail_id'])) {
        $rdetail_id = $_POST['bis_re_edit_detail_id'];
    }
    // Check if redirect rule exists
    $results_map = $re_rules_engine_modal->is_redirect_rule_exists($rule_name, $rdetail_id);
    $status = $results_map[BIS_STATUS];

    if ($status === BIS_ERROR) {
        RulesEngineUtil::generate_json_response($results_map);
    }

    $rules_vo = new RulesVO();

    $rules_vo->set_name($rule_name);
    $rules_vo->set_description($_POST['bis_re_redirect_description']);

    $action = array("target_url" => $_POST['bis_re_target_url'], "redirect_type" => $_POST['bis_re_redirect_type']);

    $rules_vo->set_action(json_encode($action));
    $rules_vo->set_status($_POST['bis_re_rule_status']);
    $rules_vo->set_rule_type_id(BIS_REDIRECT_TYPE_RULE);
    $rules_vo->set_id($rdetail_id);

    if (isset($_POST['bis_re_rule_id'])) {
        $rules_vo->set_logical_rule_id($_POST['bis_re_rule_id']);
    }
    
    $popUpVO = new PopUpVO();

    $show_popUp = null;
    
    $metaContent = file_get_contents(BIS_PLATFORM_HOME_DIR . "/template/bis-redirect-meta-template.html");
    RedirectRulesUtil::update_option(BIS_REDIRECT_META_TEMPLATE, $metaContent);
    
    if (isset($_POST["bis_re_show_popup"])) {
        
        $dynamicContent = file_get_contents(BIS_REDIRECT_RULES_HOME_DIR . "/template/bis-redirect-rules-popup-template.html");
        RedirectRulesUtil::update_option(BIS_REDIRECT_POPUP_TEMPLATE, $dynamicContent);
        
        $show_popUp = 1;
        
        if (isset($_POST["bis_re_popup_title"])) {
            $popUpVO->setTitle($_POST["bis_re_popup_title"]);
        }
        
        if (isset($_POST["bis_re_popup_title_class"])) {
            $popUpVO->setTitleClass($_POST["bis_re_popup_title_class"]);
        }
        
        if (isset($_POST["bis_re_popup_color"])) {
            $popUpVO->setPopUpBackgroundColor($_POST["bis_re_popup_color"]);
        }
        
        if (isset($_POST["bis_re_heading"])) {
         
            $popUpVO->setHeadingOne($_POST["bis_re_heading"]);
        }
        if (isset($_POST["bis_re_heading_class"])) {
            $popUpVO->setHeadingOneClass($_POST["bis_re_heading_class"]);
        }
        if (isset($_POST["bis_re_sub_heading"])) {
            $popUpVO->setHeadingTwo($_POST["bis_re_sub_heading"]);
        }
        if (isset($_POST["bis_re_sub_heading_class"])) {
            $popUpVO->setHeadingTwoClass($_POST["bis_re_sub_heading_class"]);
        }
        
        if (isset($_POST["bis_re_image_id"])) {
            $image_id = $_POST["bis_re_image_id"];
            $image_size = $_POST["bis_re_cont_img_size"];
            $image = $re_rules_engine_modal->get_image_from_media_library($image_id, $image_size);
            $popUpVO->setImageOneId($image_id);
            $popUpVO->setImageOneSize($image_size);
            $popUpVO->setImageOneUrl($image->get_url());
        }

        $popUpVO->setButtonLabelOne($_POST["bis_re_red_btn_label"]);

        if (isset($_POST["bis_re_red_btn_class"])) {
            $popUpVO->setButtonOneClass($_POST["bis_re_red_btn_class"]);
        }
        
        $popUpVO->setButtonLabelTwo($_POST["bis_re_cancel_button"]);

        if (isset($_POST["bis_re_can_btn_class"])) {
            $popUpVO->setButtonTwoClass($_POST["bis_re_can_btn_class"]);
        }
        if (isset($_POST["bis_re_btn_hover"])) {
            $popUpVO->setButtonHoverColor($_POST["bis_re_btn_hover"]);
        }
        if (isset($_POST["bis_re_auto_red"])) {
            $popUpVO->setAutoCloseTime(intval($_POST["bis_re_auto_red"]));
        } else {
            $popUpVO->setAutoCloseTime(0);
        }
        if (isset($_POST["bis_re_country_flag"])) {
            $popUpVO->setCheckBoxOne($_POST["bis_re_country_flag"]);
        }
        $popUpVO->setButtonOneUrl($_POST['bis_re_target_url']);
        
        // show popup
        $rules_vo->set_general_col1($show_popUp);
        $rules_vo->set_general_col2(json_encode($popUpVO));
    }
    
   
    $rules_vo->set_reset_rule_key(BIS_REDIRECT_RULE_RESET);

    RulesEngineCacheWrapper::set_session_attribute(BIS_SESSION_RULEVO, $rules_vo);
    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_update_redirect_rule() {

    $rules_vo = RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_RULEVO);
    $re_rules_engine_modal = new RedirectRulesEngineModel();
    $results_map = $re_rules_engine_modal->update_redirect_rule($rules_vo);

    $status = $results_map[BIS_STATUS];

    if ($status == BIS_SUCCESS) {
        RulesEngineCacheWrapper::set_reset_time(BIS_REDIRECT_RULE_RESET);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_SESSION_RULEVO);
    }

    RulesEngineUtil::generate_json_response($results_map);
}

/**
 * This method shows the list of rules.
 *
 */
function bis_re_show_edit_redirect_rule() {

    $nonce = $_GET ['bis_nonce'];


    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $pluginPath = RedirectRulesUtil::getIncludesDirPath();
    include $pluginPath . "redirect-rule-edit.php";

    flush();
    exit;
}

function bis_re_redirect_delete_rule() {

    $nonce = $_GET ['bis_nonce'];

    $rule_id = $_GET ['ruleId'];

    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    header("Content-Type: application/json");

    $re_rules_engine_modal = new RedirectRulesEngineModel();

    $results_map = $re_rules_engine_modal->delete_redirect_rule($rule_id);

    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_redirect_rules_list() {

    $nonce = $_GET ['bis_nonce'];


    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    // Every plugin should remove the session value
    RulesEngineCacheWrapper::remove_session_attribute(BIS_LOGICAL_RULE_ID);

    $re_rules_engine_modal = new RedirectRulesEngineModel();

    $results_map = $re_rules_engine_modal->get_redirect_rules_list();

    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_add_redirect_rule() {
    $nonce = $_POST ['bis_nonce'];


    //generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $re_rules_engine_modal = new RedirectRulesEngineModel();

    $rule_name = $_POST['bis_re_redirect_name'];

    // Check if redirect rule exists
    $results_map = $re_rules_engine_modal->is_redirect_rule_exists($rule_name);
    $status = $results_map[BIS_STATUS];

    if ($status === BIS_ERROR) {
        RulesEngineUtil::generate_json_response($results_map);
    }

    $rules_vo = new RulesVO();

    $rules_vo->set_name($_POST['bis_re_redirect_name']);
    $rules_vo->set_description($_POST['bis_re_redirect_description']);

    $action = array("target_url" => $_POST['bis_re_target_url'], "redirect_type" => $_POST['bis_re_redirect_type']);

    $rules_vo->set_action(json_encode($action));
    $rules_vo->set_logical_rule_id($_POST['bis_re_rule_id']);
    $rules_vo->set_status($_POST['bis_re_rule_status']);
    $rules_vo->set_rule_type_id(BIS_REDIRECT_TYPE_RULE);
    $rules_vo->set_reset_rule_key(BIS_REDIRECT_RULE_RESET);
           
    RulesEngineCacheWrapper::set_session_attribute(BIS_SESSION_RULEVO, $rules_vo);

    include RedirectRulesUtil::getIncludesDirPath() . 'redirect-rules-parent-add.php';
    flush();
    exit;
}

/**
 * This method is used to get show the add redirect rules page.
 */
function bis_re_show_add_redirect_rule() {


    $nonce = $_POST ['bis_nonce'];

    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    require_once RedirectRulesUtil::getIncludesDirPath() . 'redirect-rules-add.php';

    flush();
    exit();
}

function bis_re_create_redirect_rule() {
    $nonce = $_POST ['bis_nonce'];


    //generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $re_rules_engine_modal = new RedirectRulesEngineModel();

    $rules_vo = new RulesVO();

    $rules_vo->set_name($_POST['bis_re_redirect_name']);
    $rules_vo->set_description($_POST['bis_re_redirect_description']);

    $action = array("target_url" => $_POST['bis_re_target_url'], "redirect_type" => $_POST['bis_re_redirect_type']);

    $rules_vo->set_action(json_encode($action));
    $rules_vo->set_logical_rule_id($_POST['bis_re_rule_id']);
    $rules_vo->set_status($_POST['bis_re_rule_status']);
    $rules_vo->set_rule_type_id(BIS_REDIRECT_TYPE_RULE);
    $rules_vo->set_reset_rule_key(BIS_REDIRECT_RULE_RESET);

    $results_map = $re_rules_engine_modal->save_redirect_rule($rules_vo);

    $status = $results_map[BIS_STATUS];

    if ($status == BIS_SUCCESS) {
        RulesEngineCacheWrapper::set_reset_time(BIS_REDIRECT_RULE_RESET);
    }

    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_redirect_info() {
    
    $nonce = $_GET ['bis_nonce'];

    $rulesVO = RulesEngineCacheWrapper::get_session_attribute(BIS_REDIRECT_POPUP_VO);

    //generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }
    
    $results_map[BIS_STATUS] = BIS_SUCCESS;
    $results_map[BIS_DATA] = $rulesVO;
    
    RulesEngineCacheWrapper::remove_session_attribute(BIS_REDIRECT_POPUP_VO);
    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_redirect_meta($rulesVO) {
    
    $results_map = array();
    $results_map[BIS_STATUS] = BIS_SUCCESS;
    $results_map[BIS_DATA] = $rulesVO;
    
    return $results_map;
}
function bis_re_redirect() {
    
    $query = null;
    $postId = null;
    $categoryId = null;
    $referralUrl = null; 
    $is404 = false;
    $bis_cache_installed = RulesEngineUtil::get_option(BIS_RULES_ENGINE_CACHE_INSTALLED);
    
    if ($bis_cache_installed === "true" || 
            isset($_GET['bis_re_cache_404'])) {
        
        $results_map = array();
        $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;

        if(isset($_GET['bis_prd']) && RulesEngineUtil::is_ajax_request()) {
            RulesEngineCacheWrapper::remove_session_attribute(BIS_REDIRECT_POPUP_VO);
            RulesEngineUtil::generate_json_response($results_map);
        }

        if(isset($_GET['bis_re_cache_post_id'])) {  
            $postId = $_GET['bis_re_cache_post_id'];
        }
        if(isset($_GET['bis_re_cache_cat_id'])) {  
            $categoryId = $_GET['bis_re_cache_cat_id'];
        }
        if(isset($_GET['bis_re_cache_reffer_path'])) {  
            $referralUrl = $_GET['bis_re_cache_reffer_path'];
        }
        if(isset($_GET['bis_re_cache_404'])) {  
            $is404 = $_GET['bis_re_cache_404'];
        }

        $isAjaxRequest = true;
        
        $cacheVO = new CacheVO($postId, $categoryId, $referralUrl, $is404, $isAjaxRequest);
        $rulesEngine = new RulesEngine();
        $rulesEngine->bis_evaluate_request_rules($query, $cacheVO);
    }
    
    $rulesVO = RulesEngineCacheWrapper::get_session_attribute(BIS_REDIRECT_POPUP_VO);
    $results_map = array();
    $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;

    if($rulesVO != null || $rulesVO != FALSE ) { 
        
        if($rulesVO->showpopup == 1) {
            $results_map = bis_re_redirect_popup($rulesVO);
        } else if ($bis_cache_installed === "true" || $is404 === "true") {
            $results_map = bis_re_redirect_meta($rulesVO);
        }

        RulesEngineCacheWrapper::remove_session_attribute(BIS_REDIRECT_POPUP_VO);
    }
    status_header(200);
    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_redirect_popup($rulesVO) {

    $results_map = array();
    $popupMap = array();
    $donotShow = __("Do not show me this message again.", "redirectrules");
    $timerMsg = __("Site redirects in ", "redirectrules");
    $secMsg = __("seconds", "redirectrules");
    $jsonObj = json_decode($rulesVO->popupvo);
    $jsonObj->donot_show_msg = $donotShow;
    
    if($jsonObj->autoCloseTime !== "0") {
        $jsonObj->timerMsg = $timerMsg;
        $jsonObj->secMsg = $secMsg;
    }
    
    // Check if explicit target URL is added, if yes, use the url.
    if(isset($rulesVO->target_url) && $rulesVO->target_url != NULL) {
        $jsonObj->buttonOneUrl = $rulesVO->target_url;
    }

    // Condition for pattern search
    if(isset($rulesVO->patternaction) && $rulesVO->patternaction != NULL) {
        $paction = json_decode($rulesVO->patternaction);
        $jsonObj->buttonOneUrl = $paction->target_url;
        $rulesVO->patternaction = null;
    }
    
    $geoLocVO = RulesEngineCacheWrapper::get_session_attribute(BIS_GEOLOCATION_VO);
    $jsonObj->headingOne = RulesEngineUtil::replace_geo_placeholders($jsonObj->headingOne, $geoLocVO);
    $jsonObj->headingTwo = RulesEngineUtil::replace_geo_placeholders($jsonObj->headingTwo, $geoLocVO);
    $dynamicContent = RulesEngineUtil::get_option(BIS_REDIRECT_POPUP_TEMPLATE);
    $redirectMetaTemplate = RulesEngineUtil::get_option(BIS_REDIRECT_META_TEMPLATE);
    $jsonObj->popupTemplate = $dynamicContent;
    $jsonObj->metaTemplate = $redirectMetaTemplate;
    
    $results_map[BIS_STATUS] = BIS_SUCCESS;
    $popupMap[BIS_GEOLOCATION_DATA] = $geoLocVO;
    $popupMap[BIS_POPUP_DATA] = $jsonObj;
    $results_map[BIS_DATA] = $popupMap;
   
    return $results_map;
}
