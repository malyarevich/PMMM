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

use bis\repf\vo\RulesVO;
use bis\repf\vo\LabelValueVO;


/**
 * This class is a Model for Post Rules.
 *
 */
class PostRulesEngineModel extends BaseRulesEngineModel
{


    /**
     * Save Post rules.
     *
     * @param RulesVO $rules_vo
     * @return array
     */
    public function save_post_rule(RulesVO $rules_vo)
    {

        return parent::save_child_rule($rules_vo);

    }

    /**
     * Save Post rules.
     *
     * @param RulesVO $rules_vo
     * @return array
     */
    public function updatePostRule(RulesVO $rules_vo)
    {

        return parent::update_child_rule($rules_vo);
    }

    /**
     * This method is used to get the applied post rules.
     *
     * @param $logical_rules
     * @internal param \unknown $applied_rules
     * @return string
     */
    function get_applied_post_rules($logical_rules)
    {

        return parent::get_applied_rules($logical_rules, BIS_POST_TYPE_RULE);

    }

    /**
     * Delete post rule using ruleId.
     *
     * @param  $ruleId .
     * @return \multitype
     */
    public function delete_post_rule($ruleId)
    {

        $status = parent::delete_child_rule($ruleId);
        $results_map = $this->get_post_rules();

        if (!$status) {
            $results_map[BIS_STATUS] = BIS_ERROR;
        }

        return $results_map;
    }

    /**
     * This method is used to get all post rules.
     *
     * @return multitype:
     */
    public function get_post_rules()
    {
        return parent::get_child_rules(BIS_POST_TYPE_RULE);
    }

    /**
     * This method is used to get the Post Rule based on ruleId.
     * @param unknown $ruleId
     * @return NULL
     */
    public function get_post_rule($ruleId)
    {

        global $wpdb;

        $rd_query = $wpdb->prepare("SELECT  rrd.id as post_detail_id, rrd.action_hook, rrd.name as post_rule, rrd.description, rrd.status, rrd.action,
				   rlr.name AS rule_name, rlr.id AS rule_id, rrd.general_col1 as pd_title, rrd.general_col2 as pd_body,
                   rrd.general_col3 as gencol3,  rrd.general_col4 as gencol4, rrd.general_col5 as gencol5  FROM bis_re_rule_details rrd
				   JOIN bis_re_logical_rules rlr ON rlr.id = rrd.logical_rule_id WHERE rrd.id = %d", $ruleId);

        $posts_table = $wpdb->prefix . "posts";

        $wpp_query = "SELECT wpp.post_title as post_name, wpp.id as post_id FROM bis_re_rules rr  JOIN $posts_table wpp ON wpp.id = rr.parent_id WHERE rule_details_id = %d";

        $row = $wpdb->get_row($rd_query);

        $rules_vo = null;
        $selected_posts = array();

        $rules_vo = new RulesVO();
        $rules_vo->set_name($row->post_rule);
        $rules_vo->set_description($row->description);
        $rules_vo->set_rule_name($row->rule_name);
        $rules_vo->set_action($row->action);
        $rules_vo->set_status($row->status);
        $rules_vo->set_id($row->post_detail_id);
        $rules_vo->set_rule_type_id(BIS_POST_TYPE_RULE);
        $rules_vo->set_action_hook($row->action_hook);
        $rules_vo->set_rule_id($row->rule_id);
        $rules_vo->set_general_col1($row->pd_title);
        $rules_vo->set_general_col2($row->pd_body);
        $rules_vo->set_general_col3($row->gencol3);
        $rules_vo->set_general_col4($row->gencol4);
        $rules_vo->set_general_col5($row->gencol5);

        $post_rows = $wpdb->get_results($wpdb->prepare($wpp_query, $row->post_detail_id));

        if(count($post_rows) > 0) {
            foreach ($post_rows as $post_row) {
                $label_value_vo = new LabelValueVO();
                $label_value_vo->set_label($post_row->post_name);
                $label_value_vo->set_value($post_row->post_id);
                array_push($selected_posts, $label_value_vo);
            }
        }

        $rules_vo->set_rule_type_value($selected_posts);

        return $rules_vo;
    }

}


