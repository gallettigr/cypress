<?php
/**
 * Cypress core plugin.
 *
 * @package Cypress
 * @subpackage Functions
 */

/**
 * Outputs a given post meta value.
 *
 * @uses cypress_get_meta() To get post meta value
 * @param string $meta The post meta key ID
 * @param string $default Fallback text if no value is found. Default: false (default Cypress value)
 * @param int $id The target post ID. Default: false (current post ID)
 */

function cypress_meta($meta, $default = false, $id = false) {
  echo cypress_get_meta($meta, $default, $id);
}

    /**
     * Returns a given post meta value.
     *
     * @uses apply_filter() Filter 'cypress_meta_default' to change the default fallback value.
     * @uses get_post_meta() WordPress function to return post meta value given post ID and meta key.
     * @param string $meta The post meta key ID
     * @param string $default Fallback text if no value is found. Default: false (default Cypress value)
     * @param int $id The target post ID. Default: false (current post ID)
     * @return mixed String value or array if it's a group of values.
     */

    function cypress_get_meta($meta, $default = false, $id = false ) {
      global $post; if( !$id ) $id = $post->ID; if( !$default ) $default = apply_filters( 'cypress_meta_default', __('Default dummy meta value by Cypress') );
      $metas = get_post_meta( $post->ID, $meta );
      if( !empty($metas) ) :
        if( count($metas) == 1 ) $metas = $metas[0];
        return $metas;
      else:
        return $default;
      endif;
    }

/**
 * Returns a cached transient post query. This boosts overall performance.
 *
 * @uses wp_parse_args() WordPress function to merge query parameters with default values.
 * @uses get_transient() WordPress function to retrieve cached query from database
 * @uses get_posts() WordPress function to retrieve array of posts
 * @uses set_transient() WordPress function to set a cached query to database
 * @param string $id Custom query name
 * @param array $params WP_Query parameters
 * @param int $hours Expiration time in hours. Default: 24 (one day)
 * @param bool $cache Whether the data should be added to WordPress cache. Default: false
 * @return object Cached query result object
 */

function cypress_query($id, $params, $hours = 24, $cache = false) {
  $default = array( 'no_found_rows' => true, 'cache_results' => $cache );
  $query = wp_parse_args($default, $params);
  $posts = get_transient($id);
  if( !$posts ) :
    $posts = get_posts( $query );
    set_transient( $id, $posts, $hours*60*60 );
    $posts = get_transient($id);
  endif;
  return $posts;
}



function cypress_option($id, $default = false) {
  echo $this->get_ot_option($id, $default);
}

    function cypress_get_option($id, $default = false) {
      if( function_exists('ot_get_option') )
        if( !$default ) $default = apply_filters( 'cypress_options_default', __('Default dummy option value by Cypress') );
        return ot_get_option( $id, $default );
    }

/**
 * Outputs current Cypress version.
 *
 * @uses cypress_get_version() To get Cypress version.
 */

function cypress_version() {
  echo cypress_get_version();
}
    /**
     * Returns current Cypress version.
     *
     * @return string Cypress version.
     */

    function cypress_get_version() {
      return cypress()->version;
    }


