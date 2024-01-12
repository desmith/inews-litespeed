<?php

include_once('settings/theme-settings.php');
include_once('settings/sendEmail.php');

// CSS/JS
include_once('_inc/scripts.php');

// Search configs/overrides
include_once('_inc/search.php');

//Navigation menus
include_once('_inc/menu.php');

//SMTP Settings ->
include_once('_inc/smtp.php');

// Posts, Pagination, Most Popular, etc.
include_once('_inc/posts.php');

// Data migration
include_once('_inc/migration.php');

// Image management
include_once('_inc/images.php');

// Admin
include_once ('_inc/admin.php');

/************ Site Title Function **************/
function new_mail_from_name($old): string {
  return 'ISKCON NEWS';
}

/*** Add cache-control headers to response 
disabling due to error in the server log:
[2097] [167.88.61.211:35682#prod.iskconnews.org] [STDERR] PHP Warning: Attempt to read property "post_type" on null in /mnt/efs/www/iskconnews.org/www/wp-content/themes/iskconnews/functions.php on line 39 

function add_cache_control_headers() {
    global $post;
    if (!is_admin() && $post->post_type == 'post')
    {
        header("Cache-Control: public, must-revalidate, max-age=3600");
    }
}

add_action( 'template_redirect', 'add_cache_control_headers' );

***/


// To fix the warning in Site Health screen
add_filter('action_scheduler_run_schedule', function($arg) { return 86400; });

add_filter('wp_mail_from_name', 'new_mail_from_name');

//To keep the count accurate, lets get rid of prefetching
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);


/**** Disable Comment on media files ****/
function filter_media_comment_status($open, $post_id) {
  $post = get_post($post_id);
  if ($post->post_type == 'attachment') {
    return false;
  }
  return $open;
}

add_filter('comments_open', 'filter_media_comment_status', 10, 2);
