<?php

use bis\repf\vo\LabelValueVO;

class RedirectRulesUtil {

    private function __construct() {
        
    }

    public static function getIncludesDirPath() {

        $pluginPath = RedirectRulesUtil::getPluginAbsPath() . "/includes/";

        return $pluginPath;
    }

    public static function getPluginAbsPath() {

        $dirName = plugin_dir_path(__FILE__);
        $pluginPath = realpath($dirName);
        $path_array = explode("util", $pluginPath);

        return $path_array[0];
    }

    public static function get_bis_re_redirect_validations() {
        $translation_array = array(
        'enter_rule_name' => __('Enter rule name', BIS_REDIRECT_RULES_TEXT_DOMAIN ),
        'select_logical_rule' => __('Select logical rule', BIS_REDIRECT_RULES_TEXT_DOMAIN),
        'rule_name_special_char' => __('Rule name should not contain special characters.', BIS_REDIRECT_RULES_TEXT_DOMAIN ),
        'select_redirect' => __('Select redirect', BIS_REDIRECT_RULES_TEXT_DOMAIN),
        'delete_confirm' => __('Do want to delete rule', BIS_REDIRECT_RULES_TEXT_DOMAIN),
        'invalid_redirect' => __('Invalid redirect file, Enter a mo redirect file.', BIS_REDIRECT_RULES_TEXT_DOMAIN),
        'enter_redirect_file' => __('Enter redirect file name.', BIS_REDIRECT_RULES_TEXT_DOMAIN),
        'enter_redirect_button_label' => __('Enter redirect button label.', BIS_REDIRECT_RULES_TEXT_DOMAIN),
        'enter_cancel_button_label' => __('Enter cancel button label.', BIS_REDIRECT_RULES_TEXT_DOMAIN)
        );
        return $translation_array;
    }
    
    public static function get_option($option) {

        if (is_multisite()) { // Sets in network optionsif multisite
            return get_site_option($option);
        } else {
            return get_option($option);
        }
    }
    
    public static function delete_option($option) {
        if (is_multisite()) {
            delete_site_option($option);
        } else {
            delete_option($option);
        }
    }
    
    public static function update_option($option, $value) {
        if (is_multisite()) {
            update_site_option($option, $value);
        } else {
            update_option($option, $value);
        }
    }
    
    public static function get_auto_redirect_times() {
        $times = Array();
        
        $value1 = new LabelValueVO(0, "Never");
        array_push($times, $value1);
        
        $value2 = new LabelValueVO(2000, "2");
        array_push($times, $value2);
        
        $value3 = new LabelValueVO(4000, "4");
        array_push($times, $value3);
        
        $value4 = new LabelValueVO(5000, "5");
        array_push($times, $value4);
        
        $value5 = new LabelValueVO(10000, "10");
        array_push($times, $value5);
        
        $value6 = new LabelValueVO(20000, "20");
        array_push($times, $value6);
        
        $value7 = new LabelValueVO(30000, "30");
        array_push($times, $value7);
        
        $value8 = new LabelValueVO(60000, "60");
        array_push($times, $value8);
        
        return $times;
    }

}
