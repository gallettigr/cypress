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
        <?php var_dump( cypress_get_meta( 'home_openings' ) ); ?>
      </div>
    </div>
  </div>
</section>
<?php get_template_part('layout/home', 'press'); ?>
<?php get_footer(); ?>
