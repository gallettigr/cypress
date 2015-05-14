<?php
/**
 * Cypress theme functions and definitions
 *
 * @package Cypress
 * @author gallettigr
 */


if ( ! function_exists( 'cypress_setup' ) ) :
  function cypress_setup() {

  	load_theme_textdomain( 'cypress-theme', get_template_directory() . '/languages' );

  	add_theme_support( 'post-thumbnails' );
      add_image_size('intro', 1280);
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
      'plugins' => array('options', 'cache', 'api'),
      'lazy-load' => true
    ) );

    add_theme_support( 'title-tag' );
  	add_theme_support( 'html5', array(
  		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
  	) );

    register_nav_menus( array(
      'primary' => __( 'Primary Menu', 'cypress-theme' ),
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
function cypress_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'cypress-theme' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="panel">',
		'after_widget'  => '</div></aside>',
		'before_title'  => ' <div class="panel-heading"><h3 class="panel-title">',
		'after_title'   => '</h3></div>',
	) );
}
add_action( 'widgets_init', 'cypress_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function cypress_scripts() {
  wp_register_style('app', get_template_directory_uri() . '/assets/css/app.css' );

  wp_register_script('app', get_template_directory_uri() . '/assets/js/app.min.js', array('jquery'), null, true );
  wp_register_script('lazy', get_template_directory_uri() . '/assets/js/public.min.js', array('jquery'), null, true );

  wp_enqueue_style('app');
  wp_enqueue_script('app');
  wp_enqueue_script('lazy');
}
add_action( 'wp_enqueue_scripts', 'cypress_scripts' );

/**
 * Register post types.
 */
