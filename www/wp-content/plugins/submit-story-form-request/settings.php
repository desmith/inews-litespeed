<?php
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR .'classes/class.ief_data_query.php' );
add_action('admin_menu', 'ief_admin_add_page');

//Menu for the plugin
function ief_admin_add_page() {
  add_menu_page( 'Submit Story Form Request', 'Submit Story Form Request', 'manage_options', 'ief_listing','ief_listing',plugins_url( 'submit-story-form-request/assets/img/handshake.png' ),2);
  add_submenu_page( 'ief_listing','Settings Page','Settings Page','manage_options','ief_settings', 'ief_settings');
  add_submenu_page( NULL,'View Submit Story Form Request Details','View Submit Story Form Request Details','manage_options','ief_view', 'ief_view');
}

//Listing Page
function ief_listing(){

	$quote = new ief_contact_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	
	$nmquote = new ief_data_query;
	$data = $nmquote->getAllRecords();
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/listing.php');
}

//Settings Page
function ief_settings(){

	$quote = new ief_contact_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/settings.php');
}

//Loading the individual details page
function ief_view(){

	$quote = new ief_contact_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	$id = trim($_GET['id']);
	
	$nmquote = new ief_data_query;
	$data = $nmquote->getRecordById($id);
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/view.php');
}

//Loading the form fields for settings
function ief_settings_fields(){
	register_setting("ief-emails-settings", "ief_email_from");
	register_setting("ief-emails-settings", "ief_email_to");
	register_setting("ief-emails-settings", "ief_email_cc");

	register_setting("ief-emails-settings", "ief_user_email_subject");
	register_setting("ief-emails-settings", "ief_user_email_body"); 
	register_setting("ief-emails-settings", "ief_admin_email_subject");
	register_setting("ief-emails-settings", "ief_admin_email_body");

	register_setting("ief-emails-settings", "ief_success");
	register_setting("ief-emails-settings", "ief_error");

	add_settings_section("ief-emails-settings", "Career Form Request Settings", null, "ief_settings");

	add_settings_field("ief_email_from", "Email From", "ief_email_from", "ief_settings", "ief-emails-settings");  
	add_settings_field("ief_email_to", "Email To", "ief_email_to", "ief_settings", "ief-emails-settings");  
	add_settings_field("ief_email_cc", "Email CC", "ief_email_cc", "ief_settings", "ief-emails-settings");  

	add_settings_field("ief_user_email_subject", "User Email Subject", "ief_user_email_subject", "ief_settings", "ief-emails-settings");  
	add_settings_field("ief_user_email_body", "User Email", "ief_user_email_body", "ief_settings", "ief-emails-settings");  
	add_settings_field("ief_admin_email_subject", "Admin Email Subject", "ief_admin_email_subject", "ief_settings", "ief-emails-settings");  
	add_settings_field("ief_admin_email_body", "Admin Email", "ief_admin_email_body", "ief_settings", "ief-emails-settings");  

	add_settings_field("ief_success", "Success Message", "ief_success", "ief_settings", "ief-emails-settings");  
	add_settings_field("ief_error", "Failure Message", "ief_error", "ief_settings", "ief-emails-settings"); 

}
add_action("admin_init", "ief_settings_fields");

//---All form fields---//

	//Email From Field
	function ief_email_from()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="ief_email_from" id="ief_email_from" value="<?php echo get_option('ief_email_from'); ?>" />
	  </div>    
	  <?php
	}

	//Email To Field
	function ief_email_to()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="ief_email_to" id="ief_email_to" value="<?php echo get_option('ief_email_to'); ?>" />
	  </div>    
	  <?php
	}

	//Email CC Field
	function ief_email_cc()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="ief_email_cc" id="ief_email_cc" value="<?php echo get_option('ief_email_cc'); ?>" />
	  </div>    
	  <?php
	}

	//User Email Subject Field
	function ief_user_email_subject()
	{
	?>
	  <div class="email-body-section inputField">
	      <input type="text" name="ief_user_email_subject" id="ief_user_email_subject" value="<?php echo get_option('ief_user_email_subject'); ?>" />
	  </div>    
	  <?php
	}

	//User Email Field
	function ief_user_email_body()
	{
	?>
	  	<div class="email-body-section editorField">
	  		<?php 
          		$ief_user_email_body = get_option('ief_user_email_body');
	  			$settings = array(
				    'teeny' => true,
				    'textarea_rows' => 15,
				    'tabindex' => 1
				);
				wp_editor($ief_user_email_body, 'ief_user_email_body', $settings);
	  		?>
	  	</div>    
	  <?php
	}

	//Admin Email Subject Field
	function ief_admin_email_subject()
	{
	?>
	  <div class="email-body-section inputField">
	      <input type="text" name="ief_admin_email_subject" id="ief_admin_email_subject" value="<?php echo get_option('ief_admin_email_subject'); ?>" />
	  </div>    
	  <?php
	}

	//Admin Email Field
	function ief_admin_email_body()
	{
	?>
		<div class="email-body-section editorField">
		  	<?php 
          		$ief_admin_email_body = get_option('ief_admin_email_body');
	  			$settings = array(
				    'teeny' => true,
				    'textarea_rows' => 15,
				    'tabindex' => 1
				);
				wp_editor($ief_admin_email_body, 'ief_admin_email_body', $settings);
	  		?>
		</div>    
	  <?php
	}

	//Success Message Field
	function ief_success()
	{
	?>
	  <div class="message-section inputField">
	      <input type="text" name="ief_success" id="ief_success" value="<?php echo get_option('ief_success'); ?>" />
	  </div>    
	  <?php
	}

	//Error Message Field
	function ief_error()
	{
	?>
	  <div class="message-section inputField">
	      <input type="text" name="ief_error" id="ief_error" value="<?php echo get_option('ief_error'); ?>" />
	  </div>    
	  <?php
	}

	//---All form fields---//

	add_action("admin_init", "ief_download_csv");

	function ief_download_csv() {

	if (isset($_POST['ief_download_csv'])) {

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

		$nmquote = new ief_data_query;

		$records = $nmquote->getAllRecords();
		$data = [];

		if(count($records) > 0){
		   	foreach($records as $key => $val){				
				$data[] = array(
					'Sl No'    			=> ++$key,
					'Name'	        => $val['wb_fullname'],
					'Email'				  => $val['wb_email'],
					'Phone No'      => $val['wb_phone'],
					'Location' 			=> $val['wb_location'],
					'Message' 			=> $val['wb_message'],
					'IP Address'	  => !empty($val['ip_addr'])? $val['ip_addr'] :'N/A',
					'Request Submitted On' => date('d-m-y', strtotime($val['added_on']))
				);
				
			}
		}
		$dname = date('d.m.Y-H.i.s');
	    outputCsv( "Isk-Submit-Story-Form-Requests".$dname.".csv", $data );

	    exit; // This is really important - otherwise it showes all of your page code into the download

	}

	}
//end
?>