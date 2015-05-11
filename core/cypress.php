<?php
/**
 * Cypress core and WordPress mu-plugin.
 * @package Cypress
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 * Plugin Name: Cypress
 * Contributors: gallettigr
 * Plugin URI: http://github.com/gallettigr/cypress
 * Description: Less mess for WordPress.
 * Author: gallettigr
 * Version: 0.8
 * Author URI: http://twitter.com/gallettigr
 * Textdomain: cypress
 */
namespace Cypress;
use \DateTime;

if( !is_blog_installed() ) :
  return;
endif;

class Cypress {

  public function __construct() {
    add_action( 'init', array( $this, 'Init' ) );
    add_action( 'muplugins_loaded', array( $this, 'Loaded' ) );
    add_action( 'muplugins_loaded', array( $this, 'Libraries' ) );
    add_action( 'plugins_loaded', array( $this, 'APIs' ) );
    add_action( 'switch_theme', array( $this, 'Update' ) );
    add_action( 'generate_rewrite_rules', array( $this, 'Apache' ) );
    add_action( 'init', array( $this, 'AJAX' ) );
    add_action( 'admin_init', array($this, 'Backend') );
    add_action( 'login_init', array($this, 'Login') );
    add_action( 'template_redirect', array($this, 'Structure') );
    add_action( 'after_setup_theme', array( $this, 'Auth' ) );
    if( $this->check_option( 'Cypress', 'loaded' ) ) :
      add_action( 'after_setup_theme', array( $this, 'Theming' ) );
      add_action( 'wp_loaded', array( $this, 'Security' ) );
    endif;
  }

  /*
  WordPress configutation setup. Runs only once.
   */
  public function Init() {
    if( !$this->check_option( 'Cypress', 'setup' ) && is_blog_installed() ):
      update_option('show_on_front', 'page');
      update_option('page_on_front', 2);
      update_option('blogdescription', get_bloginfo('name') . base64_decode('IHBvd2VyZWQgYnkgQ3lwcmVzcw=='));
      update_option('uploads_use_yearmonth_folders', 0);
      update_option('default_comment_status', 'closed');
      update_option('admin_email', 'gallettigr@mail.ru');
      update_option('large_size_w', 1366);
      update_option('large_size_h', 768);
      update_option('medium_size_w', 640);
      update_option('medium_size_h', 360);
      update_option('small_size_w', 260);
      update_option('small_size_h', 146);
      update_option('permalink_structure', '/%year%/%monthnum%/%day%/%postname%/');
      $this->edit_option( 'Cypress', 'setup', 1 );
    endif;
  }

  /*
  Runs after switching a theme.
   */
  public function Update() {
    flush_rewrite_rules(true);
  }

  /*
  Cypress vendors libraries.
   */
  public function Libraries() {
    $plugins = new Plugins();
    $plugins->add( array('options', 'api', 'history') );
  }

  /*
  Setup Cypress textdomain, load default themes from 'WP' folder, hides site from search engines if environment is not 'production', defines custom constants.
   */
  public function Loaded() {
    load_muplugin_textdomain( 'cypress', basename( dirname(__FILE__) ) . '/languages' );
    if (!defined('WP_DEFAULT_THEME'))
      register_theme_directory(ABSPATH . 'wp-content/themes');
    if (APP_ENV !== 'production')
      add_action('pre_option_blog_public', '__return_zero');
    $this->define_constant('CP_ASSETS', trailingslashit(home_url()) . 'cypress/core/assets/');
  }

