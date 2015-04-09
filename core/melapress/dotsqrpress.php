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


# DOTSQRPRESS INIT
add_action('init', 'dotsqrpress_init', 1);
function dotsqrpress_init() {
	// INIT START
	// LOAD DOTSQRPRESS CORE LOCALE
  $core_domain = 'dotsqrpress_core';
  $core_locale = apply_filters( 'plugin_locale', get_locale(), $core_domain );
  if ( $loaded = load_textdomain( $core_domain, trailingslashit( WP_LANG_DIR ) . $core_domain . '/' . $core_domain . '-' . $core_locale . '.mo' ) ) {
  	return $loaded;
  } else {
    load_plugin_textdomain( $core_domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
  }
	// RESTRICT ACCESS TO WORDPRESS WP-LOGIN AND WP-ADMIN TO NOT-ADMIN USERS. LOGOUT USERS WHO VISIT 'LOGOUT' PAGE
  if( (strpos(strtolower($_SERVER['REQUEST_URI']),'wp-login.php') !== false) && !isset($_REQUEST['action']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')) {
    wp_redirect(get_option('siteurl').'/404');
    exit;
  }
  else if( (strpos(strtolower($_SERVER['REQUEST_URI']),'logout') !== false)) {
    wp_logout();
    wp_redirect(get_option('siteurl').'/goodbye');
    exit;
  }
  else if( (strpos(strtolower($_SERVER['REQUEST_URI']),'wp-admin') !== false) && !current_user_can('manage_options') && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')) {
    wp_redirect(get_option('siteurl').'/404');
    exit;
  }

  // CLEAN UP WP HEADER TAGS AND IMPROVE WP SEO
  remove_action('wp_head', 'feed_links', 2);
  remove_action('wp_head', 'feed_links_extra', 3);
  remove_action('wp_head', 'rsd_link');
  remove_action('wp_head', 'wlwmanifest_link');
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  remove_action('wp_head', 'wp_generator');
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
  global $wp_widget_factory;
  if( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
    remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
  }
  add_filter('use_default_gallery_style', '__return_null');
  add_filter('the_generator', '__return_false');
  if (!class_exists('WPSEO_Frontend')) {
   remove_action('wp_head', 'rel_canonical');
   add_action('wp_head', 'dotsqrpress_canonical_urls');
 }
  // ADDS DOTSQRPRESS REWRITE RULES. RENAMES URLS FOR LOGIN, REGISTER, THEME 'ASSETS' FOLDER AND WP-INCLUDES.
 add_rewrite_rule( 'login/?$', 'wp-login.php', 'top' );
 add_rewrite_rule( 'register/?$', 'wp-login.php?action=register', 'top' );
 add_rewrite_rule( 'retrieve/?$', 'wp-login.php?action=lostpassword', 'top' );
 add_rewrite_rule( 'api/auth/?$', 'wp-login.php?action=oauth1_authorize', 'top' );
 add_rewrite_rule( 'views/(.*)', THEME_PATH . '/assets/$1', 'top' );
 add_rewrite_rule( 'vendors/(.*)', 'wp-includes/$1', 'top' );
 add_rewrite_rule( 'lib/(.*)', DOTSQRPRESS_CONTENTS . '/plugins/$1', 'top' );
 add_rewrite_rule( 'ui/(.*)', THEME_PATH, 'top' );
 // ADDS AJAX LOGIN & SIGNUP FOR UNLOGGED USERS
 if (!is_user_logged_in()) {
    add_action('init', 'ajax_auth_init');
 }
	// INIT END
}

# LOAD OPTION-TREE FRAMEWORK AND SETTINGS, VISIT GITHUB REPO FOR MORE INFO (github.com/valendesigns/option-tree)
include( 'option-tree/ot-loader.php' );


# LOAD WP-API PLUGIN AND MODULES, VISIT WEBSITE FOR MORE INFO (wp-api.com)
include( 'api/plugin.php' );
include( 'api/oauth1/oauth-server.php' );
remove_action( 'wp_head', 'json_output_link_wp_head', 10 );

# LOAD METABOXES
include( 'metabox/meta-box.php' );


# CANONICAL URLS TO HEADER IF NOT USING WPSEO
function dotsqrpress_canonical_urls() {
  global $wp_the_query;
  if (!is_singular()) {
    return;
  }
  if (!$id = $wp_the_query->get_queried_object_id()) {
    return;
  }
  $link = get_permalink($id);
  echo "\t<link rel=\"canonical\" href=\"$link\">\n";
}

# DOTSQRPRESS THEME LOCALE DEFAULTS. JUST REMEMBER TO USE YOUR THEME NAME AS DOMAIN PATH
function dotsqrpress_themes_setup(){
  if ( $loaded = load_theme_textdomain( THEME_NAME, trailingslashit( WP_LANG_DIR ) . THEME_NAME ) ) {
    return $loaded;
  } elseif ( $loaded = load_theme_textdomain( THEME_NAME, get_stylesheet_directory() . '/languages' )) {
    return $loaded;
  } else {
    load_theme_textdomain( THEME_NAME, get_template_directory() . '/languages' );
  }
}
add_action('after_setup_theme', 'dotsqrpress_themes_setup');


# UPDATE PROD SITE URL
if (defined('PROD_URL')) {
	update_option('siteurl',PROD_URL);
	update_option('home',PROD_URL);

  $host = DB_HOST;
  $username = DB_USER;
  $password = DB_PASSWORD;
  $database = DB_NAME;
  $string_to_replace  = WP_SITEURL;
  $new_string = PROD_URL;

  mysql_connect($host, $username, $password);
  mysql_select_db($database);
  $sql = "SHOW TABLES FROM ".$database;
  $tables_result = mysql_query($sql);

  if (!$tables_result) {
    echo "Database error, could not list tables\nMySQL error: " . mysql_error();
    exit;
  }

  echo "In these fields '$string_to_replace' have been replaced with '$new_string'\n\n";
  while ($table = mysql_fetch_row($tables_result)) {
    echo "Table: {$table[0]}\n";
    $fields_result = mysql_query("SHOW COLUMNS FROM ".$table[0]);
    if (!$fields_result) {
      echo 'Could not run query: ' . mysql_error();
      exit;
    }
    if (mysql_num_rows($fields_result) > 0) {
      while ($field = mysql_fetch_assoc($fields_result)) {
        if (stripos($field['Type'], "VARCHAR") !== false || stripos($field['Type'], "TEXT") !== false) {
          echo "  ".$field['Field']."\n";
          $sql = "UPDATE ".$table[0]." SET ".$field['Field']." = replace(".$field['Field'].", '$string_to_replace', '$new_string')";
          mysql_query($sql);
        }
      }
      echo "\n";
    }
  }

  mysql_free_result($tables_result);
}

# HTACCESS CUSTOM RULES. YOU CAN ADD EXTRAS IF YOU KNOW WHAT YOU'RE DOING.
function dotsqrpress_custom_rules( $rules )
{
  $my_content = <<<EOD
  \n # BEGIN CUSTOM RULES
  <Files wp-config.php>
    Order Allow,Deny
    Deny from all
  </Files>

  Options All -Indexes

  <IfModule mod_rewrite.c>
   <Files sitemap.xml>
    Header set X-Robots-Tag "noindex"
  </Files>
</IfModule>

ExpiresActive Off
ExpiresByType image/gif "access plus 30 days"
ExpiresByType image/jpeg "access plus 30 days"
ExpiresByType image/png "access plus 30 days"
ExpiresByType text/css "access plus 1 week"
ExpiresByType text/javascript "access plus 1 week"

# END CUSTOM RULES\n
EOD;
return $my_content . $rules;
}
//add_filter('mod_rewrite_rules', 'dotsqrpress_custom_rules');

# REDIRECT USERS AFTER SUCCESSFUL LOGIN. IF ADMIN TO BACKEND; IF USER TO WELCOME PAGE.
add_filter( 'login_redirect', 'dotsqrpress_login_redirect', 10, 3 );
function dotsqrpress_login_redirect( $redirect_to, $request, $user ) {
  global $user;
  if ( isset( $user->roles ) && is_array( $user->roles ) ) {
    if ( in_array( 'administrator', $user->roles ) ) {
      return admin_url();
    } else {
      return 'welcome';
    }
  } else {
    return 'welcome';
  }
}
# LETS USERS USE BOTH USERNAME OR EMAIL TO LOGIN
add_filter('authenticate', 'dotsqrpress_allow_email_login', 20, 3);
function dotsqrpress_allow_email_login( $user, $username, $password ) {
  if ( is_email( $username ) ) {
    $user = get_user_by_email( $username );
    if ( $user ) $username = $user->user_login;
  }
  return wp_authenticate_username_password( null, $username, $password );
}

# CHANGE DEFAULT WP LOGOUT URL. MAKE SURE TO MATCH INIT AND HTACCESS SETTINGS.
add_filter('logout_url', 'dotsqrpress_logout_url', 10, 2);
function dotsqrpress_logout_url($logout_url, $redirect)
{
  return "/logout";
}
add_filter( 'login_url', 'dotsqrpress_login_url', 10, 2 );
function dotsqrpress_login_url( $login_url, $redirect ) {
  return "/login";
}
add_filter('register','dotsqrpress_register_url');
function dotsqrpress_register_url($url){
  return str_replace(site_url('wp-login.php?action=register', 'login'),site_url('register', 'login'),$url);
}
add_filter('lostpassword_url','dotsqrpress_retrievepass_url');
function dotsqrpress_retrievepass_url($url){
 return '/retrieve';
}

# REMOVE FILE VERSION FROM ALL CSS AND JS, ALSO WP-INCLUDE
add_filter( 'style_loader_src', 'dotsqrpress_remove_ver_src', 9999 );
add_filter( 'script_loader_src', 'dotsqrpress_remove_ver_src', 9999 );
function dotsqrpress_remove_ver_src( $src ) {
  $src = remove_query_arg( array('ver','version'), $src );
  return $src;
}

add_filter( 'style_loader_src', 'dotsqrpress_pretty_urls', 9999 );
add_filter( 'script_loader_src', 'dotsqrpress_pretty_urls', 9999 );
add_filter( 'attachment_link', 'dotsqrpress_pretty_urls', 9999 );
function dotsqrpress_pretty_urls( $src ) {
  $src = str_replace('wp-includes','vendors',$src);
  $src = str_replace(THEME_PATH,'ui',$src);
  $src = str_replace(DOTSQRPRESS_CONTENTS . '/plugins','lib',$src);
  return $src;
}


# STOP REDIRECTING TO SIMILAR URL
add_filter('redirect_canonical', 'dotsqrpress_no_similar_url');
function dotsqrpress_no_similar_url($url) {
 if (is_404()) {
   return false;
 }
 return $url;
}

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

# CUSTOM ROBOTS.TXT
add_filter( 'robots_txt', 'dotsqrpress_robots', 10, 2 );
function dotsqrpress_robots( $output, $public ) {
  $output .= "Disallow: /wp-admin/" . "\n" .
  "Disallow: /wp-includes/" . "\n" .
  "Disallow: /wp-content/plugins/" . "\n" .
  "Disallow: /wp-content/themes/" . "\n" .
  "Disallow: /feed/" . "\n" .
  "Disallow: */feed/" . "\n";
  return $output;
}

# CLEAN STYLES LINKS
function dotsqrpress_clean_style($input) {
	preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $input, $matches);
	$media = $matches[3][0] === 'print' ? ' media="print"' : '';
	return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n";
}
add_filter('style_loader_tag', 'dotsqrpress_clean_style');

# CLEAN SCRIPT TAGS AND MAKE THEM DEFER OR ASYNC
function dotsqrpress_scripts_tags ($url) {
  $clean_url = str_replace(array('?defer', '?async'),'',$url);
  if (strpos( $url, '.js?defer' )) {
    return "$clean_url' defer='defer";
  } elseif (strpos( $url, '.js?async' )) {
    return "$clean_url' defer='defer' async='async";
  } else {
    return $url;
  }
}
add_filter( 'clean_url', 'dotsqrpress_scripts_tags', 11, 1 );

# REMOVE BODYCLASS, ADD PAGESLUG CLASS
function dotsqrpress_body_class($classes) {
  if (is_single() || is_page() && !is_front_page()) {
    $classes[] = basename(get_permalink());
  }
  $parent_page_class = 'parent-pageid-' . get_post_ancestors( get_the_ID() )[0];
  $home_id_class = 'page-id-' . get_the_ID();
  $page_id_class = 'postid-' . get_the_ID();
  $template_name_class = basename(get_page_template(), '.php');
  $remove_classes = array('page-template-default', 'page-child', 'page-parent', $parent_page_class, $home_id_class, $page_id_class, $template_name_class);
  $classes = array_diff($classes, $remove_classes);

  return $classes;
}
add_filter('body_class', 'dotsqrpress_body_class');

# ADD PARENT PAGE CLASS TO CURRENT PAGE BODY
function dotsqrpress_parentpage_body_class($classes) {
  global $wpdb, $post;
  $query = new WP_Query();
  $all_pages = $query->query(array('post_type' => 'page'));
  if (is_page()) {
    if ($post->post_parent) {
      $parent  = end(get_post_ancestors($current_page_id));
      $post_data = get_post($parent, ARRAY_A);
      $classes[] = 'parent-' . $post_data['post_name'];
    } elseif (get_page_children(get_the_ID(), $all_pages)) {
      $classes[] = 'parent-page';
    }
  }
  return $classes;
}
add_filter('body_class','dotsqrpress_parentpage_body_class');

# REMOVE DEFAULT CONTACT FIELDS, REPLACE WITH USEFUL ONES
function dotsqrpress_useful_user_fields( $user_fields ) {
	unset($user_fields['aim']);
	unset($user_fields['jabber']);
	unset($user_fields['yim']);
	$user_fields['phone'] = 'Phone';
	$user_fields['mobile'] = 'Mobile';
	$user_fields['address'] = 'Address';

	return $user_fields;
}
add_filter('user_contactmethods', 'dotsqrpress_useful_user_fields');

# ADD DOTSQRPRESS FAVICON PACK
function dotsqrpress_favicon() {
 echo '<link rel="apple-touch-icon" sizes="57x57" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-57x57.png">
 <link rel="apple-touch-icon" sizes="114x114" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-114x114.png">
 <link rel="apple-touch-icon" sizes="72x72" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-72x72.png">
 <link rel="apple-touch-icon" sizes="144x144" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-144x144.png">
 <link rel="apple-touch-icon" sizes="60x60" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-60x60.png">
 <link rel="apple-touch-icon" sizes="120x120" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-120x120.png">
 <link rel="apple-touch-icon" sizes="76x76" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-76x76.png">
 <link rel="apple-touch-icon" sizes="152x152" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-152x152.png">
 <link rel="apple-touch-icon" sizes="180x180" href="' . get_bloginfo('url') . '/core/assets/favicon/apple-touch-icon-180x180.png">
 <link rel="icon" type="image/png" href="' . get_bloginfo('url') . '/core/assets/favicon/favicon-192x192.png" sizes="192x192">
 <link rel="icon" type="image/png" href="' . get_bloginfo('url') . '/core/assets/favicon/favicon-160x160.png" sizes="160x160">
 <link rel="icon" type="image/png" href="' . get_bloginfo('url') . '/core/assets/favicon/favicon-96x96.png" sizes="96x96">
 <link rel="icon" type="image/png" href="' . get_bloginfo('url') . '/core/assets/favicon/favicon-16x16.png" sizes="16x16">
 <link rel="icon" type="image/png" href="' . get_bloginfo('url') . '/core/assets/favicon/favicon-32x32.png" sizes="32x32">
 <meta name="msapplication-TileImage" content="' . get_bloginfo('url') . '/core/assets/favicon/mstile-144x144.png">
 <meta name="apple-mobile-web-app-title" content="DOTSQRPress - ' . get_bloginfo('name') . '">
 <meta name="application-name" content="DOTSQRPress - ' . get_bloginfo('name') . '">
 <meta name="msapplication-TileColor" content="#2b5797">
 <meta name="msapplication-config" content="' . get_bloginfo('url') . '/core/assets/favicon/browserconfig.xml"> ';
}
add_action( 'admin_head', 'dotsqrpress_favicon' );

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

# CHECKS BROWSERS AND ADDS DEVICE AND BROWSER CLASSES TO BODY
add_filter('body_class','dotsqrpress_device_class');
add_filter('body_class','dotsqrpress_browser_class');
function dotsqrpress_device_class($classes) {
  global $is_mobile, $is_ios, $is_android, $is_bbos, $is_windows, $is_iphone, $is_ipad;
  $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
  if (empty($agent)) {
    return;
  }
  $mobile_devices = array(
    'is_iphone' => 'iphone',
    'is_ipad' => 'ipad',
    'is_kindle' => 'kindle'
    );
  $mobile_oss = array(
    'is_ios' => 'ip(hone|ad|od)',
    'is_android' => 'android',
    'is_webos' => '(web|hpw)os',
    'is_palmos' => 'palm(\s?os|source)',
    'is_windows' => 'windows (phone|ce)',
    'is_symbian' => 'symbian(\s?os|)|symbos',
    'is_bbos' => 'blackberry(.*?version\/\d+|\d+\/\d+)',
    'is_bada' => 'bada',
    'is_mac' => 'macintosh'
    );
  $mobile_browsers = array(
    'is_opera_mobile' => 'opera (mobi|mini)',
    'is_webkit_mobile' => '(android|nokia|webos|hpwos|blackberry).*?webkit|webkit.*?(mobile|kindle|bolt|skyfire|dolfin|iris)', // Webkit mobile
    'is_firefox_mobile' => 'fennec',
    'is_ie_mobile' => 'iemobile|windows ce',
    'is_netfront' => 'netfront|kindle|psp|blazer|jasmine',
    'is_uc_browser' => 'ucweb'
    );
  $groups = array($mobile_devices, $mobile_oss, $mobile_browsers);
  foreach ($groups as $group) {
    foreach ($group as $name => $regex) {
      if (preg_match('/'.$regex.'/i', $agent)) {
        global $$name;
        $is_mobile = $$name = true;
        break;
      }
    }
  }
  if ($is_mobile === false) {
    $regex = 'nokia|motorola|sony|ericsson|lge?(-|;|\/|\s)|htc|samsung|asus|mobile|phone|tablet|pocket|wap|wireless|up\.browser|up\.link|j2me|midp|cldc|kddi|mmp|obigo|novarra|teleca|openwave|uzardweb|pre\/|hiptop|avantgo|plucker|xiino|elaine|vodafone|sprint|o2';
    $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
    if (false !== strpos($accept,'text/vnd.wap.wml')
      || false !== strpos($accept,'application/vnd.wap.xhtml+xml')
      || isset($_SERVER['HTTP_X_WAP_PROFILE'])
      || isset($_SERVER['HTTP_PROFILE'])
      || preg_match('/'.$regex.'/i', $agent)
      ) {
      $is_mobile = true;
    }
  }
  // OS
  if     ($is_ios)     $classes[] = 'ios';
  elseif ($is_android) $classes[] = 'android';
  elseif ($is_bbos)    $classes[] = 'blackberry';
  elseif ($is_windows) $classes[] = 'windows';
  elseif ($is_mac)     $classes[] = 'mac';

  // DEVICE
  if     ($is_iphone) $classes[] = 'iphone';
  elseif ($is_ipad)   $classes[] = 'ipad';

  // MOBILE OR DESKTOP
  if     ($is_mobile)  $classes[] = 'mobile';
  elseif (!$is_mobile) $classes[] = 'desktop';


  return $classes;
}
function dotsqrpress_browser_class($classes) {
  global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome;
  if($is_lynx) $classes[] = 'lynx';
  elseif($is_gecko) $classes[] = 'gecko';
  elseif($is_opera) $classes[] = 'opera';
  elseif($is_NS4) $classes[] = 'ns4';
  elseif($is_safari) $classes[] = 'safari';
  elseif($is_chrome) $classes[] = 'chrome';
  elseif($is_IE) $classes[] = 'ie';
  else $classes[] = 'unknown';
  return $classes;
}
# ADD SCREEN ORIENTATION BODY CLASS
function dotsqrpress_orientation_class() {
  ?>
  <script async type="text/javascript">function orient(){var n=$(window).height(),o=$(window).width(),a="";if(o>n){var a="landscape";return a}var a="portrait";return a}var $=jQuery;$(window).load(function(){$("body").addClass(orient())}),$(window).resize(function(){$("body").removeClass("portrait landscape").addClass(orient())}),$(window).on("orientationchange",function(){$("body").removeClass("portrait landscape").addClass(orient())});</script>
  <?php
}
add_action('wp_footer', 'dotsqrpress_orientation_class');

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

# REWRITE SEARCH URLS AND CUSTOM SEARCH FORM. MAKE SURE TO ENABLE CUSTOM PERMALINK STRUCTURE.
function dotsqrpress_search_redirect() {
  global $wp_rewrite;
  if (!isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks()) {
    return;
  }
  $search_base = $wp_rewrite->search_base;
  if (is_search() && !is_admin() && strpos($_SERVER['REQUEST_URI'], "/{$search_base}/") === false) {
    wp_redirect(home_url("/{$search_base}/" . urlencode(get_query_var('s'))));
    exit();
  }
}
add_action('template_redirect', 'dotsqrpress_search_redirect');
function dotsqrpress_request_filter($query_vars) {
  if (isset($_GET['s']) && empty($_GET['s'])) {
    $query_vars['s'] = ' ';
  }
  return $query_vars;
}
add_filter('request', 'dotsqrpress_request_filter');

# MELAPRESS DEFAULT MENU NAV WALKER.
class MelaPress_Walker extends Walker_Nav_Menu {

  function start_lvl(&$output, $depth = 0, $args = array()) {
    $output .= "\n<ul class=\"dropdown-menu\">\n";
  }

  function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
    $element->is_dropdown = !empty($children_elements[$element->ID]);
    if ($element->is_dropdown) {
      if ($depth === 0) {
        $element->classes[] = 'dropdown';
      } elseif ($depth === 1) {
        $element->classes[] = 'dropdown-submenu';
      }
    }
    parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
  }
}

