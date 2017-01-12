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
class CacheVO {
    
    private $postId = null;
    private $categoryId = null;
    private $referralUrl = null;
    private $is404 = false;
    private $isAjaxRequest = false;
    
    public function __construct($postId, $categoryId, $referralUrl, $is404=false,
        $isAjaxRequest = false) {
        $this->postId = $postId;
        $this->categoryId = $categoryId;
        $this->referralUrl = $referralUrl;
        $this->is404 =  $is404;
        $this->isAjaxRequest =  $isAjaxRequest;
    }

    public function isAjaxRequest() {
        return $this->isAjaxRequest;
    }

    public function setAjaxRequest($isAjaxRequest) {
        $this->isAjaxRequest = $isAjaxRequest;
    }

    public function is404() {
        return $this->is404;
    }

    public function set404($is404) {
        $this->is404 = $is404;
    }

    public function getPostId() {
        return $this->postId;
    }

    public function getCategoryId() {
        return $this->categoryId;
    }

    public function getReferralUrl() {
        return $this->referralUrl;
    }

    public function setPostId($postId) {
        $this->postId = $postId;
    }

    public function setCategoryId($categoryId) {
        $this->categoryId = $categoryId;
    }

    public function setReferralUrl($referralUrl) {
        $this->referralUrl = $referralUrl;
    }
}