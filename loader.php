<?php
/**
 * Cypress loader.
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 */

/**
 * Load environment variables.
 */
if ( file_exists( $dotenv = __DIR__ . '/.env') ) {
  eval( file_get_contents($dotenv) );
}
if (!( getenv('DB_NAME') && getenv('DB_USER') && getenv('DB_PASSWORD') )) throw new RuntimeException("One or more environment variables failed assertions.");

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
