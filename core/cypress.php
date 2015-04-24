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

if( !is_blog_installed() ) {
  $activate = 0;
  return;
}

class Cypress {

  public function __construct() {
    add_action( 'muplugins_loaded', array( $this, 'Loaded' ) );
    add_action( 'wp_loaded', array( $this, 'Security' ) );
    add_action( 'after_setup_theme', array( $this, 'Theming' ) );
    add_action( 'plugins_loaded', array( $this, 'APIs' ) );
    add_action( 'switch_theme', array( $this, 'Setup' ) );
    add_action( 'generate_rewrite_rules', array( $this, 'Apache' ) );
    add_action( 'init', array( $this, 'AJAX' ) );
    add_action( 'admin_init', array($this, 'Backend') );
    add_action( 'login_init', array($this, 'Login') );
    add_action( 'template_redirect', array($this, 'Structure') );
    add_action( 'after_setup_theme', array( $this, 'Auth' ) );
  }

  /*
  WordPress configutation setup. Runs only once.
   */
  public function Init() {
    # code...
  }

  /*
  Theme setup. Runs after switching a theme.
   */
  public function Setup() {
    flush_rewrite_rules(true);
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
    add_rewrite_rule( 'api/auth/?$', WP_RPATH . '/wp-login.php?action=oauth1_authorize', 'top' );
    add_rewrite_rule( 'register/?$', WP_RPATH . '/wp-login.php?action=register', 'top' );
    add_rewrite_rule( 'retrieve/?$', WP_RPATH . '/wp-login.php?action=lostpassword', 'top' );
    add_rewrite_rule( 'views/(.*)', trailingslashit(APP_RPATH) . 'themes/' . basename(get_stylesheet_directory()) . '/$1', 'top' );
    add_rewrite_rule( 'app/(.*)', trailingslashit(APP_RPATH) . 'themes/' . basename(get_stylesheet_directory()) . '/app/$1', 'top' );
    add_rewrite_rule( 'includes/(.*)', WP_RPATH . '/wp-includes/$1', 'top' );
    add_rewrite_rule( 'plugins/(.*)', APP_RPATH . '/plugins/$1', 'top' );
    add_rewrite_rule( 'uploads/(.*)', APP_RPATH . '/uploads/$1', 'top' );

    add_filter( 'mod_rewrite_rules', function($rules){ $append = "\nOptions All -Indexes\n\nRewriteEngine on\nRewriteCond %{HTTP_HOST} !^www(.*)$ [NC]\nRewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]\n"; return $rules . $append; } );
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
    add_filter('style_loader_tag', function($tag) {preg_match_all("!<link rel='stylesheet'\s?(id='[^']+')?\s+href='(.*)' type='text/css' media='(.*)' />!", $tag, $matches); $media = $matches[3][0] === 'print' ? ' media="print"' : ''; return '<link rel="stylesheet" href="' . $matches[2][0] . '"' . $media . '>' . "\n"; });
    add_filter('wp_headers', function($headers) { unset($headers['X-Pingback']); return $headers; });
    add_action('wp_head', function(){global $wp_the_query; if ($id = $wp_the_query->get_queried_object_id()) : $data = get_post($id); if($data) $type = $data->post_type; if($type && $type == 'page') : echo '<link rel="canonical" href="' . get_permalink( $id ) . '">'; elseif($type && $type == 'post') : $date = new DateTime($data->post_date); $category = get_the_category($id)[0]->slug; $path = 'articles' . $date->format('/d/m/Y/') . $category . '/' . $data->post_name; echo '<link rel="canonical" href="' . home_url($path) . '">'; endif; endif; });

    add_filter('style_loader_src', function($src) { return $this->uri_cleaner($src); });
    add_filter('script_loader_src', function($src){ return $this->uri_cleaner($src); });
    add_filter('attachment_link', function($src) { return $this->uri_cleaner($src); });
    add_filter('wp_get_attachment_url', function($src) { return $this->uri_cleaner($src); });
    add_filter('wp_get_attachment_link', function($src) { return $this->uri_cleaner($src); });

    add_filter('body_class', function($class) {global $post; $class = []; if( is_page() && !is_front_page() ) : $class[] = 'page'; elseif( is_single() ) : $class[] = 'single'; elseif( is_front_page() ) : $class[] = 'home'; elseif( is_archive() ) : $class[] = 'archive'; elseif( is_search() ) : $class[] = 'search'; elseif( is_404() ) : $class[] = '404'; else: $class[] = get_post_type(); endif; if( !is_front_page() && !is_404() && !is_search() ) $class[] = $post->post_name; if( is_page() && $post->post_parent ) : $parents = get_post_ancestors( $post ); $i = 0; foreach ($parents as $parent ) {if($i == 0) $class[] = 'parent-' . get_post($parent)->post_name; $i++; } endif; return $class; });

    add_action('wp_head', function(){
      global $post;
      $meta = '';
      if( is_404() ) return;
      if( is_search() ) return;
      $meta .= '<meta property="og:url" content="' . get_permalink() . '"/>';
      $meta .= '<meta property="og:site_name" content="' . get_bloginfo( 'name' ) . '"/>';
      if( is_front_page() ) :
        $meta .= '<meta property="og:title" content="' . get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' ) . '"/>';
        $meta .= '<meta property="og:image" content="' . home_url( 'app/assets/images/icon-large.png' ) . '" />';
      endif;
      if( is_single() )
        $meta .= '<meta property="og:type" content="article"/>';
      else
        $meta .= '<meta property="og:type" content="website"/>';
      if( has_excerpt($post->ID) ) :
        $meta .= '<meta property="og:description" content="' . strip_tags( get_the_excerpt() ) . '"/>';
      elseif( is_front_page() ) :
        $meta .= '<meta property="og:description" content="' . get_bloginfo( 'description' ) . '"/>';
      else :
        $meta .= '<meta property="og:description" content="' . str_replace( "\r\n", ' ' , substr( strip_tags( strip_shortcodes( $post->post_content ) ), 0, 160 ) ) . '"/>';
      endif;
      echo $meta;
    },1);

    add_action('wp_head', function(){
      if( current_theme_supports( 'web-app' ) ) :
        $meta = "<!-- Web application tags -->\n";
        $meta .= "<meta name='application-name' content='{$this->cypress_support('web-app', 'name')}'>\n<meta name='apple-mobile-web-app-title' content='{$this->cypress_support('web-app', 'name')}'>";
        $meta .= "<meta name='manifest' content='" . json_encode( $this->cypress_support('web-app'), JSON_UNESCAPED_SLASHES ) . "'>";
        if( $this->cypress_support( 'web-app', 'standalone') ) :
        endif;
        foreach ($this->cypress_support('web-app', 'icons') as $group => $icon) {
         $meta .= "<link sizes='{$icon['sizes']}' rel='apple-touch-icon' media='(-webkit-device-pixel-ratio: {$icon['density']})' href='{$icon['src']}' >";
        }
        echo $meta;
      endif;
    });

    show_admin_bar(false);
  }

