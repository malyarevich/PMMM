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
use bis\repf\model\PageRulesEngineModel;
use bis\repf\model\AnalyticsEngineModel;
use RulesEngineUtil;

/**
 * Base class for all rule engines
 *
 * Class BaseRulesEngine
 */
abstract class BaseRulesEngine {

    public function clear_applied_rules($user = null) {
        $reset_time = RulesEngineCacheWrapper::get_reset_time(BIS_LOGICAL_RULE_RESET);

        RulesEngineCacheWrapper::set_session_attribute(BIS_LOGICAL_RULE_RESET, $reset_time);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_RULES_ARRAY);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_EXCLUDE_PAGES);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_APPEND_TO_PAGES);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_EXCLUDE_WIDGETS);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_LOAD_RULE_THEME);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_EXCLUDE_CATEGORIES);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_EXCLUDE_POSTS);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_REQUEST_RULES_ARRAY);
        RulesEngineCacheWrapper::remove_session_attribute(BIS_APPLY_LANGUAGE);
        $rulesEngine = new RulesEngine();
        $rulesEngine->add_logical_rules($user);
    }

    public function get_applied_session_logical_rules($user = null) {
        return $this->get_applied_logical_rules($user);
    }
    
    /**
     * This method return applied logical rules returns only the session rules.
     *
     * @return $applied_rules
     */
    public function get_applied_logical_rules($user = null) {
        $applied_rules = null;

        // Commented to fix performance issue.
        /*if (RulesEngineCacheWrapper::get_reset_time(BIS_LOGICAL_RULE_RESET) !== false) {
            $reset_time = RulesEngineCacheWrapper::get_reset_time(BIS_LOGICAL_RULE_RESET);

            $session_reset_time = 0;

            if (RulesEngineCacheWrapper::get_session_attribute(BIS_LOGICAL_RULE_RESET) != null) {
                $session_reset_time = RulesEngineCacheWrapper::get_session_attribute(BIS_LOGICAL_RULE_RESET);
            }

            if ($session_reset_time < $reset_time) {
                $rulesEngine = new RulesEngine();
                $this->clear_applied_rules($user);
                $rulesEngine->add_logical_rules();
            }
        }*/

        if (RulesEngineCacheWrapper::get_session_attribute(BIS_RULES_ARRAY) != null) {
            $applied_rules = RulesEngineCacheWrapper::get_session_attribute(BIS_RULES_ARRAY);
        }

        return $applied_rules;
    }

    /**
     * This method return excluded widget from session if available
     *
     * @return $exclude_widgets
     */
    public static function get_widget_rules() {

        $exclude_widgets = null;
        $session_reset_time = 0;

        if (RulesEngineCacheWrapper::get_reset_time(BIS_WIDGET_RULE_RESET) !== false) {
            $reset_time = RulesEngineCacheWrapper::get_reset_time(BIS_WIDGET_RULE_RESET);

            if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_WIDGET_RULE_RESET)) {
                $session_reset_time = RulesEngineCacheWrapper::get_session_attribute(BIS_WIDGET_RULE_RESET);
            }

            if ($session_reset_time < $reset_time) {
                // Check if excluded category exists in session.
                if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_EXCLUDE_WIDGETS)) {
                    RulesEngineCacheWrapper::set_session_attribute(BIS_WIDGET_RULE_RESET, $reset_time);
                    RulesEngineCacheWrapper::set_session_attribute(BIS_EXCLUDE_WIDGETS, null);
                    $exclude_widgets = null;
                }
            } else {
                // Check if excluded category exists in session.
                if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_EXCLUDE_WIDGETS)) {
                    $exclude_widgets = RulesEngineCacheWrapper::get_session_attribute(BIS_EXCLUDE_WIDGETS);
                }
            }
        } else {
            // Check if excluded widgets exists in session.
            if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_EXCLUDE_WIDGETS)) {
                $exclude_widgets = RulesEngineCacheWrapper::get_session_attribute(BIS_EXCLUDE_WIDGETS);
            }
        }

        return $exclude_widgets;
    }

    /**
     * This method returns excluded pages if available in session
     * @return $exclude_pages
     */
    public static function get_page_rules() {

        $exclude_pages = null;

        // Check if excluded pages exists in session.
        if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_EXCLUDE_PAGES)) {
            $exclude_pages = RulesEngineCacheWrapper::get_session_attribute(BIS_EXCLUDE_PAGES);
        }
        

        return $exclude_pages;
    }

    /**
     * This method is used get the append page rules.
     *
     * @return $append_to_pages
     */
    public static function get_append_page_rules() {

        $append_to_pages = null;

        // Check if excluded pages exists in session.
        if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_APPEND_TO_PAGES)) {
            $append_to_pages = RulesEngineCacheWrapper::get_session_attribute(BIS_APPEND_TO_PAGES);
        }

        return $append_to_pages;
    }

    /**
     * This method is used to get themes from session.
     * @return $load_themes
     */
    public static function get_theme_rules() {
        $load_themes = null;

        if (RulesEngineCacheWrapper::get_reset_time(BIS_THEME_RULE_RESET) !== false) {
            $reset_time = RulesEngineCacheWrapper::get_reset_time(BIS_THEME_RULE_RESET);
            $session_reset_time = 0;

            if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_THEME_RULE_RESET)) {
                $session_reset_time = RulesEngineCacheWrapper::get_session_attribute(BIS_THEME_RULE_RESET);
            }

            if ($session_reset_time < $reset_time) {
                // Check if excluded category exists in session.
                if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_LOAD_RULE_THEME)) {
                    RulesEngineCacheWrapper::set_session_attribute(BIS_THEME_RULE_RESET, $reset_time);
                    RulesEngineCacheWrapper::set_session_attribute(BIS_LOAD_RULE_THEME, null);
                    $load_themes = null;
                }
            } else {
                // Check if excluded category exists in session.
                if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_LOAD_RULE_THEME)) {
                    $load_themes = RulesEngineCacheWrapper::get_session_attribute(BIS_LOAD_RULE_THEME);
                }
            }
        } else {
            // Check if excluded widgets exists in session.
            if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_LOAD_RULE_THEME)) {
                $load_themes = RulesEngineCacheWrapper::get_session_attribute(BIS_LOAD_RULE_THEME);
            }
        }

        return $load_themes;
    }

    /**
     * This method is used to get the category rules from session.
     *
     * @return $exclude_categories
     */
    public function get_posts_rules() {

        $exclude_posts = null;

        $session_reset_time = 0;

        if (RulesEngineCacheWrapper::get_reset_time(BIS_POST_RULE_RESET) !== false) {
            $reset_time = RulesEngineCacheWrapper::get_reset_time(BIS_POST_RULE_RESET);

            if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_POST_RULE_RESET)) {
                $session_reset_time = RulesEngineCacheWrapper::get_session_attribute(BIS_POST_RULE_RESET);
            }

            if ($session_reset_time < $reset_time) {
                // Check if excluded posts exists in session.
                if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_EXCLUDE_POSTS)) {

                    RulesEngineCacheWrapper::set_session_attribute(BIS_EXCLUDE_POSTS, null);
                    RulesEngineCacheWrapper::set_session_attribute(BIS_APPEND_TO_POSTS, null);
                    $exclude_posts = RulesEngineCacheWrapper::get_session_attribute(BIS_EXCLUDE_POSTS);
                    RulesEngineCacheWrapper::set_session_attribute(BIS_POST_RULE_RESET, $reset_time);
                }
            } else {
                // Check if excluded posts exists in session.
                if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_EXCLUDE_POSTS)) {
                    $exclude_posts = RulesEngineCacheWrapper::get_session_attribute(BIS_EXCLUDE_POSTS);
                }
            }
        } else {
            // Check if excluded pages exists in session.
            if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_EXCLUDE_POSTS)) {
                $exclude_posts = RulesEngineCacheWrapper::get_session_attribute(BIS_EXCLUDE_POSTS);
            }
        }

        return $exclude_posts;
    }

    /**
     * This method is used to return the request rules like Post, Page and Request URL.
     * @return array|mixed|null
     */
    public function get_request_rules() {
        $request_rules = RulesEngineCacheWrapper::get_value(BIS_REQUEST_RULES . session_id());

        if ($request_rules === false) {

            $request_rules = null;

            if (RulesEngineCacheWrapper::is_session_attribute_set(BIS_REQUEST_RULES_ARRAY)) {
                $request_rules = RulesEngineCacheWrapper::get_session_attribute(BIS_REQUEST_RULES_ARRAY);
            }

            if ($request_rules == null) {
                $request_rules = array();
                RulesEngineCacheWrapper::set_session_attribute(BIS_REQUEST_RULES_ARRAY, $request_rules);
            }

            RulesEngineUtil::set_request_rules($request_rules);
        }

        return $request_rules;
    }

    /**
     * This method is used to get the current post Id if available or return false if not available.
     *
     * @return bool
     */
    public static function get_the_ID() {
        $id = null;

        if (get_post()) {
            return get_the_ID();
        }

        return false;
    }

    /**
     * This method is used to apply the redirect rules.
     *
     * @param $applied_rules
     */
    public function apply_request_redirect_rules($applied_rules, $isAjaxRequest=false) {

        $rule_count = count($applied_rules);

        if ($rule_count > 0) {

            $page_rules_modal = new PageRulesEngineModel();
            $row = $page_rules_modal->get_redirect_applied_rule_details($applied_rules);
        
            if (RulesEngineUtil::is_redirect() && $row != null) {
                $this->call_hook($row);
                $redirect_rule = $row[0];
                $redirect_val = json_decode($redirect_rule->action);
                AnalyticsEngineModel::audit_redirect_request(RulesEngineUtil::get_audit_report_data_id());
                $red_cookie = null;
                $applied_rd_rule = BaseRulesEngine::get_applied_logical_rule($applied_rules, $redirect_rule);
              

                if(isset($applied_rd_rule->condId)) {
                    $redirect_rule->condId = $applied_rd_rule->condId;
                    // Condition for pattern match.
                    if($applied_rd_rule->condId == 13) {
                        if(RulesEngineUtil::isContains($redirect_val->target_url, "/**")) {
                            $redirect_val->target_url = str_replace("/**", 
                                $applied_rd_rule->patternRedirect, $redirect_val->target_url);
                            
                            if (RulesEngineUtil::isContains($redirect_val->target_url, "wc-ajax=get_refreshed_fragments")) {
                                $redirect_val->target_url = str_replace("?wc-ajax=get_refreshed_fragments", "", $redirect_val->target_url);
                            }
                            
                            if(RulesEngineUtil::isContains($redirect_val->target_url, "?")) {
                                $redirect_val->target_url = $redirect_val->target_url . '&bis_prd=1';
                            } else {
                                $redirect_val->target_url = $redirect_val->target_url .'?bis_prd=1';
                            }
                            
                            if (($redirect_rule->showpopup !== "1" || $red_cookie === BIS_REDIRECT_COOKIE_REDIRECT)
                                &&!$isAjaxRequest) {
                                   wp_redirect($redirect_val->target_url, $redirect_val->redirect_type);
                                exit;
                            } else if($redirect_rule->showpopup === "1") {
                                if(!$isAjaxRequest) {
                                   $redirect_rule->patternaction = json_encode($redirect_val);
                                }
                                RulesEngineCacheWrapper::set_session_attribute(BIS_REDIRECT_POPUP_VO, $redirect_rule);
                                
                            }
                        }
                    }
                }
                
                if (isset($_COOKIE[BIS_REDIRECT_COOKIE])) {
                    $red_cookie = $_COOKIE[BIS_REDIRECT_COOKIE];
                }
                 
                if (!isset($applied_rd_rule->condId) || $applied_rd_rule->condId !== 13) {
                    RulesEngineCacheWrapper::set_session_attribute(BIS_REDIRECT_POPUP_VO, $redirect_rule);
                }
               
                if (($redirect_rule->showpopup !== "1" || 
                    $red_cookie === BIS_REDIRECT_COOKIE_REDIRECT)
                    && !$isAjaxRequest && $applied_rd_rule != null) {
                      
                    if(isset($applied_rd_rule->condId) && $applied_rd_rule->condId !== 13) {
                       wp_redirect($redirect_val->target_url, $redirect_val->redirect_type);
                    } else {
                       wp_redirect($redirect_val->target_url, $redirect_val->redirect_type);
                    }
                    exit; // Exit from page after redirect
                }
               
            }
        }
    }
    
    public static function get_applied_logical_rule($applied_rules, $redirect_rule) {
        $red_applied_log_rule = null;
        
        foreach ($applied_rules as $applied_rule) {
            if($applied_rule->ruleId === $redirect_rule->lrId) {
                $red_applied_log_rule = $applied_rule;
                break;
            }
        }
        return $red_applied_log_rule;
    }

    /**
     * This method is used to call the defined hook using logical rule.
     *
     * @param $logical_rule
     * @throws RuntimeException
     */
    public function call_hook($logical_rule) {
        if (isset($logical_rule->action_hook)) {
            $hookName = $logical_rule->action_hook;
            if ($hookName == null || $hookName === "") {
                return;
            }

            $strHookName = "'" . $hookName . "'";

            add_action($strHookName, $hookName);

            // Check if hook exists
            if (has_action($strHookName)) {
                do_action($strHookName);
            } else { // If hook not found throw exception
                $message = "Hook with name " . $strHookName . " not found ";
                throw new RuntimeException($message);
            }
        }
    }
    
    /**
     * 
     * This method is used to return the site URL.
     * 
     * @return type site url
     */
    public function get_site_url() {
        
        $site_url = get_site_url();

        if (is_ssl()) {
            $scheme = "https";
            $site_url = get_site_url(null, null, $scheme);
        }
        
        return $site_url;
    }
    
}