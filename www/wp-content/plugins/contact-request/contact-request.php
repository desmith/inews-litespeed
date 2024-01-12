<?php
/*
Plugin Name: Contact Request
Plugin URI:  https://sundewsolutions.com/
Description: A custom Contact Request form plugin for Iskcon News
Version:     1.0.0
Author:      Sun Dew Solutions
Author URI:  https://sundewsolutions.com/
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'wp-load.php');
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'ajax.php' );
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'settings.php' );
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/class.rhl_cr_register.php' );

register_activation_hook(__FILE__, ['rhl_cr_register','rhl_cr_install']); 
register_deactivation_hook(__FILE__, ['rhl_cr_register','rhl_cr_uninstall']); 
register_uninstall_hook(__FILE__, ['rhl_cr_register','rhl_cr_uninstall']); 

add_action('plugins_loaded', array( 'rhl_cr_register', 'init' ));

require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/class.rhl_cr_contact_request.php' );

function contact_request_form_shortcode() {
    ob_start();
    $quote = new rhl_cr_contact_request();
    $quote->loadForm();
    return ob_get_clean();
}

add_shortcode( 'contact_request', 'contact_request_form_shortcode' );
?>