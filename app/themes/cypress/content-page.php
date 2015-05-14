<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package Cypress
 * @author gallettigr
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

  <div class="card">

    <div class="entry-container">
      <header class="entry-header">
        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
      </header><!-- .entry-header -->

      <div class="entry-content">
        <?php the_content(); ?>
        <?php
          wp_link_pages( array(
            'before' => '<div class="page-links">' . __( 'Pages:', 'cypress-theme' ),
            'after'  => '</div>',
          ) );
        ?>
      </div><!-- .entry-content -->

      <footer class="entry-footer">
        <?php edit_post_link( __( 'Edit', 'cypress-theme' ), '<span class="edit-link">', '</span>' ); ?>
      </footer><!-- .entry-footer -->
    </div> <!-- .entry-container -->
  </div> <!-- .card -->
</article><!-- #post-## -->
