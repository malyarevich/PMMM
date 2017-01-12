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

use bis\repf\model\LogicalRulesEngineModel;
use bis\repf\vo\SearchVO;
use bis\repf\vo\LogicalRulesCriteriaVO;
use bis\repf\vo\LogicalRulesVO;
use bis\repf\common\RulesEngineCacheWrapper;
use bis\repf\common\RulesEngineLocalization;

add_action('wp_ajax_bis_get_logical_rules', 'bis_get_logical_rules');
add_action('wp_ajax_bis_get_sub_options', 'bis_get_sub_options');
add_action('wp_ajax_bis_get_conditions', 'bis_get_conditions');
add_action('wp_ajax_bis_create_logical_rule', 'bis_create_logical_rule');
add_action('wp_ajax_bis_create_logical_rule_new', 'bis_create_logical_rule_new');
add_action('wp_ajax_bis_re_get_value', 'bis_re_get_value');
add_action('wp_ajax_bis_re_delete_rule', 'bis_re_delete_rule');
add_action('wp_ajax_bis_re_edit_rule', 'bis_re_edit_rule');
add_action('wp_ajax_bis_re_new_rule', 'bis_re_new_rule');
add_action('wp_ajax_bis_re_update_rule', 'bis_re_update_rule');
add_action('wp_ajax_bis_re_update_rule_new', 'bis_re_update_rule_new');
add_action('wp_ajax_bis_re_get_rule_values', 'bis_re_get_rule_values');
add_action('wp_ajax_bis_re_search_rule', 'bis_re_search_rule');

add_action('wp_ajax_bis_re_basic_rule_include', 'bis_re_basic_rule_include');
add_action('wp_ajax_bis_re_advance_rule_include', 'bis_re_advance_rule_include');
add_action('wp_ajax_bis_re_exising_rule_include', 'bis_re_exising_rule_include');

add_action('wp_ajax_bis_re_basic_rule_edit_include', 'bis_re_basic_rule_edit_include');
add_action('wp_ajax_bis_re_advance_rule_edit_include', 'bis_re_advance_rule_edit_include');
add_action('wp_ajax_bis_re_exising_rule_edit_include', 'bis_re_exising_rule_edit_include');

add_action('wp_ajax_bis_re_logical_rule', 'bis_re_logical_rule');
add_action('wp_ajax_bis_re_use_exising_rule', 'bis_re_use_exising_rule');


