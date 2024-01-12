<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'wp-load.php');
//Remove Contact Request
	add_action( 'wp_ajax_nopriv_rhl_cr_remove', 'rhl_cr_remove' );
	add_action( 'wp_ajax_rhl_cr_remove', 'rhl_cr_remove' );
//end
//contact request submission
	add_action( 'wp_ajax_nopriv_rhl_cr_contact_form_submission', 'rhl_cr_contact_form_submission' );
	add_action( 'wp_ajax_rhl_cr_contact_form_submission', 'rhl_cr_contact_form_submission' );
//end

//delete
	function rhl_cr_remove(){
		$id = trim($_POST['id']);
		$nm_query = new rhl_cr_data_query;
		$rec = $nm_query->removeById($id);
		if(!$rec){
			echo json_encode(['status' => 'error', 'message' => 'Record could not be deleted!']);exit;
		}
		echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully!']);exit;
	}
//end

function rhl_cr_contact_form_submission() {

	$data = [];
    // sanitize user form input	   
	$data['rhl_cr_fname'] 							= sanitize_text_field( $_POST['rhl_cr_fname'] );
	$data['rhl_cr_lname'] 							= sanitize_text_field( $_POST['rhl_cr_lname'] ); 
	$data['rhl_cr_phone'] 							= sanitize_text_field( $_POST['rhl_cr_phone'] );
	$data['rhl_cr_email'] 							= sanitize_text_field( $_POST['rhl_cr_email'] );
	$data['rhl_cr_message'] 						= sanitize_text_field( $_POST['rhl_cr_message'] );	
	$data['added_on']							    = date('Y-m-d h:i:s');

    //Validate the data     
    contact_form_validation($data);

	//Save the data and send emails	 
    complete_contact_form($data);
}
	
//Server side validation
function contact_form_validation($data = []){
	global $form_errors;
	$form_errors = new WP_Error;
	//print_r($data);
	//validating the input received as argument
	if (
		empty( $data['rhl_cr_fname'] )	|| 
		empty( $data['rhl_cr_lname'] )	||
		empty( $data['rhl_cr_email'] )  ||
		empty( $data['rhl_cr_message'] )  

	){
		$form_errors->add('field', 'Some of required form fields are missing');
	}
}

function complete_contact_form($data = []){

	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = get_option('rhl_cr_success');
	$error = get_option('rhl_cr_error');
	
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
    $query = $wpdb->insert( $wpdb->prefix.'contact_requests', $data);
    
   // print_r($query);exit;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }

     rhl_cr_send_user_email($data);
     rhl_cr_send_admin_email($data);

    echo json_encode(['status' => true, 'message' => $success]);
	exit;
}


// //Email Template
function rhl_cr_send_user_email($data = []){
	$mail 			= new SendEmail;	
	$from 			= get_option('rhl_cr_email_from');
	$to 			= $data['rhl_cr_email'];
	//$cc 			= get_option('rhl_cr_email_cc');
	$subject 		= get_option('rhl_cr_user_email_subject');
	$template_body 	= get_option('rhl_cr_user_email_body');
	$first_name 	= $data['rhl_cr_fname'];

	$template_body 	= str_replace("{your_name}",$first_name,$template_body);

	$body 			= $mail->template($template_body);
	
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from
	];

	wp_mail( $to, $subject, $body, $headers );		
}	

//Admin Email Template
function rhl_cr_send_admin_email($data = []){
	//print_r(123); exit;
	$mail 			= new SendEmail;	
	$from 			= get_option('rhl_cr_email_from');
	$to 			= get_option('rhl_cr_email_to');
	$cc 			= get_option('rhl_cr_email_cc');
	$subject 		= get_option('rhl_cr_admin_email_subject');
	$template_body 	= get_option('rhl_cr_admin_email_body');

	
	$template_body 	= str_replace("{first_name}",$data['rhl_cr_fname'],$template_body);
	$template_body 	= str_replace("{last_name}",$data['rhl_cr_lname'],$template_body);
	$template_body 	= str_replace("{email}",$data['rhl_cr_email'],$template_body);
	$template_body 	= str_replace("{contact_number}",$data['rhl_cr_phone'],$template_body);
	$template_body 	= str_replace("{message}",$data['rhl_cr_message'],$template_body);

	$template_body 	= str_replace("{ip}",$data['ip_addr'],$template_body);

	$body 			= $mail->template($template_body);
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from, 
    	'Cc: '.$cc, 
    	//'Bcc:'.$bcc, 
    	'Reply-To: '.$data["full_name"].' <'.$data["email"].'>'
	];

	wp_mail( $to, $subject, $body, $headers );
}
?>