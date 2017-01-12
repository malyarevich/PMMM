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

class AuditReportVO {
    
    private $id = null;
    private $country = null;
    private $region = null;
    private $city = null;
    private $ipAddress = null;
    private $browser = null;
    private $device = null;
    private $manufacturer = null;
    private $responseCode = null;
    private $requestTime = null;
    private $reportTypeId = null;
    private $reportAuthId = null;
    private $postId = null;
    private $termTaxonomyId = null;
    private $user = null;
    
    public function getPostId() {
        return $this->postId;
    }
    
    public function setPostId($postId) {
        return $this->postId = $postId;
    }
    
    public function getTermTaxonomyId() {
        return $this->termTaxonomyId;
    }

    public function setTermTaxonomyId($termTaxonomyId) {
        return $this->termTaxonomyId = $termTaxonomyId;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getReportAuthId() {
        return $this->reportAuthId;
    }

    public function setReportAuthId($reportAuthId) {
        $this->reportAuthId = $reportAuthId;
    }

    public function getId() {
        return $this->id;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getRegion() {
        return $this->region;
    }

    public function getCity() {
        return $this->city;
    }

    public function getIpAddress() {
        return $this->ipAddress;
    }

    public function getBrowser() {
        return $this->browser;
    }

    public function getDevice() {
        return $this->device;
    }

    public function getManufacturer() {
        return $this->manufacturer;
    }

    public function getResponseCode() {
        return $this->responseCode;
    }

    public function getRequestTime() {
        return $this->requestTime;
    }

    public function getReportTypeId() {
        return $this->reportTypeId;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function setRegion($region) {
        $this->region = $region;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setIpAddress($ipAddress) {
        $this->ipAddress = $ipAddress;
    }

    public function setBrowser($browser) {
        $this->browser = $browser;
    }

    public function setDevice($device) {
        $this->device = $device;
    }

    public function setManufacturer($manufacturer) {
        $this->manufacturer = $manufacturer;
    }

    public function setResponseCode($responseCode) {
        $this->responseCode = $responseCode;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setUserRole($userRole) {
        $this->userRole = $userRole;
    }

    public function setRequestTime($requestTime) {
        $this->requestTime = $requestTime;
    }

    public function setReportTypeId($reportTypeId) {
        $this->reportTypeId = $reportTypeId;
    } 
    
}