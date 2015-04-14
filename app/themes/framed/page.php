<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package materialwp
 */

get_header(); ?>
<h1 id="test-1">Hello</h1>
<script type="text/javascript" charset="utf-8" async defer>
  jQuery(document).ready(function($) {
    $('#test-1').click(function(event) {
      $.ajax({
         data: {
          action: 'redirect',
          ajax: 'framepress'
        },
         type: 'post',
         success: function(output) {
                      console.log(output);
                  }
      });
    });
  });
</script>

<div class="container">
	<div class="row">

	<div id="primary" class="col-md-8 col-lg-8">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;
				?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