# CUSTOM WALKER SETTINGS. DEPTH 3, NO CONTAINER, SIMPLIFIED WRAPPER.
function dotsqrpress_nav_menu_args($args = '') {
  $dotsqrpress_nav_menu_args['container'] = false;
  if (!$args['items_wrap']) {
    $dotsqrpress_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
    $dotsqrpress_nav_menu_args['depth'] = 3;
  }
  if (!$args['walker']) {
    $dotsqrpress_nav_menu_args['walker'] = new MelaPress_Walker();
  }
  return array_merge($args, $dotsqrpress_nav_menu_args);
}

add_filter('wp_nav_menu_args', 'dotsqrpress_nav_menu_args');
function is_it_empty($val) {
  return empty($val);
}
function dotsqrpress_nav_menu_css_class($classes, $item) {
  $slug = sanitize_title($item->title);
  $classes = preg_replace('/(current(-menu-|[-_]page[-_]|_page_)(item|parent|ancestor))/', 'active', $classes);
  $classes = preg_replace('/^((menu|page)[-_\w+]+)+/', '', $classes);
  $classes[] = 'item-' . $slug;
  $classes = array_unique($classes);

  return array_filter($classes);
}
add_filter('nav_menu_css_class', 'dotsqrpress_nav_menu_css_class', 10, 2);
add_filter('nav_menu_item_id', '__return_null');

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

