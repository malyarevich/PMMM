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

namespace bis\repf\model;

use RulesEngineUtil;
use bis\repf\vo\AuditReportVO;
use bis\repf\util\GeoPluginWrapper;
use bis\repf\util\uagent_info;

/**
 * This class is a base model for all models.
 *
 */
class AnalyticsEngineModel {

    public function get_requests_by_device($from = null, $to = null) {
        global $wpdb;
        $blog_id = get_current_blog_id();

        $range = "";

        // $pages = '2';
        if ($from != null & $to != null) {
            $range = " and  date(request_time) >=  '" . $from . "' AND date(request_time) <= '" . $to . "' ";
        }

        $pre_query = "SELECT device, COUNT(device) as count FROM `bis_re_report_data` where site_id = " . $blog_id .$range." and report_type_id = 1 GROUP BY device";
        $rows = $wpdb->get_results($pre_query);
   
        $results_map[BIS_DATA] = $rows;
        
        if (count($rows) > 0) {
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;
        }
        
        return $results_map;
    }

    public function get_requests_by_country($from = null, $to = null) {
        global $wpdb;
        $blog_id = get_current_blog_id();

        $range = "";

        // $pages = '2';
        if ($from != null & $to != null) {
            $range = " and  date(request_time) >=  '" . $from . "' AND date(request_time) <= '" . $to . "' ";
        }

        $pre_query = "SELECT country, COUNT(country) AS requests FROM `bis_re_report_data` where site_id = " . $blog_id . $range . " and report_type_id = 1 GROUP BY country;";
        $rows = $wpdb->get_results($pre_query);

        $results_map[BIS_DATA] = $rows;
        
        if (count($rows) > 0) {
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;
        }

        return $results_map;
    }

    public function get_requests_by_manufacturer() {
        global $wpdb;
        $blog_id = get_current_blog_id();

        $pre_query = "SELECT manufacturer, COUNT(manufacturer) AS COUNT FROM `bis_re_report_data` where site_id = " . $blog_id . " GROUP BY manufacturer;";
        $rows = $wpdb->get_results($pre_query);

        return $rows;
    }

    public function get_redirects_by_manufacturer($from = null, $to = null) {
        global $wpdb;
        $blog_id = get_current_blog_id();
        
        $range = "";

        // $pages = '2';
        if ($from != null & $to != null) {
            $range = " and  date(request_time) >=  '" . $from . "' AND date(request_time) <= '" . $to . "' ";
        }

        $pre_query = "SELECT manufacturer, COUNT(manufacturer) AS count FROM `bis_re_report_data` "
                . " WHERE report_type_id = 3  AND site_id = " . $blog_id .$range. " GROUP BY manufacturer;";
        $rows = $wpdb->get_results($pre_query);
 
        $results_map[BIS_DATA] = $rows;
        
        if (count($rows) > 0) {
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;
        }

        return $results_map;
    }

    public function get_redirects_by_country($from = null, $to = null) {
        global $wpdb;
        $blog_id = get_current_blog_id();

        $pre_query = "SELECT country, COUNT(country) AS count FROM `bis_re_report_data` "
                . " WHERE report_type_id = 3  AND site_id = " . $blog_id . "  GROUP BY country;";
        $rows = $wpdb->get_results($pre_query);

        return $rows;
    }

    public function get_redirects_by_device($from = null, $to = null) {
        global $wpdb;
        $blog_id = get_current_blog_id();

        $range = "";

        // $pages = '2';
        if ($from != null & $to != null) {
            $range = " and  date(request_time) >=  '" . $from . "' AND date(request_time) <= '" . $to . "' ";
        }

        $pre_query = "SELECT device, COUNT(device) AS count FROM `bis_re_report_data` "
                . "WHERE report_type_id = 3 AND site_id = " . $blog_id .$range. " GROUP BY device;";
        $rows = $wpdb->get_results($pre_query);

        $results_map[BIS_DATA] = $rows;
        
        if (count($rows) > 0) {
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;
        }

        return $results_map;
    }

    public function get_page_views($pages = null, $from = null, $to = null, $post_type = 'page') {
        global $wpdb;
        $blog_id = get_current_blog_id();

        $range = "";
        $page_cond = "";

        $posts_table = $wpdb->prefix . "posts";

        if ($pages != null) {
            $pages = implode(",", $pages);
            $page_cond = "and " . $posts_table . ".ID in (" . $pages . ")";
        }

        // $pages = '2';
        if ($from != null & $to != null) {
            $range = "  date(request_time) >=  '" . $from . "' AND date(request_time) <= '" . $to . "' ";
        }


        $pre_query = "SELECT COUNT(post_id) as sample, DATE_FORMAT(request_time, '%Y%m%d') as pdate  FROM `bis_re_report_data` " .
                " JOIN ".$posts_table." ON post_id = " . $posts_table . ".ID AND site_id = " . $blog_id .
                " WHERE " . $range . "" . $page_cond . " GROUP BY DATE_FORMAT(request_time, '%m-%d-%Y');";

       
        $rows = $wpdb->get_results($pre_query);

        $results_map[BIS_DATA] = $rows;
        
        if (count($rows) > 0) {
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;
        }

        return $results_map;
    }

