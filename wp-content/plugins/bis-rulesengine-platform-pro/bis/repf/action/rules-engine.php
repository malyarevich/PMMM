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

namespace bis\repf\action;

use bis\repf\common\RulesEngineCacheWrapper;
use bis\repf\model\LogicalRulesEngineModel;
use bis\repf\model\AnalyticsEngineModel;
use bis\repf\common\BISSessionWrapper;
use bis\repf\util\GeoPluginWrapper;
use bis\repf\util\RulesStack;
use bis\repf\vo\GeolocationVO;
use bis\repf\util\uagent_info;
use RulesEngineUtil;

/**
 * This class is used to evaluate logical rules.
 *
 * Class RulesEngine
 */
class RulesEngine extends BaseRulesEngine {
    
    
    /**
     *
     * This function returns the true if rule is valid.
     *
     * @param $rule_name
     * @return bool
     */
    public function is_rule_valid($rule_name) {

        // Get the valid session
        $bis_session_rules = RulesEngineUtil::get_applied_session_rules();

        // Get the valid request rules

        $bis_request_rules = $this->get_request_rules();

        // Condition check whether rule exists in session rules
        if ($bis_session_rules != null && count($bis_session_rules) > 0) {
            foreach ($bis_session_rules as $valid_rule) {
                if (RulesEngineUtil::isEqual($valid_rule->name, $rule_name)) {
                    return true;
                }
            }
        }

        // Validates request rules like page
        if ($bis_request_rules != null && count($bis_request_rules) > 0) {
            foreach ($bis_request_rules as $valid_rule) {
                if (RulesEngineUtil::isEqual($valid_rule->name, $rule_name) && $valid_rule->eval
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    public function bis_start_session() {
        
        if (RulesEngineUtil::is_reset_rule()) {
            RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);
        }

        if(RulesEngineCacheWrapper::get_session_attribute(BIS_GEOLOCATION_VO) == null) {
            $sessionWrapper = new BISSessionWrapper();
            $geoPlugin = $sessionWrapper->getGeoPlugin();
            $geoVO = new GeolocationVO();
            $geoVO->setCountryCode($geoPlugin->getCountryCode());
            $geoVO->setCountryCodeLowerCase(strtolower($geoPlugin->getCountryCode()));
            $geoVO->setCountryName($geoPlugin->getCountryName());
            $geoVO->setCity($geoPlugin->getCity());
            $geoVO->setRegion($geoPlugin->getRegion());
            $geoVO->setContinentCode($geoPlugin->getContinentCode());
            RulesEngineCacheWrapper::set_session_attribute(BIS_GEOLOCATION_VO, $geoVO);
        }
       
        if (!RulesEngineCacheWrapper::is_session_attribute_set(BIS_SESSION_INITIATED) ||
                (RulesEngineCacheWrapper::get_session_attribute(BIS_SESSION_INITIATED) === false)) {

            RulesEngineCacheWrapper::set_session_attribute(BIS_SESSION_INITIATED, true);

            $auditVO = RulesEngineCacheWrapper::get_session_attribute(BIS_AUDIT_INFO);

            if ($auditVO == null) {
                $auditVO = AnalyticsEngineModel::audit_user_request(1);
                RulesEngineCacheWrapper::set_session_attribute(BIS_AUDIT_INFO, $auditVO);
            }

            $this->add_logical_rules();
        }
    }

    /**
     * This method check whether the session is started or not
     * @return bool
     */
    public static function is_session_started() {
        if (php_sapi_name() !== 'cli') {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
            } else {
                return session_id() === '' ? FALSE : TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Stores all the validated rules in session
     */
    public function add_logical_rules($user = null) {

        $logical_rules_engine_modal = new LogicalRulesEngineModel ();
        $logical_rules = $logical_rules_engine_modal->get_active_rules();

        foreach ($logical_rules as $logical_rule) {
            $eVal = null;

            $rules_criteria_list = $logical_rules_engine_modal->get_rule_criteria_by_ruleId($logical_rule->ruleId);

            $expression = $this->get_rules_expression($rules_criteria_list, $user);

            // If expression contains X values then donot evaluate,
            // Expressions with X values should be evaluated at request time not session time.
            //@dev
            //var_dump("Expression ", $expression);

            $logical_rule->expression = null;

            if (!RulesEngineUtil::isContains($expression, "X")) {
                $eVal = $this->evaluate_expression($expression);
            } else {
                $eVal = true;
                $logical_rule->expression = trim($expression);
            }

            //@dev
           // var_dump("Final Expression ", $eVal);
            // Final value after evaluating the stack;
            if ($eVal) {
               $this->register_and_store_hook($logical_rule);
            }
        }
    }

    /**
     * This method will evaluate and return the rules status from the list of rules.
     * @param $rules_criteria_list
     * @return string
     */
    public function get_rules_expression($rules_criteria_list, $user = null) {

        $expression = "";

        foreach ($rules_criteria_list as $rule_criteria) {

            for ($c = 0; (int) $rule_criteria->lb > $c; $c++) {
                $expression = $expression . "( ";
            }

            // Evaluate the rule
            $value = $this->evaluate_rule($rule_criteria, $user);

            if (RulesEngineUtil::isContains($value, "X")) {
                $str_value = $value;
            } else {

                $str_value = "F";

                if ($value) {
                    $str_value = "T";
                }
            }

            $expression = $expression . "" . $str_value;

            for ($c = 0; (int) $rule_criteria->rb > $c; $c++) {
                $expression = $expression . " )";
            }

            if ($rule_criteria->operId == 1) {
                $expression = $expression . " + ";
            } else if ($rule_criteria->operId == 2) {
                $expression = $expression . " - ";
            }
        }

        return $expression;
    }

    /**
     * This method is used to evaluate logical rule.
     *
     * @param $logical_rule
     * @return bool|string
     */
    private function evaluate_rule($logical_rule, $user = null) {

        $sub_opt_id = (int) $logical_rule->subOptId; // Option Id

        $condId = $logical_rule->condId;

        $rule_value = $logical_rule->value;

        require_once(ABSPATH . '/wp-includes/pluggable.php');

        $eVal = false;
        switch ($sub_opt_id) {

            case 1:    //User Role
                $eVal = false;

                $user = wp_get_current_user();
                if ($user->ID != 0) {
                    foreach ($user->roles as $roleName) {

                        $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $roleName, $condId);
                        if ($eVal) {
                            break;
                        }
                    }
                }

                break;

            case 2: //emailId Rule
                $eVal = false;
                $logged_in_user = wp_get_current_user();
                if ($logged_in_user->ID != 0) {
                    $email = $logged_in_user->user_email;
                    if ($condId == 1 || $condId == 2) {
                        $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $email, $condId);
                    } else {
                        $eVal = RulesEngineUtil::evaluateStringTypeRule($email, $rule_value, $condId);
                    }
                }

                break;

            case 3: // Registered date
                $eVal = false;
                $logged_in_user = wp_get_current_user();

                if ($logged_in_user->ID != 0) {
                    $reg_date = $logged_in_user->user_registered;
                    $reg_date = date_format(date_create($reg_date), "Y-m-d");
                    $eVal = RulesEngineUtil::evaluateDateTypeRule($reg_date, $rule_value, $condId);
                }
                break;

            case 4: // Country
                $geo_plugin = new GeoPluginWrapper();
                $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $geo_plugin->getCountryCode(), $condId);
                break;

            case 5: // Currency
                $geo_plugin = new GeoPluginWrapper();
                $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $geo_plugin->getCurrencyCode(), $condId);
                break;

            case 6: // Referral Path
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_REFERRAL_PATH_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 7: // language
                $eVal = RulesEngineUtil::evaluateStringTypeRule($rule_value, get_locale(), $condId);
                break;

            case 8: // Browser
                $eVal = RulesEngineUtil::evaluate_browser_rule($rule_value, $condId);
                break;

            case 9: // Date
                // Get the current date
                $current_date = date("Y-m-d");
                $eVal = RulesEngineUtil::evaluateDateTypeRule($current_date, $rule_value, $condId);
                break;

            case 10: // Time
                $current_time = date('H:i', current_time('timestamp', 0));
                $t1 = strtotime($current_time);
                $t2 = strtotime($rule_value);
                $eVal = RulesEngineUtil::evaluateIntTypeRule($t1, $t2, $condId);
                break;

            case 14: // Date and Time rules should be evaluated at runtime.
                // These value will always true.
                // Get the current date
                $current_date = date('Y-m-d H:i', current_time('timestamp', 0));
                $d1 = strtotime($current_date);
                $d2 = strtotime($rule_value);
                $eVal = RulesEngineUtil::evaluateIntTypeRule($d1, $d2, $condId);
                break;

            case 15: // IP Address Rule
                $geo_plugin = new GeoPluginWrapper();
                $eVal = RulesEngineUtil::evaluateStringTypeRule($geo_plugin->getIPAddress(), $rule_value, $condId);
                break;

            case 16: // Status code       
                $eVal = RulesEngineUtil::get_dynamic_rule_expression_val_type(BIS_RULE_STATUS_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 17: // WordPress category code       
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_CATEGORY_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 25: // WooCommerce category code       
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_CATEGORY_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 11: // Mobile
            case 12: // Tablet
            case 13: // Mobile Operating System
            case 28:  // Mobile Device Type
                $eVal = $this->evaluate_mobile_rule($rule_value);

                // Not equal
                if ($condId == 2) {
                    $eVal = !$eVal;
                }

                break;

            case 18: // User Id
                $eVal = false;
                $logged_in_user = wp_get_current_user();
                if ($logged_in_user->ID != 0) {
                    $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $logged_in_user->user_login, $condId);
                }
                break;

            case 19: // Guest User
                $eVal = false;

                $logged_in_user = wp_get_current_user();

                // Guest User
                if ($logged_in_user->ID == 0 && $rule_value == 25 && $condId == 1) {
                    $eVal = true;
                }

                // Logged In User
                if ($logged_in_user->ID != 0 && $rule_value == 25 && $condId == 2) {
                    $eVal = true;
                }


                break;

            case 23: // Day of the Week
                $day_of_week = jddayofweek(cal_to_jd(CAL_GREGORIAN, date("m"), date("d"), date("Y")), 1);
                $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $day_of_week, $condId);
                break;

            case 24: // Months
                $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, date('M'), $condId);
                break;

            case 20: // Continent
                $geo_plugin = new GeoPluginWrapper();
                $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $geo_plugin->getContinentCode(), $condId);
                break;

