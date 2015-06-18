<?php
/**
 * Services listing.
 */

?>
<?php $posts = cypress_query( 'services', array( 'posts_per_page' => -1, 'post_type' => 'services', 'order' => 'ASC', 'orderby' => 'menu_order' ), 24 ); if($posts) : ?>
<section id="core" class="core services">
  <div class="container">
    <?php $i = 0; foreach ($posts as $post) : setup_postdata($post); if( $i < 3 ) : ?>
    <div class="row item">
      <div class="col-md-6">
        <h5><i class="<?php cypress_meta( 'service_icon', 'fa fa-diamond' ); ?>"></i><?php cypress_meta( 'service_title', 'Our great service' ); ?></h5>
        <p><?php the_content(); ?></p>
      </div>
      <div class="col-md-6"><?php the_post_thumbnail( 'screenshot', array( 'id' => $post->post_name, 'class' => 'img-responsive' ) ); ?></div>
    </div>
    <?php endif; $i++; endforeach; ?>
  </div>
</section>
<section id="secondary" class="secondary services">
  <div class="container">
    <div class="row">
      <?php $i = 0; foreach ($posts as $post) : setup_postdata($post); if( $i > 2 ) : ?>
      <div class="col-md-4 item">
        <h5><i class="<?php cypress_meta( 'service_icon', 'fa fa-diamond' ); ?>"></i><?php cypress_meta( 'service_title', 'Our great service' ); ?></h5>
        <p><?php the_content(); ?></p>
      </div>
      <?php endif; $i++; endforeach; ?>
    </div>
  </div>
</section>
<?php endif; wp_reset_query(); ?>
