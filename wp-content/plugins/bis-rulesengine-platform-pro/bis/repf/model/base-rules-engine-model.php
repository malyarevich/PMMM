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

namespace bis\repf\model;

use bis\repf\common\RulesEngineCacheWrapper;
use bis\repf\common\RulesEngineLocalization; 
use bis\repf\vo\SearchVO;
use bis\repf\vo\RulesVO;
use bis\repf\vo\LogicalRulesVO;
use bis\repf\vo\LabelValueVO;
use bis\repf\vo\ImageVO;
use RulesEngineUtil;

/**
 * This class is a base model for all models.
 *
 */
abstract class BaseRulesEngineModel {

    public function get_woo_attribute_taxonomies($attr_label) {
        global $wpdb;
   
        $post_sql = "SELECT attribute_id AS id, attribute_label AS name from " . $wpdb->prefix . "woocommerce_attribute_taxonomies where attribute_label like %s ";
        
        $rows = $wpdb->get_results($wpdb->prepare($post_sql, '%' . $wpdb->esc_like($attr_label) . '%'));
  
        if (count($rows) > 0) {
            $results_map[BIS_STATUS] = BIS_SUCCESS;
            $results_map[BIS_DATA] = $rows;
        } else {
            $results_map[BIS_STATUS] = BIS_NO_RECORDS_FOUND;
        }

        return $results_map;
    }

    /**
     * This method is used to get all rules based on the rule type id.
     *
     * @param $rule_type_id
     * @return multitype:
     */
    public function get_child_rules($rule_type_id) {

        // Where null = SearchVO
        $results_map = $this->search_child_rules($rule_type_id);
        return $results_map;
    }
    
     /**
     * This method is used to check if child rules exists with the name.
     *
     * @param $rule_name,
     * @param $rule_type_id,
     * @return multitype
     */
    public function is_child_rule_exists($rule_name, $rdetail_id = 0, $rule_type_id) {

        global $wpdb;

        $results_map = array();
        $rd_query = "SELECT NAME AS rule_name FROM bis_re_rule_details WHERE name = %s "
                . "and id != %d and rule_type_id =  %d";

        $rows = $wpdb->get_results($wpdb->prepare($rd_query, $rule_name, $rdetail_id, $rule_type_id));
        
        if(count($rows) > 0) {
            $results_map[BIS_MESSAGE_KEY] = BIS_DUPLICATE_ENTRY;
            $results_map[BIS_STATUS] = BIS_ERROR;
        } else {
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        }
        
        return $results_map;
    }

