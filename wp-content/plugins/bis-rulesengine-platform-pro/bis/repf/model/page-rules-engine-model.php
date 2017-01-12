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
 * This class is a Model for Page Rules.
 *
 */
class PageRulesEngineModel extends BaseRulesEngineModel
{


    /**
     * Save Page rules.
     *
     * @param RulesVO $rules_vo
     * @return array
     */
    public function savePageRule(RulesVO $rules_vo)
    {

        return parent::save_child_rule($rules_vo);

    }

    /**
     * Save Page rules.
     *
     * @param RulesVO $rules_vo
     * @return array
     */
    public function updatePageRule(RulesVO $rules_vo)
    {

        return parent::update_child_rule($rules_vo);
    }

    /**
     * This method is used to get the applied page rules.
     *
     * @param $logical_rules
     * @return string
     */
    function get_applied_page_rules($logical_rules)
    {

        return parent::get_applied_rules($logical_rules, BIS_PAGE_TYPE_RULE);

    }

    /**
     * Delete page rule using ruleId.
     *
     * @param  $ruleId .
     * @return \multitype
     */
    public function delete_page_rule($ruleId)
    {

        $status = parent::delete_child_rule($ruleId);
        $results_map = $this->get_page_rules();

        if (!$status) {
            $results_map[BIS_STATUS] = BIS_ERROR;
        }

        return $results_map;
    }

    /**
     * This method is used to get all page rules.
     *
     * @return multitype:
     */
    public function get_page_rules()
    {
        return parent::get_child_rules(BIS_PAGE_TYPE_RULE);
    }

    /**
     * This method is used to get the Page Rule based on ruleId.
     * @param unknown $ruleId
     * @return NULL
     */
    public function get_page_rule($ruleId)
    {

        global $wpdb;

        $rd_query = $wpdb->prepare("SELECT  rrd.id as page_detail_id, rrd.action_hook, rrd.name as page_rule,
                    rrd.description, rrd.status, rrd.action, rlr.name AS rule_name, rlr.id AS rule_id,
                    rrd.general_col1 as gencol1, rrd.general_col2 as gencol2, rrd.general_col3 as gencol3,
                    rrd.general_col4 as gencol4, rrd.general_col5 as gencol5
                    FROM bis_re_rule_details rrd JOIN bis_re_logical_rules rlr ON rlr.id = rrd.logical_rule_id
                    WHERE rrd.id = %d", $ruleId);

        $posts_table = $wpdb->prefix . "posts";

        $wpp_query = "SELECT wpp.post_title as page_name, wpp.id as page_id FROM bis_re_rules rr  JOIN $posts_table wpp ON wpp.id = rr.parent_id WHERE rule_details_id = %d";

        $row = $wpdb->get_row($rd_query);
        $selected_pages = array();

        $rules_vo = new RulesVO();
        $rules_vo->set_name($row->page_rule);
        $rules_vo->set_description($row->description);
        $rules_vo->set_rule_name($row->rule_name);
        $rules_vo->set_action($row->action);
        $rules_vo->set_status($row->status);
        $rules_vo->set_id($row->page_detail_id);
        $rules_vo->set_rule_type_id(1);
        $rules_vo->set_action_hook($row->action_hook);
        $rules_vo->set_rule_id($row->rule_id);
        $rules_vo->set_general_col1($row->gencol1);
        $rules_vo->set_general_col2($row->gencol2);
        $rules_vo->set_general_col3($row->gencol3);
        $rules_vo->set_general_col4($row->gencol4);
        $rules_vo->set_general_col5($row->gencol5);


        $page_rows = $wpdb->get_results($wpdb->prepare($wpp_query, $row->page_detail_id));

        if(count($page_rows) > 0) {
            foreach ($page_rows as $page_row) {
                $label_value_vo = new LabelValueVO();
                $label_value_vo->set_label($page_row->page_name);
                $label_value_vo->set_value($page_row->page_id);
                array_push($selected_pages, $label_value_vo);
            }
        }

        $rules_vo->set_rule_type_value($selected_pages);

        return $rules_vo;
    }
    
    /**
     * This method is used to get all redirect rules.
     *
     * @return multitype:
     */
    public function is_page_rule_exists($rule_name, $rdetail_id = 0) {

        return parent::is_child_rule_exists($rule_name, $rdetail_id, BIS_PAGE_TYPE_RULE);
    }

}