# CLEANUP THUMBNAILS ATTRIBUTES AND MAKE THEM USEFUL. TURNS IMG TITLE INTO CLEAN CSS CLASS.
function clean_class($dsp_class)
{
  $src = 'àáâãäçèéêëìíîïñòóôõöøùúûüýÿßÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖØÙÚÛÜÝ';
  $rep = 'aaaaaceeeeiiiinoooooouuuuyysAAAAACEEEEIIIINOOOOOOUUUUY';
  $dsp_class = strtr(utf8_decode($dsp_class), utf8_decode($src), $rep);
  $dsp_class = strtolower($dsp_class);
  $dsp_class = preg_replace("/[^a-z0-9\s._-]/", "", $dsp_class);
  $dsp_class = preg_replace("/[\s._-]+/", " ", $dsp_class);
  $dsp_class = preg_replace("/[\s]/", "-", $dsp_class);
  return $dsp_class;
}

function dotsqrpress_thumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {
  $id = get_post_thumbnail_id();
  $src = wp_get_attachment_image_src($id, $size);
  $alt = get_the_title($id);
  $ext_class = clean_class($alt);
  $site = get_bloginfo('name');
  $class = $attr['class'];
  $html = '<img src="' . $src[0] . '" title="' . $alt . ' - ' . $site . '" alt="' . $alt . ' - ' . $site . '" class="img-responsive thumbnail-' . $ext_class .' ' . $class . '" />';

  return $html;
}
add_filter('post_thumbnail_html', 'dotsqrpress_thumbnail', 99, 5);

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

