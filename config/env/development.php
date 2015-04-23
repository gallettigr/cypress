<?php
/**
 * Development enviroment config.
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
define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/' . CP_DIR . '/' . getenv('WP_DIR'));
define('WP_HOME', 'http://' . $_SERVER['SERVER_NAME']);

/**
 * Debug constants.
 */
define('WP_DEBUG', true);
define('SCRIPT_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('WP_DEBUG_LOG', true);
define('SAVEQUERIES', true);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', CP_PATH . '/.logs/error_log.log');

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
 * Other useful development constants.
 */
define('WP_POST_REVISIONS', false);
define('WP_MEMORY_LIMIT', '64M');
define('WP_CACHE', false);
define('FS_METHOD', 'direct');

/**
 * Cypress development constants.
 */
define('DEVELOPER', 'gallettigr');

 ?>
