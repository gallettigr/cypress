<?php
/**
 * About us page, company profile section.
 */
$sections = cypress_get_meta( 'about_sections' );
if( is_array($sections) ) :
 ?>

<section class="about profile">
  <div class="container">
    <?php $i = 0; foreach ($sections as $section) : $section = (object) $section; ++$i; ?>
    <div id="<?php echo sanitize_title( $section->title ) ?>" class="row even item">
      <div class="col-md-6 image<?php echo ($i%2) ? ' col-md-push-6' : '' ?>">
        <?php echo wp_get_attachment_image( $section->section_image, 'square', false, array( 'class' => 'img-responsive' ) ); ?>
      </div>
      <div class="col-md-6 intro<?php echo ($i%2) ? ' col-md-pull-6' : '' ?>">
        <div class="row">
          <div class="inner">
            <div class="numbers col-xs-4">
              <span class="index"><?php echo sprintf("%02d", $i); ?></span>
              <span class="title"><?php echo $section->title; ?></span>
            </div>
            <div class="col-xs-8">
              <p><?php echo $section->section_body ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