# ADD ANALYTICS ON ALL SITE IF DEFINED IN DOTSQRPRESS CONF FILE
function dotsqrpress_analytics() {
  ?>
  <noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?php echo GTM; ?>"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <script async type="text/javascript">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','<?php echo GTM; ?>');</script>
  <?php
}
if (defined('GTM')) {
	add_action('wp_footer', 'dotsqrpress_analytics');
}

# RESIZE UPLOADED IMAGES ACCORDING TO WP LARGE SIZE SET IN WP SETTINGS. THIS AVOIDS UPLOADING VERY BIG FILES.
function dotsqrpress_replace_big_images($image_data) {
	if (!isset($image_data['sizes']['large'])) return $image_data;
	$upload_dir = wp_upload_dir();
	$uploaded_image_location = $upload_dir['basedir'] . '/' .$image_data['file'];
	$large_image_location = $upload_dir['path'] . '/'.$image_data['sizes']['large']['file'];
	unlink($uploaded_image_location);
	rename($large_image_location,$uploaded_image_location);
	$image_data['width'] = $image_data['sizes']['large']['width'];
	$image_data['height'] = $image_data['sizes']['large']['height'];
	unset($image_data['sizes']['large']);
	return $image_data;
}
add_filter('wp_generate_attachment_metadata','dotsqrpress_replace_big_images');

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

