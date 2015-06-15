<?php
/**
 * Cypress services layout.
 * Template Name: Services Template
 * Theme Name: Cypress
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<section id="summary" class="summary head">
  <div class="container"><?php the_content(); ?></div>
</section>
<?php endwhile; ?>
<?php get_template_part('layout/services', 'list'); ?>

<?php get_footer(); ?>
