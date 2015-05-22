<?php
/**
 * Clients gallery.
 */

?>
<?php $posts = cypress_query( 'clients', array( 'posts_per_page' => -1, 'post_type' => 'clients' ) ); if($posts) : ?>
<section id="clients" class="clients">
  <div class="container">
    <h4>Abbiamo collaborato con</h4>
    <div class="gallery">
      <?php foreach ($posts as $post) : ?>
        <?php the_post_thumbnail( 'preview', array( 'class' => 'svg', 'id' => $post->post_name ) ); ?>
      <?php endforeach; wp_reset_query(); ?>
    </div>
  </div>
</section>
<?php endif; ?>
