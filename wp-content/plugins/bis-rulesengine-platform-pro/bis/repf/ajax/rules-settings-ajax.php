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

add_action('wp_ajax_bis_re_save_settings', 'bis_re_save_settings');
add_action('wp_ajax_bis_re_activate_plugin', 'bis_re_activate_plugin');
add_action('wp_ajax_bis_re_acplg_error', 'bis_re_acplg_error');

function bis_re_acplg_error() {

    $nonce = $_POST ['bis_nonce'];
    $product_id = $_POST ['product_id'];
    $purchase_code = $_POST ['pur_code'];

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $domain_name = $_SERVER['SERVER_NAME'];
    $subject = $product_id . ' : ' . $domain_name . ' : Invalid purchase code used';
    $msg_body = "Product Id : " . $product_id . "\n Domain Name : " . $domain_name .
            "\n Is using an invalid purchase code. \n ."
            . "Purchase Code : " . $purchase_code;

    wp_mail(RULES_ENGINE_MAIL, $subject, $msg_body);

    $results_map = array();
    $results_map[BIS_STATUS] = BIS_SUCCESS;
    RulesEngineUtil::generate_json_response($results_map);
}

function bis_re_activate_plugin() {
    $nonce = $_POST ['bis_nonce'];
    $item_name = $_POST ['item_name'];
    $license = $_POST ['licence_type'];
    $buyer = $_POST ['buyer'];
    $product_id = $_POST ['product_id'];
    $purchase_code = $_POST ['pur_code'];
    $purchase_date = $_POST ['purchase_date'];
    

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $bis_re_prd_vrf = RulesEngineUtil::get_purchase_code($product_id);
    $bis_re_pur_key = BIS_PUR_CODE . $product_id;

    if ($bis_re_prd_vrf == false || empty($bis_re_prd_vrf)) {
        RulesEngineUtil::add_option($bis_re_pur_key, $purchase_code);
    } else {
        RulesEngineUtil::update_option($bis_re_pur_key, $purchase_code);
    }


    $domain_name = $_SERVER['SERVER_NAME'];
    $subject = $item_name . ' : ' . $license . ' Domain : ' . $domain_name;
    $msg_body = "Customer Name : " . $buyer . "\nItem Name : " . $item_name .
            "\nLicense : " . $license . "\nDomain : " . $domain_name .
            "\nPurchase Code : " . $purchase_code .
            "\nPurchase Date : ". $purchase_date;

    wp_mail(RULES_ENGINE_MAIL, $subject, $msg_body);

    $results_map = array();
    $results_map[BIS_STATUS] = BIS_SUCCESS;
    RulesEngineUtil::generate_json_response($results_map);
}

function copy_country_db($fileName, $directory) {

    $plugin_dir = plugin_dir_path(__FILE__) . 'library/front-page.php';
    $theme_dir = get_stylesheet_directory() . '/front-page.php';

    if (!copy($plugin_dir, $theme_dir)) {
        echo "failed to copy $plugin_dir to $theme_dir...\n";
    }
}


function bis_re_save_settings() {
    $nonce = $_POST ['bis_nonce'];

    // generated nonce we created earlier
    if (!wp_verify_nonce($nonce, 'bis_rules_engine_nonce')) {
        RulesEngineUtil::handle_request_forgery_error();
    }

    $bis_re_delete_db = false;
    $bis_re_del_plugin = false;
    $bis_re_enable_analytics = false;
    $bis_re_cache_enabled = false;
    
    
    $bis_geo_maxmind_db_file = $_POST['bis_geo_maxmind_db_file'];
    $bis_geolocation_db = 0;
    $bis_geolocation_ws = 0;

    if(isset($_POST['bis_geolocation_db'])) {
        $bis_geolocation_db = $_POST['bis_geolocation_db'];
    }
   
    if(isset($_POST['bis_geolocation_ws'])){
        $bis_geolocation_ws = $_POST['bis_geolocation_ws'];
    }
    $results_map = array();
    
    if($bis_geolocation_db == 1) { // validate file only if maxmind db selected
        try {
            $filePath = RulesEngineUtil::get_file_upload_path() . $bis_geo_maxmind_db_file;
            $reader = new bis\repf\MaxMind\Db\Reader($filePath);
            RulesEngineUtil::update_option(BIS_GEO_MAXMIND_DB_FILE, $bis_geo_maxmind_db_file);
        } catch (Exception $ex) {
            $results_map[BIS_STATUS] = BIS_ERROR;
            $results_map[BIS_MESSAGE_KEY] = BIS_INVALID_DATABASE_FILE;
            RulesEngineUtil::generate_json_response($results_map);
        }
    }
    
    RulesEngineUtil::update_option(BIS_GEO_LOOKUP_TYPE, $bis_geolocation_db);
    RulesEngineUtil::update_option(BIS_GEO_LOOKUP_WEBSERVICE_TYPE, $bis_geolocation_ws);
    

    if (isset($_POST ["bis_re_delete_db"])) {
        $bis_re_delete_db = $_POST ["bis_re_delete_db"];
    }

    if (isset($_POST ["bis_re_del_plugin"])) {
        $bis_re_del_plugin = $_POST ["bis_re_del_plugin"];
    }
    
    if (isset($_POST ["bis_re_enable_analytics"])) {
        $bis_re_enable_analytics = $_POST ["bis_re_enable_analytics"];
    }
    
    if (isset($_POST ["bis_re_cache_enabled"])) {
        $bis_re_cache_enabled = $_POST ["bis_re_cache_enabled"];
    }

    if ($bis_re_delete_db === "on") {
        RulesEngineUtil::update_option(BIS_RULES_ENGINE_DELETE_DB, "true");
    } else {
        RulesEngineUtil::update_option(BIS_RULES_ENGINE_DELETE_DB, "false");
    }

    if ($bis_re_del_plugin === "on") {
        RulesEngineUtil::update_option(BIS_RULES_ENGINE_PLUGIN_FORCE_DELETE, "true");
    } else {
        RulesEngineUtil::update_option(BIS_RULES_ENGINE_PLUGIN_FORCE_DELETE, "false");
    }
    
    if ($bis_re_enable_analytics === "on") {
        RulesEngineUtil::update_option(BIS_CAPTURE_ANALYTICS_DATA, "true");
    } else {
        RulesEngineUtil::update_option(BIS_CAPTURE_ANALYTICS_DATA, "false");
    }
    
    if ($bis_re_cache_enabled === "on") {
        RulesEngineUtil::update_option(BIS_RULES_ENGINE_CACHE_INSTALLED, "true");
    } else {
        RulesEngineUtil::update_option(BIS_RULES_ENGINE_CACHE_INSTALLED, "false");
    }

    if (isset($_POST ["geonameuser"])) {
        RulesEngineUtil::update_option(BIS_GEO_NAME_USER, $_POST ["geonameuser"]);
    }

    $bis_re_allowable_tags = $_POST ["bis_re_allowable_tags"];
    RulesEngineUtil::update_option(BIS_RULES_ENGINE_ALLOWABLE_TAGS_CONST, $bis_re_allowable_tags);

    $results_map = array();

    $results_map[BIS_DATA] = RulesEngineUtil::get_option(BIS_RULES_ENGINE_DELETE_DB);
    $results_map[BIS_STATUS] = BIS_SUCCESS;
    RulesEngineUtil::generate_json_response($results_map);
}