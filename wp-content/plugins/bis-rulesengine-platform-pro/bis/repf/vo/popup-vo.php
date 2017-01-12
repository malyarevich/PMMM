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
 * Class PopUpVO
 */
class PopUpVO {
    
    public $title = null;
    public $titleClass = null;
    public $headingOne = null;
    public $headingTwo = null;
    public $headingOneClass = null;
    public $headingTwoClass = null;
    public $buttonLabelOne = null;
    public $buttonLabelTwo = null;
    public $buttonOneClass = null;
    public $buttonTwoClass = null;
    public $buttonActiveColor = null;
    public $buttonInActiveColor = null;
    public $buttonHoverColor = null;
    public $popUpBackgroundColor = null;
    public $autoCloseTime = 0;
    public $imageOneUrl = null;
    public $imageOneId = null;
    public $imageOneSize = null;
    public $imageTwoUrl = null;
    public $imageTwoId = null;
    public $imageTwoSize = null;
    public $buttonOneUrl = null;
    public $buttonTwoUrl = null;
    public $checkBoxOne = 0;

    function getTitleClass() {
        return $this->titleClass;
    }

    function setTitleClass($titleClass) {
        $this->titleClass = $titleClass;
    }

    function getImageOneId() {
        return $this->imageOneId;
    }

    function getImageOneSize() {
        return $this->imageOneSize;
    }

    function getImageTwoId() {
        return $this->imageTwoId;
    }

    function getImageTwoSize() {
        return $this->imageTwoSize;
    }

    function setImageOneId($imageOneId) {
        $this->imageOneId = $imageOneId;
    }

    function setImageOneSize($imageOneSize) {
        $this->imageOneSize = $imageOneSize;
    }

    function setImageTwoId($imageTwoId) {
        $this->imageTwoId = $imageTwoId;
    }

    function setImageTwoSize($imageTwoSize) {
        $this->imageTwoSize = $imageTwoSize;
    }
    
    function getCheckBoxOne() {
        return $this->checkBoxOne;
    }

    function setCheckBoxOne($checkBoxOne) {
        $this->checkBoxOne = $checkBoxOne;
    }

    function getAutoCloseTime() {
        return $this->autoCloseTime;
    }

    function setAutoCloseTime($autoCloseTime) {
        $this->autoCloseTime = $autoCloseTime;
    }

    function getButtonOneUrl() {
        return $this->buttonOneUrl;
    }

    function getButtonTwoUrl() {
        return $this->buttonTwoUrl;
    }

    function setButtonOneUrl($buttonOneUrl) {
        $this->buttonOneUrl = $buttonOneUrl;
    }

    function setButtonTwoUrl($buttonTwoUrl) {
        $this->buttonTwoUrl = $buttonTwoUrl;
    }

    function getImageOneUrl() {
        return $this->imageOneUrl;
    }

    function getImageTwoUrl() {
        return $this->imageTwoUrl;
    }

    function setImageOneUrl($imageOneUrl) {
        $this->imageOneUrl = $imageOneUrl;
    }

    function setImageTwoUrl($imageTwoUrl) {
        $this->imageTwoUrl = $imageTwoUrl;
    }

    function getTitle() {
        return $this->title;
    }

    function getHeadingOne() {
        return $this->headingOne;
    }

    function getHeadingTwo() {
        return $this->headingTwo;
    }

    function getButtonLabelOne() {
        return $this->buttonLabelOne;
    }

    function getButtonLabelTwo() {
        return $this->buttonLabelTwo;
    }

    function getButtonActiveColor() {
        return $this->buttonActiveColor;
    }

    function getButtonInActiveColor() {
        return $this->buttonInActiveColor;
    }

    function getButtonHoverColor() {
        return $this->buttonHoverColor;
    }

    function getPopUpBackgroundColor() {
        return $this->popUpBackgroundColor;
    }

    function setTitle($title) {
        $this->title = $title;
    }

    function setHeadingOne($headingOne) {
        $this->headingOne = $headingOne;
    }

    function setHeadingTwo($headingTwo) {
        $this->headingTwo = $headingTwo;
    }

    function setButtonLabelOne($buttonLabelOne) {
        $this->buttonLabelOne = $buttonLabelOne;
    }

    function setButtonLabelTwo($buttonLabelTwo) {
        $this->buttonLabelTwo = $buttonLabelTwo;
    }

    function setButtonActiveColor($buttonActiveColor) {
        $this->buttonActiveColor = $buttonActiveColor;
    }

    function setButtonInActiveColor($buttonInActiveColor) {
        $this->buttonInActiveColor = $buttonInActiveColor;
    }

    function setButtonHoverColor($buttonHoverColor) {
        $this->buttonHoverColor = $buttonHoverColor;
    }

    function setPopUpBackgroundColor($popUpBackgroundColor) {
        $this->popUpBackgroundColor = $popUpBackgroundColor;
    }
    function getHeadingOneClass() {
        return $this->headingOneClass;
    }

    function getHeadingTwoClass() {
        return $this->headingTwoClass;
    }

    function setHeadingOneClass($headingOneClass) {
        $this->headingOneClass = $headingOneClass;
    }

    function setHeadingTwoClass($headingTwoClass) {
        $this->headingTwoClass = $headingTwoClass;
    }

    function getButtonOneClass() {
        return $this->buttonOneClass;
    }

    function getButtonTwoClass() {
        return $this->buttonTwoClass;
    }

    function setButtonOneClass($buttonOneClass) {
        $this->buttonOneClass = $buttonOneClass;
    }

    function setButtonTwoClass($buttonTwoClass) {
        $this->buttonTwoClass = $buttonTwoClass;
    }

}