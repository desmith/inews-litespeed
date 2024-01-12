<?php
/*
Plugin Name: Story Request Form
Plugin URI: https://sundewsolutions.com/
Description: A custom Career Form Request form plugin for Iskcon News
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
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/class.ief_register.php' );

register_activation_hook(__FILE__, ['ief_register','ief_install']); 
register_deactivation_hook(__FILE__, ['ief_register','ief_uninstall']); 
register_uninstall_hook(__FILE__, ['ief_register','ief_uninstall']); 

add_action('plugins_loaded', array( 'ief_register', 'init' ));

require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/class.ief_contact_request.php' );

function story_request_form_shortcode() {	
    ob_start();
    $quote = new ief_contact_request();
    $quote->loadForm($attributes);
    return ob_get_clean();
}

add_shortcode( 'story_form_request', 'story_request_form_shortcode' );
?>