# REMOVE 'PRIVATE:' PREFIX FROM HIDDEN PAGES
function dotsqrpress_private_pages($title) {
	$title = esc_attr($title);
	$findthese = array(
   '#Protected:#',
   '#Private:#',
   '#Protetto:#',
   '#Privato:#'
   );
	$replacewith = array(
	    '', // What to replace "Protected:" with
	    '' // What to replace "Private:" with
     );
	$title = preg_replace($findthese, $replacewith, $title);
	return $title;
}
add_filter('the_title', 'dotsqrpress_private_pages');


#####################
# ADMIN FEATURES. REMEMBER TO SET THE SUPER ADMIN (MAIN_USER) USERNAME IN THE CONF FILE.

# ADD ADMIN BAR LINK TO THEME SETTINGS - POWERED BY OPTION TREE
add_action( 'admin_bar_menu', 'dotsqrpress_adminbar_links', 999 );
function dotsqrpress_adminbar_links( $wp_admin_bar ) {
	$dotsqrpress_node = array(
		'id'    => 'dotsqrpress-adminbar-icon',
		'title' => '<span class="ab-icon"></span><span class="ab-label">' . get_bloginfo('name') . '</span>',
		'href'  => site_url(),
		'meta'  => array( 'class' => 'dotsqrpress-adminbar-icon' )
   );
	$view_site_node = array(
		'id'    => 'dotsqrpress_goto_site',
		'parent'=> 'dotsqrpress-adminbar-icon',
		'title' => __('Settings'),
		'href'  => admin_url('themes.php?page=ot-theme-options'),
		'meta'  => array( 'class' => 'dotsqrpress-theme-settings' )
   );
	$logout_node = array(
		'id'    => 'dotsqrpress-logout',
		'title' => '<span class="ab-icon"></span><span class="ab-label">' . __('Log Out') . ' ' . wp_get_current_user()->user_login . '</span>',
		'href'  => site_url('logout'),
		'meta'  => array( 'class' => 'dotsqrpress-logout' )
   );
	$wp_admin_bar->add_node($dotsqrpress_node);
	$wp_admin_bar->add_node($view_site_node);
	$wp_admin_bar->add_node($logout_node);
	$wp_admin_bar->remove_node('comments');
	$wp_admin_bar->remove_menu('my-account');
	$wp_admin_bar->remove_menu('site-name');
	$wp_admin_bar->remove_menu('wp-logo');
	$wp_admin_bar->remove_menu('updates');
	$wp_admin_bar->remove_menu('search');
  $wp_admin_bar->remove_menu('bp-notifications');
}