function bis_re_use_exising_rule() {
    
    if(isset($_POST['bis_nonce'])) {
        $nonce = $_POST ['bis_nonce'];
    } else {
        $nonce = $_POST ['bis_rules_engine_nonce'];
    }
    
    $rule_id = $_POST ['bis_re_ex_rule_id'];

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $rules_vo = RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_RULEVO);
    $logical_rules_engine_modal = new LogicalRulesEngineModel();
    
    if ($rules_vo != null) {
        $rules_vo->set_logical_rule_id($rule_id);
        
        // create rule
        if($rules_vo->get_id() == null || $rules_vo->get_id() == 0) {
            $results_map = $logical_rules_engine_modal->save_child_rule($rules_vo);
        } else { // update child rule
            $results_map = $logical_rules_engine_modal->update_child_rule($rules_vo);
        }
        
        $status = $results_map[BIS_STATUS];
        
        if ($status == BIS_SUCCESS) {
            RulesEngineCacheWrapper::set_reset_time($rules_vo->get_reset_rule_key());
        }
    }
    
    RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);
    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_logical_rule() {
    $nonce = $_GET ['bis_nonce'];
    $ruleId = $_GET ['rule_id'];

    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();
    
    $logical_rules_engine_modal = new LogicalRulesEngineModel();

    $results_map = $logical_rules_engine_modal->get_logical_rule($ruleId);

    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_advance_rule_include() {
    bis_re_file_include("logical-rules-advance-add-body.php");
}

function bis_re_advance_rule_edit_include() {
    bis_re_file_include("logical-rules-advance-edit-body.php");
}

function bis_re_basic_rule_edit_include() {
    bis_re_file_include("logical-rules-basic-edit-body.php");
}

function bis_re_basic_rule_include() {
    bis_re_file_include("logical-rules-basic-add-body.php");
}

function bis_re_exising_rule_include() {
    bis_re_file_include("bis-use-existing-rule.php");
}

function bis_re_file_include($file_include) {

    $nonce = $_GET ['bis_nonce'];
   
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();

    $pluginPath = RulesEngineUtil::getIncludesDirPath();
    include $pluginPath . $file_include;
    flush();
    exit;
}

function bis_re_search_rule() {

    $nonce = $_POST ['bis_nonce'];

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $bis_re_search_by = $_POST ["bis_re_search_by"];
    $bis_re_search_value = $_POST ["bis_re_search_value"];
    $bis_re_status = $_POST ["bis_re_search_status"];

    $logical_rules_engine_modal = new LogicalRulesEngineModel();

    $search_vo = new SearchVO();
    $search_vo->set_search_by($bis_re_search_by);
    $search_vo->set_search_value($bis_re_search_value);
    $search_vo->set_status($bis_re_status);

    $results_map = $logical_rules_engine_modal->search_logical_rules($search_vo);

    RulesEngineUtil::generate_json_response($results_map);
}

/**
 *
 * Gets the rule values based on the sub option Id
 *
 */
function bis_re_get_rule_values() {


    $nonce = $_GET ['bis_nonce'];

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $logical_rules_engine_modal = new LogicalRulesEngineModel();

    $sub_option_id = intval($_GET["bis_sub_criteria"]);

    $rows = $logical_rules_engine_modal->get_rule_values($sub_option_id);

    wp_send_json($rows);
}

/**
 *
 * Updates the logical rule.
 *
 */
function bis_re_update_rule() {

    $nonce = $_POST ['bis_nonce'];

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $rule_criteria_array = array();

    $bis_re_condition = $_POST ['bis_re_condition'];
    $bis_re_rule_option = $_POST ['bis_re_rule_option'];
    $bis_re_rule_value = $_POST ['bis_re_rule_value'];
    $bis_re_sub_option = $_POST ['bis_re_sub_option'];
    $bis_re_logical_op = $_POST ['bis_re_logical_op'];
    $bis_re_left_bracket = $_POST ['bis_re_left_bracket'];
    $bis_re_right_bracket = $_POST ['bis_re_right_bracket'];
    $bis_re_sub_opt_type_id = $_POST ['bis_re_sub_opt_type_id'];
    $bis_re_rId = $_POST ['bis_re_rId'];
    $bis_add_rule_type = $_POST ['bis_add_rule_type'];
    $bis_re_status = $_POST ['bis_re_status'];
    $bis_re_eval_type = $_POST ['bis_re_eval_type'];
    $bis_re_rcId = $_POST ['bis_re_rcId'];

    $bis_re_delete = $_POST['bis_re_delete'];


    $bis_re_rule_type = "option";
    // Loop over the list of rule criteria
    for ($rows = 0; $rows < count($bis_re_rule_option); $rows++) {

        $logical_rules_criteria_vo = new LogicalRulesCriteriaVO();

        if (isset($bis_re_rcId[$rows])) {
            $logical_rules_criteria_vo->set_Id($bis_re_rcId[$rows]);
        }

        $sub_option = (int) $bis_re_sub_option[$rows];
        $bis_re_rule_value_str = $bis_re_rule_value[$rows];
        $condition = (int) $bis_re_condition[$rows];
        $value_type_id = (int) $bis_re_sub_opt_type_id[$rows];

        // Check for Input Token
        if ($value_type_id == 1) {

            // If condition = equal or condition = not equal
            if ($condition == 1 || $condition == 2) {
                $bis_re_rule_value_str = stripslashes($bis_re_rule_value_str);
            }
        }

        $option_id = (int) $bis_re_rule_option[$rows];

        if ($option_id == 9 || $option_id == 10 || $option_id == 3 || $option_id == 12
                || $option_id == 11) {
            $logical_rules_criteria_vo->set_evalType(BIS_EVAL_REQUEST_TYPE);
        }

        // Option is Request
        if ($option_id == 3 || $sub_option == 32) {

            // Filter the url
            if ($sub_option == 6 && ($condition == 1 || $condition == 2)) {
                $bis_re_rule_value_str = RulesEngineUtil::get_filter_url($bis_re_rule_value_str);
            }
            $bis_re_rule_value_str = json_encode(array(array("id" => $bis_re_rule_value_str)));
        }

        // Save Only Ids for Posts and Pages
        if ($option_id == 9 || $option_id == 10 || $option_id == 12) {
            $bis_re_rule_value_str = RulesEngineUtil::get_json_ids($bis_re_rule_value_str);
        }

        $logical_rules_criteria_vo->set_rightBracket($bis_re_right_bracket[$rows]);
        $logical_rules_criteria_vo->set_leftBracket($bis_re_left_bracket[$rows]);
        $logical_rules_criteria_vo->set_value($bis_re_rule_value_str);
        $logical_rules_criteria_vo->set_optionId($option_id);
        $logical_rules_criteria_vo->set_subOptionId($bis_re_sub_option[$rows]);
        $logical_rules_criteria_vo->set_conditionId($bis_re_condition[$rows]);
        $logical_rules_criteria_vo->set_ruleType($bis_re_rule_type);
        $logical_rules_criteria_vo->set_operatorId($bis_re_logical_op[$rows]);

        array_push($rule_criteria_array, $logical_rules_criteria_vo);
    }


    $bis_re_name = $_POST ['bis_re_name'];
    $bis_re_description = $_POST ['bis_re_description'];
    $bis_re_hook = trim($_POST ['bis_re_hook']);


    // Validation for hook
    if ($bis_re_hook != null && $bis_re_hook !== "" && !function_exists($bis_re_hook) === true) {
        $results_map = array();
        $results_map[BIS_STATUS] = BIS_ERROR;
        $results_map[BIS_MESSAGE_KEY] = BIS_NO_METHOD_FOUND;
        RulesEngineUtil::generate_json_response($results_map);
    }


    $logical_rules_vo = new LogicalRulesVO($bis_re_name, $bis_re_description, 
            $bis_re_hook, $rule_criteria_array, $bis_re_eval_type, $bis_add_rule_type);
    
    // Call to model to logical rules
    $logical_rules_engine_modal = new LogicalRulesEngineModel();

    $logical_rules_vo->set_id($bis_re_rId);
    $logical_rules_vo->set_status($bis_re_status);

    $results_map = $logical_rules_engine_modal->update_rule($logical_rules_vo);

    $status = $results_map[BIS_STATUS];

    if ($status == BIS_SUCCESS) {
        RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);
    }

    RulesEngineUtil::generate_json_response($results_map);
}

