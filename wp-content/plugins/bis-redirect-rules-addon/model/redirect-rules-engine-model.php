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

use bis\repf\model\BaseRulesEngineModel;
/**
 * This class is a Model for Redirect Rules.
 *
 */
class RedirectRulesEngineModel extends BaseRulesEngineModel
{

    /**
     * Save Redirect Rules.
     *
     * @param RulesVO $rules_vo
     * @return array
     */
    public function save_redirect_rule(RulesVO $rules_vo)
    {
        return parent::save_child_rule($rules_vo);
    }

    /**
     * Save Page rules.
     *
     * @param RulesVO $rules_vo
     * @return array
     */
    public function update_redirect_rule(RulesVO $rules_vo)
    {
        return parent::update_child_rule($rules_vo);
    }

    /**
     * Delete redirect rule using ruleId.
     *
     * @param  $ruleId .
     * @return \multitype
     */
    public function delete_redirect_rule($ruleId)
    {

        $status = parent::delete_child_rule($ruleId);
        $results_map = $this->get_redirect_rules_list();

        if (!$status) {
            $results_map[BIS_STATUS] = BIS_ERROR;
        }

        return $results_map;
    }

    /**
     * This method is used to get all redirect rules.
     *
     * @return multitype:
     */
    public function get_redirect_rules_list()
    {

        return parent::get_child_rules(BIS_REDIRECT_TYPE_RULE);
    }
    
    /**
     * This method is used to get all redirect rules.
     *
     * @return multitype:
     */
    public function is_redirect_rule_exists($rule_name, $rdetail_id = 0) {

        return parent::is_child_rule_exists($rule_name, $rdetail_id, BIS_REDIRECT_TYPE_RULE);
    }

    /**
     * This method is used to get the Redirect Rule based on ruleId.
     * @param unknown $ruleId
     * @return rule details
     */
    public function get_redirect_rule_details($ruleId)
    {

        global $wpdb;

        $rd_query = "SELECT id AS rule_details_id, NAME AS rule_name, ACTION AS redirect_value
				FROM bis_re_rule_details WHERE  STATUS = 1 and rule_type_id = 2 AND logical_rule_id = %d order by id desc";


        $row = $wpdb->get_row($wpdb->prepare($rd_query, $ruleId));

        return $row;
    }
    
    /**
     * This method is used to return the applied rule based on the rule details Id.
     *
     * @param String $rule_detail_id
     * @return Array
     */
    public function get_redirect_applied_rule($rule_detail_id)
    {
        global $wpdb;

        $rd_query = "SELECT  rrd.id AS rule_detail_id, rrd.action_hook AS hook_name, rrd.name AS redirect_rule_name,
				rrd.description, rrd.status, rrd.action, rlr.id AS rule_id, rlr.name AS rule_name,
                                rrd.general_col1 as showpopup, rrd.general_col2 as popupvo FROM bis_re_rule_details rrd 
				JOIN bis_re_logical_rules rlr ON rlr.id = rrd.logical_rule_id WHERE rrd.id = " . $rule_detail_id.
                " order by rrd.id";

        $row = $wpdb->get_row($rd_query);

        return $row;
    }


    /**
     * This method is used get the applied redirect rules.
     *
     * @param $logical_rules
     * @return array|
     */
    public function get_redirect_applied_rule_details($logical_rules)
    {

        return parent::get_applied_rules($logical_rules, BIS_REDIRECT_TYPE_RULE);

    }


}