  /*
  Replace URIs with Cypress URL masking pattern and add defer or async tags to scripts tag.
   */
  private function uri_cleaner($src) {
    $src = remove_query_arg( array('ver','version'), $src );
    if( preg_match('#wp-includes#', $src) ) :
      $src = str_replace(WP_RPATH . '/wp-includes', 'includes', $src);
    elseif( preg_match('#' . APP_RPATH . '#', $src) ) :
      $src = str_replace(trailingslashit(APP_RPATH) . 'themes/' . basename(get_stylesheet_directory()), 'views', $src);
      $src = str_replace(APP_RPATH . '/plugins', 'plugins', $src);
      $src = str_replace(APP_RPATH . '/uploads', 'uploads', $src);
    endif;

    $async = strpos($src, '?async'); $defer = strpos($src, '?defer');
    if ( !$async && $defer ) : echo '<script type="text/javascript" defer src="' . str_replace('?defer', '', $src) . '"></script>';
    elseif ( $async && !$defer ) : echo '<script type="text/javascript" async src="' . str_replace('?async', '', $src) . '"></script>';
    elseif ( $async && $defer ) : echo '<script type="text/javascript" async defer src="' . str_replace(array('?async','?defer'), '', $src) . '"></script>';
    else : return $src;
    endif;
  }

