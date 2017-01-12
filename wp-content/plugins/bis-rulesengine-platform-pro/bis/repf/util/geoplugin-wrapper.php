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

namespace bis\repf\util;

use bis\repf\util\geoPlugin;
use bis\repf\MaxMind\Db\Reader;
use RulesEngineUtil;

class GeoPluginWrapper {

    private $countryName = "Other";
    private $countryCode = null;
    private $currencyCode = null;
    private $continentCode = null;
    private $ipAddress = null;
    private $currencySymbol = null;
    private $currencyConverter = null;
    private $city = "Other";
    private $region = "Other";
    private $latitude = null;
    private $longitude = null;
    private $ipLookUp = "Server";

    function __construct() {
    	
	$this->ipAddress = $this->getClientIP();
	  
        if (RulesEngineUtil::get_option(BIS_GEO_LOOKUP_WEBSERVICE_TYPE) == 1) {
            $geoplugin = new geoPlugin();
            $geoplugin->locate($this->ipAddress);
            $this->countryName = $geoplugin->countryName;
            $this->countryCode = $geoplugin->countryCode;
            $this->currencyCode = $geoplugin->currencyCode;
            $this->continentCode = $geoplugin->continentCode;
            $this->city = $geoplugin->city;
            $this->region = $geoplugin->region;
            $this->currencySymbol = $geoplugin->currencySymbol;
            $this->currencyConverter = $geoplugin->currencyConverter;
            $this->latitude = $geoplugin->latitude;
            $this->longitude = $geoplugin->longitude;
        }
        
        if (RulesEngineUtil::get_option(BIS_GEO_LOOKUP_TYPE) == 1) {
            $mmdb = RulesEngineUtil::get_option(BIS_GEO_MAXMIND_DB_FILE);    
            $reader = new Reader(RulesEngineUtil::get_file_upload_path(). $mmdb);

            $ipData = $reader->get($this->ipAddress);
            
            if($ipData != null) {
                $this->countryName = $ipData['country']['names']['en'];
                $this->countryCode = $ipData['country']['iso_code'];
                $this->continentCode = $ipData['continent']['code'];
               
                if (isset($ipData['city'])) {
                    $this->city = $ipData['city']['names']['en'];
                } 
            }
            $reader->close();
        }
    }

    function getClientIP()
    {
         $ipaddress = 'UNKNOWN';
         
        if (isset($_SERVER['X-Real-IP'])) {
            $ipaddress = $_SERVER['X-Real-IP'];
            $this->ipLookUp = "X-Real-IP";
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
            // Make sure we always only send through the first IP in the list which should always be the client IP.
            $ipaddress = trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));
            $this->ipLookUp = "Proxy Servers";
            
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
           $ipaddress = $_SERVER['REMOTE_ADDR'];
           $this->ipLookUp = "REMOTE_ADDR";
        }
     
        // Logic for local testing
        if (!filter_var($ipaddress, FILTER_VALIDATE_IP) === false) {
            if(isset($_SERVER['SERVER_NAME']) && 
                    ($ipaddress === "127.0.0.1" || $ipaddress === "::1")) {
                if($_SERVER['SERVER_NAME'] === "localhost") {
                    $ipaddress = $this->get_external_ip_address();
                    $this->ipLookUp = "External Service";
                }
            } 
            
            return $ipaddress;
        } 
          
        $ipaddress =  $this->getBisClientIpEnv();

        if (!filter_var($ipaddress, FILTER_VALIDATE_IP) === false) {
            return $ipaddress;
        }

        $ipaddress =  $this->getBisClientIpServer();

        if (!filter_var($ipaddress, FILTER_VALIDATE_IP) === false) {
            return $ipaddress;
        }
        
        return $ipaddress;
    }
    
    // Function to get the client ip address
    function getBisClientIpEnv() {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

// Function to get the client ip address
    function getBisClientIpServer() {
        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if ($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if ($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if ($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    function get_external_ip_address() {
        $service_endpoint = 'http://icanhazip.com';
        
        $ip_lookup_apis = array(
            'icanhazip' => 'http://icanhazip.com',
            'ipify' => 'http://api.ipify.org/',
            'ipecho' => 'http://ipecho.net/plain',
            'ident' => 'http://ident.me',
            'whatismyipaddress' => 'http://bot.whatismyipaddress.com'
        );
        
        $external_ip_address = null;
        
        foreach ($ip_lookup_apis as $service_endpoint) {
            $response = wp_safe_remote_get($service_endpoint, array('timeout' => 2));
            
            if (!is_wp_error($response) && $response['body']) {
                $external_ip_address = sanitize_text_field($response['body']);
                break;
            }
        }
        if(!filter_var($external_ip_address, FILTER_VALIDATE_IP)) {
            return "127.0.0.1";
        }
        
        return sanitize_text_field($external_ip_address);
    }

    function getIPAddress() {
        return $this->ipAddress;
    }

    function getCountryName() {
        return $this->countryName;
    }

    function getCountryCode() {
        return $this->countryCode;
    }

    function getCurrencyCode() {
        return $this->currencyCode;
    }

    function getContinentCode() {
        return $this->continentCode;
    }

    function getCity() {
        return $this->city;
    }

    function getRegion() {
        return $this->region;
    }

    function getCurrencySymbol() {
        return $this->currencySymbol;
    }

    function getCurrencyConverter() {
        return $this->currencyConverter;
    }

    function getLatitude() {
        return $this->latitude;
    }

    function getLongitude() {
        return $this->longitude;
    }

    function getIPLookUp() {
        return $this->ipLookUp;
    }

}