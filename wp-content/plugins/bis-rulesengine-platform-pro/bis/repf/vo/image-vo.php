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
 * Class ImageVO
 */
class ImageVO
{

    private $url = null;
    private $width = null;
    private $height = null;

    /**
     * @return null
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * @param null $url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     * @return null
     */
    public function get_width()
    {
        return $this->width;
    }

    /**
     * @param null $width
     */
    public function set_width($width)
    {
        $this->width = $width;
    }

    /**
     * @return null
     */
    public function get_height()
    {
        return $this->height;
    }

    /**
     * @param null $height
     */
    public function set_height($height)
    {
        $this->height = $height;
    }

}