/**
 *
 * Updates the logical rule.
 *
 */
function bis_re_update_rule_new() {

    $nonce = $_POST ['bis_nonce'];

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $rule_criteria_array = array();

    $bis_re_condition = $_POST ['bis_re_condition'];
    $bis_re_rule_value = $_POST ['bis_re_rule_value'];
    $bis_re_sub_option = $_POST ['bis_re_sub_option'];
    $bis_re_sub_opt_type_id = $_POST ['bis_re_sub_opt_type_id'];
    $bis_re_rId = $_POST ['bis_re_rId'];
    $bis_re_eval_type = $_POST ['bis_re_eval_type'];
    $bis_re_rcId = $_POST ['bis_re_rcId'];
    $bis_add_rule_type = $_POST ['bis_add_rule_type'];

    $bis_re_delete = $_POST['bis_re_delete'];
   
    $bis_re_rule_type = "option";
    
    
    // Loop over the list of rule criteria
    $rules_count = count($bis_re_sub_option);
    for ($rows = 0; $rows < $rules_count; $rows++) {

        $logical_rules_criteria_vo = new LogicalRulesCriteriaVO();
        
        $options = explode("_", $bis_re_sub_option[$rows]);
        $option_id = (int) $options[0];
        $sub_option = (int) $options[1];

        if (isset($bis_re_rcId[$rows])) {
            $logical_rules_criteria_vo->set_Id($bis_re_rcId[$rows]);
        }

        $bis_re_rule_value_str = $bis_re_rule_value[$rows];
        $condition = (int) $bis_re_condition[$rows];
        $value_type_id = (int) $bis_re_sub_opt_type_id[$rows];

        // Check for Input Token
        if ($value_type_id == 1) {

            // If condition = equal or condition = not equal
            if ($condition == 1 || $condition == 2) {
                $bis_re_rule_value_str = stripslashes($bis_re_rule_value_str);
            }
        }

        if ($option_id == 9 || $option_id == 10 || $option_id == 3 || $option_id == 12 || $option_id == 11) {
            $logical_rules_criteria_vo->set_evalType(BIS_EVAL_REQUEST_TYPE);
        }

        // Option is Request
        if ($option_id == 3 || $sub_option == 32) {

            // Filter the url
            if ($sub_option == 6 && ($condition == 1 || $condition == 2)) {
                $bis_re_rule_value_str = RulesEngineUtil::get_filter_url($bis_re_rule_value_str);
            }
            $bis_re_rule_value_str = json_encode(array(array("id" => $bis_re_rule_value_str)));
        }

        // Save Only Ids for Posts and Pages
        if ($option_id == 9 || $option_id == 10 || $option_id == 12) {
            $bis_re_rule_value_str = RulesEngineUtil::get_json_ids($bis_re_rule_value_str);
        }

        if(isset($_POST ['bis_re_right_bracket'])) {
            $bis_re_right_bracket = $_POST ['bis_re_right_bracket'];
            $logical_rules_criteria_vo->set_rightBracket($bis_re_right_bracket[$rows]);
        }
        
        if(isset($_POST ['bis_re_left_bracket'])) {
            $bis_re_left_bracket = $_POST ['bis_re_left_bracket'];
            $logical_rules_criteria_vo->set_leftBracket($bis_re_left_bracket[$rows]);
        }
        
        $logical_rules_criteria_vo->set_value($bis_re_rule_value_str);
        $logical_rules_criteria_vo->set_optionId($option_id);
        $logical_rules_criteria_vo->set_subOptionId($sub_option);
        $logical_rules_criteria_vo->set_conditionId($bis_re_condition[$rows]);
        $logical_rules_criteria_vo->set_ruleType($bis_re_rule_type);
        
        // Add operator not to the last row.
        if ($rows < ($rules_count - 1)) {
            if ($bis_add_rule_type == 1) {
                $logical_rules_criteria_vo->set_operatorId(1);
            } else {
                $bis_re_logical_op = $_POST ['bis_re_logical_op'];
                $logical_rules_criteria_vo->set_operatorId($bis_re_logical_op[$rows]);
            }
        } else {
            $logical_rules_criteria_vo->set_operatorId(0);
        }
        
        array_push($rule_criteria_array, $logical_rules_criteria_vo);
    }


    $bis_re_name = $_POST ['bis_re_name'];
    $bis_re_description = $_POST ['bis_re_description'];
    $bis_re_hook = trim($_POST ['bis_re_hook']);


    // Validation for hook
    if ($bis_re_hook != null && $bis_re_hook !== "" && !function_exists($bis_re_hook) === true) {
        $results_map = array();
        $results_map[BIS_STATUS] = BIS_ERROR;
        $results_map[BIS_MESSAGE_KEY] = BIS_NO_METHOD_FOUND;
        RulesEngineUtil::generate_json_response($results_map);
    }


    $logical_rules_vo = new LogicalRulesVO($bis_re_name, $bis_re_description, 
            $bis_re_hook, $rule_criteria_array, $bis_re_eval_type, $bis_add_rule_type);
    
    // Call to model to logical rules
    $logical_rules_engine_modal = new LogicalRulesEngineModel();

    $logical_rules_vo->set_id($bis_re_rId);
    
    if (isset($_POST ['bis_re_status'])) {
        $logical_rules_vo->set_status($_POST ['bis_re_status']);
    }

    $results_map = $logical_rules_engine_modal->update_rule($logical_rules_vo);

    $status = $results_map[BIS_STATUS];

    if ($status == BIS_SUCCESS) {
        RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);
        $rules_vo = RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_RULEVO);
        $results_map = $logical_rules_engine_modal->update_child_rule($rules_vo);

        $status = $results_map[BIS_STATUS];

        if ($status == BIS_SUCCESS) {
            RulesEngineCacheWrapper::set_reset_time($rules_vo->get_reset_rule_key());
        }
    }
    
    RulesEngineCacheWrapper::remove_session_attribute(BIS_SESSION_RULEVO);
    RulesEngineUtil::generate_json_response($results_map);
}

