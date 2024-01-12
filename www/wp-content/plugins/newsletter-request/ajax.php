<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'wp-load.php');
//Remove Contact Request
	add_action( 'wp_ajax_nopriv_ils_pre_remove', 'ils_pre_remove' );
	add_action( 'wp_ajax_ils_pre_remove', 'ils_pre_remove' );
//end
//contact request submission
	add_action( 'wp_ajax_nopriv_ils_pre_get_in_touch_form_submission', 'ils_pre_get_in_touch_form_submission' );
	add_action( 'wp_ajax_ils_pre_get_in_touch_form_submission', 'ils_pre_get_in_touch_form_submission' );
//end

//delete
	function ils_pre_remove(){
		$id = trim($_POST['id']);
		$nm_query = new ils_pre_data_query;
		$rec = $nm_query->removeById($id);
		if(!$rec){
			echo json_encode(['status' => 'error', 'message' => 'Record could not be deleted!']);exit;
		}
		echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully!']);exit;
	}
//end

function ils_pre_get_in_touch_form_submission() {

	$data = [];
    // sanitize user form input	   
	$data['gt_email'] 							= sanitize_text_field( $_POST['gt_email'] ); 
	$data['added_on']							= date('Y-m-d h:i:s');

    //Validate the data     
    pre_enroll_form_validation($data);

	//Save the data and send emails	 
    complete_pre_enroll_form($data);
}
	
//Server side validation
function pre_enroll_form_validation($data = []){
	global $form_errors;
	$form_errors = new WP_Error;
	//print_r($data);
	//validating the input received as argument
	if (
		empty( $data['gt_email'] )	
	){
		$form_errors->add('field', 'Some of required form fields are missing');
	}
}

function complete_pre_enroll_form($data = []){

	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = get_option('ils_pre_success');
	$error = get_option('ils_pre_error');
	
	$sql = "SELECT * FROM `".$wpdb->prefix."newsletter_requests` WHERE `flag` = 0 AND `gt_email` = '".$data['gt_email']."'";
	$result = $wpdb->get_results( $sql, 'ARRAY_A' );

	if(count($result)>0){
		echo json_encode(['status' => false, 'message' => 'Email id already subscribed.']);exit;
	}

	
	$ip = '';
	if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        //ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        //ip pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $data['ip_addr'] = !empty($ip) ? $ip : 'NULL';
    $query = $wpdb->insert( $wpdb->prefix.'newsletter_requests', $data);

   // print_r($query);exit;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }

     ils_pre_send_user_email($data);
     ils_pre_send_admin_email($data);
	
    echo json_encode(['status' => true, 'message' => $success]);
	exit;

}


// //Email Template
function ils_pre_send_user_email($data = []){
	$mail 			= new SendEmail;	
	$from 			= get_option('ils_pre_email_from');
	$to 			= $data['gt_email'];
	//$cc 			= get_option('ils_pre_email_cc');
	$subject 		= get_option('ils_pre_user_email_subject');
	$template_body 	= get_option('ils_pre_user_email_body');
	//$full_name 		= $data['gt_name'];

	//$template_body 	= str_replace("{full_name}",$full_name,$template_body);
	$template_body 	= str_replace("{email}",$data['gt_email'],$template_body);

	$body 			= $mail->template($template_body);
	
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from
	];

	wp_mail( $to, $subject, $body, $headers );		
}	

//Admin Email Template
function ils_pre_send_admin_email($data = []){
	//print_r(123); exit;
	$mail 			= new SendEmail;	
	$from 			= get_option('ils_pre_email_from');
	$to 			= get_option('ils_pre_email_to');
	$cc 			= get_option('ils_pre_email_cc');
	$subject 		= get_option('ils_pre_admin_email_subject');
	$template_body 	= get_option('ils_pre_admin_email_body');
	//$full_name 		= $data['gt_name'];

	
	//$template_body 	= str_replace("{full_name}",$full_name,$template_body);
	$template_body 	= str_replace("{email}",$data['gt_email'],$template_body);
	
	
	$template_body 	= str_replace("{ip}",$data['ip_addr'],$template_body);

	$body 			= $mail->template($template_body);
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from, 
    	'Cc: '.$cc, 
    	'Bcc:'.$bcc, 
    	'Reply-To: '.$data["email"].' <'.$data["email"].'>'
	];

	wp_mail( $to, $subject, $body, $headers );
}
?>