function cypress_post_types() {
  // SERVICES
  register_post_type( 'services', array(
    'label'               => __( 'Services', 'cypress-theme' ),
    'description'         => __( 'Publish a list of services provided by your site', 'cypress-theme' ),
    'labels'              => array(
      'name'                => _x( 'Service', 'Service offered', 'cypress-theme' ),
      'singular_name'       => _x( 'Services', 'Services offered', 'cypress-theme' ),
      'menu_name'           => __( 'Services', 'cypress-theme' ),
      'parent_item_colon'   => __( 'Parent Service:', 'cypress-theme' ),
      'all_items'           => __( 'All Services', 'cypress-theme' ),
      'add_new_item'        => __( 'Add New Service', 'cypress-theme' ),
      'add_new'             => __( 'Add New', 'cypress-theme' ),
      'new_item'            => __( 'New Service', 'cypress-theme' ),
      'edit_item'           => __( 'Edit Service', 'cypress-theme' ),
      'update_item'         => __( 'Update Service', 'cypress-theme' ),
      'view_item'           => __( 'View Service', 'cypress-theme' ),
      'search_items'        => __( 'Search Service', 'cypress-theme' ),
      'not_found'           => __( 'Not found', 'cypress-theme' ),
      'not_found_in_trash'  => __( 'Not found in Trash', 'cypress-theme' ),
    ),
    'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
    'hierarchical'        => true,
    'public'              => false,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_admin_bar'   => false,
    'show_in_nav_menus'   => false,
    'can_export'          => true,
    'menu_position'       => 20,
    'menu_icon'           => 'dashicons-sos',
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => false,
    'capability_type'     => 'post',
    'rewrite'             => array(
      'slug'          => 'services',
      'with_front'    => false,
      'pages'         => false
      )
  ) );
  // PROJECTS
  register_post_type( 'projects', array(
    'label'               => __( 'Portfolio', 'cypress-theme' ),
    'description'         => __( 'Add projects to your site portfolio', 'cypress-theme' ),
    'labels'              => array(
      'name'                => _x( 'Project', 'Portfolio project', 'cypress-theme' ),
      'singular_name'       => _x( 'Projects', 'Portfolio projects', 'cypress-theme' ),
      'menu_name'           => __( 'Portfolio', 'cypress-theme' ),
      'parent_item_colon'   => __( 'Parent Project:', 'cypress-theme' ),
      'all_items'           => __( 'All Projects', 'cypress-theme' ),
      'add_new_item'        => __( 'Add New Project', 'cypress-theme' ),
      'add_new'             => __( 'Add New', 'cypress-theme' ),
      'new_item'            => __( 'New Project', 'cypress-theme' ),
      'edit_item'           => __( 'Edit Project', 'cypress-theme' ),
      'update_item'         => __( 'Update Project', 'cypress-theme' ),
      'view_item'           => __( 'View Project', 'cypress-theme' ),
      'search_items'        => __( 'Search Project', 'cypress-theme' ),
      'not_found'           => __( 'Not found', 'cypress-theme' ),
      'not_found_in_trash'  => __( 'Not found in Trash', 'cypress-theme' ),
    ),
    'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
    'hierarchical'        => true,
    'taxonomies'          => array( 'projects' ),
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_admin_bar'   => false,
    'show_in_nav_menus'   => false,
    'can_export'          => true,
    'menu_position'       => 21,
    'menu_icon'           => 'dashicons-carrot',
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'capability_type'     => 'post',
    'rewrite'             => array(
      'slug'          => 'portfolio',
      'with_front'    => false,
      'pages'         => false
      )
  ) );
  // CLIENTS
  register_post_type( 'clients', array(
    'label'               => __( 'Clients', 'cypress-theme' ),
    'description'         => __( 'Featured clients and customer reviews', 'cypress-theme' ),
    'labels'              => array(
      'name'                => _x( 'Client', 'Portfolio project', 'cypress-theme' ),
      'singular_name'       => _x( 'Clients', 'Portfolio projects', 'cypress-theme' ),
      'menu_name'           => __( 'Clients', 'cypress-theme' ),
      'parent_item_colon'   => __( 'Parent Client:', 'cypress-theme' ),
      'all_items'           => __( 'All Clients', 'cypress-theme' ),
      'add_new_item'        => __( 'Add New Client', 'cypress-theme' ),
      'add_new'             => __( 'Add New', 'cypress-theme' ),
      'new_item'            => __( 'New Client', 'cypress-theme' ),
      'edit_item'           => __( 'Edit Client', 'cypress-theme' ),
      'update_item'         => __( 'Update Client', 'cypress-theme' ),
      'view_item'           => __( 'View Client', 'cypress-theme' ),
      'search_items'        => __( 'Search Client', 'cypress-theme' ),
      'not_found'           => __( 'Not found', 'cypress-theme' ),
      'not_found_in_trash'  => __( 'Not found in Trash', 'cypress-theme' ),
    ),
    'supports'            => array( 'title', 'editor', 'thumbnail' ),
    'hierarchical'        => true,
    'public'              => false,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_admin_bar'   => false,
    'show_in_nav_menus'   => false,
    'can_export'          => true,
    'menu_position'       => 22,
    'menu_icon'           => 'dashicons-awards',
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => false,
    'capability_type'     => 'post',
    'rewrite'             => array(
      'slug'          => 'clients',
      'with_front'    => false,
      'pages'         => false
      )
  ) );
  // PROJECT TYPES
  register_taxonomy( 'project-type', array( 'projects' ), array(
    'labels'                     => array(
      'name'                       => _x( 'Types', 'Portfolio project types', 'cypress-theme' ),
      'singular_name'              => _x( 'Type', 'Portfolio project type', 'cypress-theme' ),
      'menu_name'                  => __( 'Project types', 'cypress-theme' ),
      'all_items'                  => __( 'All Types', 'cypress-theme' ),
      'parent_item'                => __( 'Parent Type', 'cypress-theme' ),
      'parent_item_colon'          => __( 'Parent Type:', 'cypress-theme' ),
      'new_item_name'              => __( 'New Type Name', 'cypress-theme' ),
      'add_new_item'               => __( 'Add New Type', 'cypress-theme' ),
      'edit_item'                  => __( 'Edit Type', 'cypress-theme' ),
      'update_item'                => __( 'Update Type', 'cypress-theme' ),
      'view_item'                  => __( 'View Type', 'cypress-theme' ),
      'separate_items_with_commas' => __( 'Separate items with commas', 'cypress-theme' ),
      'add_or_remove_items'        => __( 'Add or remove items', 'cypress-theme' ),
      'choose_from_most_used'      => __( 'Choose from the most used', 'cypress-theme' ),
      'popular_items'              => __( 'Popular Types', 'cypress-theme' ),
      'search_items'               => __( 'Search Types', 'cypress-theme' ),
      'not_found'                  => __( 'Not Found', 'cypress-theme' ),
    ),
    'hierarchical'               => true,
    'public'                     => false,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => false,
    'show_tagcloud'              => false,
    'rewrite'                    => array(
      'slug'        => 'field',
      'with_front'  => true
      )
  ) );

}
add_action( 'init', 'cypress_post_types', 0 );


// THEME OPTIONS AND METABOXES
require_once( trailingslashit(get_template_directory()) . 'includes/metaboxes.php' );
require_once( trailingslashit(get_template_directory()) . 'includes/options.php' );
