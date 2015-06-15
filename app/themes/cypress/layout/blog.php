<?php
/**
 * Cypress blog index layout.
 * Template Name: Blog Template
 * Theme Name: Cypress
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>
<?php $posts = cypress_query( 'news', array( 'posts_per_page' => -1, 'post_type' => 'post' ) ); if($posts) : ?>
<section class="blog index" role="blog">
  <div class="container">
    <div class="row">
      <div class="col-md-8 content" role="content">
        <?php foreach ($posts as $post): setup_postdata($post); $cat = get_the_category()[0]; if( $cat->cat_ID !== 6 ) : ?>
          <div class="item <?php echo $cat->slug ?>">
            <div role="post-head">
              <div class="info">
                <span class="date"><?php the_time('d F Y'); ?></span>
                <span class="category"><?php echo $cat->name ?></span>
              </div>
              <a class="read-more" href="<?php the_permalink() ?>" title="<?php the_title(); echo ' in ' . $cat->name; ?>"><?php _e('Leggi articolo', 'cypress-theme') ?></a>
              <div class="shader"></div>
              <?php the_post_thumbnail('screenshot', array( 'class' => 'img-responsive' ) ); ?>
            </div>
            <div role="post-summary">
              <h3><a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a></h3>
              <p><?php the_excerpt() ?></p>
            </div>
          </div>
        <?php endif; endforeach; ?>
      </div>
      <div id="sidebar" class="col-md-4 sidebar" role="sidebar">
        <?php get_sidebar('blog') ?>
      </div>
    </div>
  </div>
</section>
<?php endif; wp_reset_query(); ?>
<?php get_footer(); ?>
