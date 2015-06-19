<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Cypress
 * @author gallettigr
 */
?>

<footer id="footer" class="footer" role="site-footer">
  <div class="container">
    <div class="row">
      <div class="credits col-sm-4">
        <img class="logo" width="100" src="<?php echo get_template_directory_uri() . '/assets/images/logo.svg' ?>" alt="Mela Communication">
        <p>Mela Communication è un'agenzia pubblicitaria di Roma specializzata in advertising, BTL, digital e applicazioni mobile.</p>
        <p>P.IVA 11982381003</p>
        <p>&copy; <?php echo date('Y') ?> Mela Communication s.r.l.</p>
      </div>
      <div class="navigation col-sm-4">
        <h3>Navigazione</h3>
        <?php wp_nav_menu( array( 'menu' => 'primary', 'theme_location' => 'primary', 'menu_class' => 'footer') ); ?>
      </div>
      <div class="socialize col-sm-4">
<!--         <h3>Social Pages</h3>
        <ul class="icons">
          <li><a href="#" title="Facebook Page"><i class="cycon-map-pin-streamline"></i></a></li>
          <li><a href="#" title="Facebook Page"><i class="cycon-map-pin-streamline"></i></a></li>
          <li><a href="#" title="Facebook Page"><i class="cycon-map-pin-streamline"></i></a></li>
        </ul>
 -->        <h3>Contatti</h3>
          <ul class="contacts">
            <li><i class="cycon-bubble-comment-streamline-talk"></i> +39 0642016958</li>
            <li><i class="cycon-email"></i> info@melacommunication.com</li>
          </ul>
      </div>
    </div>
  </div>

</footer><!-- #footer -->
</div><!-- #canvas -->
<?php wp_footer(); ?>
</body>
</html>
