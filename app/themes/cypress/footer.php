<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Cypress
 * @author gallettigr
 */
?>

  <footer id="colophon" class="site-footer" role="contentinfo">

    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="site-info pull-right">
            <?php printf( __( '%1$s by %2$s.', 'cypress-theme' ), 'Cypress Theme', '<a href="http://github.com/gallettigr" target="_blank" rel="designer">@gallettigr</a>' ); ?>
          </div><!-- .site-info -->
        </div> <!-- col-lg-12 -->
      </div><!-- .row -->
    </div><!-- .containr -->

  </footer><!-- #colophon -->
</div><!-- #canvas -->
<?php wp_footer(); ?>
</body>
</html>
