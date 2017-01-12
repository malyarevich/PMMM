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
use bis\repf\util\BrowserDetection;

class RulesEngineUtil {

    private function __construct() {
        
    }

    public static function getIncludesDirPath() {

        $pluginPath = RulesEngineUtil::getPluginAbsPath() . "/includes/";

        return $pluginPath;
    }

    public static function getPluginAbsPath() {

        $dirName = plugin_dir_path(__FILE__);
        $pluginPath = realpath($dirName);
        $path_array = explode("util", $pluginPath);

        return $path_array[0];
    }

    /**
     * This method is used to parse the json value and evaluat the values.
     *
     * @param unknown $tokenInput jsonToken value
     * @param unknown $arg2
     * @param unknown $condId
     * @return boolean
     */
    public static function evaluateTokenInputRule($tokenInput, $arg2, $condId) {

        $json_tokens = json_decode($tokenInput);

        if (($json_tokens != null) && (count($json_tokens) > 0)) {
            foreach ($json_tokens as $token) {
                if (RulesEngineUtil::evaluateStringTypeRule($token->id, $arg2, $condId)) {
                    return true;
                }
            }
        }

        return false;
    }
    
    /**
     * 
     * @param type $arg1 is an container
     * @param type $arg2 value to be checked
     * @param type $condId
     * @return type
     */
    public static function evaluateStringTypeRule($arg1, $arg2, $condId) {

        $eval_value = false;
        
        switch ($condId) {
            case 1 : // Equal
                $eval_value = RulesEngineUtil::isEqual($arg1, $arg2);
                break;

            case 2 : // Not Equal
                $eval_value = !RulesEngineUtil::isEqual($arg1, $arg2);
                break;

            case 3 : // Starts With
                $eval_value = RulesEngineUtil::startsWith($arg1, $arg2);
                break;

            case 4 : // Contains
                $eval_value = RulesEngineUtil::isContains($arg1, $arg2);
                break;

            case 5 : // Does not contain
                $eval_value = !RulesEngineUtil::isContains($arg1, $arg2);
                break;

            case 8 : // Domain is
                $eval_value = RulesEngineUtil::isDomain($arg1, $arg2);
                break;

            case 9 : // Ends With
                $eval_value = RulesEngineUtil::endsWith($arg1, $arg2);
                break;
            
            case 10 : // is Set
                $eval_value = !RulesEngineUtil::isNullOrEmptyString($arg1, $arg2);
                break;
            
            case 11 : // is Not Set
                $eval_value = RulesEngineUtil::isNullOrEmptyString($arg1, $arg2);
                break;
            
            case 12 : // Contains any of
                $eval_value = RulesEngineUtil::isContainsAnyOf($arg1, $arg2);
                break;
        }
        return $eval_value;
    }
    
    /**
     * This method checks if a value is contained in the comma seperated list.
     * 
     * @param type $needle is the value to be find
     * @param type $haystack comma seperated value
     * @return boolean
     */
    public static function isContainsAnyOf($needle, $haystack) {

        $heyStackList = explode(",", $haystack);
        foreach ($heyStackList as $value) {
            if (trim($value) === trim($needle)) {
                return true;
            }
        }
        return false;
    }

    public static function isDomain($arg1, $domain) {
        $email_domain = explode("@", $arg1);
        return RulesEngineUtil::isEqual($email_domain[1], $domain);
    }

    public static function isEqual($arg1, $arg2) {
        $isEqual = false;

        if (strcasecmp($arg1, $arg2) == 0) {
            $isEqual = true;
        }

        return $isEqual;
    }

