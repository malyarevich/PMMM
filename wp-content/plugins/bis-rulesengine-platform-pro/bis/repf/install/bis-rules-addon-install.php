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

namespace bis\repf\install;

use bis\repf\vo\PluginVO;
use RulesEngineUtil;

define("BIS_RULESENGINE_ADDONS_CONST", "BIS_RULESENGINE_ADDONS");

class RulesEngineAddOn {

    public static function bis_addon_activation($addOnVO) {
        // First time install
        $plugin_jsonObj = RulesEngineUtil::get_option(BIS_RULESENGINE_ADDONS_CONST);
        
        if(RulesEngineAddOn::is_addon_available_default_list($addOnVO->id, $plugin_jsonObj) === true) {
            RulesEngineAddOn::bis_addon_active_deactivation($addOnVO, 1);
        } else {
            if ($plugin_jsonObj == null) {
                $pluginArray = array();
            } else {
                $pluginArray = json_decode($plugin_jsonObj);
            }
           
            $pluginVO = new PluginVO();
            $pluginVO->set_id($addOnVO->id);
            $pluginVO->set_display_name($addOnVO->displayName);
            $pluginVO->set_status(1);
            $pluginVO->set_css_class($addOnVO->cssClass);
            $pluginVO->set_path($addOnVO->path);
            $pluginVO->set_version($addOnVO->version);
            $pluginVO->set_description($addOnVO->description);
            $pluginVO->set_apiKey($addOnVO->apiKey);
           
            array_push($pluginArray, $pluginVO);
            
          
            if(RulesEngineUtil::get_option(BIS_RULESENGINE_ADDONS_CONST) == null) {
                RulesEngineUtil::add_option(BIS_RULESENGINE_ADDONS_CONST, json_encode($pluginArray));
            } else {
                RulesEngineUtil::update_option(BIS_RULESENGINE_ADDONS_CONST, json_encode($pluginArray));
            }
        }
    }
    
    public static function is_addon_available_default_list($plugin_id, $pluginArray) {
       $pluginArrayObj = json_decode($pluginArray);
      
       $is_exists = false;

        foreach ($pluginArrayObj as $key => $pluginVO) {
            if ($pluginVO->id === $plugin_id) {
                $is_exists = true;
                break;
            }
        }
        
        return $is_exists;
    }
    
    public static function bis_addon_deactivation($plugin_id) {
        RulesEngineAddOn::bis_addon_active_deactivation($plugin_id, 0);
    }
    
    public static function bis_addon_active_deactivation($plugin_active, $status) {
        $pluginArray = RulesEngineUtil::get_option(BIS_RULESENGINE_ADDONS_CONST);
        $pluginArrayObj = json_decode($pluginArray);
        
        foreach ($pluginArrayObj as $key => $pluginVO) {
            if ($pluginVO->id === $plugin_active->id) {
                $pluginVO->status = $status;
                $pluginVO->path = $plugin_active->path;
                $pluginVO->version = $plugin_active->version;
                $pluginVO->display_name = $plugin_active->displayName;
                $pluginVO->css_class = $plugin_active->cssClass;
                $pluginVO->description = $plugin_active->description;
                $pluginVO->apiKey = $plugin_active->apiKey;
                break;
            }
        }

        RulesEngineUtil::update_option(BIS_RULESENGINE_ADDONS_CONST, json_encode($pluginArrayObj, 1));
    }

    public static function bis_addon_uninstall($plugin_id) {
        $pluginArray = RulesEngineUtil::get_option(BIS_RULESENGINE_ADDONS_CONST);
        $pluginArrayObj = json_decode($pluginArray, true);

        foreach ($pluginArrayObj as $key => $pluginVO) {
            if ($pluginVO["id"] === $plugin_id) {
                unset($pluginArrayObj[$key]);
                break;
            }
        }
        
        RulesEngineUtil::update_option(BIS_RULESENGINE_ADDONS_CONST, 
                json_encode($pluginArrayObj),is_network_admin());
    }

}
