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
namespace bis\repf\action;

use bis\repf\common\RulesEngineCacheWrapper;
use bis\repf\model\PageRulesEngineModel;
use bis\repf\model\AnalyticsEngineModel;
use RulesDynamicContent;

use RulesEngineUtil;

/**
 * Class PageRulesEngine
 */
class PageRulesEngine extends BaseRulesEngine {

    /**
     * This method exclude the search result for excluded pages and posts
     *
     * @param $query
     * @return mixed
     */
    public function bis_search_filter($query) {

        if ($query->is_search) {
            $exclude_merged_posts = null;
            $exclude_pages_array = null;
            $post_rules_engine = new PostRulesEngine();
            $exclude_page_ids = $this->get_excluded_page_ids();
            $exclude_posts_array = $post_rules_engine->bis_re_get_excluded_post_ids();

            if ($exclude_page_ids != null && $exclude_page_ids != "") {
                $exclude_pages_array = explode(",", $exclude_page_ids);
                $exclude_merged_posts = $exclude_pages_array;
            }

            if (($exclude_posts_array != null && count($exclude_posts_array) > 0) && $exclude_pages_array == null) {
                $exclude_merged_posts = $exclude_posts_array;
            }


            if ($exclude_pages_array != null && $exclude_posts_array != null) {
                $exclude_merged_posts = array_merge($exclude_pages_array, $exclude_posts_array);
            }

            if ($exclude_merged_posts != null && count($exclude_merged_posts) > 0) {
                $query->query_vars['post__not_in'] = $exclude_merged_posts;
            }
        }
        return $query;
    }

    /**
     * This method is shortcode support method to show content based on the logical rule.
     * 
     * @param type $atts
     * @param type $content
     * @return string
     */
    public function bis_content_show($atts, $content = null) {

        $atts = shortcode_atts(
                array('logicalrule' => 'null'), $atts, 'bis_content_show');

        $rule_name = $atts['logicalrule'];

        if (!RulesEngineUtil::isNullOrEmptyString($rule_name)) {
            if (RulesEngine::is_rule_valid($rule_name)) {
                return $content;
            }
        }

        return '';
    }

    /**
     * This method is used to apply page rules.
     *
     * @param $args
     * @return mixed
     */
    public function bis_re_apply_page_rule($args) {

        $sum_exclude_page = $this->get_excluded_page_ids();

        if ($sum_exclude_page != null) {
            $args ["exclude"] = $sum_exclude_page;
        }

        return $args;
    }

    /**
     * @return null|string
     */
    public function get_excluded_page_ids() {

        $applied_request_rules = $this->get_request_rules();


        $exclude_pages = $this->get_session_excluded_pages();

        // Get the excluded page rules if any
        $excluded_request_pages = $this->get_request_excluded_pages($applied_request_rules);

        // Gets the redirect request rules
        //PageRulesEngine::apply_request_redirect_rules($applied_request_rules);

        $sum_exclude_page = null;

        if ($exclude_pages != null && count($exclude_pages) > 0) {
            $exclude_pages_str = RulesEngineUtil::get_applied_page_rule_ids($exclude_pages);

            // Exclude the list of pages.
            if (strcmp($exclude_pages_str, "") != 0) {
                $sum_exclude_page = $exclude_pages_str;
            }
        }

        // Exclude the list of pages.
        if (strcmp($excluded_request_pages, "") != 0) {
            $sum_exclude_page = $excluded_request_pages . $sum_exclude_page;
        }

        return $sum_exclude_page;
    }

    /**
     * This method returns the excluded pages from DB if not found in session.
     *
     * @return array of excluded page ids
     */
    function get_session_excluded_pages() {
        $exclude_pages = $this->get_page_rules();
        $exclude_pages = null;
        if ($exclude_pages == null) {
            $this->init_applied_page_rules();
            $exclude_pages = $this->get_page_rules();
        }

        return $exclude_pages;
    }

