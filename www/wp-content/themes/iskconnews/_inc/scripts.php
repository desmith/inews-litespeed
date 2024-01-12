<?php

const INEWS_VERSION = 7.37;

function iNewsScripts(): void {
  wp_enqueue_style(
    'jquery-ui-style',
    get_stylesheet_directory_uri() . '/assets/css/_jquery-ui.css',
    '',
    INEWS_VERSION
  );
  wp_enqueue_style(
    'main-style',
    get_stylesheet_directory_uri() . '/assets/css/style.min.css',
    '',
    INEWS_VERSION
  );
  wp_enqueue_script(
    'jquery',
    get_stylesheet_directory_uri() . '/assets/js/jquery.js',
    ['jquery'],
    INEWS_VERSION
  );
  wp_enqueue_script(
    'jquery-ui',
    get_stylesheet_directory_uri() . '/assets/js/jquery-ui.min.js',
    ['jquery'],
    INEWS_VERSION
  );
  wp_enqueue_script(
    'owl',
    get_stylesheet_directory_uri() . '/assets/js/owl.js',
    ['jquery'],
    INEWS_VERSION
  );
  wp_enqueue_script(
    'jquery-validation',
    get_stylesheet_directory_uri() . '/assets/js/jquery-validator.js',
    ['jquery'],
    INEWS_VERSION
  );
  wp_enqueue_script(
    'custom-js',
    get_stylesheet_directory_uri() . '/assets/js/functions.js',
    ['jquery', 'jquery-ui', 'owl'],
    INEWS_VERSION
  );
  wp_enqueue_script(
    'utils',
    get_stylesheet_directory_uri() . '/assets/js/utils.js',
    ['utils'],
    INEWS_VERSION
  );
}

//loading styles and scripts
add_action('wp_enqueue_scripts', 'iNewsScripts');
