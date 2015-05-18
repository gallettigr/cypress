<?php if( ($services = get_transient('services')) == false ): $services = get_posts( array( 'posts_per_page' => -1, 'post_type' => 'services', 'order_by' => 'title', 'order' => 'ASC' ) ); set_transient( 'services', $services, 60*60*12 ); else: ?>
<section id="services" class="bkg-main">
  <div class="container">
    <div class="cyslider">
      <ul>
        <?php $i = 0; foreach ($services as $service) { ?>
        <li>
          <div class="row">
            <div class="col-md-4 heading">
              <i class="icon icon-lg <?php do_action( 'cypress_echo_meta', $service->ID, 'service_icon', 'cycon-development' ); ?>"></i>
              <h2 class="title"><?php do_action( 'cypress_echo_meta', $service->ID, 'service_title', 'Our great service' ); ?></h2>
            </div>
            <div class="col-md-8 content">
              <p><?php do_action( 'cypress_echo_meta', $service->ID, 'service_description', 'Lorem ipsum Occaecat cillum dolor culpa officia in ad eu ullamco cupidatat fugiat.' ); ?></p>
            </div>
          </div>
        </li>
        <?php $i++; } ?>
      </ul>
    </div>
  </div>
</section>
<?php endif;  ?>
