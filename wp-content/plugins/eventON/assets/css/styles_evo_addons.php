<?php
/**
 * EventON Styles for all addons in one place
 * @version 2.4.10
 */

//header("Content-type: text/css; charset: UTF-8");
 	$absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
 	$wp_load = $absolute_path[0] . 'wp-load.php';
 	require_once($wp_load);

  	//header('Content-type: text/css');
  	//header('Cache-control: must-revalidate');

  	ob_start ("ob_gzhandler");
    header("Content-type: text/css; charset: UTF-8");
    header("Cache-Control: must-revalidate");
    $offset = 60 * 60 ;
    $ExpStr = "Expires: " .
    gmdate("D, d M Y H:i:s",
    time() + $offset) . " GMT";
    header($ExpStr);

do_action('evo_addon_styles');