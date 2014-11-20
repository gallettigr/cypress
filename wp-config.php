<?php
/**
 * The base configurations of WordPress.
 *
 * @package WordPress
 * @author @gallettigr <gallettigr@dotsqr.co>
 */

# DATABASE CONFIGURATION
define('DB_NAME', 'DB_NAME_HERE');
define('DB_USER', 'DB_USER_HERE');
define('DB_PASSWORD', 'DB_PASSWORD_HERE');
define('DB_HOST', 'DB_HOST_HERE');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

# GENERATE SALT KEYS HERE

# DEFINE LOCALE LANG
define('WPLANG', 'en_EN'); // SET YOUR DEFAULT LANGUAGE, REMEMBER TO ADD LANGUAGE FILES TO WP-CONTENT/LANGUAGES

# DEFAULTS
define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'config/conf.php');
require_once(ABSPATH . 'wp-settings.php');
