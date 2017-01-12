<?php

/* ######################################################################################

  Copyright (C) 2016 by Ritu.  All rights reserved.  This software
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
 * Description of ActionVO
 *
 * @author Reethu
 */
class ActionVO {
    
    public $actionname = null;
    public $actionvalue = 0;
    
    function __construct($actionname, $actionvalue) {
        $this->actionname = $actionname;
        $this->actionvalue = $actionvalue;
    }
    
    function getActionname() {
        return $this->actionname;
    }

    function getActionvalue() {
        return $this->actionvalue;
    }

    function setActionname($actionname) {
        $this->actionname = $actionname;
    }

    function setActionvalue($actionvalue) {
        $this->actionvalue = $actionvalue;
    }
  
}
    