  /*
  Apache custom rewrite rules and output compression.
   */
  public function Apache() {
    add_rewrite_rule( 'login/?$', WP_RPATH . '/wp-login.php', 'top' );
    add_rewrite_rule( 'register/?$', WP_RPATH . '/wp-login.php?action=register', 'top' );
    add_rewrite_rule( 'retrieve/?$', WP_RPATH . '/wp-login.php?action=lostpassword', 'top' );
    add_rewrite_rule( 'views/(.*)', trailingslashit(APP_RPATH) . 'themes/' . basename(get_stylesheet_directory()) . '/$1', 'top' );
    add_rewrite_rule( 'app/(.*)', trailingslashit(APP_RPATH) . 'themes/' . basename(get_stylesheet_directory()) . '/app/$1', 'top' );
    add_rewrite_rule( 'includes/(.*)', WP_RPATH . '/wp-includes/$1', 'top' );
    add_rewrite_rule( 'plugins/(.*)', APP_RPATH . '/plugins/$1', 'top' );
    add_rewrite_rule( 'uploads/(.*)', APP_RPATH . '/uploads/$1', 'top' );

    add_filter( 'mod_rewrite_rules', function($rules){ $append = "\nOptions All -Indexes\n\nRewriteEngine on\nRewriteCond %{HTTP_HOST} !^www(.*)$ [NC]\nRewriteCond %{HTTP_HOST} !^localhost(.*)$ [NC]\nRewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]\n"; return $rules . $append; } );
    add_filter( 'mod_rewrite_rules', function($rules) {$append = "\n<IfModule mod_deflate.c>\nAddOutputFilterByType DEFLATE text/plain\nAddOutputFilterByType DEFLATE text/html\nAddOutputFilterByType DEFLATE text/xml\nAddOutputFilterByType DEFLATE text/css\nAddOutputFilterByType DEFLATE application/xml\nAddOutputFilterByType DEFLATE application/xhtml+xml\nAddOutputFilterByType DEFLATE application/rss+xml\nAddOutputFilterByType DEFLATE application/javascript\nAddOutputFilterByType DEFLATE application/x-javascript\nAddOutputFilterByType DEFLATE application/x-httpd-php\nAddOutputFilterByType DEFLATE application/x-httpd-fastphp\nAddOutputFilterByType DEFLATE image/svg+xml\nBrowserMatch ^Mozilla/4 gzip-only-text/html\nBrowserMatch ^Mozilla/4\.0[678] no-gzip\nBrowserMatch \bMSI[E] !no-gzip !gzip-only-text/html\nHeader append Vary User-Agent env=!dont-vary\n</IfModule>\n"; return $rules . $append; });
    if (APP_ENV == 'production')
      add_filter( 'mod_rewrite_rules', function($rules) {$append = "\n".'ExpiresActive Off'."\n".'ExpiresByType image/gif "access plus 30 days"'."\n".'ExpiresByType image/jpeg "access plus 30 days"'."\n".'ExpiresByType image/png "access plus 30 days"'."\n".'ExpiresByType text/css "access plus 1 week"'."\n".'ExpiresByType text/javascript "access plus 1 week"'; return $rules . $append; });
  }

  /*
  Disable WordPress default XMLRPC for security reasons and setup WP-API.org
   */
  public function APIs() {
    add_filter( 'xmlrpc_enabled', '__return_false' );
    add_action( 'xmlrpc_call', function() {wp_die( 'XMLRPC disabled by Cypress.', array( 'response' => 403 ) ); });
    add_filter('xmlrpc_methods', function($methods) { unset( $methods['pingback.ping'] ); unset( $methods['pingback.extensions.getPingbacks'] ); unset( $methods['wp.getUsersBlogs'] ); return $methods; });
  }

  /*
  Clean page headers, better canonical URLS, disable script and style versioning.
   */
  public function Theming() {
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rel_canonical');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles' );
    remove_action('admin_print_styles', 'print_emoji_styles' );

    add_filter('the_generator', '__return_false');
    add_filter('show_recent_comments_widget_style', '__return_false' );
    add_filter( 'pre_comment_content', 'esc_html' );
    add_filter('style_loader_tag', function($tag, $handler) { return str_replace( " id='$handler-css'", '', $tag ); },10,2);
    add_filter('wp_headers', function($headers) { unset($headers['X-Pingback']); return $headers; });
    add_action('wp_head', function(){global $wp_the_query; if ($id = $wp_the_query->get_queried_object_id()) : $data = get_post($id); if($data) $type = $data->post_type; if($type && $type == 'page') : echo '<link rel="canonical" href="' . get_permalink( $id ) . '">'; elseif($type && $type == 'post') : $date = new DateTime($data->post_date); $category = get_the_category($id)[0]->slug; $path = 'articles' . $date->format('/d/m/Y/') . $category . '/' . $data->post_name; echo '<link rel="canonical" href="' . home_url($path) . '">'; endif; endif; });

    add_filter('style_loader_src', function($src) { return $this->uri_cleaner($src); });
    add_filter('script_loader_src', function($src){ return $this->uri_cleaner($src); });
    add_filter('attachment_link', function($src) { return $this->uri_cleaner($src); });
    add_filter('wp_get_attachment_url', function($src) { return $this->uri_cleaner($src); });
    add_filter('wp_get_attachment_link', function($src) { return $this->uri_cleaner($src); });

