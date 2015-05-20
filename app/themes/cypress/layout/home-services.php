<?php
/**
 * Services slider.
 */

 ?>
<?php do_action( 'cypress_query', 'services', array( 'posts_per_page' => -1, 'post_type' => 'services', 'order_by' => 'title', 'order' => 'ASC' ), 60*60*48 ); ?>
<section id="services" class="bkg-main">
  <div class="container">
    <div class="cyslider">
      <ul>
        <?php global $cyposts; foreach ($cyposts as $post) : setup_postdata( $post ); ?>
        <li>
          <div class="row">
            <div class="col-md-4 col-sm-12 heading">
              <i class="icon icon-lg <?php do_action( 'cypress_echo_meta', 'service_icon', 'cycon-development' ); ?>"></i>
              <h2 class="title"><?php do_action( 'cypress_echo_meta', 'service_title', 'Our great service' ); ?></h2>
            </div>
            <div class="col-md-8 content hidden-xs hidden-sm">
              <p><?php do_action( 'cypress_echo_meta', 'service_description', 'Lorem ipsum Occaecat cillum dolor culpa officia in ad eu ullamco cupidatat fugiat.' ); ?></p>
            </div>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>