/**
 * This method shows the add new rule page.
 *
 */
function bis_re_new_rule() {

    $nonce = $_GET ['bis_nonce'];


    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();

    $pluginPath = RulesEngineUtil::getIncludesDirPath();
    include $pluginPath . "logical-rules-add.php";

    flush();
    exit;
}

function bis_re_edit_rule() {


    $nonce = $_GET ['bis_nonce'];

    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $pluginPath = RulesEngineUtil::getIncludesDirPath();

    include $pluginPath . "logical-rules-edit.php";

    flush();
    exit();
}

/**
 * Method used for deleting a single rule
 */
function bis_re_delete_rule() {

    $nonce = $_GET ['bis_nonce'];

    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    header("Content-Type: application/json");

    $logical_rules_engine = new LogicalRulesEngineModel ();

    $ruleId = $_GET ['ruleId'];
    $bis_logical_rules_map = $logical_rules_engine->delete_logical_rule($ruleId);

    $status = $bis_logical_rules_map["status"];

    if ($status === "success") {
        $data = array("status" => "success", "data" => $bis_logical_rules_map["data"]);
    } else if ($status === "childs_rules_exists") {
        $data = array("status" => "error", "data" => BIS_MESSAGE_LOGICAL_RULE_DELETE_FAILED);
    } else {
        $data = array("status" => "error", "data" => BIS_MESSAGE_NO_RECORD_FOUND);
    }

    echo json_encode($data);

    flush();
    exit();
}

