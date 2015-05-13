<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Cypress
 * @author gallettigr
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title><?php wp_title( '|', true, 'right' ); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
  <div id="canvas">
	<a class="skip-link screen-reader-text" href="#content"><?php _e('Skip to content', 'cypress'); ?></a>
	<header id="menu-bar" class="site-header" role="banner">
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		  <div class="container">
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
          <a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span id="logo" class="logo mela"><span class="brand"><i class="icon-icon" icon></i></span><span class="lettering"><i class="icon-brand-bold" icon></i><i class="icon-brand-light" icon></i></span></span></a>
    		</div>

  			<div class="navbar-collapse collapse" id="menu" role="menu">
          <form class="navbar-form navbar-right" role="search" method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <div class="form-control-wrapper">
                <input name="s" id="s" type="text" class="form-control col-lg-8" placeholder="<?php  _e('Search','cypress'); ?>">
              </div>
          </form>
          <?php wp_nav_menu( array( 'menu' => 'primary', 'theme_location' => 'primary', 'menu_class' => 'nav navbar-nav navbar-right') ); ?>
    		</div> <!-- #menu -->
    	</div><!-- .container -->
		</nav><!-- .navbar -->
	</header><!-- #menu-bar -->
  <?php get_template_part( 'views/header', 'intro' ); ?>
