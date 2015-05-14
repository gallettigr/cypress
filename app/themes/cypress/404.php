<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>

<div class="container">
  <div class="row">

  <div id="primary" class="col-md-12 col-lg-12">
    <main id="main" class="site-main" role="main">

      <div class="card">
        <div class="entry-container">
          <section class="error-404 not-found">
            <header>
              <h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'cypress-theme'); ?></h1>
            </header><!-- .page-header -->


            <div class="page-content">
              <p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'cypress-theme'); ?></p>

              <?php get_search_form(); ?>

              <?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

              <?php
                /* translators: %1$s: smiley */
                $archive_content = '<p>' . sprintf( __( 'Try looking in the monthly archives. %1$s', 'cypress-theme' ), convert_smilies( ':)' ) ) . '</p>';
                the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );
              ?>

              <?php the_widget( 'WP_Widget_Tag_Cloud' ); ?>

            </div><!-- .page-content -->

          </section><!-- .error-404 -->
        </div><!-- .entry-content -->
      </div><!-- .card -->

    </main><!-- #main -->
  </div><!-- #primary -->

  </div> <!-- .row -->
</div> <!-- .container -->

<?php if( is_404() ): ?>
  <script>_gaq.push(['_trackEvent', '404', document.location.pathname + document.location.search, document.referrer, 0, true]);</script>
<?php endif; ?>
<?php get_footer(); ?>