function bis_re_get_value() {

    $nonce = $_POST ['bis_nonce'];

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    header("Content-Type: application/json");

    // Get the logical rule here
    $autoValue = $_POST ['q'];
    $subCriteria = (int) $_POST ['subcriteria'];

    $valArr = array();
    $count = 0;
    $rules_engine = new LogicalRulesEngineModel();

    switch ($subCriteria) {
        case 1: // User Role
            $bis_re_editable_roles = get_editable_roles();
            foreach ($bis_re_editable_roles as $role) {
                if (stripos($role['name'], $autoValue) !== false) {
                    $valArr[$count++] = array('id' => $role['name'], 'name' => $role['name']);
                }
            }
            break;

        case 2: // Email
            $rows = $rules_engine->get_user_emails();

            foreach ($rows as $email) {
                // Check if email exists in the list
                if (stripos($email->user_email, $autoValue) !== false) {
                    $valArr[$count++] = array('id' => $email->user_email, 'name' => $email->user_email);
                }
            }
            break;

        case 4: // Country Code
            $valArr = RulesEngineLocalization::get_countries($autoValue);
            break;

        case 20: // Continent
            $valArr = RulesEngineLocalization::get_continents($autoValue);

            break;

        case 5: // Currency
        case 23: // Week days
        case 24: // Months
            $valArr = $rules_engine->get_rule_values_by_display_name($subCriteria, $autoValue);
            $valArr = RulesEngineLocalization::get_localized_values($valArr);

            break;

        case 21: // Page Rule
            $args = array(
                'offset' => 0,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'post_type' => 'page',
                'post_status' => 'publish',
                'suppress_filters' => true
            );

            $rows = get_pages($args);

            $count = 0;

            if (($rows != null) && (count($rows) > 0)) {
                foreach ($rows as $page) {

                    if (stripos($page->post_title, $autoValue) !== false) {
                        $valArr[$count++] = array('id' => $page->ID, 'name' => $page->post_title);
                    }
                }
            }

            break;

        case 22: // Post Rule
            $args = array(
                'offset' => 0,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'post_type' => 'post',
                'post_status' => 'publish',
                'suppress_filters' => true,
                'posts_per_page' => -1
            );

            $rows = get_posts($args);
            $count = 0;

            if (($rows != null) && (count($rows) > 0)) {
                foreach ($rows as $post) {
                    // Check if email exists in the list
                    if (stripos($post->post_title, $autoValue) !== false) {
                        $valArr[$count++] = array('id' => $post->ID, 'name' => $post->post_title);
                    }
                }
            }

            break;

        case 17: // wordpress category
            $args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => FALSE,
                'name__like' => $autoValue
            );

            $taxonomy = 'category';
            $categories = get_terms($taxonomy, $args);

            if (!empty($categories)) {
                foreach ($categories as $category) {
                    $valArr[$count++] = array('id' => $category->term_id, 'name' => $category->name);
                }
            }

            break;
        case 18: // userId
            $rows = $rules_engine->get_userIds();

            foreach ($rows as $user) {
                // Check if email exists in the list
                if (stripos($user->user_login, $autoValue) !== false) {
                    $valArr[$count++] = array('id' => $user->user_login, 'name' => $user->user_login);
                }
            }

            break;

        case 25:  // WooCommerce category

            $args = array(
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => FALSE,
                'name__like' => $autoValue
            );


            $taxonomy = 'product_cat';
            $categories = get_terms($taxonomy, $args);

            if (!empty($categories)) {
                foreach ($categories as $category) {
                    $valArr[$count++] = array('id' => $category->term_id, 'name' => $category->name);
                }
            }

            break;

        case 29:  // City
            $bis_geoname_user = RulesEngineUtil::get_option(BIS_GEO_NAME_USER);
            $url = "http://api.geonames.org/searchJSON?userName=" . $bis_geoname_user . "&lang=en&featureClass=P&maxRows=12&name_startsWith=" . $autoValue;
            $response = file_get_contents($url);

            if ($response != null) {
                $j_response = json_decode($response);
                $cities = $j_response->geonames;

                if (!empty($cities)) {
                    foreach ($cities as $citi) {
                        $display_name = $citi->name . ", " . $citi->adminName1 . ", " . $citi->countryName;
                        $valArr[$count++] = array('id' => $citi->toponymName, 'name' => $display_name);
                    }
                }
            }

            break;

        case 30:  // Region or state
            //userName=tompi
            // State or Region
            $bis_geoname_user = RulesEngineUtil::get_option(BIS_GEO_NAME_USER);
            $url = "http://api.geonames.org/searchJSON?userName=" . $bis_geoname_user . "&lang=en&featureClass=A&fcode=ADM1&maxRows=12&name_startsWith=" . $autoValue;

            $response = file_get_contents($url);

            if ($response != null) {
                $j_response = json_decode($response);
                $cities = $j_response->geonames;

                if (!empty($cities)) {
                    foreach ($cities as $citi) {
                        $display_name = $citi->name . ", " . $citi->countryName;
                        $valArr[$count++] = array('id' => $citi->toponymName, 'name' => $display_name);
                    }
                }
            }

            break;
        
        case 34: 
            $terms = get_terms('product_tag');
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $term) {
                    if(RulesEngineUtil::isContains($term->name, $autoValue)) {
                        $valArr[$count++] = array('id' => $term->term_id, 'name' => $term->name);
                    }
                }
            }
            break;

        case 35: 
            $results_map = $rules_engine->get_woo_attribute_taxonomies($autoValue);
            if($results_map[BIS_STATUS] === BIS_SUCCESS) {
                $valArr = $results_map[BIS_DATA];
            }
            break;
            
    } // End of switch

    $jsonRoles = json_encode($valArr);

    echo $jsonRoles;
    flush();
    exit();
}

