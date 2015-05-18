<?php
/**
 * Press release previews. Includes a slider with the most recent press releases.
 */

 ?>
<?php if( ($press = get_transient('press')) == false ): $press = get_posts( array( 'posts_per_page' => 5, 'post_type' => 'post', 'cat' => 6, 'order_by' => 'date', 'order' => 'DESC' ) ); set_transient( 'press', $press, 60*60*12 ); else: ?>
<section id="press" class="press">
  <div class="container">
    <h3>Press Releases</h3>
    <ul class="cyrousel">
    <?php $i = 0; foreach ($press as $post) : setup_postdata( $post ); ?>
      <li class="item">
        <h4><?php the_title() ?></h4>
        <p><?php the_excerpt() ?></p>
        <a class="light" href="<?php do_action( 'cypress_echo_meta', $post->ID, 'press_url', '#' ); ?>"><?php do_action( 'cypress_echo_meta', $post->ID, 'press_author', 'Source author' ); ?></a>
      </li>
    <?php $i++; endforeach; ?>
    </ul>
  </div>
</section>
<?php endif;  ?>
