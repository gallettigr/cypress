<?php
/**
 * Services slider.
 */

 ?>
<?php $posts = cypress_query( 'services', array( 'posts_per_page' => -1, 'post_type' => 'services', 'order' => 'ASC', 'orderby' => 'menu_order' ), 48 ); if( $posts ) : ?>
<section id="services" class="bkg-main hidden-xs">
  <div class="container">
    <div class="cyslider">
      <ul>
        <?php foreach ($posts as $post) : setup_postdata( $post ); ?>
        <li>
          <div class="row">
            <div class="col-xs-12 col-md-4 col-sm-12 heading">
              <i class="icon icon-lg <?php cypress_meta( 'service_icon', 'cycon-development' ); ?>"></i>
              <h2 class="title"><?php the_title() ?></h2>
            </div>
            <div class="col-md-8 content hidden-xs hidden-sm">
              <p><?php cypress_meta( 'service_description', 'Lorem ipsum Occaecat cillum dolor culpa officia in ad eu ullamco cupidatat fugiat.' ); ?></p>
            </div>
          </div>
        </li>
        <?php endforeach; wp_reset_query(); ?>
      </ul>
    </div>
  </div>
</section>
<?php endif; ?>
