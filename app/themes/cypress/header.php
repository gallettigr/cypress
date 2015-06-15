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
	<a class="skip-link screen-reader-text" href="#content"><?php _e('Skip to content', 'cypress-theme'); ?></a>
	<header id="menu-bar" class="site-header" role="header">
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		  <div class="container">
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>
          <a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span id="logo" class="logo mela"><span class="icon"><i class="icon-icon" icon></i></span><span class="brand"><i class="icon-brand-bold" icon></i></span><span class="lettering"><i class="icon-brand-light" icon></i></span></a>
    		</div>

  			<div class="navbar-collapse collapse" id="menu" role="menu">
          <?php wp_nav_menu( array( 'menu' => 'primary', 'theme_location' => 'primary', 'menu_class' => 'nav navbar-nav navbar-right') ); ?>
    		</div>
    	</div>
		</nav>
	</header>
  <?php get_template_part( 'layout/header', 'intro' ); ?>
