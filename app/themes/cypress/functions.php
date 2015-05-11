<?php
/**
 * materialwp functions and definitions
 *
 * @package Cypress
 * @author gallettigr
 */


if ( ! function_exists( 'cypress_setup' ) ) :
  function cypress_setup() {

  	load_theme_textdomain( 'cypress-theme', get_template_directory() . '/languages' );

  	add_theme_support( 'post-thumbnails' );
    add_theme_support( 'web-app', array(
      'name' => 'Cypress', // App name
      'standalone' => true, // Is it a standalone app?
      'start_url' => '/',
      'orientation' => 'portrait', // eg. landscape, portrait
      'icons' => 'app/icons',
      'theme_color' => MAIN_COLOR
    ) );
    add_theme_support( 'open-graph', array(
      'tw_username' => 'gallettigr',
      'fb_appid' => '1611715415714285',
      'copyright' => 'Cypress Framework',
      'developer' => 'Giammarco Galletti <gallettigr@mail.ru>'
    ) );
    add_theme_support( 'cypress', array(
      'secure' => true,
      'hidden' => true,
      'plugins' => array('options', 'cache', 'api')
    ) );

  	add_theme_support( 'html5', array(
  		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
  	) );

    register_nav_menus( array(
      'primary' => __( 'Primary Menu', 'materialwp' ),
    ) );

  	// add_theme_support( 'post-formats', array(
  	// 	'aside', 'image', 'video', 'quote', 'link',
  	// ) );
  }
endif;
add_action( 'after_setup_theme', 'cypress_setup' );



/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function materialwp_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'materialwp' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="panel panel-warning">',
		'after_widget'  => '</div></aside>',
		'before_title'  => ' <div class="panel-heading"><h3 class="panel-title">',
		'after_title'   => '</h3></div>',
	) );
}
add_action( 'widgets_init', 'materialwp_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function materialwp_scripts() {
  wp_register_style('app', get_template_directory_uri() . '/assets/css/app.css' );

  wp_register_script('app', get_template_directory_uri() . '/assets/js/app.min.js', array('jquery'), null, true );

  wp_enqueue_script('app');
  wp_enqueue_style('app');
}
add_action( 'wp_enqueue_scripts', 'materialwp_scripts' );

function redirect_home() {
  echo json_encode(array('data' => home_url()));
  exit;
}
add_action('ajax_redirect', 'redirect_home');


/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
//require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Comments Callback.
 */
require get_template_directory() . '/inc/comments-callback.php';
