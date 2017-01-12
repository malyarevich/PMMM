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
use bis\repf\model\PostRulesEngineModel;

use RulesEngineUtil;

/**
 * Class PostRulesEngine
 */
class PostRulesEngine extends BaseRulesEngine
{


    /**
     * This method is used to exclude posts.
     *
     * @param $query
     * @return mixed
     */
    public function bis_re_exclude_posts($query)
    {

        $excluded_posts_id_array = $this->bis_re_get_excluded_post_ids();
        $this->evaluate_request_post_rules();
        $applied_request_rules = $this->get_request_rules();
        $merged_excluded_posts = null;
        $applied_request_post_rules = null;

        if ($applied_request_rules != null && count($applied_request_rules) > 0) {
            $post_rule_modal = new PostRulesEngineModel ();
            $applied_request_post_rules = $post_rule_modal->get_applied_post_rules($applied_request_rules);
        }

        if (count($excluded_posts_id_array) > 0) {
            $merged_excluded_posts = $excluded_posts_id_array;
        }

        if ($applied_request_post_rules != null && count($applied_request_post_rules) > 0) {
            $excluded_posts_id_req_array = RulesEngineUtil::get_applied_post_rule_ids($applied_request_post_rules);

            if ($merged_excluded_posts == null) {
                $merged_excluded_posts = $excluded_posts_id_req_array;
            } else {
                $merged_excluded_posts = array_merge($merged_excluded_posts, $excluded_posts_id_req_array);
            }
        }

        // Add to session if only post rules exists
        if (count($merged_excluded_posts) > 0) {
            $query->set('post__not_in', $merged_excluded_posts);
        }

        return $query;

    }

    /**
     * This method is used to get the excluded post ids.
     * @return array
     */
    public function bis_re_get_excluded_post_ids()
    {
        
        $excluded_post_rules =  $this->get_posts_rules();

        if ($excluded_post_rules == null) {
            $this->init_applied_post_rules();
            $excluded_post_rules =  $this->get_posts_rules();
        }

        $excluded_posts_id_array = RulesEngineUtil::get_applied_post_rule_ids($excluded_post_rules);

        return $excluded_posts_id_array;
    }

    private function init_applied_post_rules()
    {

        // Check if excluded posts exists in session.
        $excluded_post_rules = $this->get_posts_rules();

        // If excluded post does not exist in session, then get from DB.
        if ($excluded_post_rules == null) {
            $excluded_post_rules = array();
            $applied_post_rules = array();
            $applied_rules = $this->get_applied_logical_rules();



            if ($applied_rules != null && count($applied_rules) > 0) {
                // Call the rules to get the excluded posts.
                $post_rule_modal = new PostRulesEngineModel ();
                $applied_post_rules = $post_rule_modal->get_applied_post_rules($applied_rules);

                if ($applied_post_rules != null && count($applied_post_rules) > 0) {
                    foreach ($applied_post_rules as $applied_post_rule) {
                        if ($applied_post_rule->action == "hide_post") {
                            array_push($excluded_post_rules, $applied_post_rule);
                        }
                    }
                }

            }
            
            RulesEngineCacheWrapper::set_session_attribute(BIS_EXCLUDE_POSTS, $excluded_post_rules);
            RulesEngineCacheWrapper::set_session_attribute(BIS_APPEND_TO_POSTS, $applied_post_rules);

        }
    }

    public function bis_re_get_appended_post_ids()
    {

        $appended_post_rules = RulesEngineCacheWrapper::get_session_attribute(BIS_APPEND_TO_POSTS);

        if ($appended_post_rules == null) {
            $this->init_applied_post_rules();
            $appended_post_rules = RulesEngineCacheWrapper::get_session_attribute(BIS_APPEND_TO_POSTS);
        }

        return $appended_post_rules;
    }

    /**
     * This method is used to evaluate post rules per request
     * @return null
     */
    public function evaluate_request_post_rules($shop_id=null)
    {

        $current_post_id = $this->get_the_ID();
       
        if($shop_id != null) {
            $current_post_id = (int)$shop_id;
        }
         
        // Null indicates that rule evaluation dependencies are not loaded.
        // Call the is_valid method after loading dependent files.

        if ($current_post_id == false) {
            return null;
        }

        $applied_rules = $this->get_request_rules();

        if ($applied_rules != null && count($applied_rules) > 0) {

            foreach ($applied_rules as $applied_rule) {

                if (RulesEngineUtil::isContains($applied_rule->expression, BIS_RULE_POST_EXPRESSION_APPEND)) {
                     
                    $expression = explode(" ", $applied_rule->expression);

                    foreach ($expression as $key => $value) {

                        if (RulesEngineUtil::isContains($value, BIS_RULE_POST_EXPRESSION_APPEND)) {
                            $rule_values = explode("$", $value);
                            $rule_post_id = (int)$rule_values[1];
                            $condId = (int)$rule_values[2];

                            $eval = RulesEngineUtil::evaluateIntTypeRule($current_post_id, $rule_post_id, $condId);

                            // Rule evaluation is completed, below code is specific to page rule.
                            // If rule page Id and curent page Id is equal.
                            // Check whether the page Id is part of the active page rule.
                            if ($eval) {
                                $eval = "T";
                            } else {
                                $eval = "F";
                            }


                            $expression[$key] = $eval;

                        }

                    } // End of express for loop

                    $eval_expression = implode(" ", $expression);

                    $applied_rule->eval = $eval_expression;

                    if (!RulesEngineUtil::isContains($eval_expression, "X")) {
                        $eval_expression = RulesEngine::evaluate_expression($eval_expression);

                        // If expression is true call hook;
                        if ($eval_expression) {
                            RulesEngine::call_hook($applied_rule);
                        }

                        $applied_rule->eval = $eval_expression;

                    } // End of if

                } // End of applied for loop

            }

            RulesEngineUtil::set_request_rules($applied_rules);
        } // End of If


    }

