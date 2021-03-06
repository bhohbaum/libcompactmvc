<?php
include_once('libcompactmvc.php');
LIBCOMPACTMVC_ENTRY;

/**
 * Application config
 *
 * @author      Botho Hohbaum <bhohbaum@googlemail.com>
 * @package     LibCompactMVC
 * @copyright   Copyright (c) Botho Hohbaum 11.02.2014
 * @link		http://www.github.com/bhohbaum
 */

// Set global constants
// Debug messages & logging
// 0 off
// 1 on
if (gethostname() == 'vweb02') {
	define('DEBUG', 1);
	define('LOG_LEVEL', 3);
	define('LOG_TARGET', 1);
	define('LOG_TYPE', 0);
} else if (gethostname() == 'production') {
	define('DEBUG', 0);
	define('LOG_LEVEL', 0);
	define('LOG_TARGET', 2);
	define('LOG_TYPE', 1);
} else {
	define('DEBUG', 1);
	define('LOG_LEVEL', 3);
	define('LOG_TARGET', 1);
	define('LOG_TYPE', 0);
}

define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
define('REDIS_FILE_FALLBACK_DISABLED', false);

define('CACHING_ENABLED', DEBUG != 1 || true);
define('LANG_DEFAULT', 'app');

define('SMTP_SERVER', '127.0.0.1');
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_SENDER', 'test@libcompactmvc.local');
define('SMTP_SENDER_NAME', 'LibCompactMVC');
// Send method: MAIL (mail() function) or SMTP
define('MAIL_TRANS_TYPE', 'SMTP');
define('MAIL_DEBUG_ADDR', 'b.hohbaum@googlemail.com');

//setlocale(LC_ALL, "de_DE.UTF-8", "de_DE@euro", "de_DE", "de", "ge");
setlocale(LC_ALL, 0);

// WebSocket config
// WS server cluster config
define('WS_SRV_COUNT', 1);
define('ST_WS_SRV_IDX', 'ws_srv_idx');
if (gethostname() == 'vweb05') {
	$GLOBALS['WS_BASE_URL'] = array(
			((is_tls_con()) ? 'wss' : 'ws') . '://app.joe-nimble.com/ws/'
	);
} else {
	$GLOBALS['WS_BASE_URL'] = array(
			((is_tls_con()) ? 'wss' : 'ws') . '://benimble.kmundp.local/ws/'
	);
}

// populate controllers with POST/GET variables?
define('REGISTER_HTTP_VARS', true);

// Session
define('SESSION_INSECURE_COOKIE', true);
define('SESSION_DYNAMIC_ID_DISABLED', true);
define('SESSION_TIMEOUT', 1200);

// Active Sessions measuring
define('ACTIVESESSIONS_MIN_HITS', 60);
define('ACTIVESESSIONS_MAX_HITS', 180);
define('ACTIVESESSIONS_HIT_INCR', 20);

// Certificate validation in CachedHttpRequest?
define('SSL_VERIFYPEER', true);
define('SSL_VERIFYHOST', 2);

// Further constants to be set at installation time
define('UPLOAD_BASE_DIR', './files/upload');				// relative to $_SERVER['DOCUMENT_ROOT']
define('IMAGES_BASE_DIR', './files/images');				// relative to $_SERVER['DOCUMENT_ROOT']
define('TEMP_DIR', './files/temp');							// relative to $_SERVER['DOCUMENT_ROOT']
define('CSV_BASE_DIR', './files/csv');						// relative to $_SERVER['DOCUMENT_ROOT']
define('LOG_FILE', '/var/log/php/cmvc.log');				// relative to $_SERVER['DOCUMENT_ROOT']
define('LOG_IDENT', 'libcompactmvc');
define('LOG_FACILITY', 'local7');
if (gethostname() == 'libo') {
	define('BASE_URL', 'http://cmvc.kmundp.local');
} else if (gethostname() == 'develwebss') {
	define('BASE_URL', 'http://cmvc.kmundp.local');
}
define('DEFAULT_TIMEZONE', 'CET');