    /**
     * This method get all the applied page rules (Excluded pages and Append Content Pages).
     *
     */
    private function init_applied_page_rules() {

        $exclude_pages = $this->get_page_rules();
        $exclude_pages = null;
        // If excluded page does not exist in session, then get from DB.
        if ($exclude_pages == null) {

            $applied_rules = $this->get_applied_logical_rules();
            $rule_count = count($applied_rules);

            if ($rule_count > 0) {
                // Call the rules to get the excluded pages.
                $page_rule_modal = new PageRulesEngineModel();
                $applied_page_rules = $page_rule_modal->get_applied_page_rules($applied_rules);

                if (count($applied_page_rules) > 0) {
                    $exclude_pages = array();

                    foreach ($applied_page_rules as $applied_page_rule) {

                        if ($applied_page_rule->action === "hide_page" ||
                                $applied_page_rule->action === "soft_page_hide") { // Page to hide
                            array_push($exclude_pages, $applied_page_rule);
                        }
                    }

                    RulesEngineCacheWrapper::set_session_attribute(BIS_EXCLUDE_PAGES, $exclude_pages);
                    RulesEngineCacheWrapper::set_session_attribute(BIS_APPEND_TO_PAGES, $applied_page_rules);
                }
            }
        }
    }

    /**
     * This method returns the excluded pages from DB if not found in session.
     *
     * @param $applied_rules
     * @return array of excluded page ids
     */
    function get_request_excluded_pages($applied_rules) {


        // Call the rules to get the excluded pages.
        $page_rule_modal = new PageRulesEngineModel();
        $exclude_pages_rules = $page_rule_modal->get_applied_page_rules($applied_rules);


        $excluded_page_ids = "";

        if ($applied_rules != null) {
            foreach ($applied_rules as $applied_rule) {

                if (isset($applied_rule->eval) && ($applied_rule->eval === true)) {
                    $exclude_page_id = RulesEngineUtil::get_exclude_page_id($exclude_pages_rules, $applied_rule->ruleId);
                    $excluded_page_ids = $exclude_page_id . $excluded_page_ids;
                }
            } // End of for loop
        } // End of If
        return $excluded_page_ids;
    }
                     

    /**
     * This method is used to redirect to home page when accessed directly of an excluded page.
     *
     * @param $content
     * @return string
     */
    function bis_re_apply_content_rule($content) {
        global $post;
        global $wp_query;
        
        $postID = $post->ID;
        
        // Logic to add page hits
        AnalyticsEngineModel::audit_page_request(RulesEngineUtil::get_audit_report_data_id(), $postID);

        $merged_append_pages = $this->get_all_page_rules();

        if ($merged_append_pages != null && count($merged_append_pages) > 0) {
            foreach ($merged_append_pages as $append_to_page) {
                if ($append_to_page->parent_id == $postID) {
                    $content = RulesDynamicContent::get_content($append_to_page, $content);
                    // Prevents hack for direct access of page using url
                    if (RulesEngineUtil::isEqual($append_to_page->action, "hide_page")) {
                        $this->bis_redirect_page($append_to_page->gencol3);
                    }
                }
            }
        }

        $post_rules_engine = new PostRulesEngine;
        $merged_append_posts = $post_rules_engine->get_all_post_rules();

        if ($merged_append_posts != null && count($merged_append_posts) > 0) {
            foreach ($merged_append_posts as $append_to_post) {
                if ($append_to_post->parent_id == $postID) {
                    $content = RulesDynamicContent::get_content($append_to_post, $content);
                }
            }
        }
        
        $current_page_id = $this->get_the_ID();
        
        if (RulesEngineUtil::is_woocommerce_installed() &&
                $current_page_id !== FALSE && is_shop()) {
            $current_page_id = (int) get_option('woocommerce_shop_page_id');
        }
        
        $referer_path = wp_get_referer();
        
        $current_category = get_the_category();
      
        $str_categories = RulesEngineUtil::getCommaSeperatedCategories($current_category);
        $woo_category_id = null;

        if (RulesEngineUtil::is_woocommerce_installed()) {
            
            // get the query object
            $cat_obj = $wp_query->get_queried_object();
            
            if($cat_obj) {
                $cat = get_query_var('cat');
                $category = get_category($cat);

                if (isset($cat_obj->term_id)) {
                    $woo_category_id = $cat_obj->term_id;
                    if ($woo_category_id != null) {
                        $str_categories = $woo_category_id;
                    }
                }
            }
        } else {
            if(isset($str_categories) && !empty($str_categories)) {
                $current_category = $str_categories;
            }
        }
 
        $hidden_page_id = '<input type="hidden" name="bis_re_cache_post_id" 
        id="bis_re_cache_post_id" value="'. $current_page_id .'" />';
        
        $hidden_cat_id = '<input type="hidden" name="bis_re_cache_cat_id" 
            id="bis_re_cache_cat_id" value="'. $str_categories .'" />';
        
        $hidden_refere_path = '<input type="hidden" name="bis_re_cache_reffer_path" 
            id="bis_re_cache_reffer_path" value="'. $referer_path .'" />';
        
        $hidden_site_url = '<input type="hidden" name="bis_re_site_url" 
            id="bis_re_site_url" value="'. get_site_url() .'" />';
        
        $content = $content.  $hidden_page_id . $hidden_cat_id. 
                $hidden_refere_path. $hidden_site_url;
        
        return $content;
    }