function bis_get_logical_rules() {

    // Get the logical rule here
    $logical_rules_engine = new LogicalRulesEngineModel ();
    $results_map = $logical_rules_engine->get_logical_rules();

    RulesEngineUtil::generate_json_response($results_map);
}

function bis_get_sub_options() {

    // Get the logical rule here
    $optionId = $_POST ['optionId'];
    header("Content-Type: application/json");

    $logical_rules_engine = new LogicalRulesEngineModel ();
    $bis_sub_options = $logical_rules_engine->get_rules_sub_options($optionId);

    if ($bis_sub_options != null) {
        $bis_sub_options = RulesEngineLocalization::get_localized_values($bis_sub_options);
        $bis_sub_options = json_encode($bis_sub_options);
    } else {
        // NO data found
    }

    echo $bis_sub_options;
    flush();
    exit();
}

function bis_get_conditions() {
    $optionId = $_POST ['optionId'];

    header("Content-Type: application/json");

    $logical_rules_engine = new LogicalRulesEngineModel ();
    $rules_conditions = $logical_rules_engine->get_rules_conditions($optionId);

    if ($rules_conditions != null) {
        $rules_conditions["RuleConditions"] = RulesEngineLocalization::get_localized_values($rules_conditions["RuleConditions"]);
        $rules_conditions = json_encode($rules_conditions);
    } else {
        // NO data found
    }

    echo $rules_conditions;
    flush();
    exit();
}

/**
 * This method is used to save the logical Rule
 *
 */
