<?php
/**
 * Base configurations of DOTSQRPress. Customize this file to fit your needs.
 * @author @gallettigr <gallettigr@dotsqr.co>
 */

# GENERAL WEBSITE DEFINITIONS
define('MAIN_COLOR', '#C12431'); // SET YOUR CUSTOMER MAIN COLOR TO CHANGE THE WP-ADMIN AND WP-LOGIN SKIN
define('MAIN_USER', 'gallettigr'); // TYPE THE WEBMASTER USERNAME TO HAVE FULL ACCESS TO WP BACKEND
define( 'WP_DEFAULT_THEME', 'dotsqrpress'); // SET YOUR DEFAULT THEME
define( 'UPLOADS', 'uploads' ); // SET YOUR DEFAULT UPLOAD FOLDER. COMMENT TO USE DEFAULT /WP-CONTENT/UPLOADS.
//define( 'GOOGLE_ANALYTICS', 'UA-XXXXXXX-X' ); // PASTE YOUR GOOGLE ANALYTICS TRACKING CODE
//define('PROD_URL', 'http://dotsqr.co') // UNCOMMENT AND INSERT YOUR SITE URL ONLY IN PROD ENV
define('POST_EXCERPT_LENGTH', 10); // SET YOUR DEFAULT EXCERPT LENGHT

# DOTSQRPRESS DEFAULTS. I RECOMMEND TO NOT EDIT ANY FURTHER UNLESS YOU KNOW WHAT YOU'RE DOING.
$table_prefix  = 'dotsqrpress_';
define( 'WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] );
define( 'WP_HOME', 'http://' . $_SERVER['SERVER_NAME'] );
define( 'NOBLOGREDIRECT', 'http://' . $_SERVER['SERVER_NAME'] );
define( 'WPMU_PLUGIN_DIR', ABSPATH . 'core' );
define( 'WPMU_PLUGIN_URL', WP_SITEURL . '/core' );
//define( 'FORCE_SSL_ADMIN', true); // UNCOMMENT IF YOU KNOW WHAT YOU'RE DOING.
define( 'WP_DEBUG', true );
if ( WP_DEBUG ) {
    define( 'WP_DEBUG_LOG', true );
    define( 'WP_DEBUG_DISPLAY', false );
    @ini_set( 'display_errors', 0 );
    @ini_set('log_errors', 1);
}
define('AUTOSAVE_INTERVAL', 3600 );
define('WP_POST_REVISIONS', false );
define( 'WP_MEMORY_LIMIT', '64M' );
define( 'WP_CACHE', true );
define( 'FS_METHOD', 'direct' ); // COMMENT ON PROD ENVIRONMENT.
define( 'FS_CHMOD_DIR', ( 0755 & ~ umask() ) );
define( 'FS_CHMOD_FILE', ( 0644 & ~ umask() ) );
define( 'EMPTY_TRASH_DAYS', 10 );
define( 'WP_ALLOW_REPAIR', true );
define( 'DISALLOW_FILE_EDIT', true );
//define( 'DISALLOW_FILE_MODS', true ); // UNCOMMENT TO DISABLE PLUGINS INSTALL AND UPDATES
define( 'AUTOMATIC_UPDATER_DISABLED', true );
define( 'WP_AUTO_UPDATE_CORE', false );
//define( 'COMPRESS_CSS', TRUE ); // UNCOMMENT ON PROD ENVIRONMENT IF YOU DON'T MINIFY YOURSELF
//define( 'COMPRESS_SCRIPTS', true ); // UNCOMMENT ON PROD ENVIRONMENT IF YOU DON'T MINIFY YOURSELF
//define( 'CONCATENATE_SCRIPTS', true ); // UNCOMMENT ON PROD ENVIRONMENT IF YOU DON'T MINIFY YOURSELF
define( 'ENFORCE_GZIP', true );
?>
