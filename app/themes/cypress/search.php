<?php
/**
 * The template for displaying search results pages.
 *
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>
<section class="results blog index" role="blog">
  <div class="container">
    <div class="row">
      <div class="col-md-8 content" role="content">
      <?php if ( have_posts() ) :  while ( have_posts() ) : the_post(); $type = get_post_type($post); ?>
        <?php if( in_array($type, array('post') ) ) : $cat = get_the_category()[0]; ?>
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
        <?php elseif( in_array($type, array('page') ) ) : ?>
        <div class="item">
          <div role="post-head">
            <div class="info">
              <span class="category">Page</span>
            </div>
            <a class="read-more" href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php _e('Visualizza', 'cypress-theme') ?></a>
            <div class="shader"></div>
            <?php the_post_thumbnail('screenshot', array( 'class' => 'img-responsive' ) ); ?>
          </div>
          <div role="post-summary">
            <h3><a href="<?php the_permalink() ?>" title="<?php the_title() ?>"><?php the_title() ?></a></h3>
          </div>
        </div>
      <?php else: ?>
        <div class="notice">
          <h3><?php _e('Woah, non così di fretta!') ?></h3>
          <p>Ci hai beccato... Quello che cercavi non possiamo ancora fartelo vedere. Ci stiamo lavorando, quindi non metterci fretta!</p>
        </div>
      <?php endif; endwhile; else: ?>
        <div class="notice">
          <h3><?php _e('Spiacenti, non ci sono risultati!') ?></h3>
          <p>La ricerca non ha prodotto risultati. Prova con un altra parola o clicca qui per tornare alla homepage.</p>
        </div>
      <?php endif; ?>
      </div>
      <div id="sidebar" class="col-md-4 sidebar" role="sidebar">
        <?php get_sidebar('blog') ?>
      </div>
    </div>
  </div>
</section>

<?php get_footer(); ?>
