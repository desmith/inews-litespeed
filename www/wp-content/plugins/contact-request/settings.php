<?php
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR .'classes/class.rhl_cr_data_query.php' );
add_action('admin_menu', 'rhl_cr_admin_add_page');

//Menu for the plugin
function rhl_cr_admin_add_page() {
  add_menu_page( 'Contact Request', 'Contact Request', 'manage_options', 'rhl_cr_listing','rhl_cr_listing',plugins_url( 'contact-request/assets/img/handshake.png' ),2);
  add_submenu_page( 'rhl_cr_listing','Settings Page','Settings Page','manage_options','rhl_cr_settings', 'rhl_cr_settings');
  add_submenu_page( NULL,'View Contact Request Details','View Contact Request Details','manage_options','rhl_cr_view', 'rhl_cr_view');
}

//Listing Page
function rhl_cr_listing(){

	$quote = new rhl_cr_contact_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	
	$nmquote = new rhl_cr_data_query;
	$data = $nmquote->getAllRecords();
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/listing.php');
}

//Settings Page
function rhl_cr_settings(){

	$quote = new rhl_cr_contact_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/settings.php');
}

//Loading the individual details page
function rhl_cr_view(){

	$quote = new rhl_cr_contact_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	$id = trim($_GET['id']);
	
	$nmquote = new rhl_cr_data_query;
	$data = $nmquote->getRecordById($id);
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/view.php');
}

//Loading the form fields for settings
function rhl_cr_settings_fields(){
	register_setting("rhl-cr-emails-settings", "rhl_cr_email_from");
	register_setting("rhl-cr-emails-settings", "rhl_cr_email_to");
	register_setting("rhl-cr-emails-settings", "rhl_cr_email_cc");

	register_setting("rhl-cr-emails-settings", "rhl_cr_user_email_subject");
	register_setting("rhl-cr-emails-settings", "rhl_cr_user_email_body"); 
	register_setting("rhl-cr-emails-settings", "rhl_cr_admin_email_subject");
	register_setting("rhl-cr-emails-settings", "rhl_cr_admin_email_body");

	register_setting("rhl-cr-emails-settings", "rhl_cr_success");
	register_setting("rhl-cr-emails-settings", "rhl_cr_error");

	add_settings_section("rhl-cr-emails-settings", "Contact Request Settings", null, "rhl_cr_settings");

	add_settings_field("rhl_cr_email_from", "Email From", "rhl_cr_email_from", "rhl_cr_settings", "rhl-cr-emails-settings");  
	add_settings_field("rhl_cr_email_to", "Email To", "rhl_cr_email_to", "rhl_cr_settings", "rhl-cr-emails-settings");  
	add_settings_field("rhl_cr_email_cc", "Email CC", "rhl_cr_email_cc", "rhl_cr_settings", "rhl-cr-emails-settings");  

	add_settings_field("rhl_cr_user_email_subject", "User Email Subject", "rhl_cr_user_email_subject", "rhl_cr_settings", "rhl-cr-emails-settings");  
	add_settings_field("rhl_cr_user_email_body", "User Email", "rhl_cr_user_email_body", "rhl_cr_settings", "rhl-cr-emails-settings");  
	add_settings_field("rhl_cr_admin_email_subject", "Admin Email Subject", "rhl_cr_admin_email_subject", "rhl_cr_settings", "rhl-cr-emails-settings");  
	add_settings_field("rhl_cr_admin_email_body", "Admin Email", "rhl_cr_admin_email_body", "rhl_cr_settings", "rhl-cr-emails-settings");  

	add_settings_field("rhl_cr_success", "Success Message", "rhl_cr_success", "rhl_cr_settings", "rhl-cr-emails-settings");  
	add_settings_field("rhl_cr_error", "Failure Message", "rhl_cr_error", "rhl_cr_settings", "rhl-cr-emails-settings"); 

}
add_action("admin_init", "rhl_cr_settings_fields");

