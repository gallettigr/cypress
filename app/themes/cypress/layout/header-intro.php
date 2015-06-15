<?php
/**
 * Header intro. Checks current view type and return header image.
 */
  global $post;
  $type = get_post_type($post);
 ?>

<?php if( in_array($type, array('page', 'post', 'projects') ) && has_post_thumbnail($post->ID) && !is_search() && !is_404() && !is_archive() ): ?>
  <section id="intro-<?php echo $post->post_name ?>" class="intro <?php echo $type; if( !is_front_page() && !is_page('contacts') ) echo ' secondary'; ?>">
    <?php $title = cypress_get_meta( 'page_title' ); if( in_array( $type, array('post') ) ) $title = get_the_title($post->ID); ?>
    <div class="tagline"><h4><?php echo $title; ?></h4></div>
    <div class="shader intro"></div>
    <?php $attrs = array('class' => 'img-responsive parallax', 'data-parallax' => '1.1'); if(is_front_page() || is_page('contacts') ) $attrs['data-parallax'] = '0.4'; the_post_thumbnail('intro', $attrs ); ?>
  </section>
<?php elseif( is_search() || is_404() || is_archive() ): $uri = parse_url($_SERVER['REQUEST_URI'])['path']; $query = urldecode( str_replace( '/search/', '', $uri ) ); ?>
  <section id="intro-search" class="intro secondary">
    <div class="tagline"><h4><?php if( is_search() ): _e('Risultati per ricerca '); echo ': ' . $query; elseif( is_404() ) : echo 'Oops...'; elseif( is_archive() ): echo get_the_archive_title(); endif; ?></h4></div>
    <div class="shader intro"></div>
    <img src="<?php echo get_template_directory_uri() ?>/assets/images/placeholder.jpg" class="img-responsive parallax" data-parallax="1.1">
  </section>
<?php endif; ?>
