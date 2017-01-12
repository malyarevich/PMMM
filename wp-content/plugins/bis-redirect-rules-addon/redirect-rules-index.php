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
 * Plugin Name: NextGen - Country and Mobile Redirect for WordPress
 * Plugin URI: http://rulesengine.in/
 * Description: This plugin is designed for defining rules for redirection for WordPress Site</strong>. This plugin requires "RulesEngine Platform". To know more read <a target="_blank" href="http://rulesengine.in/redirect-rules/">documentation.</a>
 * Version: 5.0
 * Author: Rules Engine
 * Author URI:  http://rulesengine.in/
 * Text Domain: redirectrules
 * Domain Path: /languages/
 * License: Commercial
 *
 */

use bis\repf\common\RulesEngineCacheWrapper;

if (!class_exists('BIS_Redirect_Controller')) {
    class BIS_Redirect_Controller {

        function init() {
            header('Access-Control-Allow-Origin: *');
            define("BIS_REDIRECT_RULES_VERSION", 5.0);
            $dirName = dirname(__FILE__);
            $realPath = realpath($dirName);

            define("BIS_REDIRECT_RULES_HOME_DIR", $realPath);

            include_once($realPath . "/common/redirect-rules-constants.php");
            require_once($realPath . "/util/redirect-rules-util.php");
            require_once($realPath . "/install/bis-redirect-rules-install.php");

            define("BIS_REDIRECT_RULESENGINE_PLATFORM", RedirectRulesUtil::get_option("BIS_RULES_ENGINE_PLATFORM_DIR"));

            if (!RedirectRulesInstall::bis_is_platform_installed()) {
                die("<span style=\"color:red\">\"NextGen Redirect for WordPress\" plugin requires 
            \"RulesEngine WordPress Platform\" plugin.<br/> Please install and activate \"RulesEngine WordPress Platform\" plugin
            before activating \"NextGen Redirect for WordPress\". </span>");
            }

            require_once(ABSPATH . "wp-content/plugins/" . BIS_REDIRECT_RULESENGINE_PLATFORM . "/bis/repf/install/bis-rules-addon-install.php");
            require_once(ABSPATH . "wp-content/plugins/" . BIS_REDIRECT_RULESENGINE_PLATFORM . "/bis/repf/model/base-rules-engine-model.php");
            require_once(ABSPATH . "wp-content/plugins/" . BIS_REDIRECT_RULESENGINE_PLATFORM . "/bis/repf/action/base-rules-engine.php");
            require_once(ABSPATH . "wp-content/plugins/" . BIS_REDIRECT_RULESENGINE_PLATFORM . "/bis/repf/vo/plugin-vo.php");
            require_once(ABSPATH . "wp-content/plugins/" . BIS_REDIRECT_RULESENGINE_PLATFORM . "/bis/repf/vo/cache-vo.php");

            require_once($realPath . "/model/redirect-rules-engine-model.php");
            require_once($realPath . "/action/redirect-rules-engine.php");
            require_once($realPath . '/ajax/redirect-rules-ajax.php');

            add_action('init', array(&$this, 'bis_load_redirect_dependents'));

            add_action('admin_menu', array(&$this, 'bis_show_redirect_menu'), 51);

            $redirectRulesEngine = new RedirectRulesEngine();
            
            // Content Filter
            add_filter('the_content', array(&$redirectRulesEngine, 'bis_re_show_redirect_modal'), 70);

            // Localization
            add_action('plugins_loaded', array(&$this, 'redirect_localization_init'));


            // Activate and deactivate plugin hooks
            if (is_admin()) {
                register_activation_hook(__FILE__, 'RedirectRulesInstall::bis_redirect_rules_activation');
                register_deactivation_hook(__FILE__, 'RedirectRulesInstall::bis_redirect_rules_deactivation');
                register_uninstall_hook(__FILE__, 'RedirectRulesInstall::bis_redirect_rules_uninstall');
            }
        }

        function bis_load_redirect_dependents() {
            
            $redirectRulesEngine = new RedirectRulesEngine();

            if (is_admin()) {
                wp_enqueue_script('bis-redirect-rules-js', plugins_url('/js/bis-redirect-rules.js', __FILE__));
                wp_register_style('redirect-rules-css', plugins_url('/css/redirect-rules.css', __FILE__));
                wp_enqueue_style('redirect-rules-css');
                wp_localize_script('bis-redirect-rules-js', 'redirectreCV', RedirectRulesUtil::get_bis_re_redirect_validations());
            } else {
                wp_enqueue_script('bis-redirect-site', plugin_dir_url(__FILE__) . 'js/bis-redirect-site.js', array('jquery'));
            }

            if (isset($_GET['bis_re_action']) &&
                    ($_GET['bis_re_action'] === 'bis_re_redirect')) {
                bis_re_redirect();
            }

            wp_register_style('bis-re-remodal-theme', plugins_url('/css/bis-re-remodal-theme.css', __FILE__));
            wp_enqueue_style('bis-re-remodal-theme');

            $redirectRulesEngine->apply_session_redirect_rules();
            
            if (!is_admin() && !RulesEngineUtil::is_ajax_request()) {
                $redirectRulesEngine->bis_evaluate_request_rules();
            }
             
        }

        function bis_show_redirect_menu() {
            add_submenu_page('bis_pg_dashboard', 'Redirect Rules', __('Redirect Rules', BIS_REDIRECT_RULES_TEXT_DOMAIN), 'manage_options', 'redirectrules', array(&$this, 'show_redirect_rules_config'));
        }

        /**
         * This method used to load the template and config file
         */
        function show_redirect_rules_config() {

            $dirName = dirname(__FILE__);
            // Give you the real path

            global $baseName;

            $baseName = basename(realpath($dirName));

            ob_start();

            if (is_admin()) {

                if (RulesEngineUtil::get_option(BIS_PUR_CODE . BIS_PLATFORM_REDIRECT_PLUGIN_ID)) {
                    require_once('includes/config-rules.php');
                    require_once('template/bis-redirect-rules-template.html');
                } else {
                    require_once(ABSPATH . "wp-content/plugins/" . BIS_REDIRECT_RULESENGINE_PLATFORM . "/includes/bis-verify-pcode.php");
                }
            }
        }

        function redirect_localization_init() {
            $path = dirname(plugin_basename(__FILE__)) . '/languages/';
            $loaded = load_plugin_textdomain(BIS_REDIRECT_RULES_TEXT_DOMAIN, false, $path);

            // Debug to check localization file loader
            //    if (!$loaded) {
            //      echo '<div class="error"> Localization: ' . __('Could not load the localization file: ' . $path, BIS_REDIRECT_RULES_TEXT_DOMAIN) . '</div>';
            //      return;
            //      } 
        }
    }
}

$bis_red_controller = new BIS_Redirect_Controller();
$bis_red_controller->init();