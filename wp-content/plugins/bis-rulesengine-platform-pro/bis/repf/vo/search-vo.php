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
 * Class SearchVO
 */
class SearchVO
{

    private $search_by = null;
    private $status = null;
    private $search_value = null;


    /**
     * @return null
     */
    public function  get_search_by()
    {
        return $this->search_by;
    }

    /**
     * @param $search_by
     */
    public function  set_search_by($search_by)
    {
        $this->search_by = $search_by;
    }

    /**
     * @return null
     */
    public function  get_search_value()
    {
        return $this->search_value;
    }

    /**
     * @param $search_value
     */
    public function  set_search_value($search_value)
    {
        $this->search_value = $search_value;
    }

    /**
     * @return null
     */
    public function  get_status()
    {
        return $this->status;
    }

    /**
     * @param $status
     */
    public function  set_status($status)
    {
        $this->status = $status;
    }

}