    /**
     * This method is used for searching child rules.
     *
     * @param $rule_type_id
     * @param SearchVO $search
     * @return array
     */
    public function search_child_rules($rule_type_id, SearchVO $search = null) {
        global $wpdb;

        $results_map = array();
        $blog_id = get_current_blog_id();
        
        if ($search == null || $search->get_search_value() === "*") {
            $rd_query = "SELECT  rrd.id as rule_detail_id, rrd.action_hook, rrd.name as child_rule_name, rrd.description, rrd.status,
				rrd.child_sub_rule, rrd.action, rrd.parent_type_value, rlr.name AS lrule_name, rlr.status as lrule_status,
				rlr.description  as lrule_description, rrd.general_col1 as gencol1, rrd.general_col2 as location  
                                FROM bis_re_rule_details rrd JOIN bis_re_logical_rules rlr
				ON rlr.id = rrd.logical_rule_id WHERE rrd.rule_type_id = %d and rlr.site_id = %d order by  rrd.name";

            $pre_query = $wpdb->prepare($rd_query, $rule_type_id, $blog_id);
        } else { // Is search operation
            $search_value = $search->get_search_value();

            $search_description = "";
            $search_name = "";

            if ($search->get_search_by() === "description") {
                $search_description = $search_value;
            } else {
                $search_name = $search_value;
            }

            $rule_status = $search->get_status();

            // All condition
            if (RulesEngineUtil::isEqual($rule_status, "all")) {

                $rd_query = "SELECT  rrd.id as rule_detail_id, rrd.action_hook, rrd.name as child_rule_name, rrd.description, rrd.status,
				rrd.action, rrd.parent_type_value, rlr.name AS lrule_name, rlr.status as lrule_status, rlr.description as lrule_description,
				rrd.child_sub_rule, rrd.general_col1 as gencol1, rrd.general_col2 as location FROM bis_re_rule_details rrd JOIN bis_re_logical_rules rlr
				ON rlr.id = rrd.logical_rule_id WHERE rrd.rule_type_id = %d AND rlr.site_id = %d AND rrd.name LIKE %s AND
				rrd.description LIKE %s order by  rrd.name";

                $pre_query = $wpdb->prepare($rd_query, $rule_type_id, $blog_id, 
                        '%' . $wpdb->esc_like($search_name) . '%', '%' . $wpdb->esc_like($search_description) . '%');
            } else { // Active = 1, InActive = 0;
                $rd_query = "SELECT  rrd.id as rule_detail_id, rrd.action_hook, rrd.name as child_rule_name, rrd.description, rrd.status,
				rrd.action, rrd.parent_type_value, rlr.name AS lrule_name, rlr.status as lrule_status, rlr.description as lrule_description,
				rrd.child_sub_rule, rrd.general_col1 as gencol1, rrd.general_col2 as location FROM bis_re_rule_details rrd JOIN bis_re_logical_rules rlr
				ON rlr.id = rrd.logical_rule_id WHERE rrd.rule_type_id = %d AND rlr.site_id = %d AND rrd.name LIKE %s AND
				rrd.description LIKE %s and  rrd.status = %d order by  rrd.name";

                $pre_query = $wpdb->prepare($rd_query, $rule_type_id, $blog_id, 
                        '%' . $wpdb->esc_like($search_name) . '%', '%' . $wpdb->esc_like($search_description) . '%', $search->get_status());
            }
        }

        $rows = $wpdb->get_results($pre_query);

        $rules = array();
        $rules_vo = null;

        if ($rows > 0) {

            foreach ($rows as $row) {
                $rules_vo = new RulesVO();
                $logical_rule_vo = new LogicalRulesVO();

                $logical_rule_vo->set_status($row->lrule_status);
                $logical_rule_vo->set_name($row->lrule_name);
                $logical_rule_vo->set_description($row->lrule_description);

                $rules_vo->set_logical_rule($logical_rule_vo);
                $rules_vo->set_name($row->child_rule_name);
                $rules_vo->set_description($row->description);
                $rules_vo->set_action($row->action);

                if ($rule_type_id == BIS_REDIRECT_TYPE_RULE) {
                    $rules_vo->set_action(json_decode($row->action));
                } else {

                    $actions = null;
                    $pos_values = null;
                    $location_attr = $row->location;
                    $location = null;

                    if ($location_attr != null) {
                        $location = json_decode($location_attr);
                    }

                    if ($rule_type_id == BIS_POST_TYPE_RULE) {
                        $actions = BaseRulesEngineModel::get_rule_values(BIS_POST_ACTION_ID);
                        $pos_values = BaseRulesEngineModel::get_rule_values(BIS_POST_CONTENT_POSITION);
                        if ((isset($location->content_position) && RulesEngineUtil::isContains($location->content_position, "pos_cust_scode")) 
                                || RulesEngineUtil::isContains($rules_vo->get_action(), "append_existing_scode_post")) {
                            $rules_vo->set_short_code
                                    ("[bis-post-rule-append rulename=\"" . $rules_vo->get_name() . "\"]");
                        }
                    }

                    if ($rule_type_id == BIS_PAGE_TYPE_RULE) {
                        $actions = BaseRulesEngineModel::get_rule_values(BIS_PAGE_ACTION_ID);
                        $pos_values = BaseRulesEngineModel::get_rule_values(BIS_PAGE_CONTENT_POSITION);
                     
                        if ((isset($location->content_position) && RulesEngineUtil::isContains($location->content_position, "pos_cust_scode")) 
                                || RulesEngineUtil::isContains($rules_vo->get_action(), "append_existing_scode_page")) {
                            $rules_vo->set_short_code
                                    ("[bis-page-rule-append rulename=\"" . $rules_vo->get_name() . "\"]");
                        }
                    }

                    if ($actions != null) {

                        foreach ($actions as $action) {
                            if ($row->action === $action->value) {
                                $rules_vo->set_action($action->name);
                            }
                        }
                    }


                    if ($location_attr != null && $pos_values != null) {
                        foreach ($pos_values as $pos_value) {
                            if ($location->content_position === $pos_value->value) {
                                $rules_vo->set_general_col2($pos_value);
                                break;
                            }
                        }
                    }
                }

                $rules_vo->set_status($row->status);
                $rules_vo->set_id($row->rule_detail_id);
                $rules_vo->set_rule_type_id(1);
                $rules_vo->set_action_hook($row->action_hook);
                $rules_vo->set_child_sub_rule($row->child_sub_rule);
                $rule_type_value = null;

                switch ($rule_type_id) {

                    case BIS_PAGE_TYPE_RULE:
                    case BIS_POST_TYPE_RULE:
                        $rule_type_value = $this->get_post_rule_type_value($row->rule_detail_id);
                        break;

                    case BIS_WIDGET_TYPE_RULE:
                        $rule_type_value = $this->get_widget_rule_type_value($row->rule_detail_id, $row->parent_type_value);
                        $rules_vo->set_parent_type_value($this->get_sidebar_name($row->parent_type_value));

                        break;

                    case BIS_THEME_TYPE_RULE:
                        $rule_type_value = $this->get_theme_name_type_value($row->rule_detail_id);
                        break;

                    case BIS_CATEGORY_TYPE_RULE:
                        $rules_vo->set_general_col1($row->gencol1);
                        $rule_type_value = $this->get_category_rule_type_value($row->rule_detail_id);
                        break;
                    
                     case BIS_LANGUAGE_TYPE_RULE:
                        $rule_type_value = $this->get_language_rule_type_value($row->rule_detail_id, $row->gencol1);
                        break;
                }

                $rules_vo->set_rule_type_value($rule_type_value);

                array_push($rules, $rules_vo);
            }
        }

        if (count($rules) > 0) {
            $results_map[BIS_DATA] = $rules;
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;
        }

        return $results_map;
    }

