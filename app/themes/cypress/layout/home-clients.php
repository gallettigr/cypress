<?php
/**
 * Clients gallery.
 */

?>
<?php $posts = cypress_query( 'clients', array( 'posts_per_page' => -1, 'post_type' => 'clients', 'order' => 'menu_order', 'orderby' => 'ASC' ), 24, false, 18 ); if($posts) : ?>
<section id="clients" class="clients">
  <div class="container">
    <h4><?php _e('Abbiamo lavorato per', 'cypress-theme') ?></h4>
    <div class="gallery">
      <?php $i = 0; foreach ($posts as $post) : setup_postdata( $post ); ?>
        <div class="item col-md-2 col-xs-6">
          <?php the_post_thumbnail( 'preview', array( 'id' => $post->post_name ) ); ?>
        </div>
      <?php endforeach; wp_reset_query(); ?>
    </div>
  </div>
</section>
<?php endif; ?>
