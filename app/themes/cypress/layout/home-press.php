<?php
/**
 * Press release previews. Includes a slider with the most recent press releases.
 */

 ?>
<?php do_action( 'cypress_query', 'press', array( 'posts_per_page' => 5, 'post_type' => 'post', 'cat' => 6 ) ); ?>
<section id="press" class="press">
  <div class="container">
    <h3>Press Releases</h3>
    <ul class="cyrousel">
    <?php global $cyposts; foreach ($cyposts as $post) : setup_postdata( $post ); ?>
      <li class="item">
        <h4><a href="<?php the_permalink() ?>" class="dark"><?php the_title() ?></a></h4>
        <p><?php the_excerpt() ?></p>
        <a class="light" href="<?php do_action( 'cypress_echo_meta', 'press_url', '#' ); ?>" rel="nofollow"><?php _e('Published by', 'cypress-theme') ?> <?php do_action( 'cypress_echo_meta', 'press_author', 'Source author' ); ?></a>. <span class="date"><?php echo get_the_date('l, d F Y') ?>.</span>
      </li>
    <?php endforeach; wp_reset_query(); ?>
    </ul>
  </div>
</section>