# DISABLE ADMIN BAR FOR ALL USERS
add_filter('show_admin_bar', '__return_false');

# DEFAULT DOTSQRPRESS FOOTER FOR ADMIN
function dotsqrpress_remove_footer () {
	return '';
}
add_filter ('update_footer', 'dotsqrpress_remove_footer', 99);
add_filter ('admin_footer_text', 'dotsqrpress_remove_footer');

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
    add_filter( 'ot_show_docs', '__return_false', 9999 );
  }
}
# MAKE SURE TO DISABLE WP AUTOMATIC UPDATES. YOU CAN MANUALLY UPDATE IT IF YOU ARE MAIN_USER.
add_filter( 'automatic_updater_disabled', '__return_true' );
add_filter( 'auto_update_theme', '__return_false' );
add_filter( 'auto_update_plugin', '__return_false' );
add_filter( 'auto_core_update_send_email', '__return_false' );

# RESTRICT ADMIN MENU. SHOW ALL FIELDS ONLY TO WEBMASTER, AS DEFINED IN WP-CONFIG.
function dotsqrpress_restrict_admin()
{
  global $menu;
  global $current_user;
  get_currentuserinfo();
  if($current_user->user_login !== MAIN_USER && defined('MAIN_USER')) {
    $restricted = array(__('Media'),
      __('Dashboard'),
      __('Links'),
      __('Comments'),
      __('Appearance'),
      __('Plugins'),
      __('Tools'),
      __('Settings')
      );
    end ($menu);
    while (prev($menu)){
      $value = explode(' ',$menu[key($menu)][0]);
      if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
        }// end while
    }// end if
  }
  add_action('admin_menu', 'dotsqrpress_restrict_admin');

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

