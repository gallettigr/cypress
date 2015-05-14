<?php
/**
 * @package Cypress
 * @author gallettigr
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

  <div class="card">
    <div class="entry-img">
      <?php if ( has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
        <?php the_post_thumbnail(); ?>
        </a>
      <?php endif; ?>
    </div>

    <div class="entry-container">
      <header class="entry-header">
        <?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

        <?php if ( 'post' == get_post_type() ) : ?>
        <div class="entry-meta">
        </div><!-- .entry-meta -->
        <?php endif; ?>
      </header><!-- .entry-header -->

      <div class="entry-content">
        <?php
          /* translators: %s: Name of current post */
          the_content( sprintf(
            __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'cypress-theme' ),
            the_title( '<span class="screen-reader-text">"', '"</span>', false )
          ) );
        ?>

        <?php
          wp_link_pages( array(
            'before' => '<div class="page-links">' . __( 'Pages:', 'cypress-theme' ),
            'after'  => '</div>',
          ) );
        ?>
      </div><!-- .entry-content -->

      <footer class="entry-footer">
      </footer><!-- .entry-footer -->
    </div> <!-- .entry-container -->
  </div> <!-- .card -->
</article><!-- #post-## -->