define('CAPTCHA_RES_PATH', "./include/resources"); // relative to $_SERVER['DOCUMENT_ROOT']
define('ST_CAPTCHA_SESS_VAR', "captcha");

// uncomment to use proxy
// define('PROXY_CONFIG', '');
// define('PROXY_PORT', 8080);

define('CEPH_CONF', './files/ceph/ceph.prod.conf');
define('CEPH_POOL', 'ceph');
define('CEPH_MAX_OBJ_SIZE', 64 * 1024 * 1024);

define('REDIS_KEY_PREFIX', 'CMVC_');
define('REDIS_KEY_RCACHE_PFX', 'RENDERCACHE_');
define('REDIS_KEY_RCACHE_TTL', 7200);
define('REDIS_KEY_TBLDESC_PFX', 'TBLDESC_');
define('REDIS_KEY_FKINFO_PFX', 'FKINFO_');
define('REDIS_KEY_TBLCACHE_PFX', 'TBLCACHE_');
define('REDIS_KEY_TBLCACHE_TTL', 720000);
define('REDIS_KEY_FIFOBUFF_PFX', 'FIFOBUFF_');
define('REDIS_KEY_FIFOBUFF_TTL', 10000);
define('REDIS_KEY_HTMLCACHE_PFX', 'HTMLCACHE_');
define('REDIS_KEY_HTMLCACHE_TTL', 10000);
define('REDIS_KEY_CACHEDHTTP_PFX', 'HTTPCACHE_');
define('REDIS_KEY_CACHEDHTTP_TTL', 10000);

// couchdb database
define('TRANSLATION_DATABASE', 'libcompactmvc');

// Session tokens
define('ST_USER_ID', 'user_id');

// DB Server:
// DB schema name for ORM learning
define('MYSQL_SCHEMA', 'libcompactmvc');
$GLOBALS['MYSQL_HOSTS'] = array(
		new MySQLHost("localhost", "root", "toor", MYSQL_SCHEMA, MySQLHost::SRV_TYPE_READWRITE)
);
$GLOBALS['MYSQL_NO_CACHING'] = array(
		TBLV_NEXT_RECEIVER,
		TBLV_SEND_LIST,
		TBLV_TODAYS_MAILINGS,
		TBLV_TRACKING_COMBINED,
		TBLP_TRACKING_OVERVIEW
);
define("DBA_DEFAULT_CLASS", "DBA");

define("FIREBASE_API_TOKEN", "AAAAg778THU:APA91bFRNSAc_54I3-INM-DS9Q3gg1-8ijLKzpUvY14r3hX5Wx2L38E3aCCnuqehIn2dTQMWfx4Hd7cLhaMwWU_f_M2RWkdn3qwv7myrPuGVhWKyfX0vUAoDy5VAaltUb1UJOYv5eowR");

define('TBL_APP_PFX', 'cmvc_');
define('TBL_EVENT_TYPES', 'event_types');
define('TBL_IMAGES', 'images');
define('TBL_MAILINGS', 'mailings');
define('TBL_MAILINGS_HAS_RECEIVERS', 'mailings_has_receivers');
define('TBL_MAILPARTS', 'mailparts');
define('TBL_MAILPART_TYPES', 'mailpart_types');
define('TBL_RECEIVERS', 'receivers');
define('TBL_TEXTS', 'texts');
define('TBL_TRACKING_EVENTS', 'tracking_events');
define('TBL_USER', 'user');

define('TBLV_NEXT_RECEIVER', 'next_receiver');
define('TBLV_SEND_LIST', 'send_list');
define('TBLV_TODAYS_MAILINGS', 'todays_mailings');
define('TBLV_TRACKING_COMBINED', 'tracking_combined');

define('TBLP_TRACKING_OVERVIEW', 'tracking_overview');
