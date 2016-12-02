<?php
defined('LIBCOMPACTMVC_ENTRY') || define('LIBCOMPACTMVC_ENTRY', (($_SERVER['DOCUMENT_ROOT'] == "") || ($_SERVER['DOCUMENT_ROOT'] == getcwd())) || die('Invalid entry point'));

/**
 * LibCompactMVC application loader
 *
 * @author      Botho Hohbaum <bhohbaum@googlemail.com>
 * @package     LibCompactMVC
 * @copyright   Copyright (c) Botho Hohbaum 11.02.2014
 * @link		http://www.github.com/bhohbaum
 */

require_once('./include/libcompactmvc/functions.php');

cmvc_include('mysqlhost.php');
cmvc_include('config.php');
cmvc_include('singleton.php');
cmvc_include('log.php');

register_shutdown_function("fatal_handler");
$GLOBALS["FATAL_ERR_MSG"] = "";

function fatal_handler() {
	$error = error_get_last();
	if ($error == null) return;
	try {
		throw new Exception();
	} catch (Exception $e) {
		$trace = $e->getTraceAsString();
	}
	$msg = "Fatal PHP error: " . print_r($error, true) . (array_key_exists("FATAL_ERR_MSG", $GLOBALS) ? $GLOBALS["FATAL_ERR_MSG"] : "") . "\nStack trace:\n" . $trace;
	ELOG($msg);
}

// first include the configuration

if (!defined("LOG_FILE")) {
	die("LOG_FILE is undefined, please define it in config.php - exiting.\n");
}
@touch(LOG_FILE);
if (!file_exists(LOG_FILE)) {
	die(LOG_FILE." cannot be created, exiting.\n");
}
if (!is_writable(LOG_FILE)) {
	die(LOG_FILE." is not writable by the current process, exiting.\n");
}

if (defined('DEBUG') && (DEBUG == 0)) {
	ob_start();
}

// include files with content that is required elsewhere
cmvc_include('inputsanitizer.php');
cmvc_include('actionmapperinterface.php');
cmvc_include('cmvccontroller.php');
cmvc_include('cmvccomponent.php');

// application-specific early loading
cmvc_include('include.php');

// load the rest
cmvc_include_dir("./include/libcompactmvc/");
cmvc_include_dir("./application/", array(
		"CWebDriverTestCase.php"
));

// let's begin the execution...
