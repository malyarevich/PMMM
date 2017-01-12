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

use bis\repf\vo\PluginVO;
use bis\repf\install\RulesEngineAddOn;

class RedirectRulesInstall {

    public static function bis_redirect_rules_activation() {

        if (!is_network_admin() && is_multisite()) {
            die("<span style=\"color:red\">\"NextGen - Country and Mobile Redirect for WordPress\" 
            should be activated from network admin. </span>"
            );
        }
        RedirectRulesInstall::bis_redirect_activate_parent_rule();

        $pluginVO = new PluginVO();
        $pluginVO->set_id(BIS_REDIRECT_PLUGIN_ID);
        $pluginVO->set_display_name(BIS_REDIRECT_PLUGIN_DISPLAY_NAME);
        $pluginVO->set_css_class(BIS_REDIRECT_CSS_CLASS);
        $pluginVO->set_path(BIS_REDIRECT_PLUGIN_PATH);
        $pluginVO->set_description(BIS_REDIRECT_PLUGIN_DESCRIPTION);
        $pluginVO->set_version(BIS_REDIRECT_RULES_VERSION);
        $pluginVO->set_apiKey(BIS_REDIRECT_API_KEY);
        $pluginVO->set_status(1);

        RulesEngineAddOn::bis_addon_activation($pluginVO);

        // First time install
        if (RulesEngineUtil::get_option(BIS_REDIRECT_RULES_VERSION_CONST) == null) {
            RulesEngineUtil::add_option(BIS_REDIRECT_RULES_VERSION_CONST, BIS_REDIRECT_RULES_VERSION);
            RedirectRulesInstall::bis_create_sample_data();
        } else {
            RulesEngineUtil::update_option(BIS_REDIRECT_RULES_VERSION_CONST, BIS_REDIRECT_RULES_VERSION);
        }
    }

    public static function bis_redirect_rules_deactivation() {

        $pluginVO = new PluginVO();
        $pluginVO->set_id(BIS_REDIRECT_PLUGIN_ID);
        $pluginVO->set_version(BIS_REDIRECT_RULES_VERSION);

        RulesEngineAddOn::bis_addon_deactivation($pluginVO);
    }

    public static function bis_redirect_rules_uninstall() {
        RedirectRulesUtil::delete_option(BIS_REDIRECT_RULES_VERSION_CONST);
        RedirectRulesUtil::delete_option(BIS_REDIRECT_PUR_CODE);
        RedirectRulesInstall::bis_addon_uninstall(BIS_REDIRECT_PLUGIN_ID);
    }

    public static function bis_redirect_activate_parent_rule() {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if (is_plugin_active(BIS_REDIRECT_RULESENGINE_PLATFORM . "/wp-rulesengine-platform.php") === false) {
            activate_plugins(BIS_REDIRECT_RULESENGINE_PLATFORM . "/wp-rulesengine-platform.php", true);
            wp_redirect("plugins.php");
        }
    }

    public static function bis_is_platform_installed() {

        if (RedirectRulesUtil::get_option("BIS_RULES_ENGINE_PLATFORM_DIR") == false) {
            return false;
        }

        return true;
    }

    public static function bis_addon_uninstall($plugin_id) {
        $pluginArray = RedirectRulesUtil::get_option("BIS_RULESENGINE_ADDONS");
        $pluginArrayObj = json_decode($pluginArray, true);

        foreach ($pluginArrayObj as $key => $pluginVO) {
            if ($pluginVO["id"] === $plugin_id) {
                unset($pluginArrayObj[$key]);
                break;
            }
        }

        RedirectRulesUtil::update_option("BIS_RULESENGINE_ADDONS", json_encode($pluginArrayObj), is_network_admin());
    }

