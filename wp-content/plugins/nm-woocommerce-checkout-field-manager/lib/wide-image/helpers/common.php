<?php
	/**
	 * @package Demos
	 */
	define('LIB_PATH', realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR);
	
	//echo 'lib path '.LIB_PATH; exit;
	
	error_reporting(E_ALL | E_STRICT);
	ini_set('display_errors', true);
	
	require_once LIB_PATH . 'helpers/Request.php';
	require_once LIB_PATH . 'helpers/Demo.php';
	require_once LIB_PATH . 'helpers/Field.php';
	require_once LIB_PATH . 'helpers/CheckboxField.php';
	require_once LIB_PATH . 'helpers/CheckboxSetField.php';
	require_once LIB_PATH . 'helpers/FileSelectField.php';
	require_once LIB_PATH . 'helpers/CoordField.php';
	require_once LIB_PATH . 'helpers/IntField.php';
	require_once LIB_PATH . 'helpers/SelectField.php';
	require_once LIB_PATH . 'helpers/FormatSelectField.php';