    /**
     * This method returns the pages that to be appended with content.
     *
     * @return array
     */
    public function get_request_pages_append_contents() {

        $applied_rules = $this->get_request_rules();
        // Call the rules to get the excluded pages.
        $page_rule_modal = new PageRulesEngineModel();
        $exclude_pages = $page_rule_modal->get_applied_page_rules($applied_rules);

        $content_append_pages = array();

        if ($applied_rules != null) {
            foreach ($applied_rules as $applied_rule) {
                if (isset($applied_rule->eval) && ($applied_rule->eval === true)) {
                    if ($exclude_pages != null) {
                        foreach ($exclude_pages as $exclude_page) {
                            if ($exclude_page->lrId == $applied_rule->ruleId) {
                                array_push($content_append_pages, $exclude_page);
                            }
                        }
                    }
                } // End of for loop
            } // End of If
        }
        return $content_append_pages;
    }

    /**
     * This method returns the excluded pages from DB if not found in session.
     *
     * @return array of excluded page ids
     */
    public function get_session_pages_append_contents() {
        $append_to_pages = $this->get_append_page_rules();

        if ($append_to_pages == null) {
            $this->init_applied_page_rules();
            $append_to_pages = $this->get_append_page_rules();
        }

        return $append_to_pages;
    }

    /**
     *
     * Redirects the page to the given url
     *
     * @param $redirect_url
     */
    public function bis_redirect_page($redirect_url) {
        ?>
        <script>
            window.location.href = '<?php echo $redirect_url; ?>';
        </script>
        <?php
        exit;
    }

    /**
     * This method will hide the menus based on the defined rules.
     *
     * @param  $items
     * @return $items
     */
    public function bis_re_apply_menu_rule($items) {
        $exclude_pages = $this->get_excluded_page_ids();

        if ($exclude_pages != null && count($exclude_pages) > 0) {
            $exclude_pages_array = explode(",", $exclude_pages);

            if (in_array($items->object_id, $exclude_pages_array)) {
                $items->_invalid = true;
            }
        }

        return $items;
    }

    /**
     * This method is used to append content to page or page using shortcodes.
     *
     * @param $atts
     * @param null $title
     * @internal param null $content
     * @return string
     */
    public function bis_page_rule_shortcode($atts, $title = null) {
        global $post;

        $postID = $post->ID;
        $content = "";

        $merged_append_pages = $this->get_all_page_rules();

        extract(shortcode_atts(array(
            'rulename' => '',
            'ruletype' => ''
                        ), $atts));

        if ($merged_append_pages != null && count($merged_append_pages) > 0) {

            // Logic to check if shortcode
            foreach ($merged_append_pages as $append_to_page) {

                if (!($append_to_page->action === "hide_page" ||
                        $append_to_page->action === "replace_page_content" ||
                        $append_to_page->action === "soft_page_hide")) {

                    $location = json_decode($append_to_page->gencol2)->content_position;

                    if ($location === "pos_cust_scode_page" && $append_to_page->parent_id == $postID && $append_to_page->crulename === $rulename) {
                        if ($append_to_page->action === "append_existing_scode_page") {
                            $content = do_shortcode(stripslashes($append_to_page->gencol1));
                        } else {
                            $content = RulesDynamicContent::get_dynamic_content($append_to_page, $title);
                        }
                    }
                }
            }
        }

        return $content;
    }

    /**
     * This method is used to get all page rules.
     *
     * @return array
     */
    public function get_all_page_rules() {

        $append_to_pages_request = $this->get_request_pages_append_contents();
        $append_to_pages = $this->get_session_pages_append_contents();

        $merged_append_pages = $append_to_pages_request;

        if ($merged_append_pages != null) {
            if ($append_to_pages != null) {
                $merged_append_pages = array_merge($merged_append_pages, $append_to_pages);
            }
        } else {
            $merged_append_pages = $append_to_pages;
        }

        return $merged_append_pages;
    }

}