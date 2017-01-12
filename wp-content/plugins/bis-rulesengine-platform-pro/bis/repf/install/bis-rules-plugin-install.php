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

namespace bis\repf\install;

use bis\repf\vo\PluginVO;
use RulesEngineUtil;

if (!class_exists('BISRE_Plugin_Install')) {

    class BISRE_Plugin_Install {

        /**
         * This function is used for setting up database table for rules engine.
         */
        function bis_rules_engine_activation() {

            if (!is_network_admin() && is_multisite()) {
                die("<span style=\"color:red\">\"RulesEngine Platform\" 
            should be activated from network admin. </span>"
                );
            }

            // Get the installed version of plugin
            $installed_version = RulesEngineUtil::get_option(BIS_RULES_ENGINE_VERSION_CONST, is_network_admin());

            // First time installation
            if ($installed_version == false) {
                $this->bis_create_rules_engine_database();
                $this->bis_rules_engine_install_data();
                $this->bis_rules_engine_configure_addons();

                RulesEngineUtil::update_option(BIS_RULES_ENGINE_DELETE_DB, "true", is_network_admin());
                RulesEngineUtil::add_option(BIS_GEO_NAME_USER, "rules4wp", is_network_admin());
            } else {
                $installed_version = floatval($installed_version);
                $this->bis_rules_engine_configure_addons();
                if ($installed_version < BIS_RULES_ENGINE_VERSION) { // logic to check if upgrade
                    $this->bis_upgrade_rules_engine();
                }
            }

            $re_plugin_dir = explode("/bis/repf/install", plugin_basename(__FILE__));

            $re_plugin_dir_opt = RulesEngineUtil::get_option(BIS_RULES_ENGINE_PLATFORM_DIR, is_network_admin());

            if ($re_plugin_dir_opt == false) {
                RulesEngineUtil::add_option(BIS_RULES_ENGINE_PLATFORM_DIR, $re_plugin_dir[0], is_network_admin());
            } else {
                RulesEngineUtil::update_option(BIS_RULES_ENGINE_PLATFORM_DIR, $re_plugin_dir[0], is_network_admin());
            }
            
            $this->copy_maxmind_db();       
        }

        function create_upload_directory() {
            $upload_dir = wp_upload_dir();
            $upload_dirname = $upload_dir['basedir'] . '/' . BIS_UPLOAD_DIRECTORY;
            if (!file_exists($upload_dirname)) {
                wp_mkdir_p($upload_dirname);
            }
        }
        
        function copy_maxmind_db() {
            $this->create_upload_directory();
            $upload_dir = wp_upload_dir();
            $upload_dirname = $upload_dir['basedir'] . '/' . BIS_UPLOAD_DIRECTORY;
            $bis_geo_maxmind_db_file = RulesEngineUtil::get_option(BIS_GEO_MAXMIND_DB_FILE);
            
            // Check if file name exists in options.
            // File doesnot exists
            if (RulesEngineUtil::isNullOrEmptyString($bis_geo_maxmind_db_file) || 
                    $bis_geo_maxmind_db_file == false) { 
                $from_filePath = RulesEngineUtil::getPluginAbsPath() . 'bis/repf/MaxMind/Db/' . BIS_GEO_MAXMIND_DB_FILE_NAME;
                $to_filePath = $upload_dirname . "/" . BIS_GEO_MAXMIND_DB_FILE_NAME;
                if (!copy($from_filePath, $to_filePath)) {
                    echo "failed to copy $from_filePath to $to_filePath...\n";
                }
            } else { // file exists
                $from_filePath = RulesEngineUtil::getPluginAbsPath() . 'bis/repf/MaxMind/Db/' . $bis_geo_maxmind_db_file;
                $to_filePath = $upload_dirname . "/" . $bis_geo_maxmind_db_file;
                if (!file_exists($to_filePath) && file_exists($from_filePath)) {
                    if (!copy($from_filePath, $to_filePath)) {
                        echo "failed to copy $from_filePath to $to_filePath...\n";
                    }
                }
                // File name does not exists in the source
                // This condition for upgrade case.
                if(!file_exists($from_filePath)) {
                    $from_filePath = RulesEngineUtil::getPluginAbsPath() . 'bis/repf/MaxMind/Db/' . BIS_GEO_MAXMIND_DB_FILE_NAME;
                    $to_filePath = $upload_dirname . "/" . BIS_GEO_MAXMIND_DB_FILE_NAME;
                    if (!copy($from_filePath, $to_filePath)) {
                        echo "failed to copy $from_filePath to $to_filePath...\n";
                    } else {
                        RulesEngineUtil::update_option(BIS_GEO_MAXMIND_DB_FILE, BIS_GEO_MAXMIND_DB_FILE_NAME);
                    }
                }
            }
        }

        /**
         * This method is used to show error message if any addon is active.
         * 
         */
        function bis_rules_engine_deactivate() {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            $bis_plugin_force_delete = RulesEngineUtil::get_option(BIS_RULES_ENGINE_PLUGIN_FORCE_DELETE);

            if ($bis_plugin_force_delete !== "true") {

                $bis_addons = RulesEngineUtil::get_option(BIS_RULESENGINE_ADDONS_CONST);
                $bis_addons_active = FALSE;

                if ($bis_addons != null) {

                    $bis_active_rules_json = json_decode($bis_addons);
                    foreach ($bis_active_rules_json as $ac_rule) {
                        if ($ac_rule->status == '1') {
                            $bis_addons_active = TRUE;
                        }
                    }
                }

                if ($bis_addons_active) {
                    wp_redirect("admin.php?page=bis_pg_settings&deactive=false");
                    die();
                }
            }
        }

        /**
         * This method is used for future upgrades
         *
         */
        function bis_upgrade_country_names() {
            global $wpdb;

            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Afghanistan - افغانستان' where id = '31';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Albania - Shqipëria' where id = '32';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Algeria - الجزائر‎' where id = '33';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'American Samoa - Amerika Sāmoa' where id = '34';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Andorra' where id = '35';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Angola' where id = '36';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Anguilla' where id = '37';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Antarctica' where id = '38';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Antigua and Barbuda' where id = '39';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Argentina' where id = '40';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Armenia - Հայաստան' where id = '41';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Aruba' where id = '42';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Australia' where id = '43';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Austria - Österreich' where id = '44';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Azerbaijan - Azərbaycan' where id = '45';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Bahrain - البحرين' where id = '46';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Bangladesh - বাংলাদেশ' where id = '47';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Barbados' where id = '48';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Belarus - Беларусь' where id = '49';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Belgium - België' where id = '50';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Belize' where id = '51';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Benin - Bénin' where id = '52';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Bermuda' where id = '53';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Bhutan - འབྲུག་ཡུལ' where id = '54';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Bolivia' where id = '55';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Bosnia and Herzegovina - Bosna i Hercegovina' where id = '56';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Botswana' where id = '57';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Brazil - Brasil' where id = '58';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'British Indian Ocean Territory' where id = '59';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'British Virgin Islands' where id = '60';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Brunei Darussalam - بروني' where id = '61';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Bulgaria' where id = '62';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Burkina Faso' where id = '63';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Burma - မြန်မာ' where id = '64';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Burundi' where id = '65';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Cambodia - កម្ពុជា' where id = '66';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Cameroon - Cameroun' where id = '67';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Canada' where id = '68';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Cape Verde - Cabo Verde' where id = '69';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Cayman Islands' where id = '70';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Central African Republic - République Centrafricaine' where id = '71';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Chad - Tchad' where id = '72';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Chile' where id = '73';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'China - 中国' where id = '74';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Christmas Island' where id = '75';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Cocos (Keeling Islands' where id = '76';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Colombia' where id = '77';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Comoros - Komori' where id = '78';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Democratic Republic of the Congo - République démocratique du Congo' where id = '79';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Republic of the Congo - République du Congo' where id = '80';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Cook Islands' where id = '81';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Costa Rica' where id = '82';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Croatia - Hrvatska' where id = '83';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Cuba' where id = '84';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Cyprus - Kypros' where id = '85';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Czech Republic - Česká republika' where id = '86';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Denmark - Danmark' where id = '87';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Djibouti - جيبوتي' where id = '88';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Dominica' where id = '89';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Dominican Republic - República Dominicana' where id = '90';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Ecuador' where id = '91';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Egypt - مصر' where id = '92';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'El Salvador' where id = '93';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Equatorial Guinea - Guinea Ecuatorial' where id = '94';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Eritrea - إرتريا' where id = '95';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Estonia - Eesti' where id = '96';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Ethiopia - ኢትዮጵያ' where id = '97';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Falkland Islands (Islas Malvinas' where id = '98';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Faroe Islands - Færøerne' where id = '99';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Fiji' where id = '100';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Finland - Suomi' where id = '101';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'France' where id = '102';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'French Polynesia - Polynésie française' where id = '103';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Gabon' where id = '104';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Georgia - საქართველო' where id = '105';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Germany - Deutschland' where id = '106';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Ghana' where id = '107';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Gibraltar' where id = '108';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Greece - Ελλάδα' where id = '109';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Greenland - Kalaallit Nunaat' where id = '110';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Grenada' where id = '111';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Guam - Guåhån' where id = '112';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Guatemala' where id = '113';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Guinea - Guinée' where id = '114';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Guinea Bissau - Guiné-Bissau' where id = '115';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Guyana' where id = '116';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Haiti - Haïti' where id = '117';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Holy See (Vatican City' where id = '118';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Honduras' where id = '119';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Hong Kong (SAR' where id = '120';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Hungary - Magyarország' where id = '121';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Iceland - Ísland' where id = '122';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'India - भारत' where id = '123';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Indonesia' where id = '124';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Iran - ایران' where id = '125';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Iraq - العراق' where id = '126';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Ireland - Éire' where id = '127';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Israel - ישראל' where id = '128';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Italy - Italia' where id = '129';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Jamaica' where id = '130';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Japan - 日本' where id = '131';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Jersey' where id = '132';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Jordan - الأردن' where id = '133';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Kazakhstan - Қазақстан' where id = '134';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Kenya' where id = '135';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Kiribati' where id = '136';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Korea where id =  North' where id = '137';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Korea where id =  South' where id = '138';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Kuwait - دولة الكويت' where id = '139';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Kyrgyzstan' where id = '140';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Laos - ປະເທດລາວ' where id = '141';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Latvia - Latvija' where id = '142';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Lebanon - لبنان' where id = '143';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Lesotho' where id = '144';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Liberia' where id = '145';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Libya - ليبيا' where id = '146';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Liechtenstein - Liechtenstein' where id = '147';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Lithuania - Lietuva' where id = '148';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Luxembourg - Lëtzebuerg' where id = '149';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Macao' where id = '150';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Macedonia - Makedonija' where id = '151';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Madagascar - Madagasikara' where id = '152';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Malawi' where id = '153';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Malaysia - مليسيا' where id = '154';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Maldives - ދިވެހިރާއްޖެ' where id = '155';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Mali' where id = '156';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Malta' where id = '157';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Marshall Islands' where id = '158';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Mauritania' where id = '159';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Mauritius' where id = '160';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Mayotte' where id = '161';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Mexico - México' where id = '162';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Federated States of Micronesia' where id = '163';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Moldova' where id = '164';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Monaco' where id = '165';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Mongolia - Монгол Улс' where id = '166';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Montenegro - Црна Гора' where id = '167';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Montserrat' where id = '168';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Morocco - المغرب' where id = '169';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Mozambique - Moçambique' where id = '170';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Myanmar' where id = '171';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Namibia' where id = '172';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Nauru' where id = '173';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Nepal - नेपाल' where id = '174';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Netherlands - Nederlân' where id = '175';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Netherlands Antilles' where id = '176';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'New Caledonia - Nouvelle-Calédonie' where id = '177';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'New Zealand - Aotearoa' where id = '178';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Nicaragua' where id = '179';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Niger' where id = '180';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Nigeria' where id = '181';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Niue - Niuē' where id = '182';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Northern Mariana Islands' where id = '183';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Norway - Norge' where id = '184';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Oman - عُمان' where id = '185';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Pakistan - پاکستان' where id = '186';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Palau - Belau' where id = '187';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Panama - Panamá' where id = '188';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Papua New Guinea' where id = '189';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Paraguay' where id = '190';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Peru - Perú' where id = '191';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Philippines - Pilipinas' where id = '192';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Pitcairn Islands' where id = '193';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Poland - Polska' where id = '194';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Portugal' where id = '195';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Puerto Rico' where id = '196';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Qatar -  قطر' where id = '197';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Romania - România' where id = '198';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Russia - Россия' where id = '199';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Rwanda' where id = '200';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Saint Helena' where id = '201';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Saint Kitts and Nevis' where id = '202';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Saint Lucia' where id = '203';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Saint Pierre and Miquelon - Saint-Pierre et Miquelon' where id = '204';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Saint Vincent and the Grenadines' where id = '205';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Samoa' where id = '206';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'San Marino' where id = '207';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Sao Tome and Principe - São Tomé e Príncipe' where id = '208';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Saudi Arabia - المملكة العربية السعودية' where id = '209';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Senegal - Sénégal' where id = '210';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Serbia - Srbija' where id = '211';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Seychelles - Sesel' where id = '212';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Sierra Leone' where id = '213';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Singapore - Singapura' where id = '214';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Slovakia - Slovensko' where id = '215';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Slovenia - Slovenija' where id = '216';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Solomon Islands' where id = '217';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Somalia - الصومال' where id = '218';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'South Africa' where id = '219';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Spain - España' where id = '220';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Sri Lanka - ශ්‍රී ලංකාව' where id = '221';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Sudan - السودان' where id = '222';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Suriname' where id = '223';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Svalbard' where id = '224';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Swaziland' where id = '225';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Sweden - Sverige' where id = '226';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Switzerland - Schweiz' where id = '227';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Syria - سورية' where id = '228';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Taiwan - 台灣' where id = '229';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Tajikistan - Тоҷикистон' where id = '230';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Tanzania' where id = '231';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Thailand - เมืองไทย' where id = '232';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Trinidad and Tobago' where id = '233';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Tunisia - تونس' where id = '234';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Turkey - Türkiye' where id = '235';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Turkmenistan - Türkmenistan' where id = '236';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Turks and Caicos Islands' where id = '237';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Tuvalu' where id = '238';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Uganda' where id = '239';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Ukraine - Україна' where id = '240';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'United Arab Emirates - الإمارات العربيّة المتّحدة' where id = '241';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'United Kingdom' where id = '242';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'United States' where id = '243';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Uruguay - República Oriental del Uruguay' where id = '244';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Uzbekistan - Ўзбекистон' where id = '245';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Vanuatu' where id = '246';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Venezuela' where id = '247';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Vietnam - Việt Nam' where id = '248';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Wallis and Futuna - Wallis-et-Futuna' where id = '249';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Western Sahara' where id = '250';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Yemen - اليمن' where id = '251';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Zambia' where id = '252';");
            $wpdb->query("UPDATE `bis_re_logical_rule_value`  set display_name = 'Zimbabwe' where id = '253';");

            // update the conditions
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (31, 4), (31, 5)");
        }

        /**
         * This method is used for future upgrades
         *
         */
        function bis_upgrade_rules_engine() {
            // Method for future use
            $this->bis_rules_engine_update_data();
            RulesEngineUtil::update_option(BIS_RULES_ENGINE_VERSION_CONST, BIS_RULES_ENGINE_VERSION, is_network_admin());
        }

        function bis_rules_engine_update_data() {
            global $wpdb;
            // Get the installed version of plugin
            $installed_version = RulesEngineUtil::get_option(BIS_RULES_ENGINE_VERSION_CONST, is_network_admin());

            // Used for upgrading to 3.5
            if (floatval($installed_version) < 3.5) {
                $wpdb->query("insert  into `bis_re_sub_option`(`id`,`name`,`option_id`,`value_type_id`) values (29,'City',6,1);");
                $wpdb->query("insert  into `bis_re_sub_option`(`id`,`name`,`option_id`,`value_type_id`) values (30,'State or Region',6,1);");
                $wpdb->query("insert  into `bis_re_sub_option_condition`(`sub_option_id`,`condition_id`) values (29,1),(29,2);");
                $wpdb->query("insert  into `bis_re_sub_option_condition`(`sub_option_id`,`condition_id`) values (30,1),(30,2);");
                RulesEngineUtil::add_option(BIS_GEO_NAME_USER, "demo", is_network_admin());
            }

            // Used for upgrading to 3.6
            if (floatval($installed_version) < 3.6) {
                $wpdb->query("update `bis_re_logical_rule_value` SET parent_id = 24 WHERE id = 303");
            }

            // Used for upgrading to 3.7
            if (floatval($installed_version) < 4.0) {
                $wpdb->query("DELETE FROM `bis_re_logical_rule_value` WHERE id IN (18, 19, 20)");
                $wpdb->query("INSERT INTO `bis_re_option`(id, NAME) VALUES (11, 'Response');");
                $wpdb->query("INSERT INTO `bis_re_sub_option`(id, NAME, option_id, value_type_id) VALUES (16, 'Status Code', 11, 2);");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (16, 1), (16,2);");

                $wpdb->query("INSERT INTO `bis_re_option`(id, NAME) VALUES(12, 'Category')");
                $wpdb->query("INSERT INTO `bis_re_sub_option`(id, NAME, option_id, value_type_id) VALUES(17, 'WordPress', 12, 1);");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (17, 1), (17, 2)");
                $wpdb->query("INSERT INTO `bis_re_sub_option`(id, NAME, option_id, value_type_id) VALUES(25, 'WooCommerce', 12, 1);");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (25, 1), (25, 2)");
                bis_create_analytics_tables();
            }

            // Used for upgrading to 4.2
            if (floatval($installed_version) < 4.2) {
                $wpdb->query("INSERT INTO `bis_re_logical_rule_value` (VALUE, display_name, parent_id) VALUES ('soft_page_hide', 'Soft Page Hide', 1000);");
            }


            if (floatval($installed_version) < 5.0) {
                $wpdb->query("ALTER TABLE `bis_re_report_data` ADD COLUMN site_id INT NOT NULL DEFAULT 1");
                $wpdb->query("ALTER TABLE `bis_re_logical_rules` ADD COLUMN site_id INT NOT NULL DEFAULT 1;");
            }

            if (floatval($installed_version) < 5.5) {
                $wpdb->query("delete from `bis_re_logical_rule_value` where value = 'show_page'");
            }

            if (floatval($installed_version) < 5.7) {
                $wpdb->query("ALTER TABLE `bis_re_logical_rules_criteria` MODIFY `value` VARCHAR(5000);");
            }

            if (floatval($installed_version) < 5.8) {
                $wpdb->query("ALTER TABLE `bis_re_report_data` ADD COLUMN post_id BIGINT UNSIGNED;");
                $wpdb->query("ALTER TABLE `bis_re_report_data` ADD COLUMN term_taxonomy_id BIGINT UNSIGNED;");
                $wpdb->query("INSERT INTO `bis_re_report` VALUES (4, 'Page Rules')");
                RulesEngineUtil::add_option(BIS_CAPTURE_ANALYTICS_DATA, "false", is_network_admin());
            }

            if (floatval($installed_version) < 6.1) {
                $wpdb->query("INSERT INTO `bis_re_sub_option` VALUES(31, 'Referral URL', 3, 4);");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (31, 1), (31, 2);");
                $wpdb->query("ALTER TABLE `bis_re_logical_rules` ADD COLUMN eval_type INT (3) DEFAULT 1");
            }

            if (floatval($installed_version) < 6.5) {
                $wpdb->query("insert into bis_re_condition(`id` , `name`) values (10, 'is set'), (11, 'is not set');");
                $wpdb->query("INSERT INTO `bis_re_sub_option` VALUES(26, 'Parameter', 3, 4);");
                $wpdb->query("INSERT INTO `bis_re_sub_option` VALUES(27, 'Form Data', 3, 4);");
                $wpdb->query("INSERT INTO `bis_re_sub_option` VALUES(32, 'Cookie', 11, 4);");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (26, 1), (26, 2), (26, 4), (26, 5);");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (27, 1), (27, 2);");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (32, 1), (32, 2), (32, 10),(32, 11);");
            }

            if (floatval($installed_version) < 6.7) {
                $this->bis_upgrade_country_names();
            }


            if (floatval($installed_version) < 6.9) {
                RulesEngineUtil::update_option(BIS_CAPTURE_ANALYTICS_DATA, "false", is_network_admin());
                RulesEngineUtil::add_option(BIS_GEO_LOOKUP_TYPE, 1, is_network_admin());
                RulesEngineUtil::add_option(BIS_GEO_MAXMIND_DB_FILE, BIS_GEO_MAXMIND_DB_FILE_NAME, is_network_admin());
            }

            if (floatval($installed_version) < 7.0) {
                $wpdb->query("ALTER TABLE `bis_re_logical_rules` ADD COLUMN add_rule_type INT (1) DEFAULT 2");
                $wpdb->query("INSERT INTO `bis_re_condition` VALUES(12, 'contains any of');");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (15, 12)");
            }

            if (floatval($installed_version) < 7.1) {
                $wpdb->query("ALTER TABLE `bis_re_rule_details` ADD COLUMN child_sub_rule INT (5)");
            }

            if (floatval($installed_version) < 7.6) {
                $wpdb->query("INSERT INTO `bis_re_condition` VALUES(13, 'pattern match');");
                $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (6, 13);");

                $dynamicContent = file_get_contents(BIS_PLATFORM_HOME_DIR . "/template/bis-redirect-meta-template.html");
                RulesEngineUtil::add_option(BIS_REDIRECT_META_TEMPLATE, $dynamicContent, is_network_admin());
            }

            if (floatval($installed_version) < 8.0) {
                $wpdb->query("ALTER TABLE `bis_re_logical_rules_criteria` ADD INDEX `sub_option_id` (`sub_option_id`), ADD INDEX `condition_id` (`condition_id`)");
                $wpdb->query("ALTER TABLE `bis_re_logical_rules` ADD INDEX `bis_status` (`status`)");
                $wpdb->query("ALTER TABLE `bis_re_logical_rules_criteria` ADD INDEX `option_id` (`option_id`)");
                $wpdb->query("ALTER TABLE `bis_re_sub_option` ADD INDEX `value_type_id` (`value_type_id`)");
                $wpdb->query("ALTER TABLE `bis_re_rule_details` ADD INDEX `logical_rule` (`logical_rule_id`), ADD INDEX `status` (`status`), ADD INDEX `rule_type_id` (`rule_type_id`)");
                $wpdb->query("ALTER TABLE `bis_re_rules` ADD INDEX `parent_type_id` (`parent_type_id`)");
                $wpdb->query("ALTER TABLE `bis_re_logical_rule_value` ADD INDEX `lg_rule_parent_id` (`parent_id`)");
                $wpdb->query("ALTER TABLE `bis_re_logical_rule_value` ADD INDEX `lg_rule_value` (`value`)");
                $wpdb->query("ALTER TABLE `bis_re_logical_rule_value` ADD INDEX `lg_display_name` (`display_name`)");
            }

            // Get the installed version of plugin
            $metaTemplate = RulesEngineUtil::get_option(BIS_REDIRECT_META_TEMPLATE, is_network_admin());


            if ($metaTemplate == false || RulesEngineUtil::isNullOrEmptyString($metaTemplate)) {
                $dynamicContent = file_get_contents(BIS_PLATFORM_HOME_DIR . "/template/bis-redirect-meta-template.html");
                RulesEngineUtil::add_option(BIS_REDIRECT_META_TEMPLATE, $dynamicContent, is_network_admin());
            }
        }

        function bis_create_analytics_tables() {

            global $wpdb;

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $charset_collate = $wpdb->get_charset_collate();

            /* Table structure for table `bis_re_report` */

            $bis_re_report_query = "CREATE TABLE `bis_re_report` (
        `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
         PRIMARY KEY (`id`)
        ) ENGINE=INNODB " . $charset_collate . ";";

            dbDelta($bis_re_report_query);

            /* Table structure for table `bis_re_report_data` */

            $bis_re_report_data_query = "CREATE TABLE `bis_re_report_data` (
        `id` bigint(100) unsigned NOT NULL AUTO_INCREMENT,
        `country` varchar(100) NOT NULL,
        `region` varchar(100) DEFAULT NULL,
        `city` varchar(100) DEFAULT NULL,
        `ipaddress` varchar(100) NOT NULL,
        `browser` varchar(100) NOT NULL,
        `device` varchar(50) NOT NULL,
        `manufacturer` varchar(50) DEFAULT NULL,
        `response_code` int(10) NOT NULL DEFAULT '200',
        `request_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `report_type_id` int(10) unsigned NOT NULL DEFAULT '1',
        `parent_report_data_id` bigint(100) unsigned DEFAULT NULL,
        `ip_verified` tinyint(1) NOT NULL DEFAULT '0',
        `site_id` int(11) NOT NULL DEFAULT '1',
        `post_id` bigint(20) unsigned DEFAULT NULL,
        `term_taxonomy_id` bigint(20) unsigned DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `resp_status` (`response_code`),
        KEY `req_client` (`browser`),
        KEY `country` (`country`),
        KEY `req_time` (`request_time`),
        KEY `report_type_id` (`report_type_id`),
        KEY `parent_report_data_id` (`parent_report_data_id`),
        KEY `ip_verified` (`ip_verified`),
        CONSTRAINT `report_type_const` FOREIGN KEY (`report_type_id`) REFERENCES `bis_re_report` (`id`)
        ) ENGINE = InnoDB " . $charset_collate . ";";

            dbDelta($bis_re_report_data_query);

            /* Table structure for table `bis_re_report_auth` */

            $bis_re_report_auth_query = "CREATE TABLE `bis_re_report_auth` (
        `id` bigint(100) unsigned NOT NULL AUTO_INCREMENT,
        `userid` varchar(100) NOT NULL,
        `user_role` varchar(100) NOT NULL,
        `login` timestamp NULL DEFAULT NULL,
        `logout` timestamp NULL DEFAULT '0000-00-00 00:00:00',
        `report_data_id` bigint(100) unsigned NOT NULL,
        PRIMARY KEY (`id`),
        KEY `report_data_id` (`report_data_id`),
        CONSTRAINT `bis_re_report_auth_ibfk_1` FOREIGN KEY (`report_data_id`) REFERENCES `bis_re_report_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE = InnoDB " . $charset_collate . ";";

            dbDelta($bis_re_report_auth_query);

            // Adding index to table
            $wpdb->query("ALTER TABLE `bis_re_logical_rules_criteria` ADD INDEX `sub_option_id` (`sub_option_id`), ADD INDEX `condition_id` (`condition_id`)");
            $wpdb->query("ALTER TABLE `bis_re_logical_rules` ADD INDEX `bis_status` (`status`)");
            $wpdb->query("ALTER TABLE `bis_re_logical_rules_criteria` ADD INDEX `option_id` (`option_id`)");
            $wpdb->query("ALTER TABLE `bis_re_sub_option` ADD INDEX `value_type_id` (`value_type_id`)");
            $wpdb->query("ALTER TABLE `bis_re_rule_details` ADD INDEX `logical_rule` (`logical_rule_id`), ADD INDEX `status` (`status`), ADD INDEX `rule_type_id` (`rule_type_id`)");
            $wpdb->query("ALTER TABLE `bis_re_rules` ADD INDEX `parent_type_id` (`parent_type_id`)");
            $wpdb->query("ALTER TABLE `bis_re_logical_rule_value` ADD INDEX `lg_rule_parent_id` (`parent_id`)");
            $wpdb->query("ALTER TABLE `bis_re_logical_rule_value` ADD INDEX `lg_rule_value` (`value`)");
            $wpdb->query("ALTER TABLE `bis_re_logical_rule_value` ADD INDEX `lg_display_name` (`display_name`)");

            $wpdb->query("insert into `bis_re_report`(`id`, `name`) values (1, 'General'), (2, '404 '), (3, 'Redirection from Devices'), (4, 'Page Rules')");
        }

        /**
         * Plugin db cration method
         */
        function bis_create_rules_engine_database() {

            global $wpdb;

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            $charset_collate = $wpdb->get_charset_collate();

            /* Table structure for table `bis_re_condition` */
            $bis_re_condition_query = "CREATE TABLE bis_re_condition (
               `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(100) NOT NULL,
              PRIMARY KEY (id)
            ) ENGINE=InnoDB " . $charset_collate . ";";

            dbDelta($bis_re_condition_query);

            /* Data for the table `bis_re_condition` */
            $bis_re_logical_rule_value = "CREATE TABLE bis_re_logical_rule_value (
          id int(10) unsigned NOT NULL AUTO_INCREMENT, `value` varchar(50) NOT NULL,
          display_name varchar(70) NOT NULL, parent_id int(11) DEFAULT NULL, PRIMARY KEY (id))
          ENGINE=InnoDB " . $charset_collate . ";";

            dbDelta($bis_re_logical_rule_value);

            /* Table structure for table `bis_re_logical_rule_value` */
            $bis_re_logical_rules = "CREATE TABLE bis_re_logical_rules (
        id int(11) unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(50) NOT NULL,
        description varchar(500) DEFAULT NULL,
        action_hook varchar(50) DEFAULT NULL,
        status tinyint(1) NOT NULL DEFAULT '1',
        `site_id` int(11) NOT NULL DEFAULT '1',
        PRIMARY KEY (id),
        general_col1 varchar(100) DEFAULT NULL,
        general_col2 varchar(100) DEFAULT NULL,
        UNIQUE KEY name (name)
        ) ENGINE=InnoDB " . $charset_collate . ";";


            dbDelta($bis_re_logical_rules);


            /* Table structure for table `bis_re_logical_rules_criteria` */
            //  operator_id '0=None; 1=And; 2=Or
            // eval_type '1=Session;2=Request;3=Context
            $bis_re_logical_rules_criteria = "CREATE TABLE bis_re_logical_rules_criteria (
        id int(11) NOT NULL AUTO_INCREMENT,
        option_id int(11) NOT NULL,
        sub_option_id int(11) NOT NULL,
        condition_id int(11) NOT NULL,
        value varchar(5000) NOT NULL,
        logical_rule_id int(11) unsigned NOT NULL,
        operator_id int(5) NOT NULL DEFAULT '0',
        left_bracket tinyint(1) NOT NULL DEFAULT '0',
        right_bracket tinyint(1) NOT NULL DEFAULT '0',
        eval_type tinyint(1) NOT NULL DEFAULT '1',
        PRIMARY KEY (id),
        KEY Logical_Rule_Id (logical_rule_id),
        CONSTRAINT bis_logical_rule FOREIGN KEY (logical_rule_id) REFERENCES bis_re_logical_rules (id) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB " . $charset_collate . ";";


            dbDelta($bis_re_logical_rules_criteria);

            /* Table structure for table `bis_re_rule_details` */
            // rule_type_id  '1=Page Rule; 2=Redirect Rule; 3=Post Rule; 4=Widget Rule; 5=Theme Rule'
            $bis_re_rule_details = "CREATE TABLE bis_re_rule_details (
        id int(10) unsigned NOT NULL AUTO_INCREMENT,
        name varchar(50) COLLATE utf8_estonian_ci NOT NULL,
        description varchar(500) COLLATE utf8_estonian_ci DEFAULT NULL,
        action varchar(250) COLLATE utf8_estonian_ci DEFAULT NULL,
        action_hook varchar(100) COLLATE utf8_estonian_ci DEFAULT NULL,
        rule_type_id int(11) NOT NULL,
        status tinyint(1) NOT NULL DEFAULT '1',
        logical_rule_id int(11) NOT NULL,
        parent_type_value varchar(50) COLLATE utf8_estonian_ci DEFAULT NULL,
        PRIMARY KEY (id),
        priority int(5) NOT NULL DEFAULT '1',
        child_sub_rule int(5) DEFAULT NULL,
        general_col1 varchar(500) DEFAULT NULL,
        general_col2 varchar(1000) DEFAULT NULL,
        general_col3 varchar(500) DEFAULT NULL,
        general_col4 varchar(100) DEFAULT NULL,
        general_col5 varchar(100) DEFAULT NULL,
        UNIQUE KEY unique_rule (name,rule_type_id)
        ) ENGINE=InnoDB " . $charset_collate . ";";

            dbDelta($bis_re_rule_details);

            /* Table structure for table `bis_re_rules` */
            // parent_type_id  '1=Page or Post; 2=widget; 3=sidebar; 5=Theme'
            $bis_re_rules = "CREATE TABLE bis_re_rules (
        parent_id varchar(100) COLLATE utf8_estonian_ci DEFAULT NULL,
        rule_details_id int(11) unsigned NOT NULL,
        parent_type_id int(11) NOT NULL DEFAULT '1',
        KEY rule_detail_id (rule_details_id),
        CONSTRAINT rule_detail_constraint FOREIGN KEY (rule_details_id) REFERENCES bis_re_rule_details (id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB " . $charset_collate . ";";

            dbDelta($bis_re_rules);


            /* Table structure for table `bis_re_option` */
            $bis_re_option = "CREATE TABLE bis_re_option (
            id int(11) NOT NULL AUTO_INCREMENT, name varchar(100) NOT NULL, PRIMARY KEY (id)
        ) ENGINE=InnoDB " . $charset_collate . ";";


            dbDelta($bis_re_option);

            /* Table structure for table `bis_re_sub_option` */
            //  value_type_id 1=InputToken; 2=selectbox; 3=date; 4=textbox;
            $bis_re_sub_option = "CREATE TABLE bis_re_sub_option (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) COLLATE utf8_bin NOT NULL,
        option_id int(100) NOT NULL,
        value_type_id int(10) NOT NULL DEFAULT '1',
        PRIMARY KEY (id),
        KEY Option_Index (option_id),
        CONSTRAINT option_constaint FOREIGN KEY (option_id) REFERENCES bis_re_option (id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB " . $charset_collate . ";";


            dbDelta($bis_re_sub_option);


            /* Table structure for table `bis_re_sub_option_condition` */
            $bis_re_sub_option_condition = "CREATE TABLE bis_re_sub_option_condition (
        sub_option_id int(11) NOT NULL,
        condition_id int(11) NOT NULL,
        KEY sub_option (sub_option_id),
        KEY `condition` (`condition_id`),
        CONSTRAINT sub_condition FOREIGN KEY (condition_id) REFERENCES bis_re_condition (id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT sub_option FOREIGN KEY (sub_option_id) REFERENCES bis_re_sub_option (id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB " . $charset_collate . ";";


            dbDelta($bis_re_sub_option_condition);

            $this->bis_create_analytics_tables();

            RulesEngineUtil::add_option(BIS_RULES_ENGINE_VERSION_CONST, BIS_RULES_ENGINE_VERSION, is_network_admin());
            RulesEngineUtil::add_option(BIS_RULES_ENGINE_ALLOWABLE_TAGS_CONST, BIS_RULES_ENGINE_ALLOWABLE_TAGS, is_network_admin());
            RulesEngineUtil::add_option(BIS_CAPTURE_ANALYTICS_DATA, "false", is_network_admin());
            RulesEngineUtil::add_option(BIS_GEO_LOOKUP_TYPE, 1, is_network_admin());
            RulesEngineUtil::add_option(BIS_GEO_MAXMIND_DB_FILE, BIS_GEO_MAXMIND_DB_FILE_NAME, is_network_admin());
        }

        /**
         * This function is used for setting up initial data for rules engine.
         */
        function bis_rules_engine_install_data() {
            global $wpdb;

            $wpdb->query("insert  into bis_re_condition(`id` , `name`) values (1,'is equal to'),(2,'is not equal to'),(3,'begins with'),(4,'contains'),(5,'does not contain'),(6,'greater than'),(7,'less than'),(8,'domain is'),(9, 'ends with');");
            $wpdb->query("insert  into `bis_re_logical_rule_value`(`id`,`value`,`display_name`,`parent_id`) values (1,'IOS','IOS',13),(2,'Andriod','Andriod',13),(3,'Windows','Windows',13),(4,'iPhone','iPhone',11),(5,'Andriod_Phone','Andriod Phone',11),(6,'BlackBerry_Phone','BlackBerry Phone',11),(7,'iPad','iPad',12),(8,'Andriod_Tablet','Andriod Tablet',12),(9,'BlackBerry_Tablet','BlackBerry Tablet',12),(10,'HP_TouchPad','HP TouchPad',12),(11,'ie','Internet Explorer',8),(12,'chrome','Google Chrome',8),(13,'firefox','Firefox',8),(14,'safari','Safari',8),(16,'301','301 - Moved Permanently',15),(17,'500','500 - Internal Server Error',17),(21,'404','404 - Not Found',16),(23,'302','302 - Found',15),(24,'307','307 - Temporary Redirect',15),(25,'','Unregistered User',19),(26,'Mobile','Mobile',28),(27,'Tablet','Tablet',28),(28,'Smart_Phone','Smart Phone',28),(29,'Windows_Phone','Windows Phone',11),(30,'opera','Opera',8),(31,'AF','Afghanistan - افغانستان ',4),(32,'AL','Albania - Shqipëria',4),(33,'DZ','Algeria - الجزائر‎',4),(34,'AS','American Samoa - Amerika Sāmoa',4),(35,'AD','Andorra',4),(36,'AO','Angola',4),(37,'AI','Anguilla',4),(38,'AQ','Antarctica',4),(39,'AG','Antigua and Barbuda',4),(40,'AR','Argentina',4),(41,'AM','Armenia - Հայաստան',4),(42,'AW','Aruba',4),(43,'AU','Australia',4),(44,'AT','Austria - Österreich',4),(45,'AZ','Azerbaijan - Azərbaycan',4),(46,'BH','Bahrain - البحرين',4),(47,'BD','Bangladesh - বাংলাদেশ',4),(48,'BB','Barbados',4),(49,'BY','Belarus - Беларусь',4),(50,'BE','Belgium - België',4),(51,'BZ','Belize',4),(52,'BJ','Benin - Bénin',4),(53,'BM','Bermuda',4),(54,'BT','Bhutan - འབྲུག་ཡུལ',4),(55,'BO','Bolivia',4),(56,'BA','Bosnia and Herzegovina - Bosna i Hercegovina',4),(57,'BW','Botswana',4),(58,'BR','Brazil - Brasil',4),(59,'IO','British Indian Ocean Territory',4),(60,'VG','British Virgin Islands',4),(61,'BN','Brunei Darussalam - بروني',4),(62,'BG','Bulgaria',4),(63,'BF','Burkina Faso',4),(64,'MM','Burma - မြန်မာ',4),(65,'BI','Burundi',4),(66,'KH','Cambodia - កម្ពុជា',4),(67,'CM','Cameroon - Cameroun',4),(68,'CA','Canada',4),(69,'CV','Cape Verde - Cabo Verde',4),(70,'KY','Cayman Islands',4),(71,'CF','Central African Republic - République Centrafricaine',4),(72,'TD','Chad - Tchad',4),(73,'CL','Chile',4),(74,'CN','China - 中国',4),(75,'CX','Christmas Island',4),(76,'CC','Cocos (Keeling) Islands',4),(77,'CO','Colombia',4),(78,'KM','Comoros - Komori',4),(79,'CD','Democratic Republic of the Congo - République démocratique du Congo',4),(80,'CG','Republic of the Congo - République du Congo',4),(81,'CK','Cook Islands',4),(82,'CR','Costa Rica',4),(83,'HR','Croatia - Hrvatska',4),(84,'CU','Cuba',4),(85,'CY','Cyprus - Kypros',4),(86,'CZ','Czech Republic - Česká republika',4),(87,'DK','Denmark - Danmark',4),(88,'DJ','Djibouti - جيبوتي',4),(89,'DM','Dominica',4),(90,'DO','Dominican Republic - República Dominicana',4),(91,'EC','Ecuador',4),(92,'EG','Egypt - مصر',4),(93,'SV','El Salvador',4),(94,'GQ','Equatorial Guinea - Guinea Ecuatorial',4),(95,'ER','Eritrea - إرتريا',4),(96,'EE','Estonia - Eesti',4),(97,'ET','Ethiopia - ኢትዮጵያ',4),(98,'FK','Falkland Islands (Islas Malvinas)',4),(99,'FO','Faroe Islands - Færøerne',4),(100,'FJ','Fiji',4),(101,'FI','Finland - Suomi',4),(102,'FR','France',4),(103,'PF','French Polynesia - Polynésie française',4),(104,'GA','Gabon',4),(105,'GE','Georgia - საქართველო',4),(106,'DE','Germany - Deutschland',4),(107,'GH','Ghana',4),(108,'GI','Gibraltar',4),(109,'GR','Greece - Ελλάδα',4),(110,'GL','Greenland - Kalaallit Nunaat',4),(111,'GD','Grenada',4),(112,'GU','Guam - Guåhån',4),(113,'GT','Guatemala',4),(114,'GN','Guinea - Guinée',4),(115,'GW','Guinea Bissau - Guiné-Bissau',4),(116,'GY','Guyana',4),(117,'HT','Haiti - Haïti',4),(118,'VA','Holy See (Vatican City)',4),(119,'HN','Honduras',4),(120,'HK','Hong Kong (SAR)',4),(121,'HU','Hungary - Magyarország',4),(122,'IS','Iceland - Ísland',4),(123,'IN','India - भारत',4),(124,'ID','Indonesia',4),(125,'IR','Iran - ایران',4),(126,'IQ','Iraq - العراق',4),(127,'IE','Ireland - Éire',4),(128,'IL','Israel - ישראל',4),(129,'IT','Italy - Italia',4),(130,'JM','Jamaica',4),(131,'JP','Japan - 日本',4),(132,'JE','Jersey',4),(133,'JO','Jordan - الأردن',4),(134,'KZ','Kazakhstan - Қазақстан',4),(135,'KE','Kenya',4),(136,'KI','Kiribati',4),(137,'KP','Korea, North',4),(138,'KR','Korea, South',4),(139,'KW','Kuwait - دولة الكويت',4),(140,'KG','Kyrgyzstan',4),(141,'LA','Laos - ປະເທດລາວ',4),(142,'LV','Latvia - Latvija',4),(143,'LB','Lebanon - لبنان',4),(144,'LS','Lesotho',4),(145,'LR','Liberia',4),(146,'LY','Libya - ليبيا',4),(147,'LI','Liechtenstein - Liechtenstein',4),(148,'LT','Lithuania - Lietuva',4),(149,'LU','Luxembourg - Lëtzebuerg',4),(150,'MO','Macao',4),(151,'MK','Macedonia - Makedonija',4),(152,'MG','Madagascar - Madagasikara',4),(153,'MW','Malawi',4),(154,'MY','Malaysia - مليسيا',4),(155,'MV','Maldives - ދިވެހިރާއްޖެ',4),(156,'ML','Mali',4),(157,'MT','Malta',4),(158,'MH','Marshall Islands',4),(159,'MR','Mauritania',4),(160,'MU','Mauritius',4),(161,'YT','Mayotte',4),(162,'MX','Mexico - México',4),(163,'FM','Micronesia, Federated States of',4),(164,'MD','Moldova',4),(165,'MC','Monaco',4),(166,'MN','Mongolia - Монгол Улс',4),(167,'ME','Montenegro - Црна Гора',4),(168,'MS','Montserrat',4),(169,'MA','Morocco - المغرب',4),(170,'MZ','Mozambique - Moçambique',4),(171,'MM','Myanmar',4),(172,'NA','Namibia',4),(173,'NR','Nauru',4),(174,'NP','Nepal - नेपाल',4),(175,'NL','Netherlands - Nederlân',4),(176,'AN','Netherlands Antilles',4),(177,'NC','New Caledonia - Nouvelle-Calédonie',4),(178,'NZ','New Zealand - Aotearoa',4),(179,'NI','Nicaragua',4),(180,'NE','Niger',4),(181,'NG','Nigeria',4),(182,'NU','Niue - Niuē',4),(183,'MP','Northern Mariana Islands',4),(184,'NO','Norway - Norge',4),(185,'OM','Oman - عُمان',4),(186,'PK','Pakistan - پاکستان',4),(187,'PW','Palau - Belau',4),(188,'PA','Panama - Panamá',4),(189,'PG','Papua New Guinea',4),(190,'PY','Paraguay',4),(191,'PE','Peru - Perú',4),(192,'PH','Philippines - Pilipinas',4),(193,'PN','Pitcairn Islands',4),(194,'PL','Poland - Polska',4),(195,'PT','Portugal',4),(196,'PR','Puerto Rico',4),(197,'QA','Qatar -  قطر',4),(198,'RO','Romania - România',4),(199,'RU','Russia - Россия',4),(200,'RW','Rwanda',4),(201,'SH','Saint Helena',4),(202,'KN','Saint Kitts and Nevis',4),(203,'LC','Saint Lucia',4),(204,'PM','Saint Pierre and Miquelon - Saint-Pierre et Miquelon',4),(205,'VC','Saint Vincent and the Grenadines',4),(206,'WS','Samoa',4),(207,'SM','San Marino',4),(208,'ST','Sao Tome and Principe - São Tomé e Príncipe',4),(209,'SA','Saudi Arabia - المملكة العربية السعودية',4),(210,'SN','Senegal - Sénégal',4),(211,'RS','Serbia - Srbija',4),(212,'SC','Seychelles - Sesel',4),(213,'SL','Sierra Leone',4),(214,'SG','Singapore - Singapura',4),(215,'SK','Slovakia - Slovensko',4),(216,'SI','Slovenia - Slovenija',4),(217,'SB','Solomon Islands',4),(218,'SO','Somalia - الصومال',4),(219,'ZA','South Africa',4),(220,'ES','Spain - España',4),(221,'LK','Sri Lanka - ශ්‍රී ලංකාව',4),(222,'SD','Sudan - السودان',4),(223,'SR','Suriname',4),(224,'SJ','Svalbard',4),(225,'SZ','Swaziland',4),(226,'SE','Sweden - Sverige',4),(227,'CH','Switzerland - Schweiz',4),(228,'SY','Syria - سورية',4),(229,'TW','Taiwan - 台灣',4),(230,'TJ','Tajikistan - Тоҷикистон',4),(231,'TZ','Tanzania',4),(232,'TH','Thailand - เมืองไทย',4),(233,'TT','Trinidad and Tobago',4),(234,'TN','Tunisia - تونس',4),(235,'TR','Turkey - Türkiye',4),(236,'TM','Turkmenistan - Türkmenistan',4),(237,'TC','Turks and Caicos Islands',4),(238,'TV','Tuvalu',4),(239,'UG','Uganda',4),(240,'UA','Ukraine - Україна',4),(241,'AE','United Arab Emirates - الإمارات العربيّة المتّحدة',4),(242,'GB','United Kingdom',4),(243,'US','United States',4),(244,'UY','Uruguay - República Oriental del Uruguay',4),(245,'UZ','Uzbekistan - Ўзбекистон',4),(246,'VU','Vanuatu',4),(247,'VE','Venezuela',4),(248,'VN','Vietnam - Việt Nam',4),(249,'WF','Wallis and Futuna - Wallis-et-Futuna',4),(250,'EH','Western Sahara',4),(251,'YE','Yemen - اليمن',4),(252,'ZM','Zambia',4),(253,'ZW','Zimbabwe 22',4),(254,'INR','Indian Rupee',5),(255,'AUD','Australian Dollar',5),(256,'EUR','Euro',5),(257,'BRL','Brazilian Real',5),(258,'CAD','Canadian Dollar',5),(259,'CNY','Yuan Renminbi',5),(260,'DKK','Danish Krone',5),(261,'GBP','Pound Sterling',5),(262,'HKD','Hong Kong Dollar',5),(263,'IRR','Iranian Rial',5),(264,'ILS','Israeli New Shekel',5),(265,'JPY','Japanese Yen',5),(266,'KRW','	Korean Won',5),(267,'NZD','New Zealand Dollar',5),(268,'SGD','Singapore Dollar',5),(269,'ZAR','South African Rand',5),(270,'SEK','Swedish Krona',5),(271,'CHF','Swiss Franc',5),(272,'TWD','Taiwan Dollar',5),(273,'TRY','Turkish Lira',5),(274,'GBP','Pound Sterling',5),(275,'USD','US Dollar',5),(276,'AED','Arab Emirates Dirham',5),(277,'AF','Africa',20),(278,'AN','Antarctica',20),(279,'AS','Asia',20),(280,'EU','Europe',20),(281,'NA','North america',20),(282,'OC','Oceania',20),(283,'SA','South america',20),(285,'Sunday','Sunday',23),(286,'Monday','Monday',23),(287,'Tuesday','Tuesday',23),(288,'Wednesday','Wednesday',23),(289,'Thursday','Thrusday',23),(290,'Friday','Friday',23),(291,'Saturday','Saturday',23),(292,'Jan','January',24),(293,'Feb','February',24),(294,'Mar','March',24),(295,'Apr','April',24),(296,'May','May',24),(297,'Jun','June',24),(298,'Jul','July',24),(299,'Aug','August',24),(300,'Sep','September',24),(301,'Oct','October',24),(302,'Nov','November',24),(303,'Dec','December',24),(304,'hide_page','Hide Page',1000),(305,'replace_page_content','Replace Page Content',1000),(306,'append_page_content','Append content to Page',1000),(308,'append_image_page','Append image to Page',1000),(309,'pos_top_page','Top of Page',1001),(310,'pos_bottom_page','Bottom of Page',1001),(311,'pos_dialog_page','Show as Dialog',1001),(312,'large','Large',1002),(313,'medium','Medium',1002),(314,'thumbnail','Thumbnail',1002),(315,'full','Full',1002),(316,'pos_cust_scode_page','Generate Short Code',1001),(317,'append_image_bg_page','Append content with image background',1000),(318,'hide_post','Hide Post',1003),(319,'replace_post_content','Replace Post Content',1003),(320,'append_post_content','Append content to Post',1003),(321,'append_image_post','Append image to Post',1003),(322,'pos_top_post','Top of Post',1004),(323,'pos_bottom_post','Bottom of Post',1004),(324,'pos_dialog_post','Show as Dialog',1004),(325,'pos_cust_scode_post','Generate Short Code',1004),(326,'append_image_bg_post','Append content with image background',1003),(327,'append_existing_scode_page','Append Third-Party Shortcode',1000),(328,'append_existing_scode_post','Append Third-Party Shortcode',1003),(329,'en_US','English (United States)',7),(330,'ar','Arabic - العربية',7),(331,'az','Azerbaijani - Azərbaycan dili',7),(332,'bg_BG','Bulgarian - Български',7),(333,'bs_BA','Bosnian - Bosanski',7),(334,'ca','Catalan - Català',7),(335,'cy','Welsh - Cymraeg',7),(336,'da_DK','Danish - Dansk',7),(337,'de_CH','German (Switzerland) - Deutsch (Schweiz)',7),(338,'de_DE','German - Deutsch',7),(339,'el','Greek - Ελληνικά',7),(340,'en_CA','English (Canada) - English (Canada)',7),(341,'en_AU','English (Australia) - English (Australia)',7),(342,'en_GB','English (UK) - English (UK)',7),(343,'eo','Esperanto - Esperanto',7),(344,'es_PE','Spanish (Peru) - Español de Perú',7),(345,'es_MX','Spanish (Mexico) - Español de México',7),(346,'es_ES','Spanish (Spain) - Español',7),(347,'es_CL','Spanish (Chile) - Español de Chile',7),(348,'eu','Basque - Euskara',7),(349,'fa_IR','Persian - فارسی',7),(350,'fi','Finnish - Suomi',7),(351,'fr_FR','French (France) - Français',7),(352,'gd','Scottish Gaelic - Gàidhlig',7),(353,'gl_ES','Galician - Galego',7),(354,'haz','Hazaragi - هزاره گی',7),(355,'he_IL','Hebrew - עִבְרִית',7),(356,'hr','Croatian - Hrvatski',7),(357,'hu_HU','Hungarian - Magyar',7),(358,'id_ID','Indonesian - Bahasa Indonesia',7),(359,'is_IS','Icelandic - Íslenska',7),(360,'it_IT','Italian - Italiano',7),(361,'ja','Japanese - 日本語',7),(362,'ko_KR','Korean - 한국어',7),(363,'lt_LT','Lithuanian - Lietuvių kalba',7),(364,'my_MM','Myanmar (Burmese) - ဗမာစာ',7),(365,'nb_NO','Norwegian (Bokmål) - Norsk bokmål',7),(366,'nl_NL','Dutch - Nederlands',7),(367,'nn_NO','Norwegian (Nynorsk) - Norsk nynorsk',7),(368,'oci','Occitan - Occitan',7),(369,'pl_PL','Polish - Polski',7),(370,'ps','Pashto - پښتو',7),(371,'pt_BR','Portuguese (Brazil) - Português do Brasil',7),(372,'pt_PT','Portuguese (Portugal) - Português',7),(373,'ro_RO','Romanian - Română',7),(374,'ru_RU','Russian - Русский',7),(375,'sk_SK','Slovak - Slovenčina',7),(376,'sl_SI','Slovenian - Slovenščina',7),(377,'sq','Albanian - Shqip',7),(378,'sr_RS','Serbian - Српски језик',7),(379,'sv_SE','Swedish - Svenska',7),(380,'th','Thai - ไทย',7),(381,'tr_TR','Turkish - Türkçe',7),(382,'ug_CN','Uighur - Uyƣurqə',7),(383,'uk','Ukrainian - Українська',7),(384,'zh_CN','Chinese (China) - 简体中文',7),(385,'zh_TW','Chinese (Taiwan) - 繁體中文',7),(386,'soft_page_hide','Soft Page Hide',1000);");
            $wpdb->query("insert  into `bis_re_option`(`id`,`name`) values (1,'User Role'),(2,'User Profile'),(3,'Request'),(4,'Mobile Device'),(5,'Language'),(6,'Geo Location'),(7,'Date and Time'),(8,'Browser'),(9,'Page'),(10,'Post');");
            $wpdb->query("insert  into `bis_re_sub_option`(`id`,`name`,`option_id`,`value_type_id`) values (1,'User Role Name',1,1),(2,'Email',2,1),(3,'Registered Date',2,3),(4,'Country',6,1),(5,'Currency',6,1),(6,'URL',3,4),(7,'Language Name',5,2),(8,'Browser Name',8,2),(9,'Date',7,3),(10,'Time',7,3),(11,'Mobile',4,2),(12,'Tablet',4,2),(13,'Mobile Operating System',4,2),(14,'Date and Time',7,3),(15,'IP Address',6,4),(18,'User Id',2,1),(19,'Unregistered(Not Logged In)',2,2),(20,'Continent',6,1),(21,'Page Title',9,1),(22,'Post Title',10,1),(23,'Day of Week',7,1),(24,'Month',7,1),(28,'Mobile Device Type',4,2);");
            $wpdb->query("insert  into `bis_re_sub_option_condition`(`sub_option_id`,`condition_id`) values (2,1),(2,2),(2,4),(2,5),(1,1),(1,2),(10,1),(10,2),(10,6),(10,7),(2,8),(3,1),(3,2),(3,6),(3,7),(11,1),(11,2),(12,1),(12,2),(13,1),(13,2),(14,1),(14,2),(14,6),(14,7),(9,1),(9,2),(9,6),(9,7),(8,1),(8,2),(6,1),(6,2),(6,4),(6,5),(7,1),(7,2),(18,1),(18,2),(19,1),(19,2),(28,1),(28,2),(4,1),(4,2),(5,1),(5,2),(20,1),(20,2),(23,1),(23,2),(24,1),(24,2),(21,1),(21,2),(22,1),(22,2),(15,1),(15,2),(15,3),(15,4),(15,5),(15,9);");
            $wpdb->query("insert  into `bis_re_sub_option`(`id`,`name`,`option_id`,`value_type_id`) values (29,'City',6,1);");
            $wpdb->query("insert  into `bis_re_sub_option`(`id`,`name`,`option_id`,`value_type_id`) values (30,'State or Region',6,1);");
            $wpdb->query("insert  into `bis_re_sub_option_condition`(`sub_option_id`,`condition_id`) values (29,1),(29,2);");
            $wpdb->query("insert  into `bis_re_sub_option_condition`(`sub_option_id`,`condition_id`) values (30,1),(30,2);");

            $wpdb->query("DELETE FROM `bis_re_logical_rule_value` WHERE id IN (18, 19, 20)");
            $wpdb->query("INSERT INTO `bis_re_option`(id, NAME) VALUES (11, 'Response');");
            $wpdb->query("INSERT INTO `bis_re_sub_option`(id, NAME, option_id, value_type_id) VALUES (16, 'Status Code', 11, 2);");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (16, 1), (16,2);");

            $wpdb->query("INSERT INTO `bis_re_option`(id, NAME) VALUES(12, 'Category')");
            $wpdb->query("INSERT INTO `bis_re_sub_option`(id, NAME, option_id, value_type_id) VALUES(17, 'WordPress', 12, 1);");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (17, 1), (17, 2)");
            $wpdb->query("INSERT INTO `bis_re_sub_option`(id, NAME, option_id, value_type_id) VALUES(25, 'WooCommerce', 12, 1);");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (25, 1), (25, 2)");
            $wpdb->query("INSERT INTO `bis_re_logical_rule_value` (VALUE, display_name, parent_id) VALUES ('soft_page_hide', 'Soft Page Hide', 1000);");

            $wpdb->query("INSERT INTO `bis_re_sub_option` VALUES(31, 'Referral URL', 3, 4);");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (31, 1), (31, 2);");
            $wpdb->query("ALTER TABLE `bis_re_logical_rules` ADD COLUMN eval_type INT (3) DEFAULT 1");
            $wpdb->query("ALTER TABLE `bis_re_logical_rules` ADD COLUMN add_rule_type INT (1) DEFAULT 2");

            $wpdb->query("insert into bis_re_condition(`id` , `name`) values (10, 'is set'), (11, 'is not set');");
            $wpdb->query("INSERT INTO `bis_re_sub_option` VALUES(26, 'Parameter', 3, 4);");
            $wpdb->query("INSERT INTO `bis_re_sub_option` VALUES(27, 'Form Data', 3, 4);");
            $wpdb->query("INSERT INTO `bis_re_sub_option` VALUES(32, 'Cookie', 11, 4);");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (26, 1), (26, 2), (26, 4), (26, 5);");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (27, 1), (27, 2);");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (32, 1), (32, 2), (32, 10),(32, 11);");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (31, 4), (31, 5)");
            $wpdb->query("INSERT INTO `bis_re_condition` VALUES(12, 'contains any of');");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (15, 12)");
            $wpdb->query("INSERT INTO `bis_re_condition` VALUES(13, 'pattern match');");
            $wpdb->query("INSERT INTO `bis_re_sub_option_condition` VALUES (6, 13);");

            $dynamicContent = file_get_contents(BIS_PLATFORM_HOME_DIR . "/template/bis-redirect-meta-template.html");
            RulesEngineUtil::add_option(BIS_REDIRECT_META_TEMPLATE, $dynamicContent, is_network_admin());
        }

        function bis_rules_engine_configure_addons() {

            $addon_list = array();

            // PAGE
            $pgPluginVO = new PluginVO();
            $pgPluginVO->set_id(BIS_PLATFORM_PAGE_PLUGIN_ID);
            $pgPluginVO->set_apiKey(BIS_PLATFORM_PAGE_API_KEY);
            $pgPluginVO->set_display_name(BIS_PLATFORM_PAGE_PLUGIN_DISPLAY_NAME);
            $pgPluginVO->set_description(BIS_PLATFORM_PAGE_PLUGIN_DESCRIPTION);
            $pgPluginVO->set_css_class(BIS_PLATFORM_PAGE_CSS_CLASS);
            $pgPluginVO->set_path(BIS_PLATFORM_PAGE_PLUGIN_PATH);
            $pgPluginVO->set_abs_path(BIS_PLATFORM_PAGE_PLUGIN_ABSPATH);
            $pgPluginVO->set_status(-1);
            array_push($addon_list, $pgPluginVO);

            // POST
            $pstPluginVO = new PluginVO();
            $pstPluginVO->set_id(BIS_PLATFORM_POST_PLUGIN_ID);
            $pstPluginVO->set_apiKey(BIS_PLATFORM_POST_API_KEY);
            $pstPluginVO->set_display_name(BIS_PLATFORM_POST_PLUGIN_DISPLAY_NAME);
            $pstPluginVO->set_description(BIS_PLATFORM_POST_PLUGIN_DESCRIPTION);
            $pstPluginVO->set_css_class(BIS_PLATFORM_POST_CSS_CLASS);
            $pstPluginVO->set_path(BIS_PLATFORM_POST_PLUGIN_PATH);
            $pstPluginVO->set_abs_path(BIS_PLATFORM_POST_PLUGIN_ABSPATH);
            $pstPluginVO->set_status(-1);
            array_push($addon_list, $pstPluginVO);

            // CATEGORY
            $categoryPluginVO = new PluginVO();
            $categoryPluginVO->set_id(BIS_PLATFORM_CATEGORY_PLUGIN_ID);
            $categoryPluginVO->set_apiKey(BIS_PLATFORM_CATEGORY_API_KEY);
            $categoryPluginVO->set_display_name(BIS_PLATFORM_CATEGORY_PLUGIN_DISPLAY_NAME);
            $categoryPluginVO->set_description(BIS_PLATFORM_CATEGORY_PLUGIN_DESCRIPTION);
            $categoryPluginVO->set_css_class(BIS_PLATFORM_CATEGORY_CSS_CLASS);
            $categoryPluginVO->set_path(BIS_PLATFORM_CATEGORY_PLUGIN_PATH);
            $categoryPluginVO->set_abs_path(BIS_PLATFORM_CATEGORY_PLUGIN_ABSPATH);
            $categoryPluginVO->set_status(-1);
            array_push($addon_list, $categoryPluginVO);

            // WIDGET
            $wPluginVO = new PluginVO();
            $wPluginVO->set_id(BIS_PLATFORM_WIDGET_PLUGIN_ID);
            $wPluginVO->set_apiKey(BIS_PLATFORM_WIDGET_API_KEY);
            $wPluginVO->set_display_name(BIS_PLATFORM_WIDGET_PLUGIN_DISPLAY_NAME);
            $wPluginVO->set_description(BIS_PLATFORM_WIDGET_PLUGIN_DESCRIPTION);
            $wPluginVO->set_css_class(BIS_PLATFORM_WIDGET_CSS_CLASS);
            $wPluginVO->set_path(BIS_PLATFORM_WIDGET_PLUGIN_PATH);
            $wPluginVO->set_abs_path(BIS_PLATFORM_WIDGET_PLUGIN_ABSPATH);
            $wPluginVO->set_status(-1);
            array_push($addon_list, $wPluginVO);

            // THEME
            $themePluginVO = new PluginVO();
            $themePluginVO->set_id(BIS_PLATFORM_THEME_PLUGIN_ID);
            $themePluginVO->set_apiKey(BIS_PLATFORM_THEME_API_KEY);
            $themePluginVO->set_display_name(BIS_PLATFORM_THEME_PLUGIN_DISPLAY_NAME);
            $themePluginVO->set_description(BIS_PLATFORM_THEME_PLUGIN_DESCRIPTION);
            $themePluginVO->set_css_class(BIS_PLATFORM_THEME_CSS_CLASS);
            $themePluginVO->set_path(BIS_PLATFORM_THEME_PLUGIN_PATH);
            $themePluginVO->set_abs_path(BIS_PLATFORM_THEME_PLUGIN_ABSPATH);
            $themePluginVO->set_status(-1);
            array_push($addon_list, $themePluginVO);

            // LANGUAGE
            $langPluginVO = new PluginVO();
            $langPluginVO->set_id(BIS_PLATFORM_LANGUAGE_PLUGIN_ID);
            $langPluginVO->set_apiKey(BIS_PLATFORM_LANGUAGE_API_KEY);
            $langPluginVO->set_display_name(BIS_PLATFORM_LANGUAGE_PLUGIN_DISPLAY_NAME);
            $langPluginVO->set_description(BIS_PLATFORM_LANGUAGE_PLUGIN_DESCRIPTION);
            $langPluginVO->set_css_class(BIS_PLATFORM_LANGUAGE_CSS_CLASS);
            $langPluginVO->set_path(BIS_PLATFORM_LANGUAGE_PLUGIN_PATH);
            $langPluginVO->set_abs_path(BIS_PLATFORM_LANGUAGE_PLUGIN_ABSPATH);
            $langPluginVO->set_status(-1);
            array_push($addon_list, $langPluginVO);

            // REDIRECT
            $redirectPluginVO = new PluginVO();
            $redirectPluginVO->set_id(BIS_PLATFORM_REDIRECT_PLUGIN_ID);
            $redirectPluginVO->set_apiKey(BIS_PLATFORM_REDIRECT_API_KEY);
            $redirectPluginVO->set_display_name(BIS_PLATFORM_REDIRECT_PLUGIN_DISPLAY_NAME);
            $redirectPluginVO->set_description(BIS_PLATFORM_REDIRECT_PLUGIN_DESCRIPTION);
            $redirectPluginVO->set_css_class(BIS_PLATFORM_REDIRECT_CSS_CLASS);
            $redirectPluginVO->set_path(BIS_PLATFORM_REDIRECT_PLUGIN_PATH);
            $redirectPluginVO->set_abs_path(BIS_PLATFORM_REDIRECT_PLUGIN_ABSPATH);
            $redirectPluginVO->set_status(-1);
            array_push($addon_list, $redirectPluginVO);


            // Delete the plugin details.
            RulesEngineUtil::delete_option(BIS_RULESENGINE_ADDONS_CONST, is_network_admin());

            // Add them after delete.
            RulesEngineUtil::add_option(BIS_RULESENGINE_ADDONS_CONST, json_encode($addon_list), is_network_admin());
        }

    }

}