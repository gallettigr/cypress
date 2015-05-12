<?php
/**
 * Header intro. Checks current view type and return header image.
 */
  global $post;
  $type = get_post_type($post);
 ?>

<?php if( in_array($type, array('page')) && has_post_thumbnail($post->ID) ): ?>
  <section id="intro-<?php echo $post->post_name ?>" class="intro <?php echo $type ?>">
    <?php echo get_the_post_thumbnail($post->ID, 'intro'); ?>
  </section>
<?php endif; ?>
