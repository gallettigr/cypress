<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Cypress
 * @author gallettigr
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<div id="search" class="widget search">
  <?php get_search_form() ?>
</div>

<?php get_template_part('layout/widget', 'social'); ?>
<?php get_template_part('layout/widget', 'categories'); ?>
<?php get_template_part('layout/widget', 'writus'); ?>