    public static function bis_create_sample_data() {
        global $wpdb;

        $pre_query = "SELECT COUNT(id) as count FROM `bis_re_logical_rules`";
        $rows = $wpdb->get_row($pre_query);

        if ($rows->count === '0') {
            $wpdb->query("INSERT  INTO `bis_re_logical_rules`(`id`,`name`,`description`,`action_hook`,`status`,`site_id`,`general_col1`,`general_col2`,`eval_type`,`add_rule_type`) VALUES (1,'Redirect India Amazon Redirect','This rule is used to redirect to India Amazon site. If the user is from India.','',1,1,NULL,NULL,1,1), (2,'Mobile Redirect Redirect','This rule is used to redirect to mobile website.','',1,1,NULL,NULL,1,1), (3,'Sample Advanced Rule Redirect',' This rule is used to redirect to http://rulesengine.in website if the users from Washington or Sidney Cities or from India.','',1,1,NULL,NULL,1,2), (4,'Sample Country Dropdown Redirect','This rule is used to redirect uses on selecting India from country dropdown.','',1,1,NULL,NULL,1,1),(5,'Country Redirect Rule Redirect','This rule is used to redirect to  http://rulesengine.in if the request is from india','',1,1,NULL,NULL,1,1),(6,'Sample PopUp Redirect','This rule is used to redirect to http://rulesengine.in  web site.','',1,1,NULL,NULL,1,1);");
            $wpdb->query("INSERT  INTO `bis_re_logical_rules_criteria`(`id`,`option_id`,`sub_option_id`,`condition_id`,`value`,`logical_rule_id`,`operator_id`,`left_bracket`,`right_bracket`,`eval_type`) VALUES (1,6,29,1,'[{\"id\":\"Washington, D.C.\",\"name\":\"Washington, Washington, D.C., United States\"}]',3,2,2,0,1), (2,6,29,1,'[{\"id\":\"Sydney\",\"name\":\"Sidney, New South Wales, Australia\"}]',3,2,0,1,1), (3,6,4,1,'[{\"id\":\"IN\",\"name\":\"India - भारत\"}]',3,0,1,2,1), (4,4,28,1,'28',2,1,0,0,1),(5,3,6,1,'[{\"id\":\"http:\\/\\/hdfcbank.com\"}]',2,0,0,0,2), (6,6,4,1,'[{\"id\":\"IN\",\"name\":\"India - भारत\"}]',1,1,0,0,1),(7,3,6,1,'[{\"id\":\"http:\\/\\/www.amazon.com\"}]',1,0,0,0,2),(8,3,27,1,'[{\"id\":\"bis_country=IN\"}]',4,0,0,0,2),(9,6,4,1,'[{\"id\":\"IN\",\"name\":\"India - भारत\"}]',5,0,0,0,1),(10,6,4,1,'[{\"id\":\"IN\",\"name\":\"India - भारत\"}]',6,0,0,0,1);");
            $wpdb->query("INSERT  INTO `bis_re_rule_details`(`id`,`name`,`description`,`action`,`action_hook`,`rule_type_id`,`status`,`logical_rule_id`,`parent_type_value`,`priority`,`child_sub_rule`,`general_col1`,`general_col2`,`general_col3`,`general_col4`,`general_col5`) VALUES (1,'Sample Redirect India Amazon','This rule is used to redirect to India Amazon site https://www.amazon.in/.  If the user access the global website http://www.amazon.com from India.','{\"target_url\":\"https:\\/\\/www.amazon.in\\/\",\"redirect_type\":\"301\"}',NULL,2,0,1,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),(2,'Sample Mobile Redirect','This rule redirect from desktop version of website http://hdfcbank.com to mobile version http://m.hdfcbank.com\n','{\"target_url\":\"http:\\/\\/m.hdfcbank.com\",\"redirect_type\":\"301\"}',NULL,2,0,2,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL), (3,'Sample Advanced Rule',' This rule is used to redirect to http://rulesengine.in website if the users from Washington or Sidney Cities or from India.','{\"target_url\":\"http:\\/\\/rulesengine.in\",\"redirect_type\":\"301\"}',NULL,2,0,3,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),(4,'Sample Country Dropdown',' This rule is used to redirect uses on selecting India from country dropdown.','{\"target_url\":\"https:\\/\\/www.amazon.in\\/\",\"redirect_type\":\"301\"}',NULL,2,0,4,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL), (5,'Sample Country Redirect','This rule is used to redirect to  http://rulesengine.in if the request is from india','{\"target_url\":\"http:\\/\\/rulesengine.in\",\"redirect_type\":\"301\"}',NULL,2,0,5,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL), (6,'Sample PopUp','This rule is used to redirect to http://rulesengine.in  web site.','{\"target_url\":\"http:\\/\\/rulesengine.in\",\"redirect_type\":\"301\"}',NULL,2,0,6,NULL,1,NULL,'1','{\"title\":\"Rulesengine.in\",\"titleClass\":\"\",\"headingOne\":\"Shopping from [bis_country_name] ? Looking for RulesEngine.in?\",\"headingTwo\":\"Guaranteed 1-day delivery. 100% purchase protection. Easy returns.\",\"headingOneClass\":\"\",\"headingTwoClass\":\"\",\"buttonLabelOne\":\"Go to Rulesengine.in\",\"buttonLabelTwo\":\"Stay On RulesEngine.com\",\"buttonOneClass\":\"\",\"buttonTwoClass\":\"\",\"buttonActiveColor\":null,\"buttonInActiveColor\":null,\"buttonHoverColor\":null,\"popUpBackgroundColor\":\"\",\"autoCloseTime\":0,\"imageOneUrl\":null,\"imageOneId\":null,\"imageOneSize\":null,\"imageTwoUrl\":null,\"imageTwoId\":null,\"imageTwoSize\":null,\"buttonOneUrl\":\"http:\\/\\/rulesengine.in\",\"buttonTwoUrl\":null,\"checkBoxOne\":0}',NULL,NULL,NULL);");
            $dynamicContent = file_get_contents(BIS_REDIRECT_RULES_HOME_DIR . "/template/bis-redirect-rules-popup-template.html");
            RedirectRulesUtil::update_option(BIS_REDIRECT_POPUP_TEMPLATE, $dynamicContent);
            $metaContent = file_get_contents(BIS_PLATFORM_HOME_DIR . "/template/bis-redirect-meta-template.html");
            RedirectRulesUtil::update_option(BIS_REDIRECT_META_TEMPLATE, $metaContent);
        }
    }

}