function bis_create_logical_rule() {
    $nonce = $_POST ['bis_rules_engine_nonce'];

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $rule_criteria_array = array();

    $bis_re_sub_option = $_POST ['bis_re_sub_option'];
    
    if(RulesEngineUtil::isContains($bis_re_sub_option[0], "_")) {
        $bis_re_options = explode("_", $bis_re_sub_option);
        $bis_re_sub_option = $bis_re_options[0];
        $bis_re_rule_option = $bis_re_options[1];
    } else {
        $bis_re_rule_option = $_POST ['bis_re_rule_option'];
    }
    
    $bis_re_condition = $_POST ['bis_re_condition']; 
    $bis_re_rule_value = $_POST ['bis_re_rule_value'];
    $bis_re_logical_op = $_POST ['bis_re_logical_op'];
    $bis_re_left_bracket = $_POST ['bis_re_left_bracket'];
    $bis_re_right_bracket = $_POST ['bis_re_right_bracket'];
    $bis_re_sub_opt_type_id = $_POST ['bis_re_sub_opt_type_id'];
    $bis_re_eval_type = $_POST ['bis_re_eval_type'];

    $bis_re_rule_type = "option";
    // Loop over the list of rule criteria
    for ($rows = 0; $rows < count($bis_re_rule_option); $rows++) {

        $logical_rules_criteria_vo = new LogicalRulesCriteriaVO();
        $sub_option = (int) $bis_re_sub_option[$rows];
        $bis_re_rule_value_str = $bis_re_rule_value[$rows];
        $condition = (int) $bis_re_condition[$rows];
        $value_type_id = (int) $bis_re_sub_opt_type_id[$rows];

        // Check for Input Token
        if ($value_type_id == 1) {

            // If condition = equal or condition = not equal
            if ($condition == 1 || $condition == 2) {
                $bis_re_rule_value_str = stripslashes($bis_re_rule_value_str);
            }
        }

        $option_id = (int) $bis_re_rule_option[$rows];

        if ($option_id == 9 || $option_id == 10 || $option_id == 3 || $option_id == 12
                || $option_id == 11) {
            $logical_rules_criteria_vo->set_evalType(BIS_EVAL_REQUEST_TYPE);
        }

        // Option is Request
        if ($option_id == 3 || $sub_option == 32) {

            // Filter the url
            if ($sub_option == 6 && ($condition == 1 || $condition == 2)) {
                $bis_re_rule_value_str = RulesEngineUtil::get_filter_url($bis_re_rule_value_str);
            }
            $bis_re_rule_value_str = json_encode(array(array("id" => $bis_re_rule_value_str)));
        }

        // Save Only Ids for Posts and Pages
        if ($option_id == 9 || $option_id == 10 || $option_id == 12) {
            $bis_re_rule_value_str = RulesEngineUtil::get_json_ids($bis_re_rule_value_str);
        }

        if(isset($bis_re_right_bracket[$rows])) {
            $logical_rules_criteria_vo->set_rightBracket($bis_re_right_bracket[$rows]);
        }
        
        if(isset($bis_re_left_bracket[$rows])) {
            $logical_rules_criteria_vo->set_leftBracket($bis_re_left_bracket[$rows]);
        }
       
        $logical_rules_criteria_vo->set_value($bis_re_rule_value_str);
        $logical_rules_criteria_vo->set_optionId($option_id);
        $logical_rules_criteria_vo->set_subOptionId($bis_re_sub_option[$rows]);
        $logical_rules_criteria_vo->set_conditionId($bis_re_condition[$rows]);
        $logical_rules_criteria_vo->set_ruleType($bis_re_rule_type);
        $logical_rules_criteria_vo->set_operatorId($bis_re_logical_op[$rows]);

        array_push($rule_criteria_array, $logical_rules_criteria_vo);
    }


    $bis_re_name = $_POST ['bis_re_name'];
    $bis_re_description = $_POST ['bis_re_description'];
    $bis_re_hook = trim($_POST ['bis_re_hook']);


    // Validation for hook
    if ($bis_re_hook != null && $bis_re_hook !== "" && !function_exists($bis_re_hook) === true) {
        $results_map = array();
        $results_map[BIS_STATUS] = BIS_ERROR;
        $results_map[BIS_MESSAGE_KEY] = BIS_NO_METHOD_FOUND;
        RulesEngineUtil::generate_json_response($results_map);
    }

    $logical_rules_vo = new LogicalRulesVO($bis_re_name, $bis_re_description, 
            $bis_re_hook, $rule_criteria_array, $bis_re_eval_type);
    
    // Call to model to logical rules
    $logical_rules_engine_modal = new LogicalRulesEngineModel();
    $results_map = $logical_rules_engine_modal->save_rule($logical_rules_vo);

    $status = $results_map[BIS_STATUS];
    
    $rules_vo = RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_RULEVO);

    if ($rules_vo != null) {
        $rules_vo->set_logical_rule_id($logical_rules_vo->get_id());
    }    

    if ($status === BIS_SUCCESS) {
        RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);
    }
    
    RulesEngineUtil::generate_json_response($results_map);
}

