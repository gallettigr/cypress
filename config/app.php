<?php
/**
 * Framepress and WP configurations.
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 */

/**
 * Define environment constants.
 */
if ( getenv('ENV') ) {
  define('APP_ENV', getenv('ENV'));
  $environment = FP_PATH . '/config/env/' . APP_ENV . '.php';
  if( file_exists($environment) ) {
    require_once $environment;
  }
}

/**
 * Define app constants.
 */
define('WP_PATH', FP_PATH . '/' . getenv('WP_DIR'));
define('WP_URL', WP_SITEURL);
define('APP_PATH', FP_PATH . '/' . getenv('APP_DIR'));
define('APP_URL', WP_HOME . '/' . FP_DIR . '/' . getenv('APP_DIR'));

define('WP_CONTENT_DIR', APP_PATH);
define('WP_CONTENT_URL', APP_URL);
define('WPMU_PLUGIN_DIR', FP_PATH . '/core');
define('WPMU_PLUGIN_URL', WP_HOME . '/' . FP_DIR . '/core');

define('WP_USE_THEMES', true);

/**
 * Load WordPress configuration.
 */
require_once 'wordpress.php';

?>
