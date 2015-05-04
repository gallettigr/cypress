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


# ADD WEB APP CAPABILITIES FOR IOS7. ADD 'MINIMAL-UI' TO VIEWPORT META.
  function dotsqrpress_ios_webapp() {
    echo '<meta name="apple-mobile-web-app-capable" content="yes">' . "\n";
    echo '<meta name="apple-mobile-web-app-title" content="' . get_bloginfo( 'name' ) . '">' . "\n";
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . "\n";
  }
  add_action( 'wp_head', 'dotsqrpress_ios_webapp' , 2 );



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

