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
//define('WP_CACHE', true);
define('SCRIPT_DEBUG', true);
define('WP_DEBUG_DISPLAY', true);
define('SAVEQUERIES', true);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', CP_PATH . '/.logs/errors.log');
// define('COMPRESS_CSS', true);
// define('COMPRESS_SCRIPTS', true);
// define('CONCATENATE_SCRIPTS', true);
// define('ENFORCE_GZIP', true);

/**
 * Salt keys. Generate from {@link https://api.wordpress.org/secret-key/1.1/salt/ }
 */



/**
 * Other useful development constants.
 */
define('WP_POST_REVISIONS', false);
define('WP_MEMORY_LIMIT', '96M');
define('FS_METHOD', 'direct');

/**
 * Cypress development constants.
 */
define('DEVELOPER', 'gallettigr');

 ?>
