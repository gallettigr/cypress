<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package Cypress
 * @author gallettigr
 */

get_header(); ?>

<section class="archive blog index" role="blog">
  <div class="container">
    <div class="row">
      <div class="col-md-8 content" role="content">
        <div class="notice">
          <h3><?php _e('Oops, hai trovato una mela marcia...') ?></h3>
          <p>La pagina che stai cercando non esiste. Ricontrolla l'indirizzo che hai digitato, oppure prova a fare una ricerca nella barra accanto.</p>
        </div>
      </div>
      <div id="sidebar" class="col-md-4 sidebar" role="sidebar">
        <?php get_sidebar('blog') ?>
      </div>
    </div>
  </div>
</section>

<?php if( is_404() ): ?>
  <script>_gaq.push(['_trackEvent', '404', document.location.pathname + document.location.search, document.referrer, 0, true]);</script>
<?php endif; ?>
<?php get_footer(); ?>
