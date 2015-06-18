<?php
/**
 * Cypress contacts layout.
 * Template Name: Contacts Template
 * Theme Name: Cypress
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>
<section class="info">
  <div class="container">
    <div class="row">
      <div class="col-md-6 content">
      <?php while ( have_posts() ) : the_post(); ?>
        <?php the_content() ?>
      <?php endwhile; ?>
      </div>
      <div class="col-md-4 invite">
        <h3>Vieni a trovarci!</h3>
        <p>Siamo pronti ad ascoltare le tue idee progettuali e insieme trovare la soluzione adatta alle tue esigenze.</p>
        <p>Abbiamo un team di professionisti pronti ad offrirti consulenza e nuove idee per sviluppare al meglio la tua azienda.</p>
      </div>
    </div>
    <div class="row">
      <div id="join-us" class="join-us">
        <div class="col-md-6">
            <h4><?php echo cypress_get_meta( 'joinus_title' ) ?></h4>
            <p><?php echo cypress_get_meta( 'joinus_text' ) ?></p>
        </div>
        <div class="col-md-6">
          <a href="mailto:<?php echo cypress_get_meta( 'joinus_email' ) ?>">Inviaci il tuo CV</a>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="map">
  <div class="container">
    <div class="foreground">
      <a class="visit-us" href="https://www.google.it/maps/place/Mela+Communication+S.R.L./@41.871976,12.477454,17z/data=!3m1!4b1!4m2!3m1!1s0x132f602977f41633:0x973ef026909d9480" target="_blank" title="Vieni a trovarci!">Vieni a trovarci!</a>
      <i class="cycon-map"></i>
      <img class="img-responsive" src="<?php echo get_template_directory_uri() . '/assets/images/city.svg' ?>" alt="Mela Communication">
    </div>
    <div class="background">
      <img class="img-responsive parallax first" width="180" data-parallax="-.2" src="<?php echo get_template_directory_uri() . '/assets/images/cloud.svg' ?>" alt="Mela Communication">
      <img class="img-responsive parallax second" width="300" data-parallax=".1" src="<?php echo get_template_directory_uri() . '/assets/images/cloud.svg' ?>" alt="Mela Communication">
      <img class="img-responsive parallax third" width="80" data-parallax=".6" src="<?php echo get_template_directory_uri() . '/assets/images/cloud.svg' ?>" alt="Mela Communication">
    </div>
  </div>
</section>
<?php get_footer() ?>
