<?php
/**
 * @package DOTSQRPress
 * @version 1.0
 */
/*
Plugin Name: DOTSQRPress Loader
Contributors: @gallettigr
Plugin URI: http://dotsqr.co
Description: Setup plugin for DOTSQRPress.
Author: @gallettigr
Version: 1.0
Author URI: http://www.twitter.com/gallettigr
Textdomain: dotsqrpress_core
Domain Path: /languages/
*/


# CHECKS IF THIS FILE HAS BEEN MODIFIED. IF TRUE, FLUSHES REWRITE RULES ON ADMIN_INIT
# THIS IS NEEDED BECAUSE A FLUSH IS REQUIRED IF DOTSQRPRESS INIT IS DEACTIVATED/ACTIVATED

add_action( 'admin_init','dotsqrpress_loader_control_check', 1 );
function dotsqrpress_loader_control_check() {

	$dotsqrpress_default_options = array('core_version' => 0, 'loader_version' => 0, 'init_flag' => 0, 'wp_ready' => 0 );
	add_option('dotsqrpress',$dotsqrpress_default_options);
	$loader_ver = filemtime( __FILE__ );
	if ( dotsqrpress_option_check('dotsqrpress','loader_version') != $loader_ver ) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		dotsqrpress_option_update('loader_version',$loader_ver,'dotsqrpress');
	}
}

function dotsqrpress_option_update($key,$value,$options){
    $new_options = get_option($options);
    $new_options[$key] = $value;
    update_option($options,$new_options);
}
function dotsqrpress_option_check($options,$key) {
	$options_check = get_option($options);
	$options_value = $options_check[$key];
	return $options_value;
}

# MAIN LOADER. REQUIRES DOTSQRPRESS PLUGINS. YOU CAN ADD YOURS HERE IF YOU KNOW WHAT YOU'RE DOING.
# PLEASE WAIT UNTIL WORDPRESS HAS BEEN CORRECTLY INSTALLED BEFORE UNCOMMENTING AND INCLUDING DOTSQRPRESS INIT FILE.
include WPMU_PLUGIN_DIR.'/dotsqrpress_core/dotsqrpress.php';
include WPMU_PLUGIN_DIR.'/dotsqrpress_core/init.php';
include WPMU_PLUGIN_DIR.'/dotsqrpress_core/login-filter.php';
?>