//---All form fields---//

	//Email From Field
	function rhl_cr_email_from()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="rhl_cr_email_from" id="rhl_cr_email_from" value="<?php echo get_option('rhl_cr_email_from'); ?>" />
	  </div>    
	  <?php
	}

	//Email To Field
	function rhl_cr_email_to()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="rhl_cr_email_to" id="rhl_cr_email_to" value="<?php echo get_option('rhl_cr_email_to'); ?>" />
	  </div>    
	  <?php
	}

	//Email CC Field
	function rhl_cr_email_cc()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="rhl_cr_email_cc" id="rhl_cr_email_cc" value="<?php echo get_option('rhl_cr_email_cc'); ?>" />
	  </div>    
	  <?php
	}

	//User Email Subject Field
	function rhl_cr_user_email_subject()
	{
	?>
	  <div class="email-body-section inputField">
	      <input type="text" name="rhl_cr_user_email_subject" id="rhl_cr_user_email_subject" value="<?php echo get_option('rhl_cr_user_email_subject'); ?>" />
	  </div>    
	  <?php
	}

	//User Email Field
	function rhl_cr_user_email_body()
	{
	?>
	  	<div class="email-body-section editorField">
	  		<?php 
          		$rhl_cr_user_email_body = get_option('rhl_cr_user_email_body');
	  			$settings = array(
				    'teeny' => true,
				    'textarea_rows' => 15,
				    'tabindex' => 1
				);
				wp_editor($rhl_cr_user_email_body, 'rhl_cr_user_email_body', $settings);
	  		?>
	  	</div>    
	  <?php
	}

	//Admin Email Subject Field
	function rhl_cr_admin_email_subject()
	{
	?>
	  <div class="email-body-section inputField">
	      <input type="text" name="rhl_cr_admin_email_subject" id="rhl_cr_admin_email_subject" value="<?php echo get_option('rhl_cr_admin_email_subject'); ?>" />
	  </div>    
	  <?php
	}

	//Admin Email Field
	function rhl_cr_admin_email_body()
	{
	?>
		<div class="email-body-section editorField">
		  	<?php 
          		$rhl_cr_admin_email_body = get_option('rhl_cr_admin_email_body');
	  			$settings = array(
				    'teeny' => true,
				    'textarea_rows' => 15,
				    'tabindex' => 1
				);
				wp_editor($rhl_cr_admin_email_body, 'rhl_cr_admin_email_body', $settings);
	  		?>
		</div>    
	  <?php
	}

	//Success Message Field
	function rhl_cr_success()
	{
	?>
	  <div class="message-section inputField">
	      <input type="text" name="rhl_cr_success" id="rhl_cr_success" value="<?php echo get_option('rhl_cr_success'); ?>" />
	  </div>    
	  <?php
	}

	//Error Message Field
	function rhl_cr_error()
	{
	?>
	  <div class="message-section inputField">
	      <input type="text" name="rhl_cr_error" id="rhl_cr_error" value="<?php echo get_option('rhl_cr_error'); ?>" />
	  </div>    
	  <?php
	}

	//---All form fields---//

	add_action("admin_init", "rhl_cr_download_csv");

	function rhl_cr_download_csv() {

	if (isset($_POST['rhl_cr_download_csv'])) {

	    function outputCsv( $fileName, $assocDataArray ) {
	        ob_clean();
	        header( 'Pragma: public' );
	        header( 'Expires: 0' );
	        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	        header( 'Cache-Control: private', false );
	        header( 'Content-Type: text/csv' );
	        header( 'Content-Disposition: attachment;filename=' . $fileName );
	        if ( isset( $assocDataArray['0'] ) ) {
	            $fp = fopen( 'php://output', 'w' );
	            fputcsv( $fp, array_keys( $assocDataArray['0'] ) );
	            foreach ( $assocDataArray AS $values ) {
	                fputcsv( $fp, $values );
	            }
	            fclose( $fp );
	        }
	        ob_flush();
	    }

		$nmquote = new rhl_cr_data_query;

		$records = $nmquote->getAllRecords();
		$data = [];

		if(count($records) > 0){
		   	foreach($records as $key => $val){				
				$data[] = array(
					'Sl No'    		=> ++$key,
					'First Name'		=> $val['rhl_cr_fname'],
					'Last Name'		=> $val['rhl_cr_lname'],
					'Email'			=> $val['rhl_cr_email'],
					'Contact Request Number'=> $val['rhl_cr_phone'],
					'Message'=> $val['rhl_cr_message'],
					'IP Address'	=> !empty($val['ip_addr'])? $val['ip_addr'] :'N/A',
					'Request Submitted On' => date('d-m-y', strtotime($val['added_on']))
				);
				
			}
		}
		$dname = date('d.m.Y-H.i.s');
	    outputCsv( "isk-Contact-Requests".$dname.".csv", $data );

	    exit; // This is really important - otherwise it showes all of your page code into the download

	}

	}
//end
?>