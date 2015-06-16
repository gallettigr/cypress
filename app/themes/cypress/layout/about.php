<?php
/**
 * Cypress about page layout.
 * Template Name: About Template
 * Theme Name: Cypress
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>
<section class="about us">
  <div class="container">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <p><?php the_content() ?></p>
  <?php endwhile; endif; ?>
  </div>
</section>

<?php get_template_part('layout/about', 'profile'); ?>

<?php get_footer(); ?>