    /**
     * This method used to the list of pages associated for a rule.
     *
     * @param $rule_detail_id
     * @internal param int $page_detail_id
     * @return string
     */
    private function get_post_rule_type_value($rule_detail_id) {
        global $wpdb;

        $posts_table = $wpdb->prefix . "posts";

        $wpp_query = $wpdb->prepare("SELECT wpp.post_title FROM bis_re_rules rr  JOIN $posts_table wpp ON wpp.id = rr.parent_id
				WHERE rule_details_id = %d", $rule_detail_id);

        $post_rows = $wpdb->get_results($wpp_query);

        $posts = "";

        if ($post_rows != null && count($post_rows) > 0) {
            $r_count = count($post_rows);
            foreach ($post_rows as $index => $post_row) {
                $posts = $posts . $post_row->post_title;

                // Do not add comma for last record
                if ($index !== ($r_count - 1)) {
                    $posts = $posts . ", ";
                }
            }
        }

        return $posts;
    }

    /**
     * This method used to the list of pages associated for a rule.
     *
     * @param $rule_detail_id
     * @param $sidebar_id
     * @internal param int $page_detail_id
     * @return string
     */
    private function get_widget_rule_type_value($rule_detail_id, $sidebar_id) {
        global $wpdb;

        $wpp_query = $wpdb->prepare("SELECT parent_id AS widget_id FROM bis_re_rules WHERE rule_details_id = %d", $rule_detail_id);
        $widget_rows = $wpdb->get_results($wpp_query);

        $widgets = "";

        if ($widget_rows != null && count($widget_rows) > 0) {
            $r_count = count($widget_rows);

            foreach ($widget_rows as $index => $widget_row) {
                $widgets = $widgets . $this->get_widget_name($widget_row->widget_id, $sidebar_id);

                // Do not add comma for last record
                if ($index !== ($r_count - 1)) {
                    $widgets = $widgets . ", ";
                }
            }
        }

        return $widgets;
    }

    /**
     * This method is used to get the widget name.
     * @param $widget_id
     * @param $sidebar_id
     * @return null
     */
    public function get_widget_name($widget_id, $sidebar_id) {

        $side_bars = RulesEngineUtil::get_option("sidebars_widgets");
        $s_widgets = $side_bars[$sidebar_id];
        $registered_widgets = $GLOBALS["wp_registered_widgets"];
        $widget_name = null;

        if ($s_widgets != null) {
            foreach ($s_widgets as $widget) {
                $r_widget = $registered_widgets[$widget];
                if ($widget_id == $r_widget["id"]) {
                    $widget_name = $r_widget["name"];
                    break;
                }
            }
        }

        return $widget_name;
    }

    /**
     * This method is used to get the side bar.
     * @param $side_bar_id
     * @return null
     */
    public function get_sidebar_name($side_bar_id) {

        $sidebar_name = null;

        foreach ($GLOBALS["wp_registered_sidebars"] as $g_sidebar) {
            if ($side_bar_id == $g_sidebar["id"]) {
                $sidebar_name = $g_sidebar["name"];
                break;
            }
        }

        return $sidebar_name;
    }

    /**
     * This method is used to get the them type.
     * @param $rule_detail_id
     * @return string
     */
    private function get_theme_name_type_value($rule_detail_id) {
        global $wpdb;

        $wpp_query = $wpdb->prepare("SELECT parent_id AS theme_details FROM bis_re_rules WHERE rule_details_id = %d", $rule_detail_id);
        $theme_row = $wpdb->get_row($wpp_query);

        $theme_name = "";

        $themes = wp_get_themes();

        foreach ($themes as $theme) {
            $template = ThemeRulesEngine::get_rule_theme_template($theme_row->theme_details);
            if ($theme->template == $template) {
                $theme_name = $theme->name;
                break;
            }
        }

        return $theme_name;
    }
    
    /**
     * This method used to get the selected language.
     *
     * @param int $rule_detail_id
     * @return string
     */
    private function get_language_rule_type_value($rule_detail_id, $lang_file) {
        global $wpdb;

        $wpp_query = $wpdb->prepare("SELECT parent_id AS lang_id FROM bis_re_rules WHERE rule_details_id = %d", $rule_detail_id);
        $language_row = $wpdb->get_row($wpp_query);
        
        if($language_row->lang_id === "bis_re_custom_language") {
            return __("Custom File:", BIS_RULES_ENGINE_TEXT_DOMAIN)." ".$lang_file;
        }
        
        $selected_language = null;
        
        $languages = $this->get_rule_values(BIS_LANGUAGE_SUBOPTION_ID);
        
        foreach ($languages as $lang) {
            
            if($lang->id === $language_row->lang_id) {
                 $selected_language  = $lang->name;
                 break;
            } 
        }
        
        return $selected_language;
    }

    /**
     * This method used to the list of category associated for a rule.
     *
     * @param int $rule_detail_id
     * @return string
     */
    private function get_category_rule_type_value($rule_detail_id) {
        global $wpdb;

        $cat_table = $wpdb->prefix . "terms";

        $precat_query = $wpdb->prepare("SELECT cat.name FROM bis_re_rules rr  JOIN $cat_table cat ON cat.term_id = rr.parent_id
				WHERE rule_details_id = %d", $rule_detail_id);

        $cat_rows = $wpdb->get_results($precat_query);

        $categories = "";

        if ($cat_rows != null && count($cat_rows) > 0) {
            $r_count = count($cat_rows);
            foreach ($cat_rows as $index => $cat_row) {
                $categories = $categories . $cat_row->name;

                // Do not add comma for last record
                if ($index !== ($r_count - 1)) {
                    $categories = $categories . ", ";
                }
            }
        }

        return $categories;
    }

    /**
     * Delete the rules based on ruleId.
     *
     * @param $ruleId
     * @return bool
     */
    public function delete_child_rule($ruleId) {

        global $wpdb;

        $tab_rules_details = "bis_re_rule_details";
        $status = false;

        $data = array();
        $data["id"] = $ruleId;


        $rows = $wpdb->delete($tab_rules_details, $data, "%d");

        if ($rows > 0) {
            $status = true;
        }

        return $status;
    }

    /**
     * Generic method to saves child rules (i.e Page Rule, Post Rule, Widget Rule etc)
     *
     * @param RulesVO $rules_vo
     * @return array
     */
    public function save_child_rule(RulesVO $rules_vo) {

        global $wpdb;
        $results_map = array();

        $table = "bis_re_rule_details";
        $data = array('name' => $rules_vo->get_name(), 'description' => $rules_vo->get_description(), 'action' => $rules_vo->get_action(),
            'status' => $rules_vo->get_status(), 'logical_rule_id' => $rules_vo->get_logical_rule_id(),
            'rule_type_id' => $rules_vo->get_rule_type_id(), 'parent_type_value' => $rules_vo->get_parent_type_value(),
            'child_sub_rule' => $rules_vo->get_child_sub_rule(),
            'general_col1' => $rules_vo->get_general_col1(), 'general_col2' => $rules_vo->get_general_col2(),
            'general_col3' => $rules_vo->get_general_col3(), 'general_col4' => $rules_vo->get_general_col4(),
            'general_col5' => $rules_vo->get_general_col5());

        $wpdb->query(BIS_DB_START_TRANSACTION);

        $status = $wpdb->insert($table, $data, array("%s", "%s", "%s", "%d", "%d", "%d", "%d", "%d", "%s", "%s", "%s", "%s", "%s"));

        $rule_detail_id = $wpdb->insert_id;
        
        // No sub rule conditions exists for Redirect rule
        if ($status > 0 && $rules_vo->get_rule_type_id() != BIS_REDIRECT_TYPE_RULE) {
            $rule_ids = $rules_vo->get_rule_type_value();
            $status = $this->save_parents($rule_ids, $rule_detail_id, $rules_vo->get_rule_type_id());
        }

        if ($status == 0) {
            // Checking for duplicate rule
            if (RulesEngineUtil::isContains($wpdb->last_error, BIS_DUPLICATE_ENTRY_SQL_MESSAGE)) {
                $results_map[BIS_MESSAGE_KEY] = BIS_DUPLICATE_ENTRY;
            } else {
                $results_map[BIS_MESSAGE_KEY] = BIS_GENERIC_DATABASE_ERROR;
            }

            $wpdb->query(BIS_DB_ROLLBACK);
            $results_map[BIS_STATUS] = BIS_ERROR;
        } else {
            $wpdb->query(BIS_DB_COMMIT);
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        }

        return $results_map;
    }

    /**
     * This method is used to save parent rule.
     * @param $rule_ids
     * @param $rule_detail_id
     * @param $parent_type_id
     * @return int
     */
    private function save_parents($rule_ids, $rule_detail_id, $parent_type_id) {
        global $wpdb;

        $rules_table = "bis_re_rules";
        $status = 0;
        if ($rule_ids != null) {
            foreach ($rule_ids as $rule_id) {
                $data = array('parent_id' => $rule_id, 'rule_details_id' => $rule_detail_id,
                    'parent_type_id' => $parent_type_id);
                $status = $wpdb->insert($rules_table, $data, array("%s", "%d", "%d"));

                // No insert then exit loop
                if ($status != 1) {
                    $status = 0;
                    break;
                }
            }
        }

        return $status;
    }

    /**
     * Update rules.
     *
     * @param RulesVO $rules_vo
     * @return array
     */
    public function update_child_rule(RulesVO $rules_vo) {

        global $wpdb;
        $table = "bis_re_rule_details";
        $results_map = array();

        $data = array('name' => $rules_vo->get_name(), 'description' => $rules_vo->get_description(), 'action' => $rules_vo->get_action(),
            'status' => $rules_vo->get_status(), 'logical_rule_id' => $rules_vo->get_logical_rule_id(),
            'rule_type_id' => $rules_vo->get_rule_type_id(), 'parent_type_value' => $rules_vo->get_parent_type_value(),
            'general_col1' => $rules_vo->get_general_col1(), 'general_col2' => $rules_vo->get_general_col2(),
            'general_col3' => $rules_vo->get_general_col3(), 'general_col4' => $rules_vo->get_general_col4(),
            'general_col5' => $rules_vo->get_general_col5());

        $rule_detail_id = $rules_vo->get_id();
        $where = array('id' => $rule_detail_id);

        $wpdb->query(BIS_DB_START_TRANSACTION);

        $success = $wpdb->update($table, $data, $where, array("%s", "%s", "%s", "%d", "%d", "%d", "%s"), array("%d"));
        $rule_ids = $rules_vo->get_rule_type_value();

        // No need to store in child table for redirect rules
        if ((!($success === false)) && $rules_vo->get_rule_type_id() != BIS_REDIRECT_TYPE_RULE) {
            $rules_table = "bis_re_rules";

            $wpdb->delete($rules_table, array('rule_details_id' => $rule_detail_id));

            // rule_type_id repesents page
            foreach ($rule_ids as $rule_id) {
                $data = array('parent_id' => $rule_id, 'rule_details_id' => $rule_detail_id);
                $success = $wpdb->insert($rules_table, $data, array("%s", "%d"));

                if (!($success >= 0)) {
                    break;
                }
            }
        }

        if (!($success === false)) {
            $wpdb->query(BIS_DB_COMMIT);
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            // Checking for duplicate rule
            if (RulesEngineUtil::isContains($wpdb->last_error, BIS_DUPLICATE_ENTRY_SQL_MESSAGE)) {
                $results_map[BIS_MESSAGE_KEY] = BIS_DUPLICATE_ENTRY;
            } else {
                $results_map[BIS_MESSAGE_KEY] = BIS_GENERIC_DATABASE_ERROR;
            }

            $wpdb->query(BIS_DB_ROLLBACK);
            $results_map[BIS_STATUS] = BIS_ERROR;
        }

        return $results_map;
    }

    /**
     * This method is used to get all the applied rules.
     * @param $logical_rules
     * @param $rule_type
     * @return array|null
     */
    public function get_applied_rules($logical_rules, $rule_type) {

        // Check whether child rules exists in cache
		
        $rule_type_key = BIS_RULE_TYPE_CONST.$rule_type;
        $rows = null;
        
        if(RulesEngineCacheWrapper::is_session_attribute_set($rule_type_key)) {
            $rows = RulesEngineCacheWrapper::get_session_attribute($rule_type_key);
        } else {
            
            global $wpdb;

            if ($rule_type != BIS_REDIRECT_TYPE_RULE) {
                $rules_query = "SELECT brd.name as crulename, logical_rule_id as lrId, parent_id as parent_id, parent_type_id, brd.parent_type_value, brd.action as action,
                            brd.general_col1 as gencol1, general_col2 as gencol2, general_col3 as gencol3, general_col4 as gencol4,
                            general_col5 as gencol5 FROM bis_re_rules brr JOIN bis_re_rule_details brd ON brd.id = brr.rule_details_id
                            WHERE brd.rule_type_id = %d AND brd.status = 1";
            } else { // Redirect rule query
                $rules_query = "SELECT logical_rule_id as lrId, rrd.id AS rule_detail_id, 
                            rrd.action_hook AS hook_name, rrd.action, rrd.general_col1 as showpopup, rrd.general_col2 as popupvo, 
                            rrd.general_col3 as gencol3, rrd.general_col4 as gencol4, rrd.general_col5 as gencol5 
                            FROM bis_re_rule_details rrd JOIN bis_re_logical_rules rlr ON rlr.id = rrd.logical_rule_id 
                            AND rrd.rule_type_id = %d AND rrd.status = 1";
            }
            $rows = $wpdb->get_results($wpdb->prepare($rules_query, $rule_type));

        }
        
        RulesEngineCacheWrapper::set_session_attribute($rule_type_key, $rows);


        $applied_logical_rules = RulesEngineUtil::get_applied_rules($logical_rules);
        
        $applied_rules = null;
        if ($rows != null && count($rows) > 0) {

            if (!empty($applied_logical_rules)) {

                $applied_rules = array();
                foreach ($rows as $row) {
                    if (in_array($row->lrId, $applied_logical_rules)) {
                        array_push($applied_rules, $row);
                    }
                }
            }
        }

      
        return $applied_rules;
    }

    /**
     * This method is used to get the list of media library images.
     *
     * @return list of images
     */
    public function get_images_from_media_library() {

        $args = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'order' => 'ASC',
            'orderby' => 'title',
            'posts_per_page' => -1
        );

        $images = get_posts($args);

        return $images;
    }

    /**
     * This method is used to get the image type post based on post Id.
     *
     * @param $post_id
     * @param string $size
     * @return list of images
     */
    public function get_image_from_media_library($post_id, $size = 'large') {

        $image_attributes = wp_get_attachment_image_src($post_id, $size);
        $image_vo = new ImageVO();
        $image_vo->set_url($image_attributes[0]);
        $image_vo->set_height($image_attributes[1]);
        $image_vo->set_width($image_attributes[2]);

        return $image_vo;
    }
    
    public function get_countries($include=false, $exclude=false) {
        global $wpdb;
        
        if($include !== false) {
            $query = "SELECT value as id, display_name as name "
                    . "FROM bis_re_logical_rule_value WHERE parent_id = 4 and VALUE IN (" . $include . ") order by display_name;";
        } else if($exclude !== false) {
            $query = "SELECT value as id, display_name as name "
                    . "FROM bis_re_logical_rule_value WHERE parent_id = 4 and VALUE NOT IN (" . $exclude . ") order by display_name;";
        } else {
            $query = $wpdb->prepare("SELECT value as id, display_name as name "
                    . "FROM bis_re_logical_rule_value WHERE parent_id = %d order by display_name;", 4);
        }
       
        $rows = $wpdb->get_results($query);
        return $rows;
    } 

    /**
     *
     * Get the rule values based on the sub option Id.
     *
     * @param $suboption_id
     * @return array
     */
    public function get_rule_values($sub_option_id, $eng_label = true, 
            $include = false, $exclude = false) {
        global $wpdb;

        switch ($sub_option_id) {
            case 7:
                
                if ($include !== false) {
                    $query = "SELECT value as id, display_name as name "
                            . "FROM bis_re_logical_rule_value WHERE parent_id = 7 and VALUE IN (" . $include . ") order by display_name;";
                } else if ($exclude !== false) {
                    $query = "SELECT value as id, display_name as name "
                            . "FROM bis_re_logical_rule_value WHERE parent_id = 7 and VALUE NOT IN (" . $exclude . ") order by display_name;";
                } else {
                    $query = "SELECT value as id, display_name as name "
                            . "FROM bis_re_logical_rule_value WHERE parent_id = 7 order by display_name;";
                }
                
                $rows = RulesEngineLocalization::get_localized_values($wpdb->get_results($query));
                
                break;

            case 15:
                $rows = $this->bis_get_categories();
                break;
            
            case 4: // country
            case 5: // currency            
            case 20: // continent
            case 23: // day of the week
            case 24: // month
                $pquery = $wpdb->prepare("SELECT value as id, display_name as name FROM bis_re_logical_rule_value WHERE parent_id = %d order by display_name", $sub_option_id);
                $rows = RulesEngineLocalization::get_localized_values($wpdb->get_results($pquery));
                break;

            default :
                $pquery = $wpdb->prepare("SELECT id, value, display_name as name FROM bis_re_logical_rule_value WHERE parent_id = %d order by display_name", $sub_option_id);
                $rows = RulesEngineLocalization::get_localized_values($wpdb->get_results($pquery));
                break;
        }
        return $rows;
    }

    /**
     * This method is used to return the list of categories.
     * 
     * @return array
     */
    function bis_get_categories() {
        $valArr = array();
        $args = array(
            'orderby' => 'name',
            'order' => 'ASC'
        );
        $categories = get_categories($args);

        foreach ($categories as $category) {
            $labelValueVO = new LabelValueVO();
            $labelValueVO->set_id($category->cat_ID);
            $labelValueVO->set_label($category->name);
            array_push($valArr, $labelValueVO);
        }

        return $valArr;
    }

    /**
     * 
     * This method is used to return the installed languages.
     * 
     * @return type
     */
    function bis_get_installed_languages($eng_label = TRUE) {

        /** WordPress Translation Install API */
        require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
        $languages = get_available_languages();
        $translations = wp_get_available_translations();
        $valArr = array();
        $count = 0;

        $labelValueVO = new LabelValueVO();
        $labelValueVO->set_id("en_US");
        $labelValueVO->set_label("English (United States)");
        array_push($valArr, $labelValueVO);
        
        foreach ($languages as $lang) {
            
            if($eng_label === 'true' || $eng_label === true) {
                $display_lang = $translations[$lang]["english_name"] . ' - ' . $translations[$lang]["native_name"];
            } else {
                $display_lang = $translations[$lang]["native_name"];
            }
            $labelValueVO = new LabelValueVO();
            $labelValueVO->set_id($lang);
            $labelValueVO->set_label($display_lang);

            array_push($valArr, $labelValueVO);
        }
   
        return $valArr;
    }

    /**
     *
     * Get the rule values based on the sub option Id.
     *
     * @param unknown $suboption_id
     * @param $autoValue
     * @return unknown
     */
    public function get_rule_values_by_display_name($suboption_id, $autoValue) {
        global $wpdb;

        $pquery = $wpdb->prepare("SELECT value as id, display_name as name FROM bis_re_logical_rule_value
					WHERE parent_id = %d and display_name like %s ", $suboption_id, '%' . $wpdb->esc_like($autoValue) . '%');


        $rows = $wpdb->get_results($pquery);

        return $rows;
    }

    /**
     * This method is used to get all the active rules.
     *
     * @param bool $session_rules
     * @return unknown
     */
    public function get_logical_active_rule_names($session_rules = false) {
        global $wpdb;
        $blog_id = get_current_blog_id();
        if ($session_rules) { // Used for Theme rules
            $rows = $wpdb->get_results("SELECT id, name, description FROM bis_re_logical_rules WHERE id IN
                  (SELECT brlr.id FROM bis_re_logical_rules brlr JOIN bis_re_logical_rules_criteria brlrc ON
                    brlrc.logical_rule_id = brlr.id WHERE brlr.status = 1 AND eval_type = 1) order by name");
        } else {
            $rows = $wpdb->get_results("SELECT id, name, description FROM bis_re_logical_rules where status = 1 
			and site_id = " . $blog_id . " order by name");
        }

        return $rows;
    }

    /**
     * This method is used to get all the logical rules.
     *
     * @param bool $session_rules
     * @return unknown
     */
    public function get_logical_rule_names($session_rules = false) {
        global $wpdb;
        $blog_id = get_current_blog_id();

        if ($session_rules) { // Used for Theme rules
            $rows = $wpdb->get_results("SELECT id, name, description FROM bis_re_logical_rules WHERE id IN
                  (SELECT brlr.id FROM bis_re_logical_rules brlr JOIN bis_re_logical_rules_criteria brlrc ON
                    brlrc.logical_rule_id = brlr.id WHERE eval_type = 1) order by name");
        } else {
            $rows = $wpdb->get_results("SELECT id, name, description FROM bis_re_logical_rules "
                    . "where site_id = " . $blog_id . " order by name");
        }

        return $rows;
    }
    
    /**
     * This method is used get the applied redirect rules.
     *
     * @param $logical_rules
     * @return array|
     */
    public function get_redirect_applied_rule_details($logical_rules) {

        return $this->get_applied_rules($logical_rules, BIS_REDIRECT_TYPE_RULE);
    }
    
    public function create_404_audit_report() {
        
    }
    
    public function save_audit_report() {
        
    }

}