# ENABLE AJAX SIGNUP & LOGIN
  function ajax_auth_init(){
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
    add_action( 'wp_ajax_nopriv_ajaxregister', 'ajax_register' );
    add_action( 'wp_ajax_nopriv_ajaxretrieve', 'ajax_retrieve' );
  }

  function ajax_login(){
    check_ajax_referer( 'ajax-login-nonce', 'security' );
    auth_user_login($_POST['email'], $_POST['password'], 'Login');
    die();
  }

  function ajax_register(){
    check_ajax_referer( 'ajax-register-nonce', 'security' );
    $info = array();
    $info['user_nicename'] = $info['nickname'] = $info['display_name'] = $info['user_login'] = sanitize_user($_POST['username']);
    $info['first_name'] = sanitize_text_field($_POST['name']);
    $info['user_pass'] = sanitize_text_field($_POST['password']);
    $info['user_email'] = sanitize_email( $_POST['email']);

    $user_register = wp_insert_user($info);
    if (is_wp_error($user_register)) {
      $error  = $user_register->get_error_codes() ;
      if(in_array('empty_user_login', $error))
        echo json_encode(array('loggedin'=>false, 'message'=>__($user_register->get_error_message('empty_user_login'))));
      elseif(in_array('existing_user_login',$error))
        echo json_encode(array('loggedin'=>false, 'message'=>__('Utente già registrato.')));
      elseif(in_array('existing_user_email',$error))
          echo json_encode(array('loggedin'=>false, 'message'=>__('Email già registrata.')));
    } else {
      auth_user_login($info['user_email'], $info['user_pass'], 'Registration');
    }
    die();
  }

  function ajax_retrieve(){
    check_ajax_referer( 'ajax-retrieve-nonce', 'security' );
    auth_retrieve($_POST['email']);
    die();
  }

  function auth_user_login($user_login, $password, $login) {
    $info = array();
    $info['user_login'] = $user_login;
    $info['user_password'] = $password;
    $info['remember'] = true;
    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon)) {
      echo json_encode(array('loggedin'=>false, 'message'=>__('Password e utente non corrispondono.')));
    } else {
      wp_set_current_user($user_signon->ID);
      echo json_encode(array('loggedin'=>true, 'message'=>__($login.' riuscito, aggiorno pagina...')));
    }
    die();
  }

  function auth_retrieve($user_login) {
    global $wpdb, $wp_hasher;
    $user_login = sanitize_text_field($user_login);

    if ( empty( $user_login) ) {
        return false;
        echo json_encode(array('loggedin'=>false, 'message'=>__('Password e utente non corrispondono.')));
    } else if ( strpos( $user_login, '@' ) ) {
        $user_data = get_user_by( 'email', trim( $user_login ) );
        if ( empty( $user_data ) )
          return false;
          echo json_encode(array('loggedin'=>false, 'message'=>__('Email non valida.')));
    } else {
        $login = trim($user_login);
        $user_data = get_user_by('login', $login);
    }
    do_action('lostpassword_post');

    if ( !$user_data ) {
      return false;
      echo json_encode(array('loggedin'=>false, 'message'=>__('Utente non esistente.')));
    }

    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;

    do_action('retrieve_password', $user_login);
    $allow = apply_filters('allow_password_reset', true, $user_data->ID);

    if ( ! $allow ) {
      return false;
    } else if ( is_wp_error($allow) ) {
      echo json_encode(array('loggedin'=>false, 'message'=>__('Il processo non è andato a buon fine.')));
      return false;
    }

    $key = wp_generate_password( 20, false );
    do_action( 'retrieve_password_key', $user_login, $key );

    if ( empty( $wp_hasher ) ) {
        require_once ABSPATH . 'wp-includes/class-phpass.php';
        $wp_hasher = new PasswordHash( 8, true );
    }
    $hashed = $wp_hasher->HashPassword( $key );
    $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

    $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
    $message .= network_home_url( '/' ) . "\r\n\r\n";
    $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
    $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
    $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
    $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

    if ( is_multisite() ) {
      $blogname = $GLOBALS['current_site']->site_name;
    } else {
      $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    }
    $title = sprintf( __('[%s] Password Reset'), $blogname );
    $title = apply_filters('retrieve_password_title', $title);
    $message = apply_filters('retrieve_password_message', $message, $key);
    if ( $message && !wp_mail($user_email, $title, $message) ) {
      echo json_encode(array('loggedin'=>false, 'message'=>__('The e-mail could not be sent. Contact the site administrators.')));
    } else {
      echo json_encode(array('loggedin'=>false, 'message'=>__('We successfully sent you an email with a link to reset your password.')));
    }
  }

