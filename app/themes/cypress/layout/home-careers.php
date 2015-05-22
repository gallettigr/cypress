<?php
/**
 * Careers and vacancies blocks.
 */

?>
<section id="jobs" class="careers">
  <div class="container">
    <div class="blocks">
      <div class="row">
        <div class="col-md-4 col-md-offset-2 item">
          <h3>We're looking for</h3>
          <p>Persons with creative sense of humor</p>
        </div>
        <div class="col-md-4 item">
          <h3>We're looking for</h3>
          <p>Suppliers to work with</p>
        </div>
      </div>
    </div>
  </div>
  <div class="background">
    <div class="shader"></div>
    <?php echo wp_get_attachment_image( cypress_get_meta( 'home_openings' ), 'intro', false, array( 'class' => 'img-responsive parallax', 'data-parallax' => '.05' ) ); ?>
  </div>
</section>
