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

# INIT & DEFINITIONS
$get_theme_name = explode('/themes/', get_template_directory());
define('CORE_ASSET', '/core/assets/'); // MODIFY ONLY IF YOU RENAMED THE DOTSQRPRESS CORE AND CORE ASSETS FOLDER
define('DOTSQRPRESS_CONTENTS', 'wp-content' ); // CHANGE ONLY IF YOU DEFINED A DIFFERENT FOLDER FOR CONTENTS
define('THEME_NAME', next($get_theme_name));
define('THEME_PATH', DOTSQRPRESS_CONTENTS . '/themes/' . THEME_NAME);
add_action('init', 'dotsqrpress_locale');
function dotsqrpress_locale()
{
    $domain = 'dotsqrpress_core';
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' ) ) {
    return $loaded;
  } else {
    load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
  }
}
add_action('after_setup_theme', 'dotsqrpress_themes_setup');
function dotsqrpress_themes_setup(){
  $domain = 'dotsqrpress';
  if ( $loaded = load_theme_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain ) ) {
    return $loaded;
  } elseif ( $loaded = load_theme_textdomain( $domain, get_stylesheet_directory() . '/languages' )) {
    return $loaded;
  } else {
    load_theme_textdomain( $domain, get_template_directory() . '/languages' );
  }
}

# UPDATE PROD SITE URL
if (defined('PROD_URL')) {
	update_option('siteurl',PROD_URL);
	update_option('home',PROD_URL);
}

# HTACCESS REWRITE RULES. RENAMES WP-ADMIN, THEME ASSETS, SIGNUP, LOGIN, LOST PASSWORD.
# IF YOU EDIT IT, REMEMBER TO FLUSH WP REWRITE RULES UPDATING THE PERMALINK SETTINGS.
add_action('generate_rewrite_rules', 'dotsqrpress_rewrite_rules');
function dotsqrpress_rewrite_rules() {
	global $wp_rewrite;
  add_rewrite_rule( 'login/?$', 'wp-login.php', 'top' );
  add_rewrite_rule( 'register/?$', 'wp-login.php?action=register', 'top' );
  add_rewrite_rule( 'retrieve/?$', 'wp-login.php?action=lostpassword', 'top' );
  add_rewrite_rule( 'views/(.*)', THEME_PATH . '/assets/$1', 'top' );
  add_rewrite_rule( 'plugins/(.*)', '/wp-includes/$1', 'top' );
  $wp_rewrite->non_wp_rules = $wp_rewrite->non_wp_rules;
}

# HTACCESS CUSTOM RULES
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

<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{QUERY_STRING} .
RewriteCond %{QUERY_STRING} !^(s|p)=.*
RewriteCond %{REQUEST_URI} !.*wp-admin.*
RewriteRule ^(.*)$ /$1? [R=301,L]
</IfModule>

# END CUSTOM RULES\n
EOD;
    return $my_content . $rules;
}
add_filter('mod_rewrite_rules', 'dotsqrpress_custom_rules');

# REDIRECT USERS WHO TRY TO DIRECTLY ACCESS WP-LOGIN AND WP-ADMIN.
# ADDED REDIRECT TO GOODBYE PAGE INSTEAD OF LOGOUT PAGE.
add_action('init', 'dotsqrpress_wp_block');
function dotsqrpress_wp_block() {
  if( (strpos(strtolower($_SERVER['REQUEST_URI']),'wp-login.php') !== false) && $_SERVER['REQUEST_METHOD'] != "POST")
  {
    wp_redirect(get_option('siteurl').'/404');
    exit;
    }
  else if( (strpos(strtolower($_SERVER['REQUEST_URI']),'logout') !== false))
  {
    wp_logout();
    wp_redirect(get_option('siteurl').'/goodbye');
    exit;
  }
  else if( (strpos(strtolower($_SERVER['REQUEST_URI']),'wp-admin') !== false) && !is_user_logged_in())
  {
    wp_redirect(get_option('siteurl').'/404');
    exit;
  }
}

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
    return str_replace('wp-includes','plugins',$src);
}