            case 21: // Page Title
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_PAGE_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 22: // Post Title
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_POST_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 26: // Param condition
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_PARAM_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 27: // Form data
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_FORM_DATA_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 29: // City Rule
                $geo_plugin = new GeoPluginWrapper();
                $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $geo_plugin->getCity(), $condId);
                break;

            case 30: // Region
                $geo_plugin = new GeoPluginWrapper();
                $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $geo_plugin->getRegion(), $condId);
                
                if(!$eVal) {
                    $region = $geo_plugin->getRegion();
                    $region = "State of ".$region;
                    $eVal = RulesEngineUtil::evaluateTokenInputRule($rule_value, $region, $condId);
                } 
                
                break;
            
            case 31: // Referral Path
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_REFERRED_PATH_EXPRESSION_APPEND, $rule_value, $condId);
                break;

            case 32: // Referral Path
                $eVal = RulesEngineUtil::get_dynamic_rule_expression(BIS_RULE_COOKIE_EXPRESSION_APPEND, $rule_value, $condId);
                break;
        } // End of sub_opt_id switch


        return $eVal;
    }

    /**
     * This method is used to evaluate mobile rule.
     *
     * @param $p_rule_value
     * @return bool|int
     */
    private function evaluate_mobile_rule($p_rule_value) {

        $uagent_info = new uagent_info();
        $rule_value = (int) $p_rule_value;
        $eVal = false;

        switch ($rule_value) {

            case 1: //"IOS"
                $eVal = $uagent_info->DetectIos();
                break;

            case 2: //"Andriod"
                $eVal = $uagent_info->DetectAndroid();
                break;

            case 3: //"Windows"
                $eVal = $uagent_info->DetectWindowsPhone();
                break;

            case 4: //"iPhone"
                $eVal = $uagent_info->DetectIphone();
                break;

            case 5: //"Andriod_Phone"
                $eVal = $uagent_info->DetectAndroidPhone();
                break;

            case 6: //"BlackBerry_Phone"
                $eVal = $uagent_info->DetectBlackBerry10Phone();
                break;

            case 7: // iPad
                $eVal = $uagent_info->DetectIpad();
                break;

            case 8: //"Andriod_Tablet"
                $eVal = $uagent_info->DetectAndroidTablet();
                break;

            case 9: //BlackBerry Tablet
                $eVal = $uagent_info->DetectBlackBerryTablet();
                break;

            case 10: //"HP_TouchPad":
                $eVal = $uagent_info->DetectWebOSTablet();
                break;

            case 29: //"Windows_Phone":
                $eVal = $uagent_info->DetectWindowsPhone();
                break;

            case 26: //"Mobile":
                $eVal = $uagent_info->DetectMobileQuick();
                break;

            case 28: //"Smart_Phone":
                $eVal = $uagent_info->DetectSmartphone();
                break;

            case 27: //"Tablet":
                $eVal = $uagent_info->DetectTierTablet();
                break;
        } // End of rule_value Mobile switch

        return $eVal;
    }

    /**
     * This method is used to evaluate expression.
     *
     * @param $expression
     * @return bool
     */
    public function evaluate_expression($expression) {
        $tokens = str_split($expression);

        // Stack for values
        $values = new RulesStack ();

        // Stack for operators
        $ops = new RulesStack ();

        for ($i = 0; $i < count($tokens); $i++) {
            // Current token is a whitespace, skip it
            if ($tokens [$i] == ' ')
                continue;

            // Current token is a number, push it to stack for numbers
            if ($tokens [$i] == 'T' || $tokens [$i] == 'F') {
                $value = false;

                if ($tokens [$i] == 'T') {
                    $value = true;
                }
                $values->push($value);
            }            // Current token is an opening brace, push it to 'ops'
            else if ($tokens [$i] == '(')
                $ops->push($tokens [$i]);

            // Closing brace encountered, solve entire brace
            else if ($tokens [$i] == ')') {
                while ($ops->peek() != '(')
                    $values->push($this->applyOp($ops->pop(), $values->pop(), $values->pop()));
                $ops->pop();
            } // Current token is an operator.
            else if ($tokens [$i] == '+' || $tokens [$i] == '-') {
                // While top of 'ops' has same or greater precedence to current
                // token, which is an operator. Apply operator on top of 'ops'
                // to top two elements in values stack
                while (!$ops->isEmpty() && $this->hasPrecedence($tokens [$i], $ops->peek()))
                    $values->push($this->applyOp($ops->pop(), $values->pop(), $values->pop()));

                // Push current token to 'ops'.
                $ops->push($tokens [$i]);
            }
        }

        // Entire expression has been parsed at this point, apply remaining
        // ops to remaining values
        $final_value = 0;
        while (!$ops->isEmpty()) {

            $first_val = $values->pop();

            if (!$values->isEmpty()) {
                $second_val = $values->pop();
                $values->push($this->applyOp($ops->pop(), $first_val, $second_val));
            } else {

                // Top of 'values' contains result, return it
                if (!$values->isEmpty()) {
                    $final_value = $values->pop();
                }
                if ($final_value == 1) {
                    return true;
                } else {
                    return false;
                }
            }
        }

        // Top of 'values' contains result, return it
        if (!$values->isEmpty()) {
            $final_value = $values->pop();
        }


        if ($final_value == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This method is used to evaluate operators.
     *
     * @param $op
     * @param $b
     * @param $a
     * @return bool
     */
    public function applyOp($op, $b, $a) {
        // + = AND
        // - = OR
        if ($op == "+") {
            return $a && $b;
        } else {
            return $a || $b;
        }
    }

    /**
     * This method is used to evaluate the precedence of operator.
     * @param $op1
     * @param $op2
     * @return bool
     */
    public function hasPrecedence($op1, $op2) {
        if ($op2 == '(' || $op2 == ')')
            return false;
        // Current expression donot contains * or / below code never executes
        if (($op1 == '*' || $op1 == '/') && ($op2 == '+' || $op2 == '-'))
            return false;
        else
            return true;
    }

    /**
     * Stores all the applicable rules with rule Name and registers the hook name.
     *
     * @param $logical_rule
     */
    public function register_and_store_hook($logical_rule) {

        // For session rules expression will be null
        // Request rules hooks will be called at request time when rules are evaluated.
        if ($logical_rule->expression == null) {
           $this->call_hook($logical_rule);
        }

        $bis_rules_array = array();
        
       /* if(RulesEngineCacheWrapper::is_session_attribute_set(BIS_RULES_ARRAY)) {
            $bis_rules_array = Base$this->get_applied_logical_rules();
        }
        * */
       
        if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_RULES_ARRAY)) {
            $bis_rules_array = RulesEngineCacheWrapper::get_session_attribute(BIS_RULES_ARRAY);
        }
        
        $bis_request_rules_array = array();

        if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_REQUEST_RULES_ARRAY)) {
            $bis_request_rules_array = RulesEngineCacheWrapper::get_session_attribute(BIS_REQUEST_RULES_ARRAY);
        }

        // Separating request rules from session rules
        if ($logical_rule->expression == null && (in_array($logical_rule, $bis_rules_array) === FALSE)) {
                array_push($bis_rules_array, $logical_rule);
                RulesEngineCacheWrapper::set_session_attribute(BIS_RULES_ARRAY, $bis_rules_array);
           
        } elseif(!in_array($logical_rule, $bis_request_rules_array)) {
                array_push($bis_request_rules_array, $logical_rule);
                RulesEngineCacheWrapper::
                set_session_attribute(BIS_REQUEST_RULES_ARRAY, $bis_request_rules_array);
        }
    }

    public function bis_reset_rules_engine_cache_login($user_login, $user) {


        if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_AUDIT_INFO)) {
            $auditVO = RulesEngineCacheWrapper::get_session_attribute(BIS_AUDIT_INFO);
            $auditVO->setUser($user);
            AnalyticsEngineModel::audit_user_login($auditVO);
        }

        RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);

        $this->bis_end_session();
    }

    public function bis_end_session() {
        RulesEngineCacheWrapper::set_session_attribute(BIS_SESSION_INITIATED, false);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_RULES_ARRAY);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_REQUEST_RULES_ARRAY);
        RulesEngineCacheWrapper::destroy_session();
    }

    public function bis_reset_rules_engine_cache_Logout() {

        $auditVO = null;

        if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_AUDIT_INFO)) {
            $auditVO = RulesEngineCacheWrapper::get_session_attribute(BIS_AUDIT_INFO);
            $auditVO->setUser(null);
            AnalyticsEngineModel::audit_user_logout($auditVO->getReportAuthId());
        }

        RulesEngineCacheWrapper::set_reset_time(BIS_LOGICAL_RULE_RESET);
        $this->bis_end_session();
    }
    
    function bis_evaluate_request_rules($query, \bis\repf\vo\CacheVO $cacheVO = null) {
      
        global $wp_query;
        $current_page_id = NULL;
        $woo_category_id = NULL;
        $referer_path = NULL;
        $is404 = FALSE;
        $isAjaxRequest = false;
        $current_category = NULL;

        if($cacheVO != NULL) {
            $current_page_id = $cacheVO->getPostId();
            
            if($cacheVO->getReferralUrl() != NULL) {
                $referer_path = $cacheVO->getReferralUrl();
            }
            
            if($cacheVO->getCategoryId() != NULL) {
                $current_category = array($cacheVO->getCategoryId());
            }
           
            $isAjaxRequest = $cacheVO->isAjaxRequest();                    
            $is404 = $cacheVO->is404();
        } else {
            if($current_page_id == NULL) {
                $current_page_id = $this->get_the_ID();
            }
            $is404 = is_404();
            
            if($current_page_id === FALSE) {
                return $query;
            }

            if (RulesEngineUtil::is_woocommerce_installed() &&
                    $current_page_id !== FALSE && is_shop()) {
                $current_page_id = (int) get_option('woocommerce_shop_page_id');
            } 
            $referer_path = wp_get_referer();

            $current_category = get_the_category();
            // get the query object
            $cat_obj = $wp_query->get_queried_object();
            $woo_category_id = null;

            if (RulesEngineUtil::is_woocommerce_installed() && $cat_obj) {

                $cat = get_query_var('cat');
                $category = get_category($cat);

                if (isset($cat_obj->term_id)) {
                    $woo_category_id = $cat_obj->term_id;
                }
            }
        }
        $applied_rules = $this->get_request_rules();

        if ($applied_rules != null && count($applied_rules) > 0) {

            foreach ($applied_rules as $applied_rule) {

                if (RulesEngineUtil::isContains($applied_rule->expression, "X")) {

                    $expression = explode(" ", $applied_rule->expression);
                    // Cookie rule
                    foreach ($expression as $key => $value) {
                        $rule_values = explode("$", $value);

                        if (RulesEngineUtil::isContains($value, BIS_RULE_COOKIE_EXPRESSION_APPEND)) {

                            if (RulesEngineUtil::is_reset_rule()) {
                                $eval = "F";
                            } else {
                                $cookie = '';
                                $rule_value = $rule_values[1];
                                $condId = (int) $rule_values[2];

                                if ($condId == 10 || $condId == 11) {
                                    $cookie_val = '';
                                    $cookie_key = $rule_values[1];
                                } else {
                                    if (RulesEngineUtil::isContains($rule_values[1], "=")) {
                                        $cookie_data = explode("=", $rule_values[1]);
                                        $cookie_key = $cookie_data[0];
                                        $cookie_val = $cookie_data[1];
                                    } else {
                                        $cookie_key = $rule_values[1];
                                        $cookie_val = '';
                                    }
                                }

                                if (isset($_COOKIE[$cookie_key])) {
                                    $cookie = $_COOKIE[$cookie_key];
                                }

                                $eval = RulesEngineUtil::evaluateStringTypeRule($cookie, $cookie_val, $condId);

                                if ($eval) {
                                    $eval = "T";

                                    if ($applied_rule->eval_type == "2") {
                                        $applied_rule->expression = TRUE;
                                    }
                                } else {
                                    $eval = "F";
                                }
                            }

                            $expression[$key] = $eval;

                            // Cookie expresssion end   
                        } else if (RulesEngineUtil::isContains($value, BIS_RULE_FORM_DATA_EXPRESSION_APPEND)
                        && (count($rule_values) > 2)) {

                            // Parameter Rules
                            $rule_value = $rule_values[1];
                            $condId = (int) $rule_values[2];

                            $eval = RulesEngineUtil::evaluateFormDataRules($rule_value, $condId);

                            if ($eval) {
                                $eval = "T";

                                if ($applied_rule->eval_type == "2") {
                                    $applied_rule->expression = TRUE;
                                }
                                if (isset($_POST[BIS_COUNTRY_SELECT])) {
                                    $country = $_POST[BIS_COUNTRY_SELECT];
                                    setcookie(BIS_COUNTRY_SELECT, $country, time() + BIS_COOKIE_EXPIRE_TIME, COOKIEPATH, COOKIE_DOMAIN);
                                }
                            } else {
                                $eval = "F";
                            }

                            $expression[$key] = $eval;

                            // Param expression expresssion    
                        } else if (RulesEngineUtil::isContains($value, BIS_RULE_PARAM_EXPRESSION_APPEND)) {

                            $rule_value = $rule_values[1];
                            $condId = (int) $rule_values[2];
                            $eval = RulesEngineUtil::evaluateParameterRules($rule_value, $condId);

                            if ($eval) {
                                $eval = "T";

                                if ($applied_rule->eval_type == "2") {
                                    $applied_rule->expression = TRUE;
                                }
                            } else {
                                $eval = "F";
                            }


                            $expression[$key] = $eval;

                            // Page rule expresssion    
                        } else if ($referer_path &&
                                RulesEngineUtil::isContains($value, BIS_RULE_REFERRED_PATH_EXPRESSION_APPEND)) {

                            $rule_value = $rule_values[1];
                            $condId = (int) $rule_values[2];
                            $eval = RulesEngineUtil::evaluateURLTypeRule(array($referer_path), $rule_value, 
                                    $condId, $applied_rule);

                            if ($eval) {
                                $eval = "T";

                                if ($applied_rule->eval_type == "2") {
                                    $applied_rule->expression = TRUE;
                                }
                            } else {
                                $eval = "F";
                            }

                            $expression[$key] = $eval;

                            // Page rule expresssion    
                        } else if (RulesEngineUtil::isContains($value, BIS_RULE_PAGE_EXPRESSION_APPEND)) {
                            $rule_values = explode("$", $value);
                            $rule_page_id = (int) $rule_values[1];
                            $condId = (int) $rule_values[2];

                            $eval = RulesEngineUtil::evaluateIntTypeRule($current_page_id, $rule_page_id, $condId);

                            if ($eval) {
                                $eval = "T";

                                if ($applied_rule->eval_type == "2") {
                                    $applied_rule->expression = TRUE;
                                }
                            } else {
                                $eval = "F";
                            }

                            $expression[$key] = $eval;
                            // Posts rule 
                        } else if (RulesEngineUtil::isContains($value, BIS_RULE_POST_EXPRESSION_APPEND)) {
                            $rule_values = explode("$", $value);
                            $rule_page_id = (int) $rule_values[1];
                            $condId = (int) $rule_values[2];

                            $eval = RulesEngineUtil::evaluateIntTypeRule($current_page_id, $rule_page_id, $condId);

                            if ($eval) {
                                $eval = "T";

                                if ($applied_rule->eval_type == "2") {
                                    $applied_rule->expression = TRUE;
                                }
                            } else {
                                $eval = "F";
                            }

                            $expression[$key] = $eval;
                            // Category 
                        } else if (RulesEngineUtil::isContains($value, BIS_RULE_CATEGORY_EXPRESSION_APPEND)) {
                            $rule_values = explode("$", $value);
                            $rule_cat_id = (int) $rule_values[1];
                            $condId = (int) $rule_values[2];
                            $is_woo_cat = false;

                            if ($woo_category_id != null) {
                                $current_category = array($woo_category_id);
                                $is_woo_cat = true;
                            }

                            $eval = RulesEngineUtil::evaluateCategoryArrayTypeRule($current_category, $rule_cat_id, $condId, $is_woo_cat);

                            if ($eval) {
                                $eval = "T";

                                if ($applied_rule->eval_type == "2") {
                                    $applied_rule->expression = TRUE;
                                }
                            } else {
                                $eval = "F";
                            }

                            $expression[$key] = $eval;
                            // Request path 
                        } else if (RulesEngineUtil::isContains($value, BIS_RULE_REFERRAL_PATH_EXPRESSION_APPEND)) {
                            $current_path = RulesEngineUtil::get_current_url();
                            $rule_values = explode("$", $value);
                            $rule_value = $rule_values[1];
                            $condId = (int) $rule_values[2];
                            $eval = RulesEngineUtil::evaluateURLTypeRule($current_path, 
                                    $rule_value, $condId, $applied_rule);

                            if ($eval) {
                                $eval = "T";
                                if ($applied_rule->eval_type == "2") {
                                    $applied_rule->expression = TRUE;
                                }
                            } else {
                                $eval = "F";
                            }

                            $expression[$key] = $eval;
                        } else if (RulesEngineUtil::isContains($applied_rule->expression, BIS_RULE_STATUS_EXPRESSION_APPEND)) {

                            $rule_values = explode("$", $value);
                            $rule_status_id = (int) $rule_values[1];
                            $condId = (int) $rule_values[2];
                            $eval = "F";
                            
                            if ($rule_status_id === 21) { // 404
                                if (($is404 === true || $is404 === "true") && $condId === 1) {
                                    $eval = "T";
                                }
                                if (($is404 === false || $is404 === "false") && $condId === 2) {
                                    $eval = "T";
                                }
                            }

                            $expression[$key] = $eval;
                        }
                    } // End of express for loop


                    $eval_expression = implode(" ", $expression);
                    $applied_rule->eval = $eval_expression;

                    if (!RulesEngineUtil::isContains($eval_expression, "X")) {

                        $eval_expression = $this->evaluate_expression($eval_expression);

                        // If expression is true call hook;
                        if ($eval_expression) {
                            $this->call_hook($applied_rule);
                        }

                        $applied_rule->eval = $eval_expression;
                    } // End of if
                } // End of if
            } // End of applied_rules
        } // End of If 

        $this->apply_request_redirect_rules($applied_rules, $isAjaxRequest);
        return $query;
    }

    public function bis_country_name($attrs) {
        $geolocVO = RulesEngineCacheWrapper::get_session_attribute(BIS_GEOLOCATION_VO);
        return $geolocVO->getCountryName();
    }
    
    public function bis_city_name($attrs) {
        $geolocVO = RulesEngineCacheWrapper::get_session_attribute(BIS_GEOLOCATION_VO);
        return $geolocVO->getCity();
    }
    
    public function bis_region_name($attrs) {
        $geolocVO = RulesEngineCacheWrapper::get_session_attribute(BIS_GEOLOCATION_VO);
        return $geolocVO->getRegion();
    }
    
    /**
     * 
     * This method is used to support country dropdown.
     * 
     * @param type $atts
     * @return string
     */
    public function bis_country_selector($atts) {

        $atts = shortcode_atts(
                array(
            'include' => '',
            'exclude' => '',
            'search_box' => '',
            'class' => '',
            'default' => '',
            'eng_label' => ''
                ), $atts, 'bis_country_selector');


        $includes = false;
        $exclude = false;
        $bis_class = '';
        $bis_search_box = 10;
        $eng_label = false;
        $default = false;

        if (!RulesEngineUtil::isNullOrEmptyString($atts['include'])) {
            $includes = RulesEngineUtil::convertToSQLString($atts['include']);
        }

        if (!RulesEngineUtil::isNullOrEmptyString($atts['eng_label'])) {
            $eng_label = $atts['eng_label'];
        }

        if (!RulesEngineUtil::isNullOrEmptyString($atts['search_box'])) {

            if ($atts['search_box'] === "false") {
                $bis_search_box = "Infinity";
            }
        }

        if (!RulesEngineUtil::isNullOrEmptyString($atts['exclude'])) {
            $exclude = RulesEngineUtil::convertToSQLString($atts['exclude']);
        }
        
        if (!RulesEngineUtil::isNullOrEmptyString($atts['default'])) {
            $default = $atts['default'];
        }

        if (!RulesEngineUtil::isNullOrEmptyString($atts['class'])) {
            $bis_class = "class=\"" . $atts['class'] . "\"";
        }

        $countries = $this->get_countries($includes, $exclude, $eng_label);
        // from drop down.   
        if (isset($_POST[BIS_COUNTRY_SELECT])) {
            $country = $_POST[BIS_COUNTRY_SELECT];
            RulesEngineCacheWrapper::set_session_attribute(BIS_COUNTRY_SELECT, $country);
        } else if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_COUNTRY_SELECT)) {
            // From session
            $country = RulesEngineCacheWrapper::get_session_attribute(BIS_COUNTRY_SELECT);
        } else if (isset($_COOKIE[BIS_COUNTRY_SELECT])) {
            // From cookie
            $country = $_COOKIE[BIS_COUNTRY_SELECT];
        } else if ($default != false) {
            // from default value
            $country = $default;
        } else { // from geo location
            $sessionWrapper = new BISSessionWrapper();
            $geo_plugin = $sessionWrapper->getGeoPlugin();
            $country = $geo_plugin->getCountryCode();
        }

        $bis_country_dropdown = "<form method=\"post\" id=\"bis_country_form\" name=\"bis_country_form\">";
        $bis_country_dropdown = $bis_country_dropdown . "<input type=\"hidden\" id=\"bis_reset_rule\" value=\"true\" name=\"bis_reset_rule\">";
        $bis_country_dropdown = $bis_country_dropdown . "<input type=\"hidden\" id=\"bis_search_box\" value=" . $bis_search_box . " name=\"bis_search_box\">";
        $bis_country_dropdown = $bis_country_dropdown . "<select " . $bis_class . "  name=\"bis_country\" id = \"bis_country\">";

        foreach ($countries as $activeCountry) {
            $selected = "";

            if ($country === $activeCountry->id) {
                $selected = "selected";
            }

            $bis_country_dropdown = $bis_country_dropdown . "<option " . $selected . " value=" . $activeCountry->id . ">"
                    . $activeCountry->name .
                    "</option>";
        }

        $bis_country_dropdown = $bis_country_dropdown . "</select></form>";
        return $bis_country_dropdown;
    }

    public function get_countries($includes, $exclude, $eng_label = false) {
        $countries = null;
        

        if(RulesEngineCacheWrapper::is_session_attribute_set(BIS_USER_COUNTRY_DROPDOWN)) {
            $countries = RulesEngineCacheWrapper::get_session_attribute(BIS_USER_COUNTRY_DROPDOWN);
            return $countries;
        } 
        
        $logicalRulesEngineModal = new LogicalRulesEngineModel();
        $countries = $logicalRulesEngineModal->get_countries($includes, $exclude);
      

            if ($eng_label !== "true" || $eng_label === "only") {
            foreach ($countries as $activeCountry) {
                if (RulesEngineUtil::isContains($activeCountry->name, "-")) {
                    $countries_name = explode("-", $activeCountry->name);
                    if ($eng_label === "only") {
                        $activeCountry->name = $countries_name[0];
                    } else {
                        $activeCountry->name = $countries_name[1];
                    }
                }
            }
        }

        RulesEngineCacheWrapper::set_session_attribute(BIS_USER_COUNTRY_DROPDOWN, $countries);

        return $countries;
    }
    
    function bis_re_append_page_info($content) {

        global $wp_query;

        $current_page_id = $this->get_the_ID();

        if (RulesEngineUtil::is_woocommerce_installed() &&
                $current_page_id !== FALSE && is_shop()) {
            $current_page_id = (int) get_option('woocommerce_shop_page_id');
        }

        $referer_path = wp_get_referer();
        $current_category = get_the_category();

        $str_categories = RulesEngineUtil::getCommaSeperatedCategories($current_category);
        $woo_category_id = null;

        if (RulesEngineUtil::is_woocommerce_installed()) {

            // get the query object
            $cat_obj = $wp_query->get_queried_object();

            if ($cat_obj) {
                $cat = get_query_var('cat');

                if (isset($cat_obj->term_id)) {
                    $woo_category_id = $cat_obj->term_id;
                    if ($woo_category_id != null) {
                        $str_categories = $woo_category_id;
                    }
                }
            }
        } else {
            if (isset($str_categories) && !empty($str_categories)) {
                $current_category = $str_categories;
            }
        }

        $hidden_page_id = '<input type="hidden" name="bis_re_cache_post_id" 
        id="bis_re_cache_post_id" value="' . $current_page_id . '" />';

        $hidden_cat_id = '<input type="hidden" name="bis_re_cache_cat_id" 
        id="bis_re_cache_cat_id" value="' . $str_categories . '" />';

        $hidden_refere_path = '<input type="hidden" name="bis_re_cache_reffer_path" 
        id="bis_re_cache_reffer_path" value="' . $referer_path . '" />';
  
        $hidden_site_url = '<input type="hidden" name="bis_re_site_url" 
 		id="bis_re_site_url" value="' . get_site_url() . '" />';

        $content = $content . $hidden_page_id . $hidden_cat_id .
                $hidden_refere_path . $hidden_site_url;

        return $content;
    }

    public static function bis_change_country_hook() {
        ?>

        <script>
            jQuery(function () {

                function template(state, container) {

                    if (!state.id) {
                        return state.text;
                    }

                    var cid = state.id;

                    var $state = jQuery(
                            '<span class="flag-icon flag-icon-' + cid.toLowerCase() + ' flag-icon-squared"></span> <span class="flag-text">' + state.text + '</span>'
                            );
                    return $state;
                }

                var minResultsSearch = jQuery("#bis_search_box").val();

                if (jQuery("#bis_country").attr("class") != null) {
                    var selectClass = jQuery("#bis_country").attr("class");

                    jQuery('#bis_country').bis_select2({
                        dropdownCssClass: selectClass,
                        minimumResultsForSearch: minResultsSearch,
                        templateSelection: template,
                        templateResult: template
                    });
                } else {
                    jQuery('#bis_country').bis_select2({
                        minimumResultsForSearch: minResultsSearch,
                        templateSelection: template,
                        templateResult: template
                    });
                }

                jQuery("#bis_country").change(function () {
                    jQuery("#bis_country_form").attr("action", window.location);
                    jQuery("#bis_country_form").submit();
                });
            });
        </script>
        <?php
    }

}
?>