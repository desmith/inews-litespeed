<?php
defined( 'ABSPATH' ) || exit;
/**
 * Plugin Name: Next3 Offload
 * Description: Make your WordPress blazing fast by offloading your files to Amazon S3 | DigitalOcean Spaces | Wasabi | Bunny CDN.
 * Plugin URI: https://themedev.net/next3-offload/
 * Author: ThemeDev
 * Version: 4.0.7
 * Author URI: https://themedev.net/
 *
 * Text Domain: next3-offload
 *
 * @package Next3Offload
 * @category Pro
 *
 * Make your WordPress blazing fast by offloading your files to Amazon S3 | DigitalOcean Spaces | Wasabi | Bunny CDN.
 *
 * License:  GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

 if( !defined('NEXT3_FILE_') ){

	define( 'NEXT3_FILE_', __FILE__ );
	define( 'NEXT3_VERSION', '4.0.7' );
	define( 'NEXT3_SELF_MODE', false );

	// enable multi site mode
	if( !defined('NEXT3_MULTI_SITE')){
		define( 'NEXT3_MULTI_SITE', true);
	}
	
	if( !defined('NEXT3_NOT_FILE')){
		define( 'NEXT3_NOT_FILE', true);
	}

	include( __DIR__ . '/functions.php' ); 
	include( __DIR__ . '/loader.php' ); 
	include( __DIR__ . '/plugin.php' );

	// load plugin
	add_action( 'plugins_loaded', function(){
		// load text domain
		load_plugin_textdomain( 'next3-offload', false, basename( dirname( __FILE__ ) ) . '/languages'  );
		if( !did_action('next3Aws/loaded') ){
			// load plugin instance
			\Next3Offload\N3aws_Plugin::instance()->init();
		}
	});

 }






	