  public function cypress_support($feature, $field = false, $sub = false, $value = false) {
    $support = get_theme_support($feature)[0];
    if( !empty($field) ) $support = $support[$field];
    if( !empty($sub) )   $support = $support[$sub];
    if( !empty($value) ) $support = $support[$value];
    return $support;
  }

  /*
  Improve WordPress default security. Mask default login and prevent access to unauthorized users.
   */
  public function Security() {
    $redirect_404 =  home_url( '/404' ); $uri = strtolower($_SERVER['REQUEST_URI']);
    if( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) :
        return;
    elseif( preg_match( '#wp-admin#', $uri ) && !current_user_can('manage_options') ) :
        wp_redirect( $redirect_404 );
        exit();
    elseif( preg_match( '#wp-login#', $uri ) && $_SERVER['REQUEST_METHOD'] !== "POST") :
        wp_redirect( $redirect_404 );
        exit();
    elseif( $uri == '/logout' ) :
        wp_logout();
        wp_redirect( home_url() );
        exit();
    endif;

    add_filter('login_redirect', function($redirect_to, $request, $user) {
      global $user;
      if ( isset( $user->roles ) && is_array( $user->roles ) ) :
        if ( in_array( 'administrator', $user->roles ) )
          return admin_url();
        else
          return home_url();
      else :
        return home_url();
      endif;
    }, 10, 3);

    add_filter('login_errors', function(){ return __('Login error. Try again.', 'cypress'); });
    if( file_exists(ROOT_PATH . '/.htaccess' ) ) :
      add_filter('logout_url', function($url, $redirect){ return home_url('/logout'); }, 10, 2);
      add_filter('login_url', function($url, $redirect){ return home_url('/login'); }, 10, 2);
      add_filter('lostpassword_url', function($url){ return home_url('/retrieve'); });
      add_filter('lostpassword_url', function($url){ return home_url('/retrieve'); });
    endif;
    add_filter('authenticate', function( $user, $username, $password ) {
      if ( is_email( $username ) ) $user = get_user_by( 'email', $username );
      if ( $user ) $username = $user->user_login;
      return wp_authenticate_username_password( null, $username, $password );
    }, 10, 3);

  }

  public function Structure() {

    global $wp_rewrite;
    if( !isset($wp_rewrite) || !is_object($wp_rewrite) || !$wp_rewrite->using_permalinks() )
      return;
    $search = $wp_rewrite->search_base;
    if( is_search() && strpos($_SERVER['REQUEST_URI'], "/{$search}/") === false ) :
      wp_redirect( home_url("/{$search}/" . urlencode(get_query_var('s'))) );
      exit();
    endif;

    add_filter('request', function($query_vars){
      if( isset($_GET['s']) && empty($_GET['s']) )
        $query_vars['s'] = ' ';
      return $query_vars;
    });

    add_filter('wp_nav_menu_args', function($args){
      $menu = array( 'container' => false, 'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s</ul>', 'depth' => 3, 'walker' => new Cypress_Menu(), 'fallback_cb' => 'wp_page_menu' );
      return array_merge($args, $menu);
    });

    if (defined('GTM')) {
      add_action('wp_footer', function(){ ?>
        <noscript><iframe src="//www.googletagmanager.com/ns.html?id=<?php echo GTM; ?>"
          height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
          <script async type="text/javascript">(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
          j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
          '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?php echo GTM; ?>');</script>
      <?php });
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

  private function Signin( $user = array() ) {
    if( !isset($user['remember']) )
      $user['remember'] = false;
    $login = wp_signon( $user, false);
    if ( is_wp_error($login) ) :
      echo json_encode( array( 'loggedin' => false, 'message' => __('Error.') ) );
    else :
      wp_set_current_user($user->ID);
      echo json_encode(array('loggedin' => true, 'message' => __('Success.')));
    endif;
    die();
  }

  private function Signup( $user = array() ) {
    $signup = wp_insert_user($user);
    if ( is_wp_error($signup) ) {
      $error  = $signup->get_error_codes() ;
      if( in_array('empty_user_login', $error) )
        echo json_encode( array( 'loggedin' => false, 'message' => __('Username is empty.') ) );
      elseif( in_array('existing_user_login', $error) )
        echo json_encode( array( 'loggedin' => false, 'message' => __('Username already exists.') ) );
      elseif( in_array('existing_user_email', $error) )
        echo json_encode( array( 'loggedin' => false, 'message' => __('Email already exists.') ) );
    } else {
        $this->Signin( $user );
    }
    die();
  }

  /*
  Customization of WordPress backend.
   */
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

    add_action( 'admin_head', function() { echo '<meta name="robots" content="noindex, nofollow">'; });
    add_filter( 'admin_title', function($wordpress, $title){ return $title . ' | Cypress';}, 10, 2);
    add_filter( 'admin_footer_text', '__return_empty_string' );
    add_filter( 'update_footer', function(){ return base64_decode('RW5oYW5jZWQgd2l0aCA8c3BhbiBjbGFzcz0iZGFzaGljb25zIGRhc2hpY29ucy1oZWFydCI+PC9zcGFuPiBieSA8c3Ryb25nPkN5cHJlc3M8L3N0cm9uZz4='); });

    add_filter( 'automatic_updater_disabled', '__return_true' );
    add_filter( 'auto_update_theme', '__return_false' );
    add_filter( 'auto_update_plugin', '__return_false' );
    add_filter( 'auto_core_update_send_email', '__return_false' );

  }

  /*
  Customization of Login page.
   */
  public function Login() {
    wp_deregister_style( 'login' );
    wp_deregister_style( 'wp-admin' );
    add_filter('wp_admin_css', '__return_false');
    //add_filter( 'style_loader_tag', '__return_null' );

    add_filter( 'login_body_class', function() { return array('cypress'); });
    add_filter( 'login_headerurl', function() { return home_url(); });
    add_filter( 'login_headertitle', function() { return get_option('blogname'); });
    add_filter( 'login_enqueue_scripts', function() {
      wp_enqueue_style( 'login', CP_ASSETS . '/hello.css', false, null, true );
    } );
  }

  public function define_constant( $constant, $value ) {
    if( ! defined($constant) )
      define($constant, $value);
  }
}

$Cypress = new Cypress();


class Cypress_Menu extends \Walker_Nav_Menu {

