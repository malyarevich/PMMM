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

namespace bis\repf\common;
use bis\repf\common\RulesEngineCacheWrapper;
use bis\repf\util\GeoPluginWrapper;
use bis\repf\common\BISSession;

class BISSessionWrapper {

    public function getGeoPlugin() {
        $session = BISSession::getInstance();
        $key = RulesEngineCacheWrapper::get_session_key(BIS_GEOLOCATION_WRAPPER);
        $geo_plugin = $session->getAttribute($key);
        
        if($geo_plugin == null) {
            $geo_plugin = new GeoPluginWrapper();
            $session->setAttribute($key, $geo_plugin);
        }
        
        return $geo_plugin;
    }

}
