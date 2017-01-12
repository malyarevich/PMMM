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
 * Description of GeolocationVO
 *
 * @author Reethu
 */
class GeolocationVO {
    public $continentCode = null;
    public $countryName = null;
    public $countryCode = null;
    public $countryCodeLowerCase = null;
    public $city = null;
    public $region = null;
    
    public function getCountryCodeLowerCase() {
        return $this->countryCodeLowerCase;
    }

    public function setCountryCodeLowerCase($countryCodeLowerCase) {
        $this->countryCodeLowerCase = $countryCodeLowerCase;
    }

    public function getContinentCode() {
        return $this->continentCode;
    }

    public function getCountryName() {
        return $this->countryName;
    }

    public function getCountryCode() {
        return $this->countryCode;
    }

    public function getCity() {
        return $this->city;
    }

    public function getRegion() {
        return $this->region;
    }

    public function setContinentCode($continentCode) {
        $this->continentCode = $continentCode;
    }

    public function setCountryName($country) {
        $this->countryName = $country;
    }

    public function  setCountryCode($countryCode) {
        $this->countryCode = $countryCode;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function setRegion($region) {
        $this->region = $region;
    }
}
