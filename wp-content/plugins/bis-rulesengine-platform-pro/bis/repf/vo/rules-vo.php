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

namespace bis\repf\vo;

/**
 * Class RulesVO
 */
class RulesVO
{
    public $id = null; // represents bis_re_rule_details table id 
    public $name = null;
    public $description = null;
    public $ruleTypeId = null;
    public $ruleTypeValue = null; // Array containing the Value exp : List of Page Id
    public $ruleCriteriaArray = null;
    public $logicalRuleId = null;
    public $status = true;
    public $action = null;
    public $ruleName = null;
    public $actionHook = null;
    public $ruleId = null; // Rule Details Id
    public $parentTypeValue = null;
    public $general_col1 = null; // Used for show confirm popup flag
    public $general_col2 = null; // Used for  store popup details in redirect rules.
    public $general_col3 = null;
    public $logical_rule = null;
    public $short_code = null;
    public $general_col4 = null;
    public $general_col5 = null;
    public $reset_rule_key = null;
    public $child_sub_rule = null; // Used for tabs or radio buttons in child rules, Product Rules


    /**
     * @param $logical_rule
     */
    public function set_child_sub_rule($child_sub_rule) {
        $this->child_sub_rule = $child_sub_rule;
    }

    /**
     * @return reset rule key
     */
    public function get_child_sub_rule() {
        return $this->child_sub_rule;
    }
    
    /**
     * @param $logical_rule
     */
    public function set_reset_rule_key($reset_rule_key) {
        $this->reset_rule_key = $reset_rule_key;
    }

    /**
     * @return reset rule key
     */
    public function get_reset_rule_key() {
        return $this->reset_rule_key;
    }

    /**
     * @return null
     */
    public function get_logical_rule()
    {
        return $this->logical_rule;
    }

    /**
     * @param $logical_rule
     */
    public function set_logical_rule($logical_rule)
    {
        $this->logical_rule = $logical_rule;
    }

    /**
     * @return null
     */
    public function get_general_col1()
    {
        return $this->general_col1;
    }

    /**
     * @param $general_col1
     */
    public function set_general_col1($general_col1)
    {
        $this->general_col1 = $general_col1;
    }

    /**
     * @return null
     */
    public function get_general_col2()
    {
        return $this->general_col2;
    }

    /**
     * @param $general_col2
     */
    public function set_general_col2($general_col2)
    {
        $this->general_col2 = $general_col2;
    }

    /**
     * @return null
     */
    public function get_general_col3()
    {
        return $this->general_col3;
    }

    /**
     * @param $general_col3
     */
    public function set_general_col3($general_col3)
    {
        $this->general_col3 = $general_col3;
    }

    /**
     * @return null
     */
    public function get_general_col4()
    {
        return $this->general_col4;
    }

    /**
     * @param $general_col4
     */
    public function set_general_col4($general_col4)
    {
        $this->general_col4 = $general_col4;
    }

    /**
     * @return null
     */
    public function get_general_col5()
    {
        return $this->general_col5;
    }

    /**
     * @param $general_col5
     */
    public function set_general_col5($general_col5)
    {
        $this->general_col5 = $general_col5;
    }

    /**
     * @return null
     */
    public function get_parent_type_value()
    {
        return $this->parentTypeValue;
    }

    /**
     * @param $parentTypeValue
     */
    public function set_parent_type_value($parentTypeValue)
    {
        $this->parentTypeValue = $parentTypeValue;
    }

    /**
     * @return null
     */
    public function get_rule_id()
    {
        return $this->ruleId;
    }

    /**
     * @param $ruleId
     */
    public function set_rule_id($ruleId)
    {
        $this->ruleId = $ruleId;
    }

    /**
     * @return null
     */
    public function get_action_hook()
    {
        return $this->actionHook;
    }

    /**
     * @param $actionHook
     */
    public function set_action_hook($actionHook)
    {
        $this->actionHook = $actionHook;
    }

    /**
     * @return null
     */
    public function get_rule_name()
    {
        return $this->ruleName;
    }

    /**
     * @param $ruleName
     */
    public function set_rule_name($ruleName)
    {
        $this->ruleName = $ruleName;
    }

    /**
     * @return null
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function set_id($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function set_name($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function get_description()
    {
        return $this->description;
    }

    /**
     * @param $description
     */
    public function set_description($description)
    {
        $this->description = $description;
    }

    /**
     * @return null
     */
    public function get_rule_type_id()
    {
        return $this->ruleTypeId;
    }

    /**
     * @param $ruleTypeId
     */
    public function set_rule_type_id($ruleTypeId)
    {
        $this->ruleTypeId = $ruleTypeId;
    }

    /**
     * @return null
     */
    public function get_rule_type_value()
    {
        return $this->ruleTypeValue;
    }

    /**
     * @param $ruleTypeValue
     */
    public function set_rule_type_value($ruleTypeValue)
    {
        $this->ruleTypeValue = $ruleTypeValue;
    }

    /**
     * @return null
     */
    public function get_criteria_array()
    {
        return $this->ruleCriteriaArray;
    }

    /**
     * @return null
     */
    public function get_logical_rule_id()
    {
        return $this->logicalRuleId;
    }

    /**
     * @param $logicalRuleId
     */
    public function set_logical_rule_id($logicalRuleId)
    {
        $this->logicalRuleId = $logicalRuleId;
    }

    /**
     * @return bool
     */
    public function get_status()
    {
        return $this->status;
    }

    /**
     * @param $status
     */
    public function set_status($status)
    {
        $this->status = $status;
    }

    /**
     * @return null
     */
    public function get_action()
    {
        return $this->action;
    }

    /**
     * @param $action
     */
    public function set_action($action)
    {
        $this->action = $action;
    }

    /**
     * @param $ruleCriteriaArray
     */
    public function set_criteria_array($ruleCriteriaArray)
    {
        $this->ruleCriteriaArray = $ruleCriteriaArray;
    }

    /**
     * @param $short_code
     */
    public function set_short_code($short_code)
    {
        $this->short_code = $short_code;
    }

    /**
     * @return null
     */
    public function get_short_code()
    {
        return $this->short_code;
    }
}