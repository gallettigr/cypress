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
<?php $siblings = array( get_previous_post( false, 6 ), get_next_post( false, 6 ) ); ?>
<section id="reader" class="reader">
  <div class="container">
  <div class="row">
    <div class="col-md-8">
      <div class="row">
      <?php foreach ($siblings as $sibling) : ?>
        <?php if( !empty($sibling) ) : ?>
        <div class="col-sm-6 item">
          <a href="<?php echo get_permalink($sibling->ID) ?>" title="<?php _e('Vedi anche:'); echo $sibling->post_title; ?>">
            <span><?php echo ( cypress_is_older($sibling) ) ? 'Previous' : 'Next'; ?></span>
            <?php echo get_the_post_thumbnail( $sibling->ID, 'screenshot', array( 'class' => 'img-responsive' ) ) ?>
            <div class="shader"></div>
          </a>
        </div>
        <?php endif; ?>
      <?php endforeach; ?>
      </div>
    </div>
  </div>
  </div>
</section>
<?php get_footer(); ?>
