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

namespace bis\repf\util;

class RulesStack
{
    protected $stack;

    public function __construct()
    {
        // initialize the stack
        $this->stack = array();
    }

    public function push($item)
    {
        // trap for stack overflow
        array_unshift($this->stack, $item);
    }

    public function pop()
    {
        if ($this->isEmpty()) {
            // trap for stack underflow
            throw new RunTimeException ('Stack is empty!');
        } else {
            // pop item from the start of the array
            return array_shift($this->stack);
        }
    }

    public function isEmpty()
    {
        return empty ($this->stack);
    }

    public function peek()
    {
        return current($this->stack);
    }
}