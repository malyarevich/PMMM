<?php

namespace bis\repf\vo;

class PluginVO {
    
    public $id = null;
    public $displayName = null;
    public $status = 0;
    public $cssClass = null;
    public $version = 1.0;
    public $path = null;
    public $absPath = null;
    public $description = null;
    public $apiKey = null;
    
    function __construct($id = null, $displayName=null, 
             $status=0, $css_class=null, $version=1.0) {
        $this->id = $id;
        $this->displayName = $displayName;
        $this->status = $status;
        $this->cssClass = $css_class;
        $this->version = $version;
    }
    
    public function get_apiKey() {
        $this->apiKey;
    }

    public function set_apiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function get_description() {
        $this->description;
    }

    public function set_description($description) {
        $this->description = $description;
    }

    public function get_path() {
        $this->path;
    }

    public function set_path($path) {
        $this->path = $path;
    }
    
    public function get_abs_path() {
        $this->absPath;
    }

    public function set_abs_path($path) {
        $this->absPath = $path;
    }

    public function get_id() {
        $this->id;
    }
    
    public function set_id($id) {
        $this->id = $id;
    }
    public function get_display_name() {
        $this->displayName;
    }

    public function set_display_name($displayName) {
        $this->displayName = $displayName;
    }
    
    public function get_status() {
        $this->$status;
    }

    public function set_status($status) {
        $this->status = $status;
    }
    
    public function get_css_class() {
        $this->cssClass;
    }

    public function set_css_class($cssClass) {
        $this->cssClass = $cssClass;
    }
    
    public function get_version() {
        $this->version;
    }

    public function set_version($version) {
        $this->version = $version;
    }

}

