<?php
/**
 * Cypress loader.
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 */

/**
 * Require composer vendors. Install them first using PHP Composer from the Cypress folder.
 */
require_once 'vendor/autoload.php';

/**
 * Load environment variables.
 */
if ( file_exists( __DIR__ . '/.env') ) {
  Dotenv::load( __DIR__ );
}
Dotenv::required(array('DB_NAME', 'DB_USER', 'DB_PASSWORD'));

/**
 * Define app constants.
 */
define('CP_PATH', __DIR__ );
define('CP_DIR', basename(CP_PATH));
if ( getenv('WP_DIR') ) {
  define('WP_DIR', getenv('WP_DIR'));
}

/**
 * Require configurations.
 */
require_once 'config/app.php';

?>
