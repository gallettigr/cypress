<?php
/**
 * Framepress core plugin 'Framer'.
 * @package Framepress
 * @author gallettigr
 * @version 0.8
 * @date    2015-04-09
 * Plugin Name: Framer
 * Contributors: gallettigr
 * Plugin URI: http://github.com/gallettigr/framepress
 * Description: Best frame for your WordPress master piece.
 * Author: gallettigr
 * Version: 0.8
 * Author URI: http://twitter.com/gallettigr
 * Textdomain: framepress
 */
namespace Framepress;
use \DateTime;

if( !is_blog_installed() )
  return;

class Framer {

  public function __construct() {
    add_action('muplugins_loaded', array( $this, 'after_load' ));
    add_action('wp_loaded', array( $this, 'prevent_access' ));
    add_action('init', array( $this, 'cleaner_head' ));
  }

  public function after_load() {
    load_muplugin_textdomain( 'framepress', basename( dirname(__FILE__) ) . '/languages' );
    if (!defined('WP_DEFAULT_THEME'))
      register_theme_directory(ABSPATH . 'wp-content/themes');
    if (APP_ENV !== 'production')
      add_action('pre_option_blog_public', '__return_zero');
    $this->define_constant( 'THEME_PATH', get_stylesheet_directory() );
    $this->define_constant( 'THEME_URI', get_stylesheet_directory_uri() );
  }

  public function init() {

  }

  public function cleaner_head() {
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rel_canonical');

    add_action('wp_head', function(){
      global $wp_the_query;
      if ($id = $wp_the_query->get_queried_object_id())
        $data = get_post($id);
      if($data)
        $type = $data->post_type;
      if($type == 'page') :
        echo '<link rel="canonical" href="' . get_permalink( $id ) . '">';
      elseif($type == 'post') :
        $date = new DateTime($data->post_date);
        $category = get_the_category($id)[0]->slug;
        $path = 'articles' . $date->format('/d/m/Y/') . $category . '/' . $data->post_name;
          echo '<link rel="canonical" href="' . home_url($path) . '">';
      endif;
    });

  }

  public function prevent_access() {
    $redirect_404 =  home_url( '/404' );
    $uri = strtolower($_SERVER['REQUEST_URI']);
    if( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) :
      return;
    elseif( preg_match( '#wp-admin#', $uri ) && !current_user_can('manage_options') ) :
      wp_redirect( $redirect_404 );
      exit;
    elseif( preg_match( '#wp-login#', $uri ) ) :
      wp_redirect( $redirect_404 );
      exit;
    elseif( $uri == '/logout' ) :
      wp_logout();
      wp_redirect( home_url() );
      exit;
    endif;
  }

  public function admin_init() {

  }

  public function define_constant( $constant, $value ) {
    if( ! defined($constant) )
      define($constant, $value);
  }
}

$Framer_Functions = new Framer();

?>