# CLEANUP WP HEAD
function dotsqrpress_head_cleaning() {
  remove_action('wp_head', 'feed_links', 2);
  remove_action('wp_head', 'feed_links_extra', 3);
  remove_action('wp_head', 'rsd_link');
  remove_action('wp_head', 'wlwmanifest_link');
  remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  remove_action('wp_head', 'wp_generator');
  remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
  global $wp_widget_factory;
  remove_action('wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style'));
  add_filter('use_default_gallery_style', '__return_null');
  if (!class_exists('WPSEO_Frontend')) {
  remove_action('wp_head', 'rel_canonical');
  add_action('wp_head', 'dotsqrpress_canonical');
  }
}

# CANONICAL URLS TO HEADER
function dotsqrpress_canonical() {
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
add_action('init', 'dotsqrpress_head_cleaning');
add_filter('the_generator', '__return_false');

# STOP REDIRECTING TO SIMILAR URL
add_filter('redirect_canonical', 'dotsqrpress_no_similar_url');
function dotsqrpress_no_similar_url($url) {
 if (is_404()) {
   return false;
 }
 return $url;
}

# DOTSQRPRESS LOGIN STYLING. REMOVES DEFAULT WP CSS WHICH IS FORCED INTO HEADER, ADDS DOTSQRPRESS CSS.
remove_filter('wp_admin_css', $tag, 99999);
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
    wp_enqueue_style( 'dotsqrpress_core', WP_SITEURL . CORE_ASSET . 'css/dotsqrpress_core.css', false, null, true );
    wp_enqueue_style('dotsqrpress_bootstrap', WP_SITEURL . '/views/css/styles.css', false, null, true);
    wp_enqueue_script( 'dotsqrpress_core_js', WP_SITEURL . CORE_ASSET . 'js/dotsqrpress_core.js', array('jquery'), null, true );
    wp_enqueue_script( 'dotsqrpress_bootstrapjs', WP_SITEURL . '/views/js/bootstrap.min.js', array('jquery'), null, false );
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

# CHANGE DEFAULT PERMALINK STRUCTURE
function dotsqrpress_permalink_structure() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%category%/%postname%/' );
}
add_action( 'init', 'dotsqrpress_permalink_structure' );

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

# REMOVE BODYCLASS, ADD PAGESLUG CLASS
function dotsqrpress_body_class($classes) {
	if (is_single() || is_page() && !is_front_page()) {
	$classes[] = basename(get_permalink());
	}
	$home_id_class = 'page-id-' . get_option('page_on_front');
	$remove_classes = array(
	'page-template-default',
	$home_id_class
	);
	$classes = array_diff($classes, $remove_classes);

	return $classes;
}
add_filter('body_class', 'dotsqrpress_body_class');

