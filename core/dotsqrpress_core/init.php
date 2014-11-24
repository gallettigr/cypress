<?php
/**
 * @package DOTSQRPress
 * @version 1.0
 */
/*
Plugin Name: DOTSQRPress First Run
Contributors: @gallettigr
Plugin URI: http://dotsqr.co
Description: Initial WordPress config by DOTSQRPress.
Author: @gallettigr
Version: 1.0
Author URI: http://www.twitter.com/gallettigr
Textdomain: dotsqrpress_core
Domain Path: /languages/
*/


# DOTSQRPRESS INIT
if ( dotsqrpress_option_check('dotsqrpress','init_flag') === 0 ) {

	add_action('init', 'dotsqrpress_first_run', 1);
	function dotsqrpress_first_run() {
		// FIRST RUN

	  // CREATE DEFAULT LANDING PAGES AFTER FRONT END USER SUCCESSFUL LOGIN (WELCOME PAGE) AND LOGOUT (GOODBYE PAGE)
		if(get_page_by_title( WELCOME_PAGE ) == NULL) {
			dotsqrpress_create_page(WELCOME_PAGE, WELCOME_PAGE, '<h1>Welcome to ' . get_bloginfo('name'));
		}
		if(get_page_by_title( GOODBYE_PAGE) == NULL) {
			dotsqrpress_create_page(GOODBYE_PAGE, GOODBYE_PAGE, '<h1>See you soon!</h1>');
		}
		if(get_page_by_title( HOMEPAGE_NAME) == NULL) {
			dotsqrpress_create_page(HOMEPAGE_NAME, HOMEPAGE_NAME, '<h1>' . get_bloginfo('name') . ' Homepage</h1>');
		}
		if(get_page_by_title( BLOGPAGE_NAME) == NULL) {
			dotsqrpress_create_page(BLOGPAGE_NAME, BLOGPAGE_NAME, '<h1>' . get_bloginfo('name') . ' Blog</h1>');
		}
	  // CHANGE SITE DEFAULT OPTIONS
	  update_option('show_on_front', 'page');
	  update_option('page_on_front', dotsqrpress_page_by_title(HOMEPAGE_NAME));
	  update_option('page_for_posts', dotsqrpress_page_by_title(BLOGPAGE_NAME));
	  update_option('blogdescription', get_bloginfo('name') . ' powered by DOTSQRPress');
	  update_option('uploads_use_yearmonth_folders', 0);
	  update_option('default_comment_status', 'closed');
	  update_option('admin_email', 'support@dotsqr.co');
	  update_option('large_size_w', 1366);
	  update_option('large_size_h', 768);
	  update_option('medium_size_w', 640);
	  update_option('medium_size_h', 360);
	  update_option('permalink_structure', '/%category%/%postname%/');
	  update_option('admin_email', 'support@dotsqr.co');
		// INIT END
	}
	# DEFAULT DOTSQRPRESS PAGES SETTINGS
	function dotsqrpress_create_page($name, $title, $content) {
		$dotsqrpress_page = array(
			'post_title'    => $title,
			'post_content'  => $content,
			'post_status'   => 'publish',
			'post_author'   => 1,
			'post_type'     => 'page',
			//'page_template' => 'notice.php', // IT'S RECOMMENDED TO CREATE A TEMPLATE FOR BOTH THIS PAGES
			'post_name'     => $name
		);
		wp_insert_post( $dotsqrpress_page );
	}
	function dotsqrpress_page_by_title($title) {
		$page = get_page_by_title($title);
		if ($page) {
			return $page->ID;
		} else {
			return null;
		}
	}
	dotsqrpress_option_update('init_flag',1,'dotsqrpress');
}

?>