    /**
     * This method is used to 
     * 
     * @param type $haystack is the container.
     * @param type $needle is the value to be checked
     * @return type
     */
    public static function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        $isStartsWith = ($needle === "" || strrpos($haystack, $needle, - strlen($haystack)) !== FALSE);
        return $isStartsWith;
    }

    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        $isEndsWith = ($needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE));
        return $isEndsWith;
    }

    /**
     * Check whether $find in contained in $container.
     *
     * @param unknown $container value container
     * @param unknown $find value to find.
     * @return boolean
     */
    public static function isContains($container, $find) {
        $isContains = false;

        if ($find != null && (strpos($container, $find) !== false)) {
            $isContains = true;
        }

        return $isContains;
    }

    public static function evaluateURLTypeRule($cpath_array, $rule_path, $condId,
                                                $applied_rule=NULL) {

        $eval = false;
        if(($condId == 1 || $condId == 2)
                && !RulesEngineUtil::endsWith($rule_path, "/")) {
            $rule_path = $rule_path ."/";
        }
        
        switch ($condId) {
            case 1 : 
                foreach ($cpath_array as $current_path) {
                    $eval = RulesEngineUtil::isEqual($current_path, $rule_path);
                    if($eval) {
                        break;
                    }    
                }
                break;

            case 2 :
                
                foreach ($cpath_array as $current_path) {                
                    $eval = RulesEngineUtil::isEqual($current_path, $rule_path);
                    if ($eval) {
                        break;
                    }
                }
               
                $eval = !$eval;
                break;

            case 4 :
                 foreach ($cpath_array as $current_path) {
                    $eval = RulesEngineUtil::isContains($current_path, $rule_path);
                    if ($eval) {
                        break;
                    }
                }    
                break;

            case 5 :
                foreach ($cpath_array as $current_path) {
                   
                    $eval = RulesEngineUtil::isContains($current_path, $rule_path);
                    
                    if ($eval) {
                        break;
                    }
                }
                $eval = !$eval;
                break;
                
            case 13 : // pattern match
               
                    $rule_values = explode("/**", $rule_path);
                    $rule_value = $rule_values[0];

                    foreach ($cpath_array as $current_path) {
                        if (!RulesEngineUtil::isContains($current_path, "bis_prd=1")) {
                            $eval = RulesEngineUtil::startsWith($current_path, $rule_value);

                            if ($eval) {
                                $applied_rule->condId = $condId;
                                $redirectPaths = explode($rule_value, $current_path);
                                $applied_rule->patternRedirect = $redirectPaths[1];
                                break;
                            }
                        }    
                    }
                break;
        }

        return $eval;
    }

      
    /**
     * Get the filter url
     *
     * @param $rule_path
     * @return string
     */
    public static function get_filter_url($rule_path) {

        /*if (!(substr($rule_path, 0, 7) === "http://" || substr($rule_path, 0, 8) === "https://")) {
            $rule_path = "http://" . $rule_path;
        }

        if (substr($rule_path, 0, 11) === "http://www.") {
            $rule_path_ar = explode("http://www.", $rule_path);
            $rule_path = "http://" . $rule_path_ar[1];
        } elseif (substr($rule_path, 0, 12) === "https://www.") {
            $rule_path_ar = explode("https://www.", $rule_path);
            $rule_path = "https://" . $rule_path_ar[1];
        }*/

        return $rule_path;
    }

    public static function evaluateParameterRules($value_type, $condId) {
        
        $query_string = $_SERVER['QUERY_STRING'];
        $eval = RulesEngineUtil::evaluateStringTypeRule($query_string, $value_type, $condId);
        
        return $eval;
    }
    
    public static function convertToSQLString($value) {
        $value = str_replace(",", "','", $value);
        $value = "'" . $value . "'";
        return $value;
    }
    
    public static function evaluateFormDataRules($value_type, $condId) {
        
        $eval = false;
        $form_param_value = '';
        $req_form_value = '';

        if(RulesEngineUtil::isContains($value_type, "=")) {
            $form_data = explode("=", $value_type); 
            $form_param =  $form_data[0];
            $form_param_value = $form_data[1];
        } else {
            $form_param = $value_type;
        }
        
        if(isset($_POST[$form_param])) {
            $req_form_value = $_POST[$form_param];
        }
              
        $eval = RulesEngineUtil::evaluateStringTypeRule($req_form_value, $form_param_value, $condId);
        
        return $eval;
    }
    
    public static function get_dynamic_rule_expression($value_type, $tokenInput, $condId) {

        $json_tokens = json_decode($tokenInput);
        $dynamic_value = "";

        $token_count = count($json_tokens);

        if ((($token_count - 1) > 0) && ($json_tokens != null)) {
            foreach ($json_tokens as $key => $token) {

                if ($key == 0) {
                    $dynamic_value = "( ";
                }

                if ($key < $token_count - 1) {
                    $dynamic_value = $dynamic_value . $value_type . $token->id . "$" . $condId . " - ";
                } else {
                    $dynamic_value = $dynamic_value . $value_type . $token->id . "$" . $condId . " )";
                }
            }
        } else if ($token_count == 1) {
            $dynamic_value = $value_type . $json_tokens[0]->id . "$" . $condId;
        }

        return $dynamic_value;
    }

    public static function get_dynamic_rule_expression_val_type($value_type, $tokenInput, $condId) {
        return $value_type . $tokenInput . "$" . $condId;
    }

    public static function evaluateIntTypeRule($arg1, $arg2, $condId) {
        $condId = (int) $condId;

        switch ($condId) {

            case 1:
                if ($arg1 == $arg2) {
                    return true;
                }
                break;

            case 2:
                if ($arg1 != $arg2) {
                    return true;
                }
                break;

            case 6: // Greaterthan
                if ($arg1 > $arg2) {
                    return true;
                }
                break;

            case 7: //Lessthan
                if ($arg1 < $arg2) {
                    return true;
                }
                break;
        }

        return false;
    }

    public static function evaluateDateTypeRule($arg1, $arg2, $condId) {

        $condId = (int) $condId;

        switch ($condId) {
            case 1 :
                return RulesEngineUtil::isDatesEqual($arg1, $arg2);
                break;
            case 2 :
                return !RulesEngineUtil::isDatesEqual($arg1, $arg2);
                break;
            case 6 :
                return RulesEngineUtil::isDateGreater($arg1, $arg2);
                break;
            case 7 :
                return !RulesEngineUtil::isDateGreater($arg1, $arg2);
                break;
        }
    }

    public static function isDatesEqual($arg1, $arg2) {

        $date1 = date_create($arg1);
        $date2 = date_create($arg2);

        $isEqual = false;

        if ($date1 == $date2) {
            $isEqual = true;
        }

        return $isEqual;
    }

    public static function isDateGreater($arg1, $arg2) {

        $date1 = date_create($arg1);
        $date2 = date_create($arg2);
        $isGreater = false;

        if ($date1 > $date2) {
            $isGreater = true;
        }

        return $isGreater;
    }

    public static function get_applied_session_rules() {
        $applied_rules = BaseRulesEngine::get_applied_logical_rules();

        if ($applied_rules == null) {
            RulesEngine::startSession();
            $applied_rules = BaseRulesEngine::get_applied_logical_rules();
        }

        return $applied_rules;
    }

    public static function getSessionValue($key) {
        $value = null;

        if (RulesEngineCacheWrapper::is_session_attribute_set($key)) {
            $value = RulesEngineCacheWrapper::get_session_attribute($key);
        }

        return $value;
    }

    public static function evaluate_browser_rule($rule_value, $condId) {

        $client_browser = RulesEngineUtil::get_client_browser();

        return RulesEngineUtil::evaluateStringTypeRule($rule_value, $client_browser, $condId);
    }

    public static function get_client_browser() {
        $brdetec = new BrowserDetection();
        $browser = $brdetec->browser_detection("full_assoc");
        $browser_name = "none";

        if ($browser["browser_working"] == "ie") {
            $browser_name = "ie";
        } else if ($browser["browser_working"] == "moz" && $browser["moz_data"][0] == "firefox") {
            $browser_name = "firefox";
        } else if ($browser["browser_working"] == "op") {
            $browser_name = "opera";
        } else if ($browser["browser_working"] == "webkit") {
            if ($browser["webkit_data"][0] == "safari") {
                $browser_name = "safari";
            } else if ($browser["webkit_data"][0] == "chrome") {
                $browser_name = "chrome";
            }
        }

        return $browser_name;
    }

    public static function get_applied_rule_ids($logical_rules) {

        $logical_rule_id_array = array();
        if ($logical_rules != null && count($logical_rules) > 0) {
            foreach ($logical_rules as $logical_rule) {

                // For request rules
                //If the rules contains expression then expression must be true
                if ($logical_rule->expression != null) {
                    if (isset($logical_rule->eval) && ($logical_rule->eval)) {
                        array_push($logical_rule_id_array, $logical_rule->ruleId);
                    }
                } else {
                    // For session rules
                    array_push($logical_rule_id_array, $logical_rule->ruleId);
                }
            }
        }

        return implode(",", $logical_rule_id_array);
    }

    public static function get_applied_rules($logical_rules) {

        $logical_rule_id_array = array();
        if ($logical_rules != null && count($logical_rules) > 0) {
            foreach ($logical_rules as $logical_rule) {
                // For request rules
                //If the rules contains expression then expression must be true
                if ($logical_rule->expression != null) {
                    if (isset($logical_rule->eval) && ($logical_rule->eval === true) &&
                            (!in_array($logical_rule->ruleId, $logical_rule_id_array))) {
                        array_push($logical_rule_id_array, $logical_rule->ruleId);
                    }
                } else {
                    // For session rules
                    if(!in_array($logical_rule->ruleId, $logical_rule_id_array)) {
                        array_push($logical_rule_id_array, $logical_rule->ruleId);
                    }
                }
            }
        }
        
        return $logical_rule_id_array;
    }

    public static function get_applied_page_rule_ids($page_rules) {

        $page_rule_id_array = array();

        if ($page_rules != null) {
            foreach ($page_rules as $page_rule) {
                array_push($page_rule_id_array, $page_rule->parent_id);
            }
        }

        return implode(",", $page_rule_id_array);
    }

    public static function get_applied_post_rule_ids($post_rules) {

        $post_rule_id_array = array();

        if ($post_rules != null && count($post_rules) > 0) {
            foreach ($post_rules as $post_rule) {
                if ($post_rule->action == "hide_post") {
                    array_push($post_rule_id_array, $post_rule->parent_id);
                }
            }
        }

        return $post_rule_id_array;
    }
    
    public static function cache_child_rule(RulesVO $rules_vo, $child_rule_path, $plugin_id) {
        RulesEngineCacheWrapper::set_session_attribute(BIS_SESSION_RULEVO, $rules_vo);
        RulesEngineCacheWrapper::set_session_attribute(BIS_CHILD_FILE_PATH, $child_rule_path);
        RulesEngineCacheWrapper::set_session_attribute(BIS_CHILD_RULE_ID, $plugin_id);
    }

    public static function clear_cached_child_rule() {
        RulesEngineCacheWrapper::remove_session_attribute(BIS_CHILD_FILE_PATH);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_SESSION_RULEVO);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_CHILD_RULE_ID);
    }

    public static function generate_json_response($results_map) {

        $status = $results_map[BIS_STATUS];

        if ($status == BIS_SUCCESS) {

            if (isset($results_map[BIS_DATA]) && count($results_map[BIS_DATA]) > 0) {
                $data = array("status" => BIS_SUCCESS, "data" => $results_map[BIS_DATA]);
            } else {
                $data = array("status" => BIS_SUCCESS);
            }
        } else if ($status == BIS_SUCCESS_WITH_NO_DATA) {

            $data = array("status" => BIS_SUCCESS_WITH_NO_DATA);
        } else {
            if (isset($results_map[BIS_DATA]) && count($results_map[BIS_DATA]) > 0) {
                $data = array("status" => BIS_ERROR, "data" => $results_map[BIS_DATA]);
            } else {
                $data = array("status" => BIS_ERROR);
            }
        }

        if (isset($results_map[BIS_MESSAGE_KEY])) {
            $data[BIS_MESSAGE_KEY] = $results_map[BIS_MESSAGE_KEY];
        }

        wp_send_json($data);
    }

    /**
     * Convert the json string value to array of values.
     *
     * @param unknown $json_value
     * @return multitype:
     */
    public static function get_values_array($json_value) {

        $rule_values = json_decode(stripslashes($json_value));

        $values = array();

        if (($rule_values != null) && (count($rule_values) > 0)) {
            foreach ($rule_values as $rule_value) {
                array_push($values, $rule_value->id);
            }
        }

        return $values;
    }

    public static function get_exclude_page_id($exclude_pages, $applied_rule_id) {
        $exclude_page_id = "";

        if (($exclude_pages != null) && count(($exclude_pages) > 0)) {
            foreach ($exclude_pages as $exclude_page) {
                if ($exclude_page->lrId == $applied_rule_id && $exclude_page->action == "hide_page") {
                    $exclude_page_id = $exclude_page->parent_id . "," . $exclude_page_id;
                }
            }
        }

        return $exclude_page_id;
    }

    public static function set_request_rules($eval_request_rules) {
        RulesEngineCacheWrapper::set_value(BIS_REQUEST_RULES . session_id(), $eval_request_rules);
    }

    /**
     * This method return the style name for the theme.
     * @param $template_name
     * @return string
     */
    public static function get_theme_style($template) {
        $stylesheet = "";
        $themes = wp_get_themes();

        foreach ($themes as $theme) {
            if ($theme->template == $template) {
                $stylesheet = $theme->stylesheet;
            }
        }

        return $stylesheet;
    }

    /**
     * This method is used to build current request url with protocol.
     *
     * @return string
     */
    public static function get_current_url() {

        $url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $url_array = array();
        
        if (is_ssl()) {
            array_push($url_array, "https://" . $url);
            array_push($url_array, "https://www." . $url);           
        } else {
            array_push($url_array, "http://" . $url);
            array_push($url_array, "http://www." . $url);
        }

        return $url_array;
    }

    /**
     * This method will display the request forgery error message.
     */
    public static function handle_request_forgery_error() {
        die("<h1><span style='color: red;'>Oops! Request forgery error, please reload page.</span></h1>");
    }

    public static function isNullOrEmptyString($value) {
        return (!isset($value) || trim($value) === '');
    }

    public function convert_to_text($text) {
        if ($text == 1)
            return 'true';
        else
            return 'false';
    }

    public static function get_json_ids($json_values) {
        $values = json_decode($json_values);
        $value_ids = array();

        if ($values != null) {
            foreach ($values as $value) {
                array_push($value_ids, array("id" => intval($value->id)));
            }
        }

        return json_encode($value_ids);
    }

    public static function is_valid_shortcode($shortcode) {
        $content = trim($shortcode);
        $first_char = substr($content, 0, 1);
        $last_char = substr($content, strlen($content) - 1, 1);

        if (!($first_char === "[" && $last_char === "]")) {
            $results_map = array();
            $results_map[BIS_MESSAGE_KEY] = BIS_INVALID_SHORTCODE;
            RulesEngineUtil::generate_json_response($results_map);
        }
        return true;
    }

    public static function is_woocommerce_installed() {

        if (class_exists('WooCommerce')) {
            return true;
        }

        return false;
    }
    
    public static function is_redirect_plugin_installed() {

        if (class_exists('RedirectRulesEngine')) {
            return true;
        }

        return false;
    }

    /**
     * Get the mobile device manufacture details, if not mobile return false.
     * If not in list returns other.
     * 
     * @return string|boolean
     */
    public static function get_mobile($uagent_info) {


        if ($uagent_info->DetectMobileQuick() ||
                $uagent_info->DetectSmartphone()) {

            //"iPhone"
            $eVal = $uagent_info->DetectIphone();
            if ($eVal) {
                return "iPhone";
            }

            // windows Phone
            $eVal = $uagent_info->DetectWindowsPhone();
            if ($eVal) {
                return "winPhone";
            }

            //"Andriod_Phone"
            $eVal = $uagent_info->DetectAndroidPhone();
            if ($eVal) {
                return "andriodPhone";
            }

            //"BlackBerry_Phone"
            $eVal = $uagent_info->DetectBlackBerry10Phone();
            if ($eVal) {
                return "blackBerry";
            }

            return "other";
        } // End of if

        return null;
    }

    /**
     * This method is used to get the table details from the list.
     * If not found in the returns other.
     * If not table return false.
     * 
     * @param type $uagent_info
     * @return string|boolean
     */
    public static function get_tablet($uagent_info) {

        // iPad
        $eVal = $uagent_info->DetectIpad();
        if ($eVal) {
            return "iPad";
        }

        //"Andriod_Tablet"
        $eVal = $uagent_info->DetectAndroidTablet();
        if ($eVal) {
            return "andriodTablet";
        }

        $eVal = $uagent_info->DetectBlackBerryTablet();
        if ($eVal) {
            return "blackBerryTablet";
        }

        //"HP_TouchPad":
        $eVal = $uagent_info->DetectWebOSTablet();
        if ($eVal) {
            return "hpTouchPad";
        }

        //"Tablet":
        $eVal = $uagent_info->DetectTierTablet();
        if ($eVal) {
            return "other";
        }

        return null;
    }
    
    /**
     * This method is used to if no redirect param is available or not.
     * 
     * @return boolean
     */
    public static function is_redirect() {
        $is_no_trd = 0;
        $is_no_redirect = false;
        
        if (isset($_GET[BIS_NO_REDIRECT])) {
            $is_no_trd = $_GET[BIS_NO_REDIRECT];

            if ($is_no_trd === 1 || $is_no_trd === '1') {
                // set to session check from session
                RulesEngineCacheWrapper::set_session_attribute(BIS_NO_REDIRECT, true);
            } else if ($is_no_trd === 0 || $is_no_trd === '0') { // explict redirect
                // set to session check from session
                RulesEngineCacheWrapper::remove_session_attribute(BIS_NO_REDIRECT);
                return true;
            }
        }
        
        if(RulesEngineCacheWrapper::is_session_attribute_set(BIS_NO_REDIRECT)) {
            if(RulesEngineCacheWrapper::get_session_attribute(BIS_NO_REDIRECT) === true
                    || RulesEngineCacheWrapper::get_session_attribute(BIS_NO_REDIRECT) === 'true') {
                return false;
            }
        }

        return true;
    }
    
    public static function get_audit_report_data_id() {
        $auditVO = RulesEngineCacheWrapper::get_session_attribute(BIS_AUDIT_INFO);
        
        if($auditVO == null) {
            return null;
        }
        return $auditVO->getId();
    }
    
    public static function get_purchase_code($product_id) {
        $bis_re_pur_key = BIS_PUR_CODE. $product_id;
        $bis_re_prd_vrf = RulesEngineUtil::get_option($bis_re_pur_key);

        if ($bis_re_prd_vrf == false) {
            return ""; 
        }
        
        return $bis_re_prd_vrf;
    } 
    
    public static function add_option($option, $value, $net_opt = false) {
        
        if (is_multisite()) {
            add_site_option($option, $value);
        } else {
            add_option($option, $value);
        }
    }
    
    public static function get_option($option, $net_opt = false) {
        
        if (is_multisite()) { // Sets in network options if multisite
            return get_site_option($option);
        } else {
           return get_option($option);
        }       
    }
    
    public static function update_option($option, $value, $net_opt = false) { 
        if (is_multisite()) {
           update_site_option($option, $value);
        } else {
           update_option($option, $value);
        }
    }
    
    public static function delete_option($option, $net_opt = false) {
        if (is_multisite()) {
            delete_site_option($option);
        } else {
            delete_option($option);
        }
    }
    
    public static function is_bis_re_plugin_page() {

        $is_plugin_page = false; 
        $bis_cpage = $_GET["page"];

        $bis_pages = array("bis_pg_dashboard", "bis_pg_rulesengine", "pagerules",
            "postrules", "categoryrules", "widgetrules", "themerules",  
            "redirectrules", "languagerules", "bis_pg_settings", "bis_pg_analytics", "bis_pg_");
        $is_plugin_page = in_array($bis_cpage, $bis_pages);
        
        if(!$is_plugin_page) {
            $is_plugin_page = RulesEngineUtil::isContains($bis_cpage, "bis_pg_");
        }
        
        return $is_plugin_page;
    }

    public static function getCommaSeperatedCategories($categories) {
        $catStr = "";
        
        if(!empty($categories)) {
            $catArray = array();

            foreach ($categories as $cat) {
                array_push($catArray, $cat->term_id);
            }
            if(!empty($catArray)) {
                $catStr = implode(',', $catArray);
            }
        }
        return $catStr;
    }
    
    public static function evaluateCategoryArrayTypeRule($array, $value, $condId, $isWooCat = false) {

        $eval = false;

        if(!empty($array)) {
            if (!$isWooCat) {
                foreach ($array as $cat) {
                    if (isset($cat->term_id) && $cat->term_id == $value) {
                        $eval = true;
                        break;
                    }
                }
            } else {
                $eval = in_array($value, $array);
            }
        }
        switch ($condId) {
            case 1:
                //Do nothing.
                //$eval = $eval;
                break;

            case 2:
                $eval = !$eval;
                break;
        }

        return $eval;
    }
    
    public static function is_reset_rule() {
        if (isset($_POST[BIS_RESET_RULE_PARAM]) ||
                isset($_GET[BIS_RESET_RULE_PARAM])) {
            if ($_POST[BIS_RESET_RULE_PARAM] == true || $_GET[BIS_RESET_RULE_PARAM] == true) {
                return true;
            }
        }
    }
    
    public static function replace_geo_placeholders($content, $geoLocVO) {
        
        if ($content !== "" && false !== strpos($content, '[')) {
            $content = str_replace('[bis_country_name]', $geoLocVO->getCountryName(), $content);
            $content = str_replace('[bis_city_name]', $geoLocVO->getCity(), $content);
            $content = str_replace('[bis_region_name]', $geoLocVO->getRegion(), $content);
        }
        
        return $content;
    }
    
    public static function get_merged_arrays($array1, $array2) {
        if (!empty($array1)) {
            if (empty($array2)) {
                $array2 = array();
            }
            $array2 = array_merge($array2, $array1);
        }

        return $array2;
    }
    
    public static function is_ajax_request() {
        if (((!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || 
                (!empty($_SERVER['X-Requested-With']) && 
                strtolower($_SERVER['X-Requested-With']) == 'xmlhttprequest' )) {
            
            return true;
        }
        return false;
    }
    
    /**
     * This method is used to return the file uplad directory.
     * 
     * @return string
     */
    public static function get_file_upload_path() {
        $upload_dirname = trailingslashit(WP_CONTENT_DIR) . 'uploads/' . BIS_UPLOAD_DIRECTORY . '/';
        return $upload_dirname;
    }
    
    /**
     * This method is used to delete directory and files recurrsively.
     * 
     * @param type $dir
     * @return boolean
     */
    public static function delete_directory($dir) {
        if (!file_exists($dir)) {
            return true;
        }
        $files = glob($dir."/*"); // get all file names
        
        foreach ($files as $file) { // iterate files
            if (is_file($file)) {
                unlink($file); // delete file
            }    
        }

        return rmdir($dir);
    }

}
