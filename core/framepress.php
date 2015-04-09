<?php
/**
 * Framepress core plugin.
 * @package Framepress
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 * Plugin Name: Framepress
 * Contributors: @gallettigr
 * Plugin URI: http://github.com/gallettigr/framepress
 * Description: Best frame for your WordPress master piece.
 * Author: gallettigr
 * Version: 0.8
 * Author URI: http://twitter.com/gallettigr
 * Textdomain: framepress
 */

namespace gallettigr\Frampress;

add_action( 'admin_init','framepress_load_check', 1 );
function framepress_load_check() {
	$frampress_defaults = array('core_version' => 0, 'loader_version' => 0, 'init_flag' => 0, 'wp_ready' => 0 );
	add_option('dotsqrpress', $frampress_defaults);
	$loader_version = filemtime( __FILE__ );
	if ( framepress_option_get('dotsqrpress','loader_version') !== $loader_version ) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
		framepress_option_set('loader_version', $loader_version, 'dotsqrpress');
	}
}

function framepress_option_set($key, $value, $options){
    $new_options = get_option($options);
    $new_options[$key] = $value;
    update_option($options,$new_options);
}
function framepress_option_get($options,$key) {
	$options_check = get_option($options);
	$options_value = $options_check[$key];
	return $options_value;
}

if (!defined('WP_DEFAULT_THEME')) {
  register_theme_directory(ABSPATH . 'wp-content/themes');
}
if (APP_ENV !== 'production') {
  add_action('pre_option_blog_public', '__return_zero');
} else {
  add_action('pre_option_blog_public', '__return_true');
}


# MAIN LOADER. REQUIRES DOTSQRPRESS PLUGINS. YOU CAN ADD YOURS HERE IF YOU KNOW WHAT YOU'RE DOING.
# PLEASE WAIT UNTIL WORDPRESS HAS BEEN CORRECTLY INSTALLED BEFORE UNCOMMENTING AND INCLUDING DOTSQRPRESS INIT FILE.
//include WPMU_PLUGIN_DIR.'/melapress/dotsqrpress.php';
//include WPMU_PLUGIN_DIR.'/melapress/init.php';
//include WPMU_PLUGIN_DIR.'/melapress/login-filter.php';
?>