  public function start_lvl( &$output, $depth = 0, $args = array() ) {
    if( $depth == 0 )
      $output .= '<ul class="dropdown-menu" role="menu">';
    if( $depth > 0 )
      $output .= '<ul class="dropdown-submenu">';
  }

  public function end_lvl( &$output, $depth = 0, $args = array() ) {
    $output .= '</ul>';
  }

  public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
    $indent = ($depth) ? str_repeat("\t", $depth) : '';
    $classes = [];
    $classes[] = 'item';
    if( is_array($item->classes) && preg_grep('#has-children#', $item->classes) )
      $classes[] = 'dropdown';
    if( $item->current )
      $classes[] = 'active';
    if( $item->current_item_parent )
      $classes[] = 'parent';
    if( !empty($item->attr_title) )
      $classes[] = $item->attr_title;

    $class = ' class="' . implode(' ', $classes) . '" ';
    $id = ' id="item-' . $item->ID . '" ';
    $item_html = '<li' . $id . $class . '>';

    $atts = [];
    $atts = array('title' => $item->title, 'href' => $item->url, 'target' => $item->target, 'rel' => $item->xfn, 'class' => '');

    if ( is_array($item->classes) && preg_grep('#has-children#', $item->classes) && $depth == 0 ) :
      $atts['href'] = '#';
      $atts['class'] = 'dropdown-toggle';
      $atts['data-toggle'] = 'dropdown';
      $atts['role'] = 'button';
      $atts['aria-haspopup'] = 'true';
      $atts['aria-expanded'] = 'false';
    endif;
    $attributes = '';
    foreach ($atts as $key => $value) {
      if( !empty($value) )
        $attributes .= ' ' . $key . '="' . $value . '"';
    }
    $item_html .= '<a' . $attributes . '>' . $item->title;

    if ( is_array($item->classes) && preg_grep('#has-children#', $item->classes) && $depth == 0 )
      $item_html .= ' <span class="caret"></span>';
    $item_html .= '</a>';
    $output .= $item_html;
  }

  public function end_el( &$output, $item, $depth = 0, $args = array() ) {
    $output .= '</li>';
  }
}
?>
