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

use bis\repf\vo\SearchVO;
use bis\repf\vo\LogicalRulesVO;
use bis\repf\common\RulesEngineCacheWrapper;

/**
 * This class is a Model for Logical Rules.
 *
 */
class LogicalRulesEngineModel extends BaseRulesEngineModel {

    /**
     * Empty constructor.
     */
    public function __construct() {
        
    }

    /**
     * This method return the unassigned and active logical rules.
     *
     * @return list of logical rules
     */
    public function get_unassigned_logical_rulenames() {

        global $wpdb;
        $blog_id = get_current_blog_id();

        $query = $wpdb->prepare("SELECT id, name, description FROM bis_re_logical_rules brlr WHERE brlr.id
        		NOT IN (SELECT logical_rule_id FROM bis_re_rule_details 
                        WHERE rule_type_id = 2) and site_id = %d ", $blog_id);

        $rows = $wpdb->get_results($query);

        return $rows;
    }

    /**
     * This method is used to get all the eligible logical rules.
     * @param $rule_detail_id
     * @return mixed
     */
    public function get_all_eligible_logical_rules($rule_detail_id) {
        global $wpdb;
        $blog_id = get_current_blog_id();
        $query = $wpdb->prepare("SELECT id, name, description FROM bis_re_logical_rules brlr WHERE brlr.id
				NOT IN (SELECT logical_rule_id FROM bis_re_rule_details brrd WHERE brrd.rule_type_id = 2
				and brrd.id != %d ) and site_id = %d ", $rule_detail_id, $blog_id);

        $rows = $wpdb->get_results($query);

        return $rows;
    }

    /**
     * Delete logical rule using ruleId.
     *
     * @param $logical_ruleId
     * @internal param \unknown $ruleId .
     * @return array
     */
    public function delete_logical_rule($logical_ruleId) {

        global $wpdb;
        $status_array = array();

        $table = "bis_re_logical_rules";

        $pquery = $wpdb->prepare("SELECT COUNT(id) as child_count FROM  bis_re_rule_details WHERE logical_rule_id = %d", $logical_ruleId);
        $row = $wpdb->get_row($pquery);

        $childs_count = (int) $row->child_count;

        // Delete rule only if no child rules exists
        if ($childs_count == 0) {
            $data = array();
            $data["id"] = $logical_ruleId;
            $wpdb->delete($table, $data, array("%d"));
            $status_array["status"] = "success";
        } else {
            $status_array["status"] = "childs_rules_exists";
        }

        $status_array["data"] = $this->get_logical_rules();


        return $status_array;
    }

    /**
     * This method is used to get all the logical rules.
     * @return array
     */
    public function get_logical_rules() {
        global $wpdb;

        $blog_id = get_current_blog_id();

        $pquery = $wpdb->prepare("SELECT lr.id AS rcId, lr.name, lr.description, "
                . "lr.action_hook, lr.status FROM bis_re_logical_rules lr WHERE lr.site_id = %d ORDER BY lr.name ", $blog_id);

        $rows = $wpdb->get_results($pquery);

        if (count($rows) > 0) {
            $results_map = array("status" => "success", "data" => $rows);
        } else {
            $results_map = array("status" => "no_data", "data" => BIS_MESSAGE_NO_RECORD_FOUND);
        }

        return $results_map;
    }

    /**
     * This method used for search rules.
     *
     * @param SearchVO $searchVO
     * @return array of rules
     */
    public function search_logical_rules(SearchVO $searchVO) {
        global $wpdb;

        $search_value = $searchVO->get_search_value();
        $search_column = "";
        $blog_id = get_current_blog_id();

        if ($search_value != "" && $search_value != "*") {
            if ($searchVO->get_search_by() === "description") {
                $search_column = "lr.site_id = %d AND lr.description LIKE %s ";
            } else {
                $search_column = "lr.site_id = %d AND lr.name LIKE %s ";
            }
        }


        if ($searchVO->get_status() != "all") {

            if ($search_column != "") {
                $search_column = " AND " . $search_column;
            }

            $query = "SELECT lr.id AS rcId, lr.name, lr.description, lr.action_hook, lr.status
                  FROM bis_re_logical_rules lr  WHERE lr.status = %d
                   " . $search_column . " ORDER BY lr.name";

            $rows = $wpdb->get_results($wpdb->prepare($query, $searchVO->get_status(), $blog_id, '%' . $wpdb->esc_like($search_value) . '%'));
        } else {

            if ($search_column != "") {
                $search_column = " WHERE " . $search_column;
            }

            $query = "SELECT lr.id AS rcId, lr.name, lr.description, lr.action_hook, lr.status
                  FROM bis_re_logical_rules lr  " . $search_column . " ORDER BY lr.name";

            $rows = $wpdb->get_results($wpdb->prepare($query, $blog_id, '%' . $wpdb->esc_like($search_value) . '%'));
        }

        if (count($rows) > 0) {
            $results_map = array(BIS_STATUS => "success", BIS_DATA => $rows);
        } else {
            $results_map = array(BIS_STATUS => "no_data", BIS_DATA => BIS_MESSAGE_NO_RECORD_FOUND);
        }

        return $results_map;
    }

    /**
     *
     * Delete the list of rules.
     *
     * @param unknown $rules .
     * @return array
     */
    public function delete_logical_rules($rules) {
        global $wpdb;
        $table = "bis_re_logical_rules";
        $table_criteria = "bis_re_logical_rules_criteria";
        $data = array();
        $c_rows = 0;

        $wpdb->query(BIS_DB_START_TRANSACTION);

        foreach ($rules as $key => $ruleId) {

            $data["id"] = $key;
            $c_data = array();
            $c_data["logical_rule_id"] = $key;
            $rows = $wpdb->delete($table, $data, array("%d"));

            if ($rows > 0) {
                $c_rows = $wpdb->delete($table_criteria, $c_data, array("%d"));
            }

            if ($c_rows < 0) {
                break;
            }
        }

        if ($c_rows > 0) {
            $wpdb->query(BIS_DB_COMMIT);
        } else {
            $wpdb->query(BIS_DB_ROLLBACK);
        }

        return $this->get_logical_rules();
    }

    public function get_rules_options() {
        global $wpdb;

        $rows = $wpdb->get_results("SELECT id, name FROM bis_re_option order by name");

        return $rows;
    }

    /**
     * This method is used to get all suboptions.
     * @param $optionId
     * @return mixed
     */
    public function get_rules_sub_options($optionId) {
        global $wpdb;

        $rows = $wpdb->get_results($wpdb->prepare("SELECT id, name FROM bis_re_sub_option
				where option_id = %d order by name", $optionId));

        return $rows;
    }

    /**
     * This method is used to get all conditions.
     *
     * @param $optionId
     * @return array
     */
    public function get_rules_conditions($optionId) {
        global $wpdb;

        $pquery = $wpdb->prepare("SELECT id, name FROM bis_re_condition WHERE id IN
					(SELECT condition_id FROM bis_re_sub_option_condition WHERE sub_option_id = %d)", $optionId);

        $rows = $wpdb->get_results($pquery);

        $row = $wpdb->get_row($wpdb->prepare("SELECT value_type_id as valueTypeId FROM bis_re_sub_option WHERE id = %d", $optionId));

        $rules = array("RuleConditions" => $rows, "ValueTypeId" => $row->valueTypeId);

        return $rules;
    }

    /**
     * This method is used to get the criteria list.
     * @param $logical_rule_id
     * @return int
     */
    public function get_rule_criterias_by_ruleId($logical_rule_id) {

        global $wpdb;
        $row_count = $wpdb->get_row($wpdb->prepare("SELECT COUNT(id) AS count FROM bis_re_logical_rules_criteria
				WHERE logical_rule_id = %d", $logical_rule_id));

        return (int) $row_count->count;
    }

    /**
     * This method is used to save the logical rule.
     *
     * @param LogicalRulesVO $logical_rules_vo
     * @return unknown
     */
    public function save_rule(LogicalRulesVO $logical_rules_vo) {
        global $wpdb;
        $results_map = array();
        $table = "bis_re_logical_rules";
        $blog_id = get_current_blog_id();
        $data = array('name' => $logical_rules_vo->get_name(), 'description' => $logical_rules_vo->get_description(),
            'action_hook' => $logical_rules_vo->get_actionHook(), 'eval_type' => $logical_rules_vo->getRuleEvalType(),
            'site_id' => $blog_id, 'add_rule_type' => $logical_rules_vo->getAddRuleType());

        // Start transaction
        $wpdb->query(BIS_DB_START_TRANSACTION);

        $status = $wpdb->insert($table, $data, array("%s", "%s", "%s", "%d", "%d", "%d"));

        if ($status > 0) {

            $rule_id = $wpdb->insert_id;
            $logical_rules_vo->set_id($rule_id);

            $table = "bis_re_logical_rules_criteria";

            foreach ($logical_rules_vo->get_ruleCriteriaArray() as $rules_criteria_vo) {

                $data = array('option_id' => $rules_criteria_vo->get_optionId(), 'sub_option_id' => $rules_criteria_vo->get_subOptionId(),
                    'condition_id' => $rules_criteria_vo->get_conditionId(), 'value' => $rules_criteria_vo->get_value(), 'logical_rule_id' => $rule_id,
                    'operator_id' => $rules_criteria_vo->get_operatorId(), 'left_bracket' => $rules_criteria_vo->get_leftBracket(),
                    'right_bracket' => $rules_criteria_vo->get_rightBracket(), 'eval_type' => $rules_criteria_vo->get_evalType());

                $status = $wpdb->insert($table, $data, array("%d", "%d", "%d", "%s", "%d", "%d", "%d", "%d", "%d"));

                // No insert then exit loop
                if ($status != 1) {
                    $status = 0;
                    break;
                }
            }
        }

        if ($status == 0) {
            $results_map[BIS_STATUS] = BIS_ERROR;
            if (RulesEngineUtil::isContains($wpdb->last_error, BIS_DUPLICATE_ENTRY_SQL_MESSAGE)) {
                $results_map[BIS_MESSAGE_KEY] = BIS_DUPLICATE_ENTRY;
            } else {
                $results_map[BIS_MESSAGE_KEY] = BIS_GENERIC_DATABASE_ERROR;
            }
            $wpdb->query(BIS_DB_ROLLBACK);
        } else {
            $wpdb->query(BIS_DB_COMMIT);
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        }

        return $results_map;
    }

    /**
     * This method is used to return the logical rule.
     * 
     * @global type $wpdb
     * @param type $ruleId
     * @return type
     */
    public function get_logical_rule($ruleId) {
        global $wpdb;
        $rule_query = $wpdb->prepare("SELECT lr.id AS rId, lr.name, lr.description, 
                            lr.action_hook, lr.status, lr.eval_type, lr.add_rule_type FROM bis_re_logical_rules lr WHERE id = %d", $ruleId);
        
        
        $rows = $wpdb->get_row($rule_query);
        
        if (count($rows) > 0) {
            $results_map = array(BIS_STATUS => "success", BIS_DATA => $rows);
        } else {
            $results_map = array(BIS_STATUS => "no_data", BIS_DATA => BIS_MESSAGE_NO_RECORD_FOUND);
        }
        
        return $results_map;
    }

    /**
     * This method will return the rule details based on ruleId
     *
     * @param unknown $ruleId
     * @return multitype:unknown
     */
    public function get_rule($ruleId) {
        global $wpdb;

        $rule_query = $wpdb->prepare("SELECT lr.id AS rId, lr.name, lr.description, lr.action_hook, lr.status, lr.eval_type, lr.add_rule_type  
				FROM bis_re_logical_rules lr WHERE id = %d", $ruleId);

        $rule_criteria_query = $wpdb->prepare("SELECT rc.left_bracket lb, rc.right_bracket rb, op.id AS optId, sop.id AS subOptId, sop.value_type_id as valueTypeId, rcon.id AS condId,
				op.name AS criteria, sop.name AS subcriteria, rcon.name AS ruleCondition, rc.id AS rcId, rc.logical_rule_id AS rId,
				rc.value, rc.operator_id as operId FROM bis_re_logical_rules_criteria rc JOIN bis_re_option op ON rc.option_id = op.id 
				JOIN bis_re_sub_option sop ON sop.id = rc.sub_option_id 
				JOIN bis_re_condition rcon ON rcon.id = rc.condition_id AND logical_rule_id = %d", $ruleId);

        $rule = $wpdb->get_row($rule_query);


        $rule_criteria = $wpdb->get_results($rule_criteria_query);

        $rule_details = array("rule" => $rule, "rule_criteria" => $rule_criteria);

        return $rule_details;
    }

    public function get_user_emails() {
        return get_users();
    }

    public function get_userIds() {
        return get_users();
    }

    /**
     * This method is used to get pages.
     *
     * @param $page_ids
     * @return mixed
     */
    public function get_pages_by_ids($page_ids) {

        return $this->get_posts_by_ids($page_ids);
    }

    /**
     * This method is used to get the posts.
     *
     * @param $post_ids
     * @return mixed
     */
    public function get_posts_by_ids($post_ids) {
        global $wpdb;

        $post_id_arr = array();
        foreach ($post_ids as $post_id) {
            array_push($post_id_arr, $post_id->id);
        }

        $post_ids = implode(",", $post_id_arr);
        $table_name = $wpdb->prefix . 'posts';
        $query = "SELECT id, post_title as name FROM " . $table_name . " where id in ($post_ids)";
        $rows = $wpdb->get_results($query);
        return $rows;
    }

    /**
     * This method is used for getting the list of wordpress categories based on id.
     * 
     * @param type $categories_ids
     * @return type
     */
    public function get_wp_categories_by_ids($categories_ids) {
        $taxonomy = 'category';
        return $this->get_categories_by_ids($taxonomy, $categories_ids);
    }

    /**
     * This method is used for getting the list of woocommerce categories based on id.
     * 
     * @param type $categories_ids
     * @return type
     */
    public function get_woocommerce_categories_by_ids($categories_ids) {
        $taxonomy = 'product_cat';
        return $this->get_categories_by_ids($taxonomy, $categories_ids);
    }

    /**
     * This method is used to return the categories json representation based on
     * category ids.
     * 
     * @param type $taxonomy
     * @param type $categories_ids
     * @return type
     */
    public function get_categories_by_ids($taxonomy, $categories_ids) {

        $category_include = "";
        foreach ($categories_ids as $categories_id) {
            $category_include = $category_include . $categories_id->id . ",";
        }

        $args = array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => FALSE,
            'include' => $category_include,
            'taxonomy' => $taxonomy
        );

        $categories = get_categories($args);

        $valArr = array();
        $count = 0;

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $valArr[$count++] = array('id' => $category->term_id, 'name' => $category->name);
            }
        }

        return $valArr;
    }

    /**
     * This method is used to get the all authors.
     *
     * @return mixed
     */
    public function get_authors() {

        return get_users('orderby=display_name&role=author');
    }

    /**
     * This method is used to get contributors.
     *
     * @return mixed
     */
    public function get_contributor() {

        return get_users('orderby=display_name&role=contributor');
    }

    /**
     * This method is used to get the author Ids.
     *
     * @return mixed
     */
    public function get_authorsIds() {

        return get_users('orderby=user_login&role=author');
    }

    /**
     * This method is used to get the contributorIds.
     *
     * @return mixed
     */
    public function get_contributorIds() {

        return get_users('orderby=user_login&role=contributor');
    }

    /**
     *
     * This method is used to update the logical rules.
     *
     * @param LogicalRulesVO $logical_rules_vo
     * @return updated record count
     */
    public function update_rule(LogicalRulesVO $logical_rules_vo) {
        global $wpdb;
        $results_map = array();

        $wpdb->query(BIS_DB_START_TRANSACTION);

        $table = "bis_re_logical_rules_criteria";

        $data = array();
        $data["logical_rule_id"] = $logical_rules_vo->get_id();
        $success = $wpdb->delete($table, $data, array("%d"));

        if (!($success === false)) {

            $table = "bis_re_logical_rules";
            $data = array('name' => $logical_rules_vo->get_name(), 'description' => $logical_rules_vo->get_description(),
                'action_hook' => $logical_rules_vo->get_actionHook(), 'eval_type' => $logical_rules_vo->getRuleEvalType(),
                'add_rule_type' => $logical_rules_vo->getAddRuleType(),'status' => $logical_rules_vo->get_status());

            $where = array('id' => $logical_rules_vo->get_id());

            $success = $wpdb->update($table, $data, $where, array("%s", "%s", "%s", "%d", "%d", "%d"), array("%d"));


            if (!($success === false)) {

                $table = "bis_re_logical_rules_criteria";

                foreach ($logical_rules_vo->get_ruleCriteriaArray() as $rules_criteria_vo) {

                    $data = array('option_id' => $rules_criteria_vo->get_optionId(), 'sub_option_id' => $rules_criteria_vo->get_subOptionId(),
                        'condition_id' => $rules_criteria_vo->get_conditionId(), 'value' => $rules_criteria_vo->get_value(), 'logical_rule_id' => $logical_rules_vo->get_id(),
                        'operator_id' => $rules_criteria_vo->get_operatorId(), 'left_bracket' => $rules_criteria_vo->get_leftBracket(),
                        'right_bracket' => $rules_criteria_vo->get_rightBracket(), 'eval_type' => $rules_criteria_vo->get_evalType());

                    $status = $wpdb->insert($table, $data, array("%d", "%d", "%d", "%s", "%d", "%d", "%d", "%d", "%d"));

                    // No insert then exit loop
                    if ($status != 1) {
                        $success = false;
                        break;
                    }
                }
            }
        }
        if (!($success === false)) {
            $wpdb->query(BIS_DB_COMMIT);
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            if (RulesEngineUtil::isContains($wpdb->last_error, BIS_DUPLICATE_ENTRY_SQL_MESSAGE)) {
                $results_map[BIS_MESSAGE_KEY] = BIS_DUPLICATE_ENTRY;
            } else {
                $results_map[BIS_MESSAGE_KEY] = BIS_GENERIC_DATABASE_ERROR;
            }
            $results_map[BIS_STATUS] = BIS_ERROR;
            $wpdb->query(BIS_DB_ROLLBACK);
        }

        return $results_map;
    }

    /**
     * This method is used to get the active logical rules.
     *
     * @return mixed
     */
    public function get_active_rules() {

        global $wpdb;
        $blog_id = get_current_blog_id();

        $query = ("SELECT id AS ruleId, NAME as name, action_hook, eval_type, "
                . "description FROM bis_re_logical_rules WHERE STATUS = 1 and site_id = " . $blog_id);
        $rows = $wpdb->get_results($query);

        return $rows;
    }

    /**
     * This method is used to get the rule criteria using ruleId.
     * @param $logical_rule_id
     * @return mixed
     */
    public function get_rule_criteria_by_ruleId($logical_rule_id) {

        $key = BIS_RULE_CRITERIA_SESSION_CONST. $logical_rule_id;
        
        // Get the rule criteria from session
        if(RulesEngineCacheWrapper::is_session_attribute_set($key)) {
            return RulesEngineCacheWrapper::get_session_attribute($key);
        }
        
        global $wpdb;

        $pquery = $wpdb->prepare("SELECT rc.id as rcId, rc.operator_id AS operId, rc.left_bracket AS lb, rc.right_bracket AS rb, lr.id AS lrId, op.id AS optId, sop.id AS subOptId, rcon.id AS condId, lr.name, lr.description, lr.action_hook, lr.status,
				op.name AS criteria, sop.name AS subcriteria, rcon.name AS ruleCondition,
				rc.value FROM bis_re_logical_rules_criteria rc JOIN bis_re_option op ON rc.option_id = op.id JOIN bis_re_sub_option sop
				ON sop.id = rc.sub_option_id JOIN bis_re_condition rcon ON rcon.id = rc.condition_id JOIN bis_re_logical_rules lr ON
				lr.id = rc.logical_rule_id AND lr.status = 1 where logical_rule_id = %d", $logical_rule_id);

        $rows = $wpdb->get_results($pquery);

        if ($rows != null && (count($rows) > 0)) {
            foreach ($rows as $row) {
                if ($row->subOptId == 8) {
                    $logical_rule_value = $this->get_logical_rule_value($row->value);
                    $row->value = $logical_rule_value;
                }
            }
        }
        
        // set the criteria values to session
        RulesEngineCacheWrapper::set_session_attribute($key, $rows);
        
        return $rows;
    }

    /**
     *
     * Get the rule values based on the sub option Id.
     *
     * @param $id
     * @internal param \unknown $suboption_id
     * @return unknown
     */
    public function get_logical_rule_value($id) {
        global $wpdb;

        $pquery = $wpdb->prepare("SELECT value as rule_value FROM bis_re_logical_rule_value WHERE id = %d", $id);
        $row = $wpdb->get_row($pquery);

        return $row->rule_value;
    }

}
