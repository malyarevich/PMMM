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
use bis\repf\model\AnalyticsEngineModel;
use bis\repf\util\BISAnalyticsUtil;

add_action('wp_ajax_bis_generate_report', 'bis_generate_report');
add_action('wp_ajax_bis_country_requests', 'bis_country_requests');
add_action('wp_ajax_bis_device_requests', 'bis_device_requests');
add_action('wp_ajax_bis_redirect_analytics', 'bis_redirect_analytics');
add_action('wp_ajax_bis_redirect_device_type', 'bis_redirect_device_type');
add_action('wp_ajax_bis_page_views', 'bis_page_views');
add_action('wp_ajax_bis_unique_visitor', 'bis_unique_visitor');

/**
 * This method shows the add new rule page.
 *
 */
function bis_unique_visitor() {

    $nonce = $_GET ['bis_nonce'];

    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();

    $pluginPath = RulesEngineUtil::getIncludesDirPath();
    include $pluginPath . "bis-unique-visitor-analytics.php";

    flush();
    exit;
}

/**
 * This method shows the add new rule page.
 *
 */
function bis_page_views() {

    $nonce = $_GET ['bis_nonce'];


    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();

    $pluginPath = RulesEngineUtil::getIncludesDirPath();
    include $pluginPath . "bis-page-views-analytics.php";

    flush();
    exit;
}

/**
 * This method shows the add new rule page.
 *
 */
function bis_redirect_device_type() {

    $nonce = $_GET ['bis_nonce'];


    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();

    $pluginPath = RulesEngineUtil::getIncludesDirPath();
    include $pluginPath . "bis-redirect-device-type-analytics.php";

    flush();
    exit;
}

/**
 * This method shows the add new rule page.
 *
 */
function bis_redirect_analytics() {

    $nonce = $_GET ['bis_nonce'];


    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();

    $pluginPath = RulesEngineUtil::getIncludesDirPath();
    include $pluginPath . "bis-redirect-analytics.php";

    flush();
    exit;
}

/**
 * This method shows the add new rule page.
 *
 */
function bis_country_requests() {

    $nonce = $_GET ['bis_nonce'];


    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();

    $pluginPath = RulesEngineUtil::getIncludesDirPath();
    include $pluginPath . "bis-country-analytics.php";

    flush();
    exit;
}

/**
 * This method shows the add new rule page.
 *
 */
function bis_device_requests() {

    $nonce = $_GET ['bis_nonce'];


    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce'))
        RulesEngineUtil::handle_request_forgery_error();

    $pluginPath = RulesEngineUtil::getIncludesDirPath();
    include $pluginPath . "bis-device-analytics.php";

    flush();
    exit;
}

function bis_generate_report() {
    $nonce = $_POST ['bis_nonce'];

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $bis_report_type_id = $_POST['bis_report_type_id'];
    $gen_report_type = $_POST['bis_re_generate'];
    $bis_report_id = $_POST['bis_report_id'];

    if ($gen_report_type === BIS_REPORT_CURRENT_MONTH) {
        $bis_re_from_date = BISAnalyticsUtil::get_current_month_first_day();
        $bis_re_to_date = BISAnalyticsUtil::get_current_month_last_day();
    } else {
        $bis_re_to_date = $_POST ['bis_re_to_date'];
        $bis_re_from_date = $_POST ['bis_re_from_date'];
    }

    $analyticsEngineModel = new AnalyticsEngineModel();

    $results_map = Array();
    switch ($bis_report_type_id) {
        case 'bis_page_views' :
            $pages = null;
            if (isset($_POST['bis_re_pageview_pages'])) {
                $pages = $_POST['bis_re_pageview_pages'];
            }
            $results_map = $analyticsEngineModel->get_page_views($pages, $bis_re_from_date, $bis_re_to_date);
            break;
            
        case 'bis_unique_visitors' :
            $pages = null;
            if (isset($_POST['bis_re_pageview_pages'])) {
                $pages = $_POST['bis_re_pageview_pages'];
            }
            $results_map = $analyticsEngineModel->get_unique_visitors($pages, $bis_re_from_date, $bis_re_to_date);
            break;
            
        case 'bis_country_requests':  
            $results_map = $analyticsEngineModel->get_requests_by_country($bis_re_from_date, $bis_re_to_date);
            break;
        
        case 'bis_device_requests':
            $results_map = $analyticsEngineModel->get_requests_by_device($bis_re_from_date, $bis_re_to_date);
            break;
        
        case 'bis_device_redirects':
            $results_map = $analyticsEngineModel->get_redirects_by_device($bis_re_from_date, $bis_re_to_date);
            break;
        
        case 'bis_device_manu_redirects':
            $results_map = $analyticsEngineModel->get_redirects_by_manufacturer($bis_re_from_date, $bis_re_to_date);
            break;
    }

    RulesEngineUtil::generate_json_response($results_map);
}
