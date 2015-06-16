<?php
/**
 * Clients gallery.
 */

?>
<?php $posts = cypress_query( 'clients', array( 'posts_per_page' => -1, 'post_type' => 'clients', 'order' => 'menu_order', 'orderby' => 'ASC' ), 24, false, 12 ); if($posts) : ?>
<section id="clients" class="clients">
  <div class="container">
    <h4>Abbiamo collaborato con</h4>
    <div class="gallery">
      <?php $i = 0; foreach ($posts as $post) : ++$i; if( $i < 18 ) : ?>
        <div class="item col-md-2 col-xs-6">
          <?php the_post_thumbnail( 'preview', array( 'id' => $post->post_name ) ); ?>
        </div>
      <?php endif; endforeach; wp_reset_query(); ?>
    </div>
  </div>
</section>
<?php endif; ?>
