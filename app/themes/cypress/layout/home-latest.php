<?php
/**
 * Latest work widget.
 */

 ?>
<?php do_action( 'cypress_query', 'latest', array( 'posts_per_page' => 1, 'post_type' => 'projects' ) ); ?>
<section id="latest" class="latest project">
<?php global $cyposts; foreach ($cyposts as $post) : setup_postdata( $post ); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-7 col-md-8 col-lg-6 preview">
        <a href="<?php the_permalink() ?>" class="overlay"><h4 class="title"><?php the_title() ?></h4></a>
        <div class="shader"></div>
        <div class="background">
          <?php $attrs = array('class' => 'img-responsive parallax', 'data-parallax' => '.005'); echo get_the_post_thumbnail($post->ID, 'preview', $attrs ); ?>
        </div>
      </div>
      <div class="col-sm-5 col-md-4 col-lg-6 details">
        <h4 class="heading"><?php _e('Latest work', 'cypress-theme') ?></h4>
        <h5 class="client"><?php _e('Client', 'cypress-theme') ?></h5>
          <p><?php echo get_the_title( do_action( 'cypress_get_meta', 'project_client', 'Cypress' ) ); ?></p>
        <h5 class="services"><?php _e('Services') ?></h5>
          <p><?php do_action('cypress_echo_services'); ?></p>
        <p class="peek"><?php the_excerpt() ?></p>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</section>
