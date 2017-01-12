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
 * Class LabelValueVO
 */
class LabelValueVO {

    public $value = null;
    public $label = null;
    public $name = null;
    public $text = null;
    public $id = null;
    public $parent_id = null;
    public $parent = false;

    
    public function __construct($id = null, $label = null) {
        $this->id = $id;
        $this->label = $label;
    }
    
    /**
     * @param $name
     */
    public function set_parent($parent) {
        $this->parent = $parent;
    }

    /**
     * @return null
     */
    public function is_parent() {
        return $this->parent;
    }

    /**
     * @param $name
     */
    public function set_parent_id($parent_id) {
        $this->parent_id = $parent_id;
    }

    /**
     * @return null
     */
    public function get_parent_id() {
        return $this->parent_id;
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
    public function get_label() {
        return $this->label;
    }

    /**
     * @param $label
     */
    public function set_label($label) {
        $this->name = $label;
        $this->label = $label;
        $this->text = $label;
    }

    /**
     * @return null
     */
    public function get_value() {
        return $this->value;
    }

    /**
     * @param $value
     */
    public function set_value($value) {
        $this->id = $value;
        $this->value = $value;
    }

}
