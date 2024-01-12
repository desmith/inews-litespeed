<?php
/**
 * Custom Submit Story Form Request Instance.
 *
 */
class ief_contact_request{
	public function __construct(){
	    add_action('wp_enqueue_scripts', $this->enqueue_public_scripts_and_styles()); //public scripts and styles
	}

	//enqueues scripts and css on the front end
	public function enqueue_public_scripts_and_styles(){

	    wp_enqueue_script('jquery-validation', get_stylesheet_directory_uri() . '/assets/js/jquery-validator.js');
	    wp_enqueue_script('ief-request', dirname(plugin_dir_url(__FILE__)) . '/assets/js/ief_request.js?v='.rand(),['custom-js']);

		wp_localize_script( 'ief-request', 'ief_form_submission', ['ajax_url' =>admin_url(  'admin-ajax.php' )]  );
	}

	//enqueue scripts and css in admin panel
	public function enqueue_admin_scripts_and_styles(){
		
		wp_enqueue_script('datatable', dirname(plugin_dir_url(__FILE__)) . '/assets/js/datatable.js');
		wp_enqueue_style('datatable-css', dirname(plugin_dir_url(__FILE__)) . '/assets/css/datatable.css');
		wp_enqueue_style('style-cr-css', dirname(plugin_dir_url(__FILE__)) . '/assets/css/admin/style.css');
	    wp_enqueue_script('ief-admin', dirname(plugin_dir_url(__FILE__)) . '/assets/js/admin/ief-actions.js');
	    wp_localize_script('ief-admin', 'ief_actions', ['ajax_url' =>admin_url(  'admin-ajax.php' )]  );
	}

	public function loadForm() {
		$nmquote = new ief_data_query;
		$data = $nmquote->getAllRecords();
		set_query_var('data',$data);
    	load_template( dirname(dirname( __FILE__ )) .'/view/form.php');
	}
}
?>