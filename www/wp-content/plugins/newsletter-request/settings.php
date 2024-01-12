<?php
require_once(dirname(__FILE__). DIRECTORY_SEPARATOR .'classes/class.ils_pre_data_query.php' );
add_action('admin_menu', 'ils_pre_admin_add_page');

//Menu for the plugin
function ils_pre_admin_add_page() {
  add_menu_page( 'Newsletter Request', 'Newsletter Request', 'manage_options', 'ils_pre_listing','ils_pre_listing',plugins_url( 'newsletter-request/assets/img/handshake.png' ),2);
  add_submenu_page( 'ils_pre_listing','Settings Page','Settings Page','manage_options','ils_pre_settings', 'ils_pre_settings');
  add_submenu_page( NULL,'View Newsletter Request Details','View Newsletter Request Details','manage_options','ils_pre_view', 'ils_pre_view');
}

//Listing Page
function ils_pre_listing(){

	$quote = new ils_pre_enroll_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	
	$nmquote = new ils_pre_data_query;
	$data = $nmquote->getAllRecords();
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/listing.php');
}

//Settings Page
function ils_pre_settings(){

	$quote = new ils_pre_enroll_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/settings.php');
}

//Loading the individual details page
function ils_pre_view(){

	$quote = new ils_pre_enroll_request;
	add_action('wp_enqueue_scripts', $quote->enqueue_admin_scripts_and_styles()); //admin scripts and styles
	$id = trim($_GET['id']);
	
	$nmquote = new ils_pre_data_query;
	$data = $nmquote->getRecordById($id);
	include(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view/admin/view.php');
}

//Loading the form fields for settings
function ils_pre_settings_fields(){
	register_setting("ils-pre-emails-settings", "ils_pre_email_from");
	register_setting("ils-pre-emails-settings", "ils_pre_email_to");
	register_setting("ils-pre-emails-settings", "ils_pre_email_cc");

	register_setting("ils-pre-emails-settings", "ils_pre_user_email_subject");
	register_setting("ils-pre-emails-settings", "ils_pre_user_email_body"); 
	register_setting("ils-pre-emails-settings", "ils_pre_admin_email_subject");
	register_setting("ils-pre-emails-settings", "ils_pre_admin_email_body");

	register_setting("ils-pre-emails-settings", "ils_pre_success");
	register_setting("ils-pre-emails-settings", "ils_pre_error");

	add_settings_section("ils-pre-emails-settings", "Newsletter Request Settings", null, "ils_pre_settings");

	add_settings_field("ils_pre_email_from", "Email From", "ils_pre_email_from", "ils_pre_settings", "ils-pre-emails-settings");  
	add_settings_field("ils_pre_email_to", "Email To", "ils_pre_email_to", "ils_pre_settings", "ils-pre-emails-settings");  
	add_settings_field("ils_pre_email_cc", "Email CC", "ils_pre_email_cc", "ils_pre_settings", "ils-pre-emails-settings");  

	add_settings_field("ils_pre_user_email_subject", "User Email Subject", "ils_pre_user_email_subject", "ils_pre_settings", "ils-pre-emails-settings");  
	add_settings_field("ils_pre_user_email_body", "User Email", "ils_pre_user_email_body", "ils_pre_settings", "ils-pre-emails-settings");  
	add_settings_field("ils_pre_admin_email_subject", "Admin Email Subject", "ils_pre_admin_email_subject", "ils_pre_settings", "ils-pre-emails-settings");  
	add_settings_field("ils_pre_admin_email_body", "Admin Email", "ils_pre_admin_email_body", "ils_pre_settings", "ils-pre-emails-settings");  

	add_settings_field("ils_pre_success", "Success Message", "ils_pre_success", "ils_pre_settings", "ils-pre-emails-settings");  
	add_settings_field("ils_pre_error", "Failure Message", "ils_pre_error", "ils_pre_settings", "ils-pre-emails-settings"); 

}
add_action("admin_init", "ils_pre_settings_fields");

//---All form fields---//

	//Email From Field
	function ils_pre_email_from()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="ils_pre_email_from" id="ils_pre_email_from" value="<?php echo get_option('ils_pre_email_from'); ?>" />
	  </div>    
	  <?php
	}

	//Email To Field
	function ils_pre_email_to()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="ils_pre_email_to" id="ils_pre_email_to" value="<?php echo get_option('ils_pre_email_to'); ?>" />
	  </div>    
	  <?php
	}

	//Email CC Field
	function ils_pre_email_cc()
	{
	?>
	  <div class="email-section inputField">
	      <input type="text" name="ils_pre_email_cc" id="ils_pre_email_cc" value="<?php echo get_option('ils_pre_email_cc'); ?>" />
	  </div>    
	  <?php
	}

	//User Email Subject Field
	function ils_pre_user_email_subject()
	{
	?>
	  <div class="email-body-section inputField">
	      <input type="text" name="ils_pre_user_email_subject" id="ils_pre_user_email_subject" value="<?php echo get_option('ils_pre_user_email_subject'); ?>" />
	  </div>    
	  <?php
	}

	//User Email Field
	function ils_pre_user_email_body()
	{
	?>
	  	<div class="email-body-section editorField">
	  		<?php 
          		$ils_pre_user_email_body = get_option('ils_pre_user_email_body');
	  			$settings = array(
				    'teeny' => true,
				    'textarea_rows' => 15,
				    'tabindex' => 1
				);
				wp_editor($ils_pre_user_email_body, 'ils_pre_user_email_body', $settings);
	  		?>
	  	</div>    
	  <?php
	}

	//Admin Email Subject Field
	function ils_pre_admin_email_subject()
	{
	?>
	  <div class="email-body-section inputField">
	      <input type="text" name="ils_pre_admin_email_subject" id="ils_pre_admin_email_subject" value="<?php echo get_option('ils_pre_admin_email_subject'); ?>" />
	  </div>    
	  <?php
	}

	//Admin Email Field
	function ils_pre_admin_email_body()
	{
	?>
		<div class="email-body-section editorField">
		  	<?php 
          		$ils_pre_admin_email_body = get_option('ils_pre_admin_email_body');
	  			$settings = array(
				    'teeny' => true,
				    'textarea_rows' => 15,
				    'tabindex' => 1
				);
				wp_editor($ils_pre_admin_email_body, 'ils_pre_admin_email_body', $settings);
	  		?>
		</div>    
	  <?php
	}

	//Success Message Field
	function ils_pre_success()
	{
	?>
	  <div class="message-section inputField">
	      <input type="text" name="ils_pre_success" id="ils_pre_success" value="<?php echo get_option('ils_pre_success'); ?>" />
	  </div>    
	  <?php
	}

	//Error Message Field
	function ils_pre_error()
	{
	?>
	  <div class="message-section inputField">
	      <input type="text" name="ils_pre_error" id="ils_pre_error" value="<?php echo get_option('ils_pre_error'); ?>" />
	  </div>    
	  <?php
	}

	//---All form fields---//

	add_action("admin_init", "ils_pre_download_csv");

	function ils_pre_download_csv() {

	if (isset($_POST['ils_pre_download_csv'])) {

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

		$nmquote = new ils_pre_data_query;

		$records = $nmquote->getAllRecords();
		$data = [];

		if(count($records) > 0){
		   	foreach($records as $key => $val){				
				$data[] = array(
					'Sl No'    		=> ++$key,
					'Email'			  => $val['gt_email'],
					'IP Address'	=> !empty($val['ip_addr'])? $val['ip_addr'] :'N/A',
					'Request Submitted On' => date('d-m-y', strtotime($val['added_on']))
				);
				
			}
		}
		$dname = date('d.m.Y-H.i.s');
	    outputCsv( "isk-newsletter-Requests".$dname.".csv", $data );

	    exit; // This is really important - otherwise it showes all of your page code into the download

	}

	}
//end
?>