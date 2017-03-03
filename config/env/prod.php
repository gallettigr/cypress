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
define('WP_DEBUG', true);
define('SCRIPT_DEBUG', false);
define('WP_DEBUG_DISPLAY', false);
define('SAVEQUERIES', false);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', CP_PATH . '/.logs/error_log.log');

/**
 * Salt keys. Generate from {@link https://api.wordpress.org/secret-key/1.1/salt/ }
 */


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
 * Cypress production constants.
 */
define('DEVELOPER', 'gallettigr');
 ?>
