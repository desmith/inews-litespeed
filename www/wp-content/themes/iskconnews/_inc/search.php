<?php

// Adjust calculated weight to consider how recently the entry was published.
/***
 * add_filter( 'searchwp_weight_mods', function( $sql ) {
 * global $wpdb;
 *
 * // Depending on the resulting calculation, you can modify how much the date influences the total weight.
 * $modifier = 1;
 *
 * $sql .= " + ( ( UNIX_TIMESTAMP( NOW() ) - ( UNIX_TIMESTAMP( NOW() ) - UNIX_TIMESTAMP( {$wpdb->posts}.post_date ) ) - (SELECT UNIX_TIMESTAMP( post_date ) FROM {$wpdb->posts} WHERE post_status = 'publish' ORDER BY post_date ASC LIMIT 1 ) ) / 86400 ) * {$modifier}";
 *
 * return $sql;
 * } );
 ***/


/**
 * Add search weight to more recently published entries
 */
function my_searchwp_add_weight_to_newer_posts($sql) {
  global $wpdb;

// Adjust how much weight is given to newer publish dates. There
// is no science here as it correlates with the other weights
// in your engine configuration, and the content of your site.
// Experiment until results are returned as you want them to be.
  $modifier = 15;
  $sql .= " + ( 100 * EXP( ( 1 - ABS( ( UNIX_TIMESTAMP( {$wpdb->prefix}posts.post_date ) - UNIX_TIMESTAMP( NOW() ) ) / 86400 ) ) / 1 ) * {$modifier} )";

  return $sql;
}

add_filter('searchwp_weight_mods', 'my_searchwp_add_weight_to_newer_posts');

// ALWAYS return search results ordered by date (instead of relevance)
add_filter('searchwp_return_orderby_date', '__return_true');

// conditionally return results ordered by date depending on search engine name

/***
 * function my_searchwp_return_orderby_date( $order_by_date, $engine ) {
 *
 * // for the Default engine (only!) we want to return search results by date
 * if ( 'default' == $engine ) {
 * $order_by_date = true;
 * }
 *
 * return $order_by_date;
 * }
 *
 * add_filter( 'searchwp_return_orderby_date', 'my_searchwp_return_orderby_date', 10, 2 );
 ***/
