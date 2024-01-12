<?php

/************ Pagination Function **************/
function my_modify_posts_per_page() {
  add_filter('option_posts_per_page', 'my_option_posts_per_page');
}

function my_option_posts_per_page($value) {
  return 9;
}

add_action('init', 'my_modify_posts_per_page', 0);

/************ Most Popular Post **************/
function wpb_set_post_views($postID) {
  $count_key = 'wpb_post_views_count';
  $count = get_post_meta($postID, $count_key, true);
  if ($count === '') {
    $count = 0;
    delete_post_meta($postID, $count_key);
    add_post_meta($postID, $count_key, '0');
  } else {
    $count++;
    update_post_meta($postID, $count_key, $count);
  }
}
