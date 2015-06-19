<?php
/**
 * Press release previews. Includes a slider with the most recent press releases.
 */

 ?>
<?php $posts = cypress_query( 'press', array( 'posts_per_page' => 5, 'post_type' => 'post', 'cat' => 6 ) ); if( $posts ) : ?>
<section id="press" class="press">
  <div class="container">
    <h3>Press</h3>
    <ul class="cyrousel">
    <?php foreach ($posts as $post) : setup_postdata( $post ); ?>
      <li class="item">
        <h4><a href="<?php the_permalink() ?>" class="dark"><?php the_title() ?></a></h4>
        <p><?php the_excerpt() ?></p>
        <a class="light" href="<?php cypress_meta( 'press_url', '#' ); ?>" rel="nofollow"><?php _e('Published by', 'cypress-theme') ?> <?php cypress_meta( 'press_author', 'Source author' ); ?></a>. <span class="date"><?php echo get_the_date('l, d F Y') ?>.</span>
      </li>
    <?php endforeach; wp_reset_query(); ?>
    </ul>
  </div>
</section>
<?php endif; ?>