# ADD PARENT PAGE CLASS TO CURRENT PAGE BODY
function dotsqrpress_parentpage_body_class($classes) {
    global $wpdb, $post;
    if (is_page()) {
        if ($post->post_parent) {
            $parent  = end(get_post_ancestors($current_page_id));
        } else {
            $parent = $post->ID;
        }
        $post_data = get_post($parent, ARRAY_A);
        $classes[] = 'parent-' . $post_data['post_name'];
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
add_action( 'wp_head', 'dotsqrpress_favicon' );

# ADD BROWSER CLASS TO BODY
add_filter('body_class','dotsqrpress_browser_class');
function dotsqrpress_browser_class($classes) {
  global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;
  if($is_lynx) $classes[] = 'lynx';
  elseif($is_gecko) $classes[] = 'gecko';
  elseif($is_opera) $classes[] = 'opera';
  elseif($is_NS4) $classes[] = 'ns4';
  elseif($is_safari) $classes[] = 'safari';
  elseif($is_chrome) $classes[] = 'chrome';
  elseif($is_IE) $classes[] = 'ie';
  else $classes[] = 'unknown';

  if($is_iphone) $classes[] = 'iphone';
  return $classes;
}

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
function dotsqrpress_search_form() {
  locate_template('/layouts/searchform.php', true, true);
}
add_filter('get_search_form', 'dotsqrpress_get_search_form');

# DOTSQRPRESS DEFAULT MENU NAV WALKER.
class DOTSQRPRESS_Nav_Walker extends Walker_Nav_Menu {
  function check_current($classes) {
    return preg_match('/(current[-_])|active|dropdown/', $classes);
  }

  function start_lvl(&$output, $depth = 0, $args = array()) {
    $output .= "\n<ul class=\"dropdown-menu\">\n";
  }

  function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $item_html = '';
    parent::start_el($item_html, $item, $depth, $args);

    if ($item->is_dropdown && ($depth === 0)) {
      $item_html = str_replace('<a', '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"', $item_html);
      $item_html = str_replace('</a>', ' <b class="caret"></b></a>', $item_html);
    }
    elseif (stristr($item_html, 'li class="divider')) {
      $item_html = preg_replace('/<a[^>]*>.*?<\/a>/iU', '', $item_html);
    }
    elseif (stristr($item_html, 'li class="nav-header')) {
      $item_html = preg_replace('/<a[^>]*>(.*)<\/a>/iU', '$1', $item_html);
    }

    $output .= $item_html;
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
function dotsqrpress_nav_menu_css_class($classes, $item) {
  $slug = sanitize_title($item->title);
  $classes = preg_replace('/(current(-menu-|[-_]page[-_])(item|parent|ancestor))/', 'active', $classes);
  $classes = preg_replace('/^((menu|page)[-_\w+]+)+/', '', $classes);
  $classes[] = 'menu-' . $slug;
  $classes = array_unique($classes);

  return array_filter($classes, 'is_element_empty');
}
add_filter('nav_menu_css_class', 'dotsqrpress_nav_menu_css_class', 10, 2);
add_filter('nav_menu_item_id', '__return_null');

# CUSTOM WALKER SETTINGS. DEPTH 3, NO CONTAINER, SIMPLIFIED WRAPPER.
function dotsqrpress_nav_menu_args($args = '') {
  $dotsqrpress_nav_menu_args['container'] = false;
  if (!$args['items_wrap']) {
    $dotsqrpress_nav_menu_args['items_wrap'] = '<ul class="%2$s">%3$s</ul>';
    $dotsqrpress_nav_menu_args['depth'] = 3;
  }
  if (!$args['walker']) {
    $dotsqrpress_nav_menu_args['walker'] = new DOTSQRPRESS_Nav_Walker();
  }
  return array_merge($args, $dotsqrpress_nav_menu_args);
}
add_filter('wp_nav_menu_args', 'dotsqrpress_nav_menu_args');

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

# ADMIN TITLE FILTER
add_filter('admin_title', 'dotsqrpress_admin_title', 10, 2);
function dotsqrpress_admin_title($admin_title, $title)
{
    return get_bloginfo('name').' - '.$title . ' | DOTSQRPress';
}

# DISABLE MEDIA ORGANIZE BY MONTH, YEAR
add_filter( 'option_uploads_use_yearmonth_folders', '__return_false', 100 );

# DISABLE HTML COMMENTS
add_filter( 'pre_comment_content', 'wp_specialchars' );

# CREATE DEFAULT PAGES WELCOME AND GOODBYE
add_action('init','dotsqrpress_check_if_page');
function dotsqrpress_check_if_page(){
	if(get_page_by_title( 'welcome' ) == NULL) {
		dotsqrpress_create_page('welcome', 'Welcome', '<h1>Welcome to ' . get_bloginfo('name'));
	}
	if(get_page_by_title( 'goodbye') == NULL) {
		dotsqrpress_create_page('goodbye', 'Goodbye', '<h1>See you soon!</h1>');
	}
}
function dotsqrpress_create_page($name, $title, $content) {
	$dotsqrpress_page = array(
		'post_title'    => $title,
		'post_content'  => $content,
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type'     => 'page',
		'page_template' => 'notice.php',
		'post_name'     => $name
	);
	wp_insert_post( $dotsqrpress_page );
}

#DISABLE AUTOSAVE
add_action( 'wp_print_scripts', 'dotsqrpress_disable_autosave' );
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

# LOAD OPTION-TREE FRAMEWORK, VISIT GITHUB REPO FOR MORE INFO (github.com/valendesigns/option-tree)
require( 'option-tree/ot-loader.php' );

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

# ADD ANALYTICS ON ALL SITE IF DEFINED IN DOTSQRPRESS CONF FILE
function dotsqrpress_google_analytics() {
?>
<script type="text/javascript">
    var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
    document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
    try {
        var pageTracker = _gat._getTracker("UA-XXXXXXX-X");
        pageTracker._trackPageview();
    } catch(err) {}</script>
<?php
}
if (defined('GOOGLE_ANALYTICS')) {
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
	$title = attribute_escape($title);
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

# ADD 'GO TO FRONT END MENU ITEM'
add_action('admin_init','dotsqrpress_frontend_link', 2);
function dotsqrpress_frontend_link() {
	add_menu_page( 'Redirecting', '<span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x"></i><i class="fa fa-eye fa-stack-1x dotsqrpress_col"></i></span>  ' . get_bloginfo('name'), 'read', 'dotsqrpress-site', 'dotsqrpress_go_homepage');
}
add_action( 'admin_bar_menu', 'toolbar_link_to_mypage', 999 );

function toolbar_link_to_mypage( $wp_admin_bar ) {
	$args = array(
		'id'    => 'dotsqrpress_theme_settings',
		'title' => '<i class="fa fa-cog"></i>' . __('Settings') . ' ' . get_bloginfo('name'),
		'href'  => admin_url('themes.php?page=ot-theme-options'),
		'meta'  => array( 'class' => 'dotsqrpress-theme-settings' )
	);
	$wp_admin_bar->add_node( $args );
}

# SIMPLIFY CUSTOMER BACKEND AND SHOW ADVANCED OPTIONS ONLY TO SUPER ADMIN
add_action('admin_init','dotsqrpress_custom_admin_ui', 2);
function dotsqrpress_custom_admin_ui() {
	global $menu;
	global $current_user;
	get_currentuserinfo();
	if($current_user->user_login !== MAIN_USER && defined('MAIN_USER')) {
		remove_action( 'admin_notices', 'update_nag');
		remove_action( 'init', 'wp_version_check');
		remove_action('load-update-core.php','wp_update_plugins');
		add_filter( 'pre_option_update_core', '__return_null' );
		add_filter('pre_site_transient_update_core','__return_null');
		add_filter('pre_site_transient_update_plugins','__return_null');
		add_filter('pre_site_transient_update_themes','__return_null');
		remove_menu_page('ot-settings');
		apply_filters( 'ot_theme_options_parent_slug', 'dotsqrpress_options', 999);
		apply_filters( 'ot_theme_options_position', 6, 999);
	}
}

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
                            __('Settings'),
                            __('Users')
        );
        end ($menu);
        while (prev($menu)){
            $value = explode(' ',$menu[key($menu)][0]);
            if(in_array($value[0] != NULL?$value[0]:"" , $restricted)){unset($menu[key($menu)]);}
        }// end while
    }// end if
}
add_action('admin_menu', 'dotsqrpress_restrict_admin');

# HIDE WEBMASTER FROM USER LIST
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


#####################
# FLUSH REWRITE RULES ON THEME CHANGE AND ON DOTSQRPRESS ACTIVATION/DEACTIVATION
add_action( 'after_switch_theme', 'dotsqrpress_flush_ontheme' );
function dotsqrpress_flush_ontheme() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
add_action( 'muplugins_loaded', 'dotsqrpress_core_plugin_loaded' );
function dotsqrpress_core_plugin_loaded() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

?>
