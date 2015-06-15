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
<section class="about profile">
  <div class="container">
    <?php $sections = cypress_get_meta( 'about_sections' ); $i = 0; foreach ($sections as $section) : $section = (object) $section; ?>
    <?php if(++$i%2) : ?>
    <div id="<?php echo $section->title ?>" class="row odd">
      <div class="col-md-6">
        <div class="number"><span><?php echo $i; ?></span><?php echo $section->title; ?></div>
        <p><?php echo $section->section_body ?></p>
      </div>
      <div class="col-md-6">
        <?php echo wp_get_attachment_image( $section->section_image, 'square', array('class' => 'img-responsive') ); ?>
      </div>
    </div>
    <?php else: ?>
    <div id="<?php echo $section->title ?>" class="row even">
      <div class="col-md-6">
        <?php echo wp_get_attachment_image( $section->section_image, 'square', array('class' => 'img-responsive') ); ?>
      </div>
      <div class="col-md-6">
        <div class="number"><span><?php echo $i; ?></span><?php echo $section->title; ?></div>
        <p><?php echo $section->section_body ?></p>
      </div>
    </div>
    <?php endif; endforeach; ?>
  </div>
</section>
<?php get_footer(); ?>
