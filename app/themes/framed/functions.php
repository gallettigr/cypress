<?php
/**
 * materialwp functions and definitions
 *
 * @package materialwp
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'materialwp_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function materialwp_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on materialwp, use a find and replace
	 * to change 'materialwp' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'materialwp', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.

	//Suport for WordPress 4.1+ to display titles
	//add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );

  /*
  Add Android manifest to header.
   */
  add_theme_support( 'web-app', array(
    'name' => 'Cypress', // App name
    'standalone' => true, // Is it a standalone app?
    'start_url' => '/',
    'splash' => '/app/icons/splash.png',
    'orientation' => 'portrait', // eg. landscape, portrait
    'icons' => array(
      array('src' => '/app/icons/icon-32x.png', 'sizes' => '16x16 32x32', 'density' => 1),
      array('src' => '/app/icons/icon-72x@2x.png', 'sizes' => '64x64 72x72', 'density' => 2),
      array('src' => '/app/icons/icon-97x@2x.png', 'sizes' => '97x97', 'density' => 2),
      array('src' => '/app/icons/icon-72x@2x.png', 'sizes' => '128x128 144x144', 'density' => 1),
      array('src' => '/app/icons/icon-97x@2x.png', 'sizes' => '194x194', 'density' => 1)
      ),
    'theme_color' => MAIN_COLOR
  ) );

  add_theme_support( 'open-graph', array(
    'tw_username' => 'gallettigr',
    'fb_appid' => '1611715415714285',
    'copyright' => 'Cypress Framework'
  ) );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'materialwp' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	// add_theme_support( 'post-formats', array(
	// 	'aside', 'image', 'video', 'quote', 'link',
	// ) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'materialwp_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // materialwp_setup
add_action( 'after_setup_theme', 'materialwp_setup' );



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
	wp_enqueue_style( 'bootstrap-styles', get_template_directory_uri() . '/css/bootstrap.min.css', array(), '3.3.1', 'all' );

	wp_enqueue_style( 'ripples-styles', get_template_directory_uri() . '/css/ripples.min.css', array(), '', 'all' );

	wp_enqueue_style( 'material-styles', get_template_directory_uri() . '/css/material-wfont.min.css', array(), '', 'all' );

	wp_enqueue_style( 'materialwp-style', get_stylesheet_uri() );

	wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/bootstrap.min.js', array('jquery'), null, true );

	wp_enqueue_script( 'ripples-js', get_template_directory_uri() . '/js/ripples.min.js', array('jquery'), null, true );

	wp_enqueue_script( 'material-js', get_template_directory_uri() . '/js/material.min.js?defer', array('jquery'), null, true );

	wp_enqueue_script( 'materialwp-navigation', get_template_directory_uri() . '/js/navigation.js?async', array(), null, true );

	wp_enqueue_script( 'materialwp-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), null, true );

	wp_enqueue_script( 'main-js', get_template_directory_uri() . '/js/main.js', array('jquery'), null, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

}
add_action( 'wp_enqueue_scripts', 'materialwp_scripts' );

function redirect_home() {
  echo json_encode(array('data' => home_url()));
  exit;
}
add_action('ajax_redirect', 'redirect_home');


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


/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

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
 * Adds a Walker class for the Bootstrap Navbar.
 */
require get_template_directory() . '/inc/bootstrap-walker.php';

/**
 * Comments Callback.
 */
require get_template_directory() . '/inc/comments-callback.php';

/**
 * TGM Plugin Activation.
 */
require get_template_directory() . '/inc/plugins.php';