# ADD WEB APP CAPABILITIES FOR IOS7. ADD 'MINIMAL-UI' TO VIEWPORT META.
  function dotsqrpress_ios_webapp() {
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="' . get_bloginfo( 'name' ) . '">' . "\n";
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
  }
  add_action( 'wp_head', 'dotsqrpress_ios_webapp' , 2 );


#####################
# FLUSH REWRITE RULES ON THEME CHANGE AND CHECKS IF DOTSQRPRESS CORE HAS BEEN MODIFIED, THEN FLUSHES.

  add_action( 'after_switch_theme', 'dotsqrpress_flush_ontheme' );
  function dotsqrpress_flush_ontheme() {
   global $wp_rewrite;
   $wp_rewrite->flush_rules();
 }

 add_action( 'admin_init','dotsqrpress_core_mods_check', 1 );
 function dotsqrpress_core_mods_check() {
   $dotsqrpress_options_check = get_option('dotsqrpress');
   $core_ver = filemtime( __FILE__ );

   if ( $dotsqrpress_options_check['core_version'] != $core_ver ) {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
    dotsqrpress_option_update('core_version',$core_ver,'dotsqrpress');
  }
}

######################
# OPEN GRAPH GENERATOR
function ogp_namespace($output) {
  return $output.' prefix="og: http://ogp.me/ns#"';
}
add_filter('language_attributes','ogp_namespace');

function ogp_images() {
  global $post, $posts, $forum_id;
  if(bbp_is_forum()) {
    $ogp_images = 'hello.jpg';
  } else {
    $content = $post->post_content;
    $output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches );
    if ( $output === FALSE ) {
      return false;
    }
    $ogp_images = array();
    foreach ( $matches[1] as $match ) {
      // If the image path is relative, add the site url to the beginning
      if ( ! preg_match('/^https?:\/\//', $match ) ) {
        // Remove any starting slash with ltrim() and add one to the end of site_url()
        $match = site_url( '/' ) . ltrim( $match, '/' );
      }
      $ogp_images[] = $match;
    }
  }
  return $ogp_images;
}

add_action( 'init', 'ogp_init', 0 );
function ogp_init() {
  if ( ! is_feed() ) {
    ob_start( 'ogp_callback' );
  }
}
function ogp_callback( $content ) {
  $title = preg_match( '/<title>(.*)<\/title>/', $content, $title_matches );
  $description = preg_match( '/<meta name="description" content="(.*)"/', $content, $description_matches );
  if ( $title !== FALSE && count( $title_matches ) == 2 ) {
    $content = preg_replace( '/<meta property="og:title" content="(.*)">/', '<meta property="og:title" content="' . $title_matches[1] . '">', $content );
  }
  if ( $description !== FALSE && count( $description_matches ) == 2 ) {
    $content = preg_replace( '/<meta property="og:description" content="(.*)">/', '<meta property="og:description" content="' . $description_matches[1] . '">', $content );
  }

  return $content;
}

function ogp_head() {
  global $post;
  ?>
  <?php if (defined('FB_APPID')) { ?>
  <meta property="fb:app_id" content="<?php echo FB_APPID; ?>"/>
  <?php } ?>
  <meta property="og:url" content="<?php $ogp_url = 'http' . (is_ssl() ? 's' : '') . "://".$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; echo $ogp_url; ?>"/>
  <meta property="og:title" content="<?php echo get_the_title(); ?>"/>
  <meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>"/>
  <meta property="og:description" content="<?php if ( has_excerpt( $post->ID ) ) { $ogp_description = strip_tags( get_the_excerpt() ); } else { $ogp_description = str_replace( "\r\n", ' ' , substr( strip_tags( strip_shortcodes( $post->post_content ) ), 0, 160 ) ); } ?>"/>
  <meta property="og:type" content="<?php if (is_single()) { $ogp_type = 'article'; } else { $ogp_type = 'website'; } echo $ogp_type; ?>"/>
  <?php $ogp_images = array();
  if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
    $thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
    $link = $thumbnail_src[0];
    if ( ! preg_match( '/^https?:\/\//', $link ) ) {
      $link = site_url( '/' ) . ltrim( $link, '/' );
    }
    $ogp_images[] = $link;
  }
  if ( ogp_images() !== false && is_singular() ) {
    $ogp_images = array_merge( $ogp_images, ogp_images() );
  }
  if ( ! empty( $ogp_images ) && is_array( $ogp_images ) ) {
    foreach ( $ogp_images as $image ) {
      echo '<meta property="og:image" content="' . esc_url( apply_filters( 'wpfbogp_image', $image ) ) . '"/>' . "\n";
    }
  }
  ?>
  <meta property="og:locale" content="<?php echo get_locale(); ?>"/>
  <?php
}
add_action('wp_head','ogp_head',50);
?>
