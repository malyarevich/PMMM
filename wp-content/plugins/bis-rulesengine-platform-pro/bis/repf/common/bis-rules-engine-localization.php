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

namespace bis\repf\common;

use bis\repf\model\LogicalRulesEngineModel;

// Localize the script with new data

class RulesEngineLocalization {

    private static $countries = null;
    private static $continents = null;
    private static $currencies = null;
    private static $months = null;
    private static $weekdays = null;

    public static function get_bis_re_child_validations() {
        $translation_array = array(
            'enter_rule_name' => __('Enter rule name', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'enter_geoname_name' => __('Enter Geoname user', BIS_RULES_ENGINE_TEXT_DOMAIN),
            'enter_logical_name' => __('Enter logical rule name', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'hook_space_rule' => __('Action hook should not contain spaces, use single word', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'hook_special_char' => __('Action hook should not contain special characters.', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_criteria' => __('Select value for criteria ', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_sub_criteria' => __('Select value for sub criteria ', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_condition' => __('Select value for condition ', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_criteria_value' => __('Enter criteria value', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_operator_value' => __('Missing operator (And/Or) for row ', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_right_brackets' => __('Missing right brackets', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_left_brackets' => __('Missing left brackets', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'rule_name_special_char' => __('Rule name should not contain special characters.', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'ipaddress_invalid' => __('Enter a valid IP Address.', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'ipaddress_invalid_characters' => __('Invalid IP Address, should contain only numbers and dots.', BIS_RULES_ENGINE_TEXT_DOMAIN ),

            // Child localization
            'enter_target_url' => __('Enter Target URL', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_logical_rule' => __('Select logical rule', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_theme' => __('Select theme', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_widgets' => __('Select widgets', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_sidebar' => __('Select sidebar', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_action' => __('Select action', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_category' => __('Select category', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_language' => __('Select language', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_image' => __('Select image', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_image_size' => __('Select image size', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_location' => __('Select location', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'enter_shortcode' => __('Enter shortcode', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'enter_content_body' => __('Enter content body', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'enter_posts' => __('Enter posts', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'select_pages' => __('Select pages', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'delete_confirm' => __('Do want to delete rule', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'invalid_url' => __('Invalid URL', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'protocal_missing' => __('Protocol is missing, URL should begin with http or https', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            
            // LV localization
            'invalid_purchase_code' => __('Invalid Item Purchase Code, Please enter a valid Purchase code.', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'error_verify_pcode' => __('Error occurred while verifying Purchase Code.', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'pc_button_activate' => __('Activate', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'pc_button_please_wait' => __('Please wait ...', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'pc_plugin_act' => __('Plugin activated, Thank you for verifying your Purchase code.', BIS_RULES_ENGINE_TEXT_DOMAIN ),
            'bis_select_from_date' => __('Select from date.', BIS_RULES_ENGINE_TEXT_DOMAIN),
            'bis_select_to_date' => __('Select to date.', BIS_RULES_ENGINE_TEXT_DOMAIN),
            'bis_from_greater_to_date' => __('From date should be less than to date.', BIS_RULES_ENGINE_TEXT_DOMAIN),
            'pattern_not_found' => __('Value must contain /**', BIS_RULES_ENGINE_TEXT_DOMAIN)
            
        );

        return $translation_array;
    }

    /**
     * This method is used to get the localized values
     *
     * @param $values
     * @return mixed
     */
    public static function get_localized_values($values) {

        if ($values != null && count($values) > 0) {
            foreach ($values as $value) {
                $value->name = __($value->name, BIS_RULES_ENGINE_TEXT_DOMAIN);
            }
        }

        return $values;
    }

    /**
     * This method is used to get the countries.
     *
     * @param $value
     * @return array
     */
    public static function get_countries($value) {

        $countries_found = array();
        if (self::$countries == null) {
              self::$countries = array("WPLANG" => RulesEngineLocalization::getLocalizedValues(4));
        }

        foreach(self::$countries["WPLANG"] as $country) {
            if (stripos($country->name, $value) !== false) {
                array_push($countries_found, $country);
            }
        }
        return $countries_found;
    }

    /**
     * This method is used to get the continents.
     *
     * @param $value
     * @return array
     */
    public static function get_continents($value) {

        $continents_found = array();
        if (self::$continents == null) {
            self::$continents = array("WPLANG" => RulesEngineLocalization::getLocalizedValues(20));
        }

        foreach(self::$continents["WPLANG"] as $continent) {
            if (stripos($continent->name, $value) !== false) {
                array_push($continents_found, $continent);
            }
        }
        return $continents_found;
    }

    /**
     * This method is used to get the currencies.
     *
     * @param $value
     * @return array
     */
    public static function get_currencies($value) {

        $currencies_found = array();
        if (self::$currencies == null) {
            self::$currencies = array("WPLANG" => RulesEngineLocalization::getLocalizedValues(5));
        }

        foreach(self::$currencies["WPLANG"] as $currency) {
            if (stripos($currency->name, $value) !== false) {
                array_push($currencies_found, $currency);
            }
        }
        return $currencies_found;
    }

    /**
     * This method is used to get the months.
     *
     * @param $value
     * @return array
     */
    public static function get_months($value) {

        $months_found = array();
        if (self::$months == null) {
            self::$months = array("WPLANG" => RulesEngineLocalization::getLocalizedValues(5));
        }

        foreach(self::$months["WPLANG"] as $month) {
            if (stripos($month->name, $value) !== false) {
                array_push($months_found, $month);
            }
        }
        return $months_found;
    }

    /**
     * This method is used to get the weekdays
     * @param $value
     * @return array
     */
    public static function get_weekdays($value) {

        $weekdays_found = array();
        if (self::$weekdays == null) {
            self::$weekdays = array("WPLANG" => RulesEngineLocalization::getLocalizedValues(5));
        }

        foreach(self::$weekdays["WPLANG"] as $weekday) {
            if (stripos($weekday->name, $value) !== false) {
                array_push($weekdays_found, $weekday);
            }
        }
        return $weekdays_found;
    }

    /**
     * This method is used to convert values to localized values.
     *
     * @param $sub_category_id
     * @return mixed
     */
    private static function getLocalizedValues($sub_category_id) {
        $logical_rule_model = new LogicalRulesEngineModel();
        return RulesEngineLocalization::get_localized_values($logical_rule_model->get_rule_values($sub_category_id));
    }

}