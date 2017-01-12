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
 * Class LogicalRulesCriteriaVO
 */
class LogicalRulesCriteriaVO
{

    private $id = null;
    private $optionId = null;
    private $subOptionId = null;
    private $conditionId = null;
    private $ruleId = null;
    private $value = null;
    private $rule_type = null;
    private $operator_id = 0;
    private $leftBracket = 0;
    private $rightBracket = 0;
    private $eval_type = 1;
    private $deleted = false;


    /**
     * This method return true is criteria is deleted.
     *
     * @return bool
     */
    public function is_deleted() {
        return $this->deleted;
    }

    /**
     * Set the criteria value for deletion.
     *
     * @param $deleted
     */
    public function set_deleted($deleted) {
        $this->deleted = $deleted;
    }

    /**
     * @return int
     */
    public function get_evalType()
    {
        return $this->eval_type;
    }

    /**
     * @param $eval_type
     */
    public function set_evalType($eval_type)
    {
        $this->eval_type = $eval_type;
    }

    /**
     * @return int
     */
    public function get_leftBracket()
    {
        return $this->leftBracket;
    }

    /**
     * @param $leftBracket
     */
    public function set_leftBracket($leftBracket)
    {
        $this->leftBracket = $leftBracket;
    }

    /**
     * @return int
     */
    public function get_rightBracket()
    {
        return $this->rightBracket;
    }

    /**
     * @param $rightBracket
     */
    public function set_rightBracket($rightBracket)
    {
        $this->rightBracket = $rightBracket;
    }

    /**
     * @return int
     */
    public function get_operatorId()
    {
        return $this->operator_id;
    }

    /**
     * @param $optId
     * @return mixed
     */
    public function set_operatorId($optId)
    {
        return $this->operator_id = $optId;
    }

    /**
     * @return null
     */
    public function get_ruleType()
    {
        return $this->rule_type;
    }

    /**
     * @param $rule_type
     */
    public function set_ruleType($rule_type)
    {
        $this->rule_type = $rule_type;
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
    public function set_Id($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function get_optionId()
    {
        return $this->optionId;
    }

    /**
     * @param $optionId
     */
    public function set_optionId($optionId)
    {
        $this->optionId = $optionId;
    }

    /**
     * @return null
     */
    public function get_subOptionId()
    {
        return $this->subOptionId;
    }


    /**
     * @param $subOptionId
     */
    public function set_subOptionId($subOptionId)
    {
        $this->subOptionId = $subOptionId;
    }

    /**
     * @return null
     */
    public function get_conditionId()
    {
        return $this->conditionId;
    }

    /**
     * @param $conditionId
     */
    public function set_conditionId($conditionId)
    {
        $this->conditionId = $conditionId;
    }

    /**
     * @return null
     */
    public function get_ruleId()
    {
        return $this->ruleId;
    }

    /**
     * @param $ruleId
     */
    public function set_ruleId($ruleId)
    {
        $this->ruleId = $ruleId;
    }

    /**
     * @return null
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * @param $value
     */
    public function set_value($value)
    {
        $this->value = $value;
    }

}