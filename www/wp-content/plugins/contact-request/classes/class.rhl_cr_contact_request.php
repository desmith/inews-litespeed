<?php
/**
 * Custom IEF Contact Request Instance.
 *
 */
class rhl_cr_contact_request{
	public function __construct(){
	    add_action('wp_enqueue_scripts', $this->enqueue_public_scripts_and_styles()); //public scripts and styles
	}

	//enqueues scripts and css on the front end
	public function enqueue_public_scripts_and_styles(){

	    wp_enqueue_script('jquery-validation', get_stylesheet_directory_uri() . '/assets/js/jquery-validator.js');
	    wp_enqueue_script('rhl-cr-request', dirname(plugin_dir_url(__FILE__)) . '/assets/js/rhl_cr_request.js?v='.rand(),['custom-js']);

		wp_localize_script( 'rhl-cr-request', 'rhl_cr_form_submission', ['ajax_url' =>admin_url(  'admin-ajax.php' )]  );
	}

	//enqueue scripts and css in admin panel
	public function enqueue_admin_scripts_and_styles(){
		
		wp_enqueue_script('datatable', dirname(plugin_dir_url(__FILE__)) . '/assets/js/datatable.js');
		wp_enqueue_style('datatable-css', dirname(plugin_dir_url(__FILE__)) . '/assets/css/datatable.css');
		wp_enqueue_style('style-cr-css', dirname(plugin_dir_url(__FILE__)) . '/assets/css/admin/style.css');
	    wp_enqueue_script('rhl-cr-admin', dirname(plugin_dir_url(__FILE__)) . '/assets/js/admin/rhl-cr-actions.js');
	    wp_localize_script('rhl-cr-admin', 'rhl_cr_actions', ['ajax_url' =>admin_url(  'admin-ajax.php' )]  );
	}

	public function loadForm() {
    	load_template( dirname(dirname( __FILE__ )) .'/view/form.php');
	}
}
?>