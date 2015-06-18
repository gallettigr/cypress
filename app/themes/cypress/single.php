<?php
/**
 * Single post template
 *
 * @package Cypress
 * @author gallettigr
 */

get_header();
$cat = get_the_category()[0]; ?>

<section class="post" role="blog">
  <article class="<?php echo $cat->slug ?>" role="post">
    <div class="container">
      <div class="row">
        <div class="col-md-8 content" role="content">
        <?php while ( have_posts() ) : the_post(); ?>
          <div role="post-head">
            <div class="row info">
              <div class="col-sm-6 date"><?php _e('Pubblicato il', 'cypress-theme') ?>: <?php the_time('d F Y'); ?></div>
              <div class="col-sm-6 category"><?php _e('Categoria', 'cypress-theme') ?>: <?php echo $cat->name ?></div>
            </div>
          </div>
          <div role="post-content">
           <div class="row">
            <div class="col-sm-12 inner">
              <h3><?php the_title() ?></h3>
              <p><?php the_content() ?></p>
            </div>
           </div>
          </div>
        <?php endwhile; ?>
        </div>
        <div id="sidebar" class="col-md-4 sidebar" role="sidebar">
          <?php get_sidebar('blog') ?>
        </div>
      </div>
    </div>
  </article>
</section>

<?php get_template_part('layout/single', 'reader'); ?>
<?php get_footer(); ?>
