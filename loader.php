<?php
/**
 * Framepress loader.
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 */

/**
 * Require composer vendors. Install them first using PHP Composer from the Framepress folder.
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
define('ROOT_PATH', dirname(__DIR__));
define('FP_PATH', __DIR__ );
define('FP_DIR', basename(FP_PATH));

/**
 * Require configurations.
 */
require_once 'config/app.php';

?>