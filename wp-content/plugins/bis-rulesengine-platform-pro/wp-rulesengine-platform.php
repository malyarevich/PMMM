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

/*
 * Plugin Name: RulesEngine Professional Edition Platform
 * Plugin URI: http://rulesengine.in
 * Description: RulesEngine WordPress Professional Edition Platform is designed for defining logical rules using User Profile, User Role, Geolocation, Date and Time etc .</strong> To know more read <a target="_blank" href="http://rulesengine.in">documentation</a> or watch quick start <a target="_blank"  href="http://rulesengine.in/">video tutorials </a>.
 * Version: 8.2
 * Author: Rules Engine
 * Author URI:  http://rulesengine.in
 * Text Domain: rulesengine
 * Domain Path: /languages/
 * License: Commercial
 *
 */

use bis\repf\common\BISSession;
use bis\repf\common\BISLogger;

if (!class_exists('BISRE_Professional_Platform')) {

    class BISRE_Professional_Platform {

        /**
         * This menu is shown for network dashboard.
         */
        function bis_show_re_network_menu() {
            $bis_common_platform = new BISRE_Common_Platform();
            add_menu_page('RulesEngine', __("RulesEngine", BIS_RULES_ENGINE_TEXT_DOMAIN), 'manage_options', 'bis_pg_dashboard', array($bis_common_platform, 'show_rules_engine_config'));
            add_submenu_page('bis_pg_dashboard', 'Dashboard', __('Dashboard', BIS_RULES_ENGINE_TEXT_DOMAIN), 'manage_options', 'bis_pg_dashboard', array($bis_common_platform, 'show_rules_engine_config'));
            add_submenu_page('bis_pg_dashboard', 'Settings', __('Settings', BIS_RULES_ENGINE_TEXT_DOMAIN), 'manage_options', 'bis_pg_settings', array($bis_common_platform, 'show_rules_engine_config'));
        }
        
        
        public function init() {
            define("BIS_RULES_ENGINE_VERSION", 8.2);
            define("BIS_RULES_ENGINE_PLATFORM_TYPE", "BIS_RE_PROF_EDITION");
           
            $dirName = dirname(__FILE__);
            $realPath = realpath($dirName);

            define("BIS_RULES_ENGINE_PLATFORM_PATH", $realPath);
            
           
            require_once($realPath."/bis/repf/common/BISLogger.php");
            require_once($realPath."/bis-platfom-common-include.php");
            require_once($realPath."/bis/repf/install/bis-rules-plugin-install.php");
            require_once($realPath."/bis/repf/util/bis-analytics-util.php");
            
            $bis_common_platform = new BISRE_Common_Platform();
            $bis_common_platform->init();
            BISSession::getInstance();
            
            require_once($realPath.'/bis/repf/ajax/logical-rules-ajax.php');
            require_once($realPath.'/bis/repf/ajax/rules-settings-ajax.php');
            require_once($realPath.'/bis/repf/ajax/bis-analytics-ajax.php');
            
            $bisre_plugin_install = new bis\repf\install\BISRE_Plugin_Install();

            register_activation_hook(__FILE__, array($bisre_plugin_install, 'bis_rules_engine_activation'));
            register_deactivation_hook(__FILE__, array($bisre_plugin_install, 'bis_rules_engine_deactivate'));
            register_uninstall_hook(__FILE__, 'BISRE_Common_Platform::bis_rules_engine_uninstall');

            if (is_multisite()) {
                add_action('network_admin_menu', array(&$this, 'bis_show_re_network_menu'));
            }
        }
    }
}

$bisre_prof_platform = new BISRE_Professional_Platform();
$bisre_prof_platform->init();