function bis_create_logical_rule_new() {
    $nonce = $_POST ['bis_rules_engine_nonce'];

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $rule_criteria_array = array();

    $bis_re_sub_option = $_POST ['bis_re_sub_option'];
    $bis_re_condition = $_POST ['bis_re_condition'];
    $bis_re_rule_value = $_POST ['bis_re_rule_value'];
    $bis_re_left_bracket = $_POST ['bis_re_left_bracket'];
    $bis_re_right_bracket = $_POST ['bis_re_right_bracket'];
    $bis_re_sub_opt_type_id = $_POST ['bis_re_sub_opt_type_id'];
    $bis_re_eval_type = $_POST ['bis_re_eval_type'];
    $bis_add_rule_type = $_POST ['bis_add_rule_type'];

    $bis_re_rule_type = "option";
    
    $rules_count = count($bis_re_sub_option);
    // Loop over the list of rule criteria
    for ($rows = 0; $rows < count($bis_re_sub_option); $rows++) {

        $logical_rules_criteria_vo = new LogicalRulesCriteriaVO();
        $options = explode("_", $bis_re_sub_option[$rows]);
        $option_id = (int) $options[0];
        $sub_option = (int) $options[1];
        
        $bis_re_rule_value_str = $bis_re_rule_value[$rows];
        $condition = (int) $bis_re_condition[$rows];
        $value_type_id = (int) $bis_re_sub_opt_type_id[$rows];

        // Check for Input Token
        if ($value_type_id == 1) {
            // If condition = equal or condition = not equal
            if ($condition == 1 || $condition == 2) {
                $bis_re_rule_value_str = stripslashes($bis_re_rule_value_str);
            }
        }

        
        if ($option_id == 9 || $option_id == 10 || $option_id == 3 || $option_id == 12 || $option_id == 11) {
            $logical_rules_criteria_vo->set_evalType(BIS_EVAL_REQUEST_TYPE);
        }

        // Option is Request
        if ($option_id == 3 || $sub_option == 32) {

            // Filter the url
            if ($sub_option == 6 && ($condition == 1 || $condition == 2)) {
                $bis_re_rule_value_str = RulesEngineUtil::get_filter_url($bis_re_rule_value_str);
            }
            $bis_re_rule_value_str = json_encode(array(array("id" => $bis_re_rule_value_str)));
        }

        // Save Only Ids for Posts and Pages
        if ($option_id == 9 || $option_id == 10 || $option_id == 12) {
            $bis_re_rule_value_str = RulesEngineUtil::get_json_ids($bis_re_rule_value_str);
        }

        if (isset($bis_re_right_bracket[$rows])) {
            $logical_rules_criteria_vo->set_rightBracket($bis_re_right_bracket[$rows]);
        }

        if (isset($bis_re_left_bracket[$rows])) {
            $logical_rules_criteria_vo->set_leftBracket($bis_re_left_bracket[$rows]);
        }

        $logical_rules_criteria_vo->set_value($bis_re_rule_value_str);
        $logical_rules_criteria_vo->set_optionId($option_id);
        $logical_rules_criteria_vo->set_subOptionId($sub_option);
        $logical_rules_criteria_vo->set_conditionId($bis_re_condition[$rows]);
        $logical_rules_criteria_vo->set_ruleType($bis_re_rule_type);
        
        // Add operator not to the last row.
        if($rows < ($rules_count - 1)) {
            if ($bis_add_rule_type == 1) {
                $logical_rules_criteria_vo->set_operatorId(1);
            } else {
                $bis_re_logical_op = $_POST ['bis_re_logical_op'];
                $logical_rules_criteria_vo->set_operatorId($bis_re_logical_op[$rows]);
            }    
        } else {
          $logical_rules_criteria_vo->set_operatorId(0);
        }
        array_push($rule_criteria_array, $logical_rules_criteria_vo);
    }


    $bis_re_name = $_POST ['bis_re_name'];
    $bis_re_description = $_POST ['bis_re_description'];
    $bis_re_hook = trim($_POST ['bis_re_hook']);


    // Validation for hook
    if ($bis_re_hook != null && $bis_re_hook !== "" && !function_exists($bis_re_hook) === true) {
        $results_map = array();
        $results_map[BIS_STATUS] = BIS_ERROR;
        $results_map[BIS_MESSAGE_KEY] = BIS_NO_METHOD_FOUND;
        RulesEngineUtil::generate_json_response($results_map);
    }

    $logical_rules_vo = new LogicalRulesVO($bis_re_name, $bis_re_description, $bis_re_hook, 
            $rule_criteria_array, $bis_re_eval_type, $bis_add_rule_type);

    // Call to model to logical rules
    $logical_rules_engine_modal = new LogicalRulesEngineModel();
    
    $results_map = $logical_rules_engine_modal->save_rule($logical_rules_vo);

    $status = $results_map[BIS_STATUS];

    $rules_vo = RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_RULEVO);

    if ($rules_vo != null) {
        $rules_vo->set_logical_rule_id($logical_rules_vo->get_id());
    }

    if ($status === BIS_SUCCESS) {
        RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);
        
        $rules_vo = RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_RULEVO);
        $results_map = $logical_rules_engine_modal->save_child_rule($rules_vo);
        $status = $results_map[BIS_STATUS];

        if ($status == BIS_SUCCESS) {
            RulesEngineCacheWrapper::set_reset_time($rules_vo->get_reset_rule_key());
        }
    }
    
    RulesEngineCacheWrapper::remove_session_attribute(BIS_SESSION_RULEVO);
    RulesEngineUtil::generate_json_response($results_map);
}

function bis_use_existing_rule() {
    
    $rule_id = $_POST ['bis_re_rule_id'];
    $rules_vo = RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_RULEVO);

    if ($rules_vo != null) {
        $rules_vo->set_logical_rule_id($rule_id);
    }

    if ($status === BIS_SUCCESS) {
        RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);
    }

    RulesEngineUtil::generate_json_response($results_map);
}