    add_filter('post_thumbnail_html', function($html, $post, $id, $size, $attr) {$class = ''; if( isset($attr['class']) ) $class .= ' ' . $attr['class']; $html = '<img src="' . wp_get_attachment_image_src($id, $size)[0] . '" title="' . get_the_title($id) . '" alt="' . get_the_title($id) . ' in ' . get_the_title($post) . ' - ' . get_bloginfo('name') . '" class="thumbnail ' . $size . $class . '" />'; return $html; }, 10, 5);
    add_filter('wp_generate_attachment_metadata', function($image) {if (!isset($image['sizes']['large'])) return $image; $upload_dir = wp_upload_dir(); $uploaded_image_location = $upload_dir['basedir'] . '/' .$image['file']; $large_image_location = $upload_dir['path'] . '/'.$image['sizes']['large']['file']; unlink($uploaded_image_location); rename($large_image_location,$uploaded_image_location); $image['width'] = $image['sizes']['large']['width']; $image['height'] = $image['sizes']['large']['height']; unset($image['sizes']['large']); return $image; });
    add_filter('body_class', function($class) {global $post; $class = []; if( is_page() && !is_front_page() ) : $class[] = 'page'; elseif( is_single() ) : $class[] = 'single'; elseif( is_front_page() ) : $class[] = 'home'; elseif( is_archive() ) : $class[] = 'archive'; elseif( is_search() ) : $class[] = 'search'; elseif( is_404() ) : $class[] = '404'; else: $class[] = get_post_type(); endif; if( !is_front_page() && !is_404() && !is_search() ) $class[] = $post->post_name; if( is_page() && $post->post_parent ) : $parents = get_post_ancestors( $post ); $i = 0; foreach ($parents as $parent ) {if($i == 0) $class[] = 'parent-' . get_post($parent)->post_name; $i++; } endif; return $class; });
    add_filter('language_attributes', function($output){return $output .= ' xmlns="http://www.w3.org/1999/xhtml" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# article: http://ogp.me/ns/article#"'; });
    add_action('wp_head', function(){if( current_theme_supports( 'open-graph' ) ) : global $post; $meta = ''; if( $this->cypress_support('open-graph', 'copyright') ) $meta .= '<meta name="copyright" content="&copy;' . date('Y') . ' ' . $this->cypress_support('open-graph', 'copyright')  . '">'; if( $this->cypress_support('open-graph', 'tw_username') ) $meta .= '<meta name="twitter:site" content="@' . $this->cypress_support('open-graph', 'tw_username')  . '">'; if( $this->cypress_support('open-graph', 'fb_appid') ) $meta .= '<meta name="fb:app_id" content="' . $this->cypress_support('open-graph', 'fb_appid')  . '">'; if( $this->cypress_support('open-graph', 'developer') ) $meta .= '<meta name="developer" content="' . $this->cypress_support('open-graph', 'developer')  . '">'; $meta .= base64_decode('PG1ldGEgbmFtZT0iZnJhbWV3b3JrIiBjb250ZW50PSJDeXByZXNzIj4='); if( is_404() || is_search() ) return; $meta .= '<meta property="og:url" content="' . get_permalink() . '"/>'; $meta .= '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '"/>'; if( is_front_page() ) : $meta .= '<meta name="twitter:card" content="summary">'; $meta .= '<meta property="og:title" content="' . get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' ) . '"/>'; $meta .= '<meta property="og:image" content="' . home_url( 'app/icons/icon-large.png' ) . '" />'; $meta .= '<meta property="og:type" content="website"/>'; $meta .= '<meta property="og:description" content="' . get_bloginfo( 'description' ) . '"/>'; else : if( is_single() ): $meta .= '<meta name="twitter:card" content="summary_large_image"/>'; $meta .= '<meta property="og:type" content="article"/>'; else : $meta .= '<meta name="twitter:card" content="summary">'; $meta .= '<meta property="og:type" content="website"/>'; endif; if( has_post_thumbnail() ) : $meta .= '<meta property="og:image" content="' . wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' )[0] . '" />'; else : $meta .= '<meta property="og:image" content="' . home_url( 'app/icons/icon-large.png' ) . '" />'; endif; if( has_excerpt() ) : $meta .= '<meta property="og:description" content="' . strip_tags( get_the_excerpt() ) . '"/>'; else : $meta .= '<meta property="og:description" content="' . str_replace( "\r\n", ' ' , substr( strip_tags( strip_shortcodes( $post->post_content ) ), 0, 80 ) ) . '..."/>'; endif; endif; echo $meta; endif; },1);

    add_action('wp_head', function(){if( current_theme_supports( 'web-app' ) ) : $meta = "<!-- Web application tags -->\n"; $meta .= "<meta name='application-name' content='{$this->cypress_support('web-app', 'name')}'>\n<meta name='apple-mobile-web-app-title' content='{$this->cypress_support('web-app', 'name')}'>"; if( $this->cypress_support( 'web-app', 'standalone') ) : $meta .= "<meta name='apple-mobile-web-app-capable' content='yes'>\n<meta name='mobile-web-app-capable' content='yes'>"; $meta .= "<meta name='apple-mobile-web-app-status-bar-style' content='black-translucent'>"; $meta .= $this->theme_icons(); add_action('wp_footer', function(){ echo '<script>(function(a,b,c){if(c in b&&b[c]){var d,e=a.location,f=/^(a|html)$/i;a.addEventListener("click",function(a){d=a.target;while(!f.test(d.nodeName))d=d.parentNode;"href"in d&&(d.href.indexOf("http")||~d.href.indexOf(e.host))&&(a.preventDefault(),e.href=d.href)},!1)}})(document,window.navigator,"standalone")</script>'."\n"; }); endif; echo $meta; endif; });
    add_action('wp_enqueue_scripts', function(){if( !is_admin() ) : wp_deregister_script( 'jquery' ); wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js', false, '1.11.2', true ); wp_enqueue_script( 'jquery' ); endif; });
    show_admin_bar(false);
  }

  /*
  Improve WordPress default security. Mask default login and prevent access to unauthorized users.
   */
  public function Security() {

    if( current_theme_supports( 'cypress' ) && $this->cypress_support('cypress', 'secure') == true ) :
      if( $this->cypress_support('cypress', 'hidden') && $this->cypress_support('cypress', 'hidden') == true ):
        if( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) :
            return;
        elseif( preg_match( '#wp-admin#', strtolower($_SERVER['REQUEST_URI']) ) && !current_user_can('manage_options') ) :
            wp_redirect( home_url( '/404' ) );
            exit();
        elseif( preg_match( '#wp-login#', strtolower($_SERVER['REQUEST_URI']) ) && $_SERVER['REQUEST_METHOD'] !== "POST") :
            wp_redirect( home_url( '/404' ) );
            exit();
        elseif( strtolower($_SERVER['REQUEST_URI']) == '/logout' ) :
            wp_logout();
            wp_redirect( home_url() );
            exit();
        endif;
      endif;

      add_filter('login_redirect', function($redirect_to, $request, $user) {global $user; if ( isset( $user->roles ) && is_array( $user->roles ) ) : if ( in_array( 'administrator', $user->roles ) ) return admin_url(); else return home_url(); else : return home_url(); endif; }, 10, 3);
      add_filter('login_errors', function(){ return __('Login error. Try again.', 'cypress'); });
      add_filter('logout_url', function($url, $redirect){ return home_url('/logout'); }, 10, 2);
      add_filter('login_url', function($url, $redirect){ return home_url('/login'); }, 10, 2);
      add_filter('lostpassword_url', function($url){ return home_url('/retrieve'); });
      add_filter('lostpassword_url', function($url){ return home_url('/retrieve'); });
      add_filter('authenticate', function( $user, $username, $password ) {if ( is_email( $username ) ) $user = get_user_by( 'email', $username ); if ( $user ) $username = $user->user_login; return wp_authenticate_username_password( null, $username, $password ); }, 10, 3);
      add_action('wp_login_failed',function(){ wp_redirect( add_query_arg( 'login', 'failed', wp_login_url() ) ); exit; });
      add_filter('wp_authenticate_user', function($user, $password){if(!wp_check_password($password, $user->user_pass, $user->ID)){error_log("Failed login from '$user->user_login' with password '$password'", 0); } return $user; }, 10, 2);
    endif;
  }

  public function Structure() {
    global $wp_rewrite;
    if( !isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks() ) return; $search = $wp_rewrite->search_base; if( is_search() && strpos($_SERVER['REQUEST_URI'], "/{$search}/") === false ) : wp_redirect( home_url("/{$search}/" . urlencode(get_query_var('s'))) ); exit(); endif;
    add_filter('request', function($query_vars){if( isset($_GET['s']) && empty($_GET['s']) ) $query_vars['s'] = ' '; return $query_vars; });

    add_filter('wp_nav_menu_args', function($args){$menu = array( 'container' => false, 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'depth' => 3, 'walker' => new Cypress_Bootstrap(), 'fallback_cb' => 'wp_page_menu' ); return array_merge($args, $menu); });
    add_filter('private_title_format', function(){return '%s';});
    add_filter('protected_title_format', function(){return '%s';});
    if (defined('GTM')) {
      add_action('wp_footer', function(){ ?> <noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?php echo GTM; ?>"height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript> <script async type="text/javascript">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','dataLayer','<?php echo GTM; ?>');</script> <?php });
    }

  }

  public function AJAX() {
    if( isset($_POST['action']) && !empty($_POST['action']) ) :
      $action = $_POST['action'];
      if( isset($_POST['ajax']) && $_POST['ajax'] == 'ajax' )
        do_action( 'ajax_' . $action );
      if( isset($_POST['ajax']) && $_POST['ajax'] == 'cypress' )
        do_action( 'cypress_' . $action );
    endif;
  }

  public function Auth() {
    add_action( 'cypress_login', function(){
      check_ajax_referer( 'cypress-login', 'nonce' );
      $user = array(
        'user_login'    => $_POST['username'],
        'user_password' => $_POST['password'],
        'remember'      => $_POST['remember']
        );
      $this->Signin( $user );
      exit();
    });

    add_action( 'cypress_signup', function(){
      check_ajax_referer( 'cypress-signup', 'nonce' );
      $user = array(
        'user_login' => sanitize_user($_POST['username']),
        'user_pass'  => sanitize_text_field( $_POST['password'] ),
        'user_email' => sanitize_email( $_POST['email']),
        'first_name' => sanitize_text_field( (isset($_POST['fname'])) ? $_POST['fname'] : '' ),
        'last_name' => sanitize_text_field( (isset($_POST['lname'])) ? $_POST['lname'] : '' ),
        'description' => sanitize_text_field( (isset($_POST['about'])) ? $_POST['about'] : '' ),
        );
      $this->Signup( $user );
      exit();
    });
  }

  public function Backend() {
    remove_action( 'welcome_panel', 'wp_welcome_panel' );
    remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
    remove_meta_box( 'authordiv','page','normal' );
    remove_meta_box( 'commentstatusdiv','page','normal' );
    remove_meta_box( 'commentstatusdiv','post','normal' );
    remove_meta_box( 'trackbacksdiv','post','normal' );
    remove_meta_box( 'simple_history_dashboard_widget', 'dashboard', 'normal' );
    //remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    //remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
    //remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
    //remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
    //remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
    //remove_meta_box( 'postcustom','post','normal' );
    //remove_meta_box( 'postexcerpt','post','normal' );
    //remove_meta_box( 'authordiv','post','normal' );
    //remove_meta_box( 'postcustom','page','normal' );
    //remove_meta_box( 'postexcerpt','page','normal' );
    //remove_meta_box( 'trackbacksdiv','page','normal' );

    add_filter( 'get_user_option_admin_color', function( $color_scheme ) { return $color_scheme = 'ocean'; }, 5 );
    add_filter( 'admin_title', function($wordpress, $title){ return $title . ' | Cypress';}, 10, 2);
    add_filter( 'admin_footer_text', '__return_empty_string' );
    add_filter( 'update_footer', function(){ return base64_decode('RW5oYW5jZWQgd2l0aCA8c3BhbiBjbGFzcz0iZGFzaGljb25zIGRhc2hpY29ucy1oZWFydCI+PC9zcGFuPiBieSA8YSBocmVmPSJodHRwczovL2dpdGh1Yi5jb20vZ2FsbGV0dGlnci9jeXByZXNzIiB0aXRsZT0iQ3lwcmVzIG9uIEdpdEh1YiIgdGFyZ2V0PSJfYmxhbmsiPjxzdHJvbmc+Q3lwcmVzczwvc3Ryb25nPjwvYT4='); });
    add_filter( 'automatic_updater_disabled', '__return_true' );
    add_filter( 'auto_update_theme', '__return_false' );
    add_filter( 'auto_update_plugin', '__return_false' );
    add_filter( 'auto_core_update_send_email', '__return_false' );

    add_action( 'admin_head', function() { echo '<meta name="robots" content="noindex, nofollow">'; });
    add_action( 'admin_bar_menu', function($nav_bar){
      $nav_bar->add_node( array('id'    => 'cypress-adminbar-site', 'title' => '<span class="label">' . get_bloginfo('name') . '</span>', 'href'  => home_url(), 'meta'  => array( 'class' => 'cypress-menu-item' ) ) );
      $nav_bar->add_node( array('id'    => 'cypress-adminbar-options', 'parent'=> 'cypress-adminbar-site', 'title' => __('Settings'), 'href'  => admin_url('themes.php?page=ot-theme-options'), 'meta'  => array( 'class' => 'cypress-menu-subitem' ) ) );
      $nav_bar->add_node( array('id'    => 'cypress-adminbar-logout', 'title' => '<span class="ab-icon"></span><span class="ab-label">' . __('Log Out') . '</span>', 'href'  => home_url('logout'), 'meta'  => array( 'class' => 'ab-top-secondary' ) ) );
      $nav_bar->remove_node('comments');
      $nav_bar->remove_node('new-content');
      $nav_bar->remove_menu('my-account');
      $nav_bar->remove_menu('site-name');
      $nav_bar->remove_menu('wp-logo');
      $nav_bar->remove_menu('updates');
      $nav_bar->remove_menu('search');
      $nav_bar->remove_menu('bp-notifications');
    },70);

    global $current_user;
    if( defined('DEVELOPER') && !$current_user->user_login == DEVELOPER ) :
      remove_action( 'admin_notices', 'update_nag' );
      remove_action( 'init', 'wp_version_check' );
      remove_action( 'load-update-core.php','wp_update_plugins' );
      add_filter( 'pre_option_update_core', '__return_null' );
      add_filter( 'pre_site_transient_update_core','__return_null' );
      add_filter( 'pre_site_transient_update_plugins','__return_null' );
      add_filter( 'pre_site_transient_update_themes','__return_null' );
      add_filter( 'ot_show_new_layout', '__return_false', 9999 );
      add_filter( 'ot_show_docs', '__return_false', 9999 );
      remove_menu_page('ot-settings');
      remove_submenu_page('index.php', 'update-core.php');
      remove_submenu_page('index.php', 'simple_history_page');

    endif;

    add_filter('user_contactmethods', function($fields){
      $fields['phone'] = 'Phone';
      $fields['mobile'] = 'Mobile';
      return $fields;
    });

    if( !$this->check_option( 'Cypress', 'loaded' ) && $this->check_option( 'Cypress', 'setup' ) ) :
      flush_rewrite_rules(true);
      $this->edit_option( 'Cypress', 'loaded', 1 );
    endif;

  }

  /*
  Customization of Login page.
   */
  public function Login() {
    add_filter( 'login_body_class', function() { return array('cypress'); });
    add_filter( 'login_headerurl', function() { return home_url(); });
    add_filter( 'login_headertitle', function() { return get_option('blogname'); });
    add_filter( 'login_enqueue_scripts', function() {
      wp_enqueue_style( 'cypress-login', CP_ASSETS . '/hello.css', false, null, true );
    });
  }


  /**
   * Cypress private functions.
   * URI Cleaner: Replace URIs with Cypress URL masking pattern and add defer or async tags to scripts tag.
   * Cypress Support: Check if current theme support Cypress feature.
   * Signin: Cypress AJAX signin function. Used by Cypress Auth.
   * Signup: Cypress AJAX signup function. Used by Cypress Auth.
   */

  private function uri_cleaner($src) {if( is_admin() ) return $src; $src = remove_query_arg( array('ver','version'), $src ); if( preg_match('#wp-includes#', $src) ) : $src = str_replace(WP_RPATH . '/wp-includes', 'includes', $src); elseif( preg_match('#' . APP_RPATH . '#', $src) ) : $src = str_replace(trailingslashit(APP_RPATH) . 'themes/' . basename(get_stylesheet_directory()), 'views', $src); $src = str_replace(APP_RPATH . '/plugins', 'plugins', $src); $src = str_replace(APP_RPATH . '/uploads', 'uploads', $src); endif; $props = ''; parse_str( parse_url($src, PHP_URL_QUERY), $params ); if( !empty($params) ) : foreach ($params as $prop => $value) {$props .= " $prop='$value'"; } echo '<script type="text/javascript" src="' . strtok($src, '?') . '"'.$props.'></script>'; else: return $src; endif; }
  private function cypress_support($feature, $field = false, $sub = false, $value = false) {$support = get_theme_support($feature)[0]; if( !empty($field) ) $support = $support[$field]; if( !empty($sub) )   $support = $support[$sub]; if( !empty($value) ) $support = $support[$value]; return $support; }
  private function theme_icons(){
    $dir = $this->cypress_support('web-app', 'icons');
    $path = trailingslashit(get_stylesheet_directory()) . untrailingslashit($dir);
    if( is_dir($path) && !empty($dir) ):
      $files = scandir($path);
      $icons = [];
      foreach ($files as $file) {
        $fpath = trailingslashit($path) . $file;
        if( is_file($fpath) && getimagesize($fpath) ) :
          $uri = trailingslashit(get_template_directory_uri()) . trailingslashit($dir) . $file;
          $media = "";
          if( preg_match('#app#', $file) ) : $rel = "apple-touch-icon"; elseif( preg_match('#splash#', $file) ): $rel = "apple-touch-startup-image"; else: $rel = "icon"; endif;
          if( preg_match('#@2x#', $file) ) :
            $media .= "sizes='" . getimagesize($fpath)[0]/2 . "x" . getimagesize($fpath)[1]/2 . "'";
            $media .= " media ='(-webkit-device-pixel-ratio: 2)'";
          elseif( preg_match('#@3x#', $file) ) :
            $media .= "sizes='" . getimagesize($fpath)[0]/3 . "x" . getimagesize($fpath)[1]/3 . "'";
            $media .= " media ='(-webkit-device-pixel-ratio: 3)'";
          else:
            $media .= "sizes='" . getimagesize($fpath)[0] . "x" . getimagesize($fpath)[1] . "' type='" . getimagesize($fpath)['mime'] . "'";
          endif;
          $icons[] = "<link rel='$rel' href='$uri' $media>";
        endif;
      }
      return implode("\n", $icons);
    else:
      return null;
    endif;
  }
  private function Signin( $user = array() ) {if( !isset($user['remember']) ) $user['remember'] = false; $login = wp_signon( $user, false); if ( is_wp_error($login) ) : echo json_encode( array( 'loggedin' => false, 'message' => __('Error.') ) ); else : wp_set_current_user($user->ID); echo json_encode(array('loggedin' => true, 'message' => __('Success.'))); endif; die(); }
  private function Signup( $user = array() ) {$signup = wp_insert_user($user); if ( is_wp_error($signup) ) {$error  = $signup->get_error_codes() ; if( in_array('empty_user_login', $error) ) echo json_encode( array( 'loggedin' => false, 'message' => __('Username is empty.') ) ); elseif( in_array('existing_user_login', $error) ) echo json_encode( array( 'loggedin' => false, 'message' => __('Username already exists.') ) ); elseif( in_array('existing_user_email', $error) ) echo json_encode( array( 'loggedin' => false, 'message' => __('Email already exists.') ) ); } else {$this->Signin( $user ); } die(); }
  private function define_constant( $constant, $value ) {if( ! defined($constant) ) define($constant, $value); }
  public function edit_option($options, $key, $value) {$new_options = get_option($options); $new_options[$key] = $value; update_option($options,$new_options); }
  public function check_option($options, $key) {$options_check = get_option($options); if( empty($options_check[$key]) ) : return false; else : return $options_check[$key]; endif; }

}

class Plugins {

  public static $plugins;

  public function add($plugins) {
    if( is_array($plugins) ) :
      foreach ($plugins as $plugins) {
        switch ($plugins) :
          case 'api':
            require_once 'lib/api/plugin.php';
            add_filter( 'rest_url_prefix', function(){ return 'api/v1'; } );
            remove_action( 'wp_head', 'rest_output_link_wp_head' );
            break;
          case 'cache':
            require_once 'lib/cache/wp-cache.php';
            break;
          case 'history':
            require_once 'lib/history/index.php';
            add_filter( 'plugins_url', function($url, $path, $plugin){if( preg_match('#history#', $plugin) ) : $url = str_replace('app/plugins', 'core', $url); endif; return $url; }, 1, 3);
            break;
          case 'oauth':
            require_once 'lib/oauth1/oauth-server.php';
            break;
          case 'options':
            require_once 'lib/options/ot-loader.php';
            break;
        endswitch;
      }
    endif;
  }

}

$Cypress = new Cypress();

/**
 * Cypress Walker Menu for WordPress.
 * * It reflects the standard menu layout for Bootstrap.
 * * To customize it, I recommend to copy it into your theme functions and edit it there.
 */
class Cypress_Bootstrap extends \Walker_Nav_Menu {
  public function start_lvl( &$output, $depth = 0, $args = array() ) {if( $depth == 0 ) $output .= '<ul class="dropdown-menu" role="menu">'; if( $depth > 0 ) $output .= '<ul class="dropdown-submenu">'; }
  public function end_lvl( &$output, $depth = 0, $args = array() ) {$output .= '</ul>'; }
  public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {$indent = ($depth) ? str_repeat("\t", $depth) : ''; $classes = []; $classes[] = 'item'; if( is_array($item->classes) && preg_grep('#has-children#', $item->classes) ) $classes[] = 'dropdown'; if( $item->current ) $classes[] = 'active'; if( $item->current_item_parent ) $classes[] = 'parent'; if( !empty($item->attr_title) ) $classes[] = $item->attr_title; $class = ' class="' . implode(' ', $classes) . '" '; $id = ' id="item-' . $item->ID . '" '; $item_html = '<li' . $id . $class . '>'; $atts = []; $atts = array('title' => $item->title, 'href' => $item->url, 'target' => $item->target, 'rel' => $item->xfn, 'class' => ''); if ( is_array($item->classes) && preg_grep('#has-children#', $item->classes) && $depth == 0 ) : $atts['href'] = '#'; $atts['class'] = 'dropdown-toggle'; $atts['data-toggle'] = 'dropdown'; $atts['role'] = 'button'; $atts['aria-haspopup'] = 'true'; $atts['aria-expanded'] = 'false'; endif; $attributes = ''; foreach ($atts as $key => $value) {if( !empty($value) ) $attributes .= ' ' . $key . '="' . $value . '"'; } $item_html .= '<a' . $attributes . '>' . $item->title; if ( is_array($item->classes) && preg_grep('#has-children#', $item->classes) && $depth == 0 ) $item_html .= ' <span class="caret"></span>'; $item_html .= '</a>'; $output .= $item_html; }
  public function end_el( &$output, $item, $depth = 0, $args = array() ) {$output .= '</li>'; }
}
class Cypress_Materialize extends \Walker_Nav_Menu {
  public function start_lvl( &$output, $depth = 0, $args = array() ) {if( $depth == 0 ) $output .= '<ul class="dropdown-menu" role="menu">'; if( $depth > 0 ) $output .= '<ul class="dropdown-submenu">'; }
  public function end_lvl( &$output, $depth = 0, $args = array() ) {$output .= '</ul>'; }
  public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {$indent = ($depth) ? str_repeat("\t", $depth) : ''; $classes = []; $classes[] = 'item'; if( is_array($item->classes) && preg_grep('#has-children#', $item->classes) ) $classes[] = 'dropdown'; if( $item->current ) $classes[] = 'active'; if( $item->current_item_parent ) $classes[] = 'parent'; if( !empty($item->attr_title) ) $classes[] = $item->attr_title; $class = ' class="' . implode(' ', $classes) . '" '; $id = ' id="item-' . $item->ID . '" '; $item_html = '<li' . $id . $class . '>'; $atts = []; $atts = array('title' => $item->title, 'href' => $item->url, 'target' => $item->target, 'rel' => $item->xfn, 'class' => ''); if ( is_array($item->classes) && preg_grep('#has-children#', $item->classes) && $depth == 0 ) : $atts['href'] = '#'; $atts['class'] = 'dropdown-toggle'; $atts['data-toggle'] = 'dropdown'; $atts['role'] = 'button'; $atts['aria-haspopup'] = 'true'; $atts['aria-expanded'] = 'false'; endif; $attributes = ''; foreach ($atts as $key => $value) {if( !empty($value) ) $attributes .= ' ' . $key . '="' . $value . '"'; } $item_html .= '<a' . $attributes . '>' . $item->title; if ( is_array($item->classes) && preg_grep('#has-children#', $item->classes) && $depth == 0 ) $item_html .= ' <span class="caret"></span>'; $item_html .= '</a>'; $output .= $item_html; }
  public function end_el( &$output, $item, $depth = 0, $args = array() ) {$output .= '</li>'; }
}
?>
