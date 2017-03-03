<?php
/**
 * Cypress and WP configurations.
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 */

/**
 * Define environment constants.
 */
if ( getenv('ENV') ) {
  define('APP_ENV', getenv('ENV'));
  $environment = CP_PATH . '/config/env/' . APP_ENV . '.php';
  if( file_exists($environment) ) {
    require_once $environment;
  }
}

/**
 * Define app constants.
 */
define('WP_PATH', CP_PATH . '/' . WP_DIR);
define('WP_BASE_URL', basename(CP_PATH) . '/' . WP_DIR);
define('WP_URL', WP_SITEURL);
define('APP_PATH', CP_PATH . '/' . getenv('APP_DIR'));
define('APP_BASE_URL', basename(CP_PATH) . '/' . getenv('APP_DIR'));
define('APP_URL', WP_HOME . '/' . basename(CP_PATH) . '/' . getenv('APP_DIR'));


define('WP_CONTENT_DIR', APP_PATH);
define('WP_CONTENT_URL', APP_URL);
define('WPMU_PLUGIN_DIR', CP_PATH . '/core');
define('WPMU_PLUGIN_URL', WP_HOME . '/' . basename(CP_PATH) . '/core');

define('WP_USE_THEMES', true);

/**
 * Load WordPress configuration.
 */
require_once 'wordpress.php';

?>
