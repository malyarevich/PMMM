<?php

if (!class_exists('BISRE_Common_Platform')) {

    class BISRE_Common_Platform {

        function init() {

            $dirName = dirname(__FILE__);
            $realPath = realpath($dirName);

            define("BIS_PLATFORM_HOME_DIR", $realPath);

            require_once($realPath . '/bis/repf/common/BISSession.php');
            require_once($realPath . '/bis/repf/common/BISLogger.php');
            require_once($realPath . "/common/bis-rules-constants.php");

            require_once($realPath . '/bis/repf/MaxMind/Db/Reader.php');
            require_once($realPath . '/bis/repf/MaxMind/Db/Reader/Decoder.php');
            require_once($realPath . '/bis/repf/MaxMind/Db/Reader/InvalidDatabaseException.php');
            require_once($realPath . '/bis/repf/MaxMind/Db/Reader/Metadata.php');
            require_once($realPath . '/bis/repf/MaxMind/Db/Reader/Util.php');

            require_once($realPath . "/bis/repf/common/bis-rules-engine-localization.php");
            require_once($realPath . "/bis/repf/install/bis-rules-plugin-install.php");
            require_once($realPath . "/bis/repf/install/bis-rules-addon-install.php");
            require_once($realPath . "/bis/repf/model/base-rules-engine-model.php");
            require_once($realPath . "/bis/repf/model/page-rules-engine-model.php");
            require_once($realPath . "/bis/repf/model/post-rules-engine-model.php");
            require_once($realPath . "/bis/repf/model/logical-rules-engine-model.php");
            require_once($realPath . "/bis/repf/model/analytics-rules-model.php");

            require_once($realPath . "/bis/repf/util/rules-stack.php");
            require_once($realPath . "/bis/repf/util/mdetect.php");
            require_once($realPath . "/bis/repf/util/geoplugin.php");
            require_once($realPath . "/bis/repf/util/browser-detection.php");
            require_once($realPath . "/bis/repf/util/geoplugin-wrapper.php");
            require_once($realPath . "/bis/repf/util/bis-cache-wrapper.php");
            require_once($realPath . "/util/rules-engine-util.php");

            require_once($realPath . "/bis/repf/common/bis-rules-engine-cache-wrapper.php");
            require_once($realPath . "/bis/repf/common/bis-session-wrapper.php");

            require_once($realPath . "/bis/repf/action/base-rules-engine.php");
            require_once($realPath . "/bis/repf/action/rules-engine.php");
            require_once($realPath . "/bis/repf/action/page-rules-engine.php");
            require_once($realPath . "/bis/repf/action/post-rules-engine.php");

            require_once($realPath . "/bis/repf/vo/plugin-vo.php");
            require_once($realPath . "/bis/repf/vo/logical-rules-criteria-vo.php");
            require_once($realPath . "/bis/repf/vo/logical-rules-vo.php");
            require_once($realPath . "/bis/repf/vo/rules-vo.php");
            require_once($realPath . "/bis/repf/vo/label-value-vo.php");
            require_once($realPath . "/bis/repf/vo/search-vo.php");
            require_once($realPath . "/bis/repf/vo/image-vo.php");
            require_once($realPath . "/bis/repf/vo/audit-report-vo.php");
            require_once($realPath . "/bis/repf/vo/geolocation-vo.php");
            require_once($realPath . "/bis/repf/vo/popup-vo.php");
            require_once($realPath . "/bis/repf/vo/cache-vo.php");

            require_once($realPath . "/template/bis-rules-dynamic-content-template.php");

            add_action('admin_menu', array(&$this, 'bis_show_rulesengine_menu'));
            add_action('admin_menu', array(&$this, 'bis_show_settings_menu'), 100);
            add_action('init', array(&$this, 'bis_load_dependents'), 3);

			// Add filters and actions for non admins
            if (!is_admin()) {
                $rulesEngine = new bis\repf\action\RulesEngine();

				//Evaluate request rule at the header load
                add_action('wp', array(&$rulesEngine, 'bis_evaluate_request_rules'), 15);

				// Content Filter
                add_filter('the_content', array(&$rulesEngine, 'bis_re_append_page_info'), 50);

				// Action added for showing country dropdown
                add_action('get_footer', array(&$rulesEngine, 'bis_change_country_hook'), 1000);
            }
        }

        /**
         * This method is used for adding rulesengine menu to wordpress
         */
        function bis_show_rulesengine_menu() {
            add_menu_page('RulesEngine', __("RulesEngine", BIS_RULES_ENGINE_TEXT_DOMAIN), 'manage_options', 'bis_pg_dashboard', array(&$this, 'show_rules_engine_config'));
            add_submenu_page('bis_pg_dashboard', 'Dashboard', __('Dashboard', BIS_RULES_ENGINE_TEXT_DOMAIN), 'manage_options', 'bis_pg_dashboard', array(&$this, 'show_rules_engine_config'));
        }

        /**
         * This method is used to load dependent for rulesengine plugin
         */
        function bis_load_dependents() {

			// From drop down
            if (isset($_POST[BIS_COUNTRY_SELECT])) {
                $country = $_POST[BIS_COUNTRY_SELECT];
                setcookie(BIS_COUNTRY_SELECT, $country, time() + BIS_COOKIE_EXPIRE_TIME, COOKIEPATH, COOKIE_DOMAIN);
            }

            $rulesEngine = new bis\repf\action\RulesEngine();
            $rulesEngine->bis_start_session();

            add_shortcode('bis_country_selector', array($rulesEngine, 'bis_country_selector'));
            add_shortcode('bis_country_name', array(&$rulesEngine, 'bis_country_name'));
            add_shortcode('bis_city_name', array(&$rulesEngine, 'bis_city_name'));
            add_shortcode('bis_region_name', array(&$rulesEngine, 'bis_region_name'));
            add_action('plugins_loaded', array(&$this, 'localization_init'));
            add_action('wp_login', array(&$rulesEngine, 'bis_reset_rules_engine_cache_login'), 10, 2);
            add_action('wp_logout', array(&$rulesEngine, 'bis_reset_rules_engine_cache_Logout'));

			// Load Rules engine configuration dependency file only for admin console.
            if (is_admin() && isset($_GET["page"])) {
                wp_dequeue_style('bis-pf-re-site-css-min');
 
                if (RulesEngineUtil::is_bis_re_plugin_page()) {
                    wp_enqueue_script('bis-bootstrap-multiselect', plugins_url('/js/lib/bootstrap-multiselect.js', __FILE__));
                    wp_enqueue_script('bis-ajax-request', plugin_dir_url(__FILE__) . 'js/bis-pf-re-admin.min.js', array('jquery'));

					// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
                    wp_localize_script('bis-ajax-request', 'BISAjax', array(
					// URL to wp-admin/admin-ajax.php to process the request
                        'ajaxurl' => admin_url('admin-ajax.php'),
                        // so that you can check it later when an AJAX request is sent
                        'bis_rules_engine_nonce' => wp_create_nonce('bis_rules_engine_nonce')
                    ));

                    wp_enqueue_script('jquery-form');
                    wp_register_style('bis-pf-re-admin-css-min', plugins_url('/css/bis-pf-re-admin.min.css', __FILE__));
                    wp_enqueue_style('bis-pf-re-admin-css-min');

                    wp_enqueue_script('bis-logical-rules-validator', plugins_url('/js/bis-logical-rules-validator.js', __FILE__));
                    wp_enqueue_script('bis-child-rules-validator-js', plugins_url('/js/bis-child-rules-validator.js', __FILE__));
                    wp_localize_script('bis-child-rules-validator-js', 'breCV', \bis\repf\common\RulesEngineLocalization::get_bis_re_child_validations());
                } else {
                    wp_dequeue_style('bootstrap_wp_admin_css');
                }
            } else { // Site libraries
                wp_dequeue_style('bootstrap_wp_admin_css');
                wp_enqueue_script('bis-ajax-request', plugin_dir_url(__FILE__) . 'js/bis-pf-re-site.min.js', array('jquery'));

				// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
                wp_localize_script('bis-ajax-request', 'BISAjax', array(
					// URL to wp-admin/admin-ajax.php to process the request
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    // so that you can check it later when an AJAX request is sent
                    'bis_rules_engine_nonce' => wp_create_nonce('bis_rules_engine_nonce')
                ));

                wp_register_style('bis-pf-re-site-css-min', plugins_url('/css/bis-pf-re-site.min.css', __FILE__));
                wp_enqueue_style('bis-pf-re-site-css-min');
            }
        }

        /**
         * This method used to load the template and config file
         */
        function show_rules_engine_config() {
            $dirName = dirname(__FILE__);
			// Give you the real path

            global $baseName;

            $baseName = basename(realpath($dirName));

            ob_start();

            if (is_admin()) {
                require_once('includes/config-rules.php');
                require_once('template/bis-rules-engine-template.html');
            }

            echo ob_get_clean();
        }

        function localization_init() {
            $path = dirname(plugin_basename(__FILE__)) . '/languages/';
            $loaded = load_plugin_textdomain('rulesengine', false, $path);

			// Debug to check localization file loader
            /* if (!$loaded) {
              echo '<div class="error"> Localization: ' . __('Could not load the localization file: ' . $path, 'rulesengine') . '</div>';
              return;
              } */
        }

        function bis_show_settings_menu() {
            add_submenu_page('bis_pg_dashboard', 'Logical Rules', __('Logical Rules', BIS_RULES_ENGINE_TEXT_DOMAIN), 'manage_options', 'bis_pg_rulesengine', array(&$this, 'show_rules_engine_config'));
            
            if(BIS_RULES_ENGINE_PLATFORM_TYPE === "BIS_RE_PROF_EDITION") {
                add_submenu_page('bis_pg_dashboard', 'Analytics', __('Analytics', BIS_RULES_ENGINE_TEXT_DOMAIN), 'manage_options', 'bis_pg_analytics', array(&$this, 'show_rules_engine_config'));
            }

            if (!is_multisite()) {
                add_submenu_page('bis_pg_dashboard', 'Settings', __('Settings', BIS_RULES_ENGINE_TEXT_DOMAIN), 'manage_options', 'bis_pg_settings', array(&$this, 'show_rules_engine_config'));
            }
        }

        /**
         * This function cleans up the rules engine database.
         */
        public static function bis_rules_engine_uninstall() {

            global $wpdb;

            $bis_delete_db = RulesEngineUtil::get_option(BIS_RULES_ENGINE_DELETE_DB, is_network_admin());

            // Delete db only if checkbox for delete is selected.
            if ($bis_delete_db === "true") {

                $tables = array();
                array_push($tables, "bis_re_sub_option_condition");
                array_push($tables, "bis_re_sub_option");
                array_push($tables, "bis_re_option");
                array_push($tables, "bis_re_rules");
                array_push($tables, "bis_re_rule_details");
                array_push($tables, "bis_re_condition");
                array_push($tables, "bis_re_logical_rule_value");
                array_push($tables, "bis_re_logical_rules_criteria");
                array_push($tables, "bis_re_logical_rules");
                array_push($tables, "bis_re_report_auth");
                array_push($tables, "bis_re_report_data");
                array_push($tables, "bis_re_report");


                foreach ($tables as $table) {
                    $wpdb->query("DROP TABLE IF EXISTS $table");
                }

                // Delete options
                RulesEngineUtil::delete_option(BIS_RULES_ENGINE_VERSION_CONST, is_network_admin());

                RulesEngineUtil::delete_option(BIS_RULES_ENGINE_DELETE_DB, is_network_admin());

                RulesEngineUtil::delete_option(BIS_RULES_ENGINE_ALLOWABLE_TAGS_CONST, is_network_admin());

                // Delete addons
                RulesEngineUtil::delete_option(BIS_RULESENGINE_ADDONS_CONST, is_network_admin());

                // Delete Geo name user
                RulesEngineUtil::delete_option(BIS_GEO_NAME_USER, is_network_admin());

                // Delete plugin location
                RulesEngineUtil::delete_option(BIS_RULES_ENGINE_PLATFORM_DIR, is_network_admin());

                RulesEngineUtil::delete_option(BIS_RULES_ENGINE_PLUGIN_FORCE_DELETE, is_network_admin());

                RulesEngineUtil::delete_option(BIS_CAPTURE_ANALYTICS_DATA, is_network_admin());

                RulesEngineUtil::delete_option(BIS_GEO_LOOKUP_TYPE, is_network_admin());

                RulesEngineUtil::delete_option(BIS_GEO_MAXMIND_DB_FILE, is_network_admin());

                RulesEngineUtil::delete_option(BIS_RULES_ENGINE_CACHE_INSTALLED, is_network_admin());

                RulesEngineUtil::delete_option(BIS_REDIRECT_META_TEMPLATE, is_network_admin());

                $upload_dir = wp_upload_dir();
                $upload_dirname = $upload_dir['basedir'] . '/' . BIS_UPLOAD_DIRECTORY;
                RulesEngineUtil::delete_directory($upload_dirname);
                
            }
        }
        
    }
}