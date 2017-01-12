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
 * Class LogicalRulesVO
 */
class LogicalRulesVO {

    public $id = null;
    public $name = null;
    public $description = null;
    public $actionHook = null;
    public $ruleCriteriaArray = null;
    public $status = null;
    public $ruleEvalType = 1;
    public $addRuleType = 1;

    /**
     * @param null $name
     * @param null $description
     * @param null $actionHook
     * @param null $ruleCriteriaArray
     */
    function __construct($name = null, $description = null, $actionHook = null, 
            $ruleCriteriaArray = null, $ruleEvalType = 1, $bisAddRuleType = 1) {
        $this->name = $name;
        $this->description = $description;
        $this->actionHook = $actionHook;
        $this->ruleCriteriaArray = $ruleCriteriaArray;
        $this->ruleEvalType = $ruleEvalType;
        $this->addRuleType = $bisAddRuleType;
    }

    /**
     * Get the rules evaluation type.
     * 
     * @return type
     */
    public function getAddRuleType() {
        return $this->addRuleType;
    }
    /**
     * Get the rules evaluation type.
     * 
     * @return type
     */
    public function setAddRuleType($addRuleType) {
         $this->addRuleType = $addRuleType;
    }
    /**
     * Get the rules evaluation type.
     * 
     * @return type
     */
    public function getRuleEvalType() {
        return $this->ruleEvalType;
    }

    /**
     * Sets the Rule Evaluation Type.
     * @param type $ruleEvalType
     */
    public function setRuleEvalType($ruleEvalType) {
        $this->ruleEvalType = $ruleEvalType;
    }

    /**
     * @return null
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * @param $status
     */
    public function set_status($status) {
        $this->status = $status;
    }

    /**
     * @return null
     */
    public function get_id() {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function set_id($id) {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function set_name($name) {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * @param $description
     */
    public function set_description($description) {
        $this->description = $description;
    }

    /**
     * @return null
     */
    public function get_actionHook() {
        return $this->actionHook;
    }

    /**
     * @param $actionHook
     */
    public function set_actionHook($actionHook) {
        $this->actionHook = $actionHook;
    }

    /**
     * @return null
     */
    public function get_ruleCriteriaArray() {
        return $this->ruleCriteriaArray;
    }

    /**
     * @param $ruleCriteriaArray
     */
    public function set_ruleCriteriaArray($ruleCriteriaArray) {
        $this->ruleCriteriaArray = $ruleCriteriaArray;
    }

}