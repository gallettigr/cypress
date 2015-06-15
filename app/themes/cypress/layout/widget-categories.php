<?php $cats = get_categories( array( 'type' => 'post', 'orderby' => 'name', 'oder' => 'ASC', 'hide_empty' => 1, 'taxonomy' => 'category', 'exclude' => get_category_by_slug('press-release')->term_id ) ); if( $cats ) : ?>
<div id="categories" class="widget categories">
  <h4>Categorie</h4>
  <ul>
  <?php foreach ($cats as $cat) { ?>
    <li id="<?php echo $cat->slug ?>"><a href="<?php echo get_category_link( $cat->cat_ID ) ?>"><?php echo $cat->name ?> <span><?php echo $cat->count ?></span></a></li>
  <?php } ?>
  </ul>
</div>
<?php endif; ?>
