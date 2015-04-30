<?php
/**
 * Cypress WP configuration.
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 */

/**
 * WordPress constants.
 */
define('POST_EXCERPT_LENGTH', 80);
define('AUTOSAVE_INTERVAL', 360 );
define('NOBLOGREDIRECT', WP_HOME);
define('FS_CHMOD_DIR', ( 0755 & ~ umask() ));
define('FS_CHMOD_FILE', ( 0644 & ~ umask() ));
define('EMPTY_TRASH_DAYS', 10);

/**
 * Cypress constants.
 */
define('MAIN_COLOR', '#00aefd');
define('SECONDARY_COLOR', '#ffffff');

?>