    public static function get_unique_visitors($pages = null, $from = null, $to = null, $post_type = 'page') {
        global $wpdb;
        $blog_id = get_current_blog_id();

        $range = "";
        $page_cond = "";

        $posts_table = $wpdb->prefix . "posts";

        if ($pages != null) {
            $pages = implode(",", $pages);
            $page_cond = "and " . $posts_table . ".ID in (" . $pages . ")";
        }

        if ($from != null & $to != null) {
            $range = " AND date(request_time) >=  '" . $from . "' AND date(request_time) <= '" . $to . "' ";
        }

        $pre_query = "SELECT DATE_FORMAT(request_time, '%Y%m%d') AS pdate, 
            COUNT(DISTINCT(ipaddress)) AS sample FROM `bis_re_report_data` WHERE report_type_id = 4 " .
                $range . " GROUP BY DATE_FORMAT(request_time, '%m-%d-%Y');";

        $rows = $wpdb->get_results($pre_query);
      
        $results_map[BIS_DATA] = $rows;
        
        if (count($rows) > 0) {
            $results_map[BIS_STATUS] = BIS_SUCCESS;
        } else {
            $results_map[BIS_STATUS] = BIS_SUCCESS_WITH_NO_DATA;
        }

        return $results_map;
    }

    public static function get_post_views($from, $to) {
        return AnalyticsEngineModel::get_page_views($from, $to, 'post');
    }

    public static function audit_user_login($auditVo) {
        global $wpdb;

        $table = "bis_re_report_auth";
        $user = $auditVo->getUser();
        $data = array('userid' => $user->user_login, 'user_role' => json_encode($user->roles)
            , 'report_data_id' => $auditVo->getId(), 'login' => current_time('mysql'));

        $status = $wpdb->insert($table, $data, array("%s", "%s", "%d", "%s"));

        if ($status) {
            $auditVo->setReportAuthId($wpdb->insert_id);
        }

        return $auditVo;
    }

    public static function audit_user_logout($auth_id) {
        global $wpdb;

        $table = "bis_re_report_auth";
        $data = array('logout' => current_time('mysql'));

        $where = array('id' => $auth_id);
        $status = $wpdb->update($table, $data, $where, array("%s"), array("%d"));

        return $status;
    }

    public static function audit_redirect_request($parent_request_id) {
        
        AnalyticsEngineModel::audit_user_request(3, $parent_request_id);
    }

    public static function audit_page_request($parent_request_id, $post_id) {
         AnalyticsEngineModel::audit_user_request(4, $parent_request_id, $post_id);
    }

    public static function audit_404_error($parent_request_id) {
        AnalyticsEngineModel::audit_user_request(2, $parent_request_id);
    }

    public static function audit_user_request($report_type_id, $parent_request_id = 0, $post_id = 0, $term_taxonomy_id = 0) {
        $bis_capture_analytics = RulesEngineUtil::get_option(BIS_CAPTURE_ANALYTICS_DATA);
        
        if($bis_capture_analytics === "true") {
            AnalyticsEngineModel::store_audit_report($report_type_id, $parent_request_id, $post_id, $term_taxonomy_id);
        }
    }
   
    public static function store_audit_report($report_type_id, $parent_request_id = 0, $post_id = 0, $term_taxonomy_id = 0) {

        global $wpdb;

        $auditVo = null;
        $geo_plugin = new GeoPluginWrapper();
        $uagent_info = new uagent_info();

        $mobile = RulesEngineUtil::get_mobile($uagent_info);
        $manufacturer = "desktop";

        $device = "desktop";

        if ($mobile != null) {
            $device = "mobile";
            $manufacturer = $mobile;
        } else {
            $tablet = RulesEngineUtil::get_tablet($uagent_info);
            if ($tablet != null) {
                $device = "tablet";
                $manufacturer = $tablet;
            }
        }

        $country = $geo_plugin->getCountryName();

        if (empty($country)) {
            $country = "other";
        }
        $blog_id = get_current_blog_id();

        $table = "bis_re_report_data";
        $data = array('country' => $country, 'region' => $geo_plugin->getRegion(),
            'city' => $geo_plugin->getCity(), 'ipaddress' => $geo_plugin->getClientIP(),
            'browser' => RulesEngineUtil::get_client_browser(), 'device' => $device,
            'manufacturer' => $manufacturer, 'report_type_id' => $report_type_id,
            'parent_report_data_id' => $parent_request_id, 'site_id' => $blog_id
            , 'post_id' => $post_id, 'term_taxonomy_id' => $term_taxonomy_id);

        $status = $wpdb->insert($table, $data, array("%s", "%s", "%s", "%s",
            "%s", "%s", "%s", "%d", "%d", "%d", "%d"));

        $report_id = null;

        if ($status) {
            $auditVo = new AuditReportVO();
            $auditVo->setId($wpdb->insert_id);
            $auditVo->setDevice($device);
            $auditVo->setManufacturer($manufacturer);
            $auditVo->setIpAddress($geo_plugin->getClientIP());
            $auditVo->setBrowser(RulesEngineUtil::get_client_browser());
            $auditVo->setCountry($geo_plugin->getCountryName());
            $auditVo->setRegion($geo_plugin->getRegion());
            $auditVo->setCity($geo_plugin->getCity());
            $auditVo->setPostId($post_id);
            $auditVo->setTermTaxonomyId($term_taxonomy_id);
        }

        return $auditVo;
    }

}