    /**
     * This method returns the pages that to be appended with content.
     *
     * @return array
     */
    public function get_request_post_append_contents()
    {
        $applied_rules = $this->get_request_rules();
        // Call the rules to get the excluded pages.
        $post_rule_modal = new PostRulesEngineModel();
        $applied_post_rules = $post_rule_modal->get_applied_post_rules($applied_rules);

        $content_append_posts = array();

        if ($applied_rules != null) {
            foreach ($applied_rules as $applied_rule) {
                if (isset($applied_rule->eval) && ($applied_rule->eval === true)) {
                    if($applied_post_rules != null) {
                        foreach ($applied_post_rules as $applied_post_rule) {
                            if ($applied_post_rule->lrId == $applied_rule->ruleId) {
                                array_push($content_append_posts, $applied_post_rule);
                            }
                        }
                    }
                } // End of for loop
            } // End of If
        }
        return $content_append_posts;
    }

    /**
     * This method returns the excluded post from DB if not found in session.
     *
     * @return array of excluded post ids
     */
    public function get_session_post_append_contents()
    {
        $append_to_posts = RulesEngineCacheWrapper::get_session_attribute(BIS_APPEND_TO_POSTS);

        if ($append_to_posts == null) {
            $this->get_applied_post_rules();
        }

        return $append_to_posts;
    }

    /**
     * This method returns the excluded posts from DB if not found in session.
     *
     * @return array of excluded post ids
     */
    public function bis_re_get_request_excluded_rule_posts()
    {

        // Check if excluded posts exists in session.
        $exclude_posts_rules =  RulesEngineCacheWrapper::get_session_attribute(BIS_EXCLUDE_REQUEST_RULE_POSTS);
        
        $applied_rules = $this->get_request_rules();
        $rule_count = count($applied_rules);

        // If excluded post does not exist in session, then get from DB.
        if ($exclude_posts_rules == null && $rule_count > 0) {

            // Call the rules to get the excluded posts.
            $post_rule_modal = new PostRulesEngineModel();
            $exclude_posts_rules = $post_rule_modal->get_applied_post_rules($applied_rules);

            if (count($exclude_posts_rules) > 0) {
                RulesEngineCacheWrapper::
                        set_session_attribute(BIS_EXCLUDE_REQUEST_RULE_POSTS, 
                                $exclude_posts_rules);
            }

        }

    }

    /**
     * This method is used to append content to post or post using shortcodes.
     *
     * @param $atts
     * @param null $title
     * @internal param null $content
     * @return string
     */
     public function bis_post_rule_shortcode($atts, $title = null) {
        global $post;

        $postID = $post->ID;
        $content = "";

        $merged_append_posts = $this->get_all_post_rules();

        extract(shortcode_atts( array(
            'rulename' => '',
            'ruletype' => ''
        ), $atts));

        if ($merged_append_posts != null && count($merged_append_posts) > 0) {

            // Logic to check if shortcode
            foreach ($merged_append_posts as $append_to_post) {

                if(!($append_to_post->action === "hide_post" ||
                    $append_to_post->action === "replace_post_content")) {

                    $location = json_decode($append_to_post->gencol2)->content_position;

                    if (($location === "pos_cust_scode_post" ||
                            $location === "pos_cust_scode_post") && $append_to_post->parent_id == $postID
                        && $append_to_post->crulename === $rulename) {
                        if($append_to_post->action === "append_existing_scode_post") {
                            $content = do_shortcode(stripslashes($append_to_post->gencol1));
                        } else {
                            $content = RulesDynamicContent::get_dynamic_content($append_to_post, $title);
                        }
                    }
                }

            }
        }

        return $content;
    }

    /**
     * This method is used to get all the applied post rules.
     *
     * @return array
     */
     public function get_all_post_rules() {

        $post_request_rules = $this->get_request_post_append_contents();
        $append_to_posts = $this->bis_re_get_appended_post_ids();

        $merged_append_posts = $post_request_rules;

        if ($merged_append_posts != null) {
            if ($append_to_posts != null) {
                $merged_append_posts = array_merge($merged_append_posts, $append_to_posts);
            }
        } else {
            $merged_append_posts = $append_to_posts;
        }

        return $merged_append_posts;
    }

}