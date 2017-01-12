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

use bis\repf\common\BISSession;

 /**
 * Utility class for caching php objects, which can further extended.
 *
 * Class RulesEngineCacheWrapper
 */
class RulesEngineCacheWrapper {
    

    /**
     * This method is used to set the cache value.
     *
     * @param $key
     * @param $data
     * @param string $group
     * @param int $expire
     */
    public static function set_value($key, $data, $group = '', $expire = 0) {
        // Set the rules in cache for future purpose.
        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            wp_cache_set($key . "_" . $blog_id, $data);
        } else {
            wp_cache_set($key, $data);
        }
    }

    /**
     * This method is used to get the cache value.
     * @param $key
     * @param string $group
     * @return mixed
     */
    public static function get_value($key, $group = '') {
        // Check whether child rules exists in cache
        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            $data = wp_cache_get($key . "_" . $blog_id);
        } else {
            $data = wp_cache_get($key);
        }
        return $data;
    }

    /**
     * Deletes the cache value.
     *
     * @param $key
     * @param string $group
     */
    public static function delete_cache($key, $group = '') {
        // Delete the cache based on the key
        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            wp_cache_delete($key . "_" . $blog_id, $group);
        } else {
            wp_cache_delete($key, $group);
        }
    }

    /**
     * This method is used to flush the cache.
     *
     */
    public static function flush_cache() {
        //Clears all cached data
        wp_cache_flush();
    }

    public static function set_reset_time($time_const) {
        // Commented to fix performance issue.
        /*$expiration = 60 * 30;
        set_transient($time_const, time(), $expiration);
         */
    }

    public static function get_reset_time($time_const) {
        // Commented to fix performance issue.
        //return get_transient($time_const);
        return 0;
    }

    public static function is_session_attribute_set($key) {
        $session = BISSession::getInstance();
        $key = RulesEngineCacheWrapper::get_session_key($key);
        return $session->isSessionAttributeSet($key);
    }

    public static function get_session_attribute($key) {
        $session = BISSession::getInstance();
        $key = RulesEngineCacheWrapper::get_session_key($key);
        return $session->getAttribute($key);
    }

    public static function set_session_attribute($key, $value) {
        $session = BISSession::getInstance();
        $key = RulesEngineCacheWrapper::get_session_key($key);
        return $session->setAttribute($key, $value);
    }

    public static function remove_session_attribute($key) {
        $session = BISSession::getInstance();
        $key = RulesEngineCacheWrapper::get_session_key($key);
        return $session->removeAttribute($key);
    }
    
    public static function destroy_session() {
        $session = BISSession::getInstance();
        $session->destroy();
    }
 
    public static function get_session_key($key) {
       
        $site_name = get_bloginfo();
        $site_name = preg_replace('/[^a-zA-Z0-9-_\.]/', '', $site_name);

        if (is_multisite()) {
            $blog_id = get_current_blog_id();
            return $site_name."_".$key . "_" . $blog_id;
        }

        return $site_name . "_" . $key;
    }

}
