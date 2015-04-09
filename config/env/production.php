<?php
/**
 * Production enviroment config.
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 */

/**
 * Database constants.
 */
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST') ? getenv('DB_HOST') : 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
$table_prefix  = 'app_';

/**
 * Host and URL constants.
 */
define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/' . getenv('WP_DIR'));
define('WP_HOME', 'http://' . $_SERVER['SERVER_NAME']);

/**
 * Debug constants.
 */
define('WP_DEBUG', false);
define('SCRIPT_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', true);
define('SAVEQUERIES', false);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/error_log.php');

/**
 * Salt keys. Generate from {@link https://api.wordpress.org/secret-key/1.1/salt/ }
 */
define('AUTH_KEY',         'Ej+%InBgi-([QVwjxu0~L]5Vu(E-Af||HzCk<=(h}u19&8)z@kfh[NDdVr/#qHfi');
define('SECURE_AUTH_KEY',  '<g+IQc0.q=+>!,%ATX-MXx3zDiFL7o4I-+ jJ~{iVvQ*#2NmTE V--qM^A|*.Mn`');
define('LOGGED_IN_KEY',    '3Sn?u1Q+jn>FcXG0(,RKe](0By7UZj>-@CDrx-+E^Z>jHB(65ir>`K>/eHl8n}^B');
define('NONCE_KEY',        '6bje>*8jN15Rqjh~~</>53Y2v$)f`8mOIX+-6^M{z*4}Me:-.GB|]-K${iI&@:G9');
define('AUTH_SALT',        'r+f`nq4~heM1`NWdA?ob{/73|(#h1nk=DWvyDI^`Lj{Qm2.=D_x~1#&p(3wH/F_2');
define('SECURE_AUTH_SALT', '<8iyj;`l$ha^iQ:7I4r5B-q$#:cD,.Y1n9F-#=dX^m`ng&C6+bz{VA0;tlM=sd,x');
define('LOGGED_IN_SALT',   '{AXF+`=s6$OdeQtQ9nPvOr^D#t{6U#FfBW0FoV&8032V&`!e}kBs+IuiiFUrU)c;');
define('NONCE_SALT',       'ks3Q@X.~FQB0Gs-*mMMjq)JLqe?mk#G]so)Jcp)cybTT-0!bE}5#7m0h}M<lIE>S');

/**
 * Other useful WordPress constants.
 */
define('WP_POST_REVISIONS', 3);
define('WP_CACHE', true);
define('WP_ALLOW_REPAIR', true);
define('DISALLOW_FILE_EDIT', true);
define('DISALLOW_FILE_MODS', true);
define('AUTOMATIC_UPDATER_DISABLED', true);
define('WP_AUTO_UPDATE_CORE', false);
define('ENFORCE_GZIP', true);
define('COMPRESS_CSS', true);
define('COMPRESS_SCRIPTS', true);
define('CONCATENATE_SCRIPTS', true);
define('DISABLE_WP_CRON', true);

/**
 * Framepress production constants.
 */
define('GTM', 'GTM-X00XXX');
define('DEVELOPER', 'gallettigr');
define('FB_APPID', '00000000000');
 ?>