<?php
/*
Plugin Name: Newsletter Request
Plugin URI: https://sundewsolutions.com/
Description: A custom newsletter request form plugin for Iskcon News
Version:     1.0.0
Author:      Sun Dew Solutions
Author URI:  https://sundewsolutions.com/
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
  exit;
}

require_once(dirname(__FILE__, 4) . DIRECTORY_SEPARATOR . 'wp-load.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'ajax.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'settings.php');
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'classes/class.ils_pre_register.php');

register_activation_hook(__FILE__, ['ils_pre_register', 'ils_pre_install']);
register_deactivation_hook(__FILE__, ['ils_pre_register', 'ils_pre_uninstall']);
register_uninstall_hook(__FILE__, ['ils_pre_register', 'ils_pre_uninstall']);

add_action('plugins_loaded', array('ils_pre_register', 'init'));

require_once(__DIR__ . DIRECTORY_SEPARATOR . 'classes/class.ils_pre_enroll_request.php');

function newsletter_form_shortcode() {
  ob_start();
  $quote = new ils_pre_enroll_request();
  $quote->loadForm();
  return ob_get_clean();
}

add_shortcode('newsletter_request', 'newsletter_form_shortcode');
?>
