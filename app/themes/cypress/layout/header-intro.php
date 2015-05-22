<?php
/**
 * Header intro. Checks current view type and return header image.
 */
  global $post;
  $type = get_post_type($post);
 ?>

<?php if( in_array($type, array('page', 'projects')) && has_post_thumbnail($post->ID) ): ?>
  <section id="intro-<?php echo $post->post_name ?>" class="intro <?php echo $type; if( !is_front_page() ) echo ' secondary'; ?>">
    <div class="tagline"><h4><?php cypress_meta( 'page_title' ); ?></h4></div>
    <div class="shader intro"></div>
    <?php $attrs = array('class' => 'img-responsive parallax', 'data-parallax' => '1.1'); if(is_front_page()) $attrs['data-parallax'] = '0.4'; the_post_thumbnail('intro', $attrs ); ?>
  </section>
<?php endif; ?>
