<?php

// https://codex.wordpress.org/Customizing_the_Login_Form

const INEWS_ADMIN_VERSION = 1.01;

function loginLogoURL(): string {
  return home_url();
}

add_filter('login_headerurl', 'loginLogoURL');

function loginHeaderText(): string {
  return get_bloginfo('name');
}

add_filter('login_headertext', 'loginHeaderText');


function adminStyles() {
  wp_enqueue_style('admin-styles',
                   get_stylesheet_directory_uri() . '/assets/css/admin.min.css',
                   '',
                   INEWS_ADMIN_VERSION);
}

add_action('login_enqueue_scripts', 'adminStyles');


// This doesn't really work for this purpose, since it places the code at the top of the page,
// which then gets overewritten by the default stylesheets.
/***
 * function my_login_logo(): void { ?>
 * <style>
 * #login h1 a, .login h1 a {
 * background-image: url(<?php echo get_theme_file_uri('/assets/img/logo.png');?> height: 65px; width: 320px; background-size:
 * 320px 65px; background-repeat: no-repeat; padding-bottom: 30px; }
 * </style>
 * <?php }
 *
 * add_action('login_enqueue_scripts', 'my_login_logo', false);
 ***/
