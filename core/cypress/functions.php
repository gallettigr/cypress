<?php
/**
 * Cypress core plugin.
 *
 * @package Cypress
 * @subpackage Functions
 */

/** Output or return current Cypress version */
function cypress_version() {
  echo cypress_get_version();
}
    function cypress_get_version() {
      return cypress()->version;
    }

/** Output or return current post meta */
function cypress_meta($meta, $default, $id) {
  echo cypress_get_meta($meta, $default, $id);
}
    function cypress_get_meta($meta, $default = 'Dummy post meta by Cypress', $id = false ) {
      global $post; if( !$id ) $id = $post->ID;
      $metas = get_post_meta( $post->ID, $meta );
      if( !empty($metas) ) :
        if( count($metas) == 1 ) $metas = $metas[0];
        return $metas;
      else:
        return $default;
      endif;
    }
