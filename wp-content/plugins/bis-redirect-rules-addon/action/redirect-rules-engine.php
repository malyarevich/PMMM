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

use bis\repf\action\BaseRulesEngine;
use bis\repf\action\RulesEngine;
use bis\repf\common\RulesEngineCacheWrapper;
use bis\repf\model\AnalyticsEngineModel;

/**
 * Class RedirectRulesEngine
 */
class RedirectRulesEngine extends BaseRulesEngine {

    public function bis_evaluate_request_rules() {


        $applied_rules = $this->get_request_rules();

        if ($applied_rules != null && count($applied_rules) > 0) {
            
            $current_page_id = parent::get_the_ID();
            $current_path = RulesEngineUtil::get_current_url();
            
            if ($current_page_id == NULL) {
                $current_page_id = url_to_postid($current_path[0]);
            }
            
            if($current_page_id == 0) {
                $home_page_url = get_home_url();
                
                if(!RulesEngineUtil::endsWith($home_page_url, "/")) {
                    $home_page_url = $home_page_url ."/";
                }
                
                if(in_array($home_page_url, $current_path)) {
                    $current_page_id = get_option('page_on_front');
                }
            }
            
            $categoryId = null;
            $referer_path = wp_get_referer();
            global $wp_query;

            $cacheVO = new \bis\repf\vo\CacheVO($current_page_id, $categoryId, $referer_path);
            $rulesEngine = new RulesEngine();
            $rulesEngine->bis_evaluate_request_rules($wp_query, $cacheVO);

        } // End of If 

    }

    public function apply_session_redirect_rules() {
        $applied_rules = $this->get_applied_session_logical_rules();

        if (RulesEngineUtil::is_redirect() && count($applied_rules) > 0) {

            $redirect_rules_modal = new RedirectRulesEngineModel();
            $rows = $redirect_rules_modal->get_redirect_applied_rule_details($applied_rules);
            RulesEngineCacheWrapper::set_session_attribute(BIS_REDIRECT_SESSION_RULE_EVALUATED, TRUE);

            if (!empty($rows)) {
                $redirect_rule = $rows[0];
                $redirect_val = json_decode($redirect_rule->action);
                AnalyticsEngineModel::audit_redirect_request(RulesEngineUtil::get_audit_report_data_id());
                $red_cookie = null;
                
                if (isset($_COOKIE[BIS_REDIRECT_COOKIE])) {
                    $red_cookie = $_COOKIE[BIS_REDIRECT_COOKIE];
                }
                
                RulesEngineCacheWrapper::set_session_attribute(BIS_REDIRECT_POPUP_VO, $redirect_rule);
                
                if (($redirect_rule->showpopup == NULL) || ($red_cookie === BIS_REDIRECT_COOKIE_REDIRECT)) {
                        wp_redirect($redirect_val->target_url, $redirect_val->redirect_type);
                    exit; // Exit from page after redirect
                } 
            }
        }
    }

    public function bis_re_show_redirect_modal($content) {

        $dynamicContent = RulesEngineUtil::get_option(BIS_REDIRECT_POPUP_TEMPLATE);
        $redirectMetaTemplate = RulesEngineUtil::get_option(BIS_REDIRECT_META_TEMPLATE);

        if ($dynamicContent !== FALSE) {
            $content = $content . '<div id="bis_re_modal_div"></div>' . $dynamicContent;
        }

        if ($redirectMetaTemplate !== FALSE) {
            $content = $content . '<span id = "bis_re_rd_meta_span"></span>' . $redirectMetaTemplate;
        }

        return $content;
    }

}
?>