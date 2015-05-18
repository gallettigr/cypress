<?php
/**
 * Cypress homepage layout.
 * Template Name: Home Template
 * Theme Name: Cypress
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>
<?php get_template_part('views/services', 'preview'); ?>
<?php get_template_part('views/press', 'preview'); ?>
<div class="container">
  <div class="row">

  <div id="primary" class="col-md-8 col-lg-8">
    <main id="main" class="site-main" role="main">

      <?php while ( have_posts() ) : the_post(); ?>

        <?php get_template_part( 'content', 'page' ); ?>

      <?php endwhile; // end of the loop. ?>

    </main><!-- #main -->
  </div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
