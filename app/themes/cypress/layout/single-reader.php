<?php
/**
 * Careers and vacancies blocks.
 */
$cat = (object) get_the_category()[0];
$siblings = array( get_previous_post( false, 6 ), get_next_post( false, 6 ) ); if( $cat->term_id == 6 ) $siblings = null;;
if( $siblings ) :
?>
<section id="reader" class="reader">
  <div class="container">
  <div class="row">
    <div class="col-md-8">
      <div class="row">
      <?php foreach ($siblings as $sibling) : ?>
        <?php if( !empty($sibling) ) : ?>
        <div class="col-sm-6 item">
          <a href="<?php echo get_permalink($sibling->ID) ?>" title="<?php _e('Vedi anche:'); echo $sibling->post_title; ?>">
            <span><?php echo ( cypress_is_older($sibling) ) ? 'Previous' : 'Next'; ?></span>
            <?php echo get_the_post_thumbnail( $sibling->ID, 'screenshot', array( 'class' => 'img-responsive' ) ) ?>
            <div class="shader"></div>
          </a>
        </div>
        <?php endif; ?>
      <?php endforeach; ?>
      </div>
    </div>
  </div>
  </div>
</section>
<?php endif; ?>
