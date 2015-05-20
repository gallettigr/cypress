<?php
/**
 * Cypress homepage layout.
 * Template Name: Home Template
 * Theme Name: Cypress
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>
<?php get_template_part('layout/home', 'services'); ?>
<?php get_template_part('layout/home', 'latest'); ?>
<section id="quotes" class="quotes">
  <div class="container">
    <ul>
      <li class="single"><span>Un approccio sperimentale</span><span>crea l'equilibrio nel caos.</span></li>
    </ul>
  </div>
</section>
<section id="jobs">
  <div class="container">
    <div class="careers">
      <div class="background">

      </div>
    </div>
  </div>
</section>
<?php get_template_part('layout/home', 'press'); ?>
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
