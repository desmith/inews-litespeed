<?php
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'wp-load.php');
define("BCC", "");
//Remove Contact Request
	add_action( 'wp_ajax_nopriv_ief_remove', 'ief_remove' );
	add_action( 'wp_ajax_ief_remove', 'ief_remove' );
//end
//contact request submission
	add_action( 'wp_ajax_nopriv_ief_form_submission', 'ief_form_submission' );
	add_action( 'wp_ajax_ief_form_submission', 'ief_form_submission' );
//end
  
//delete
	function ief_remove(){
		$id = trim($_POST['id']);
		$nm_query = new ief_data_query;
		$rec = $nm_query->removeById($id);
		if(!$rec){
			echo json_encode(['status' => 'error', 'message' => 'Record could not be deleted!']);exit;
		}
		echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully!']);exit;
	}
//end

function ief_form_submission() {

	$data = [];
    // sanitize user form input	   
	$data['wb_fullname'] 						= sanitize_text_field( $_POST['wb_fullname'] );
	$data['wb_email'] 							= sanitize_text_field( $_POST['wb_email'] );
	$data['wb_phone'] 							= sanitize_text_field( $_POST['wb_phone'] );
	$data['wb_location'] 						= sanitize_text_field( $_POST['wb_location'] );
	$data['wb_message'] 						= sanitize_text_field( $_POST['wb_message'] );
	$data['cv'] 								= $_FILES['cv'];
	$data['added_on']							= date('Y-m-d h:i:s');

    //Validate the data     
    vps_form_validation($data);

	//Save the data and send emails	 
    complete_vps_form($data,$_POST['page_id']);
}
	
//Server side validation
function vps_form_validation($data = []){
	global $form_errors;
	$form_errors = new WP_Error;
	//print_r($data);
	//validating the input received as argument
	if (
		empty( $data['wb_fullname'] )  || 
		empty( $data['wb_email'] )     ||
		empty( $data['cv'] )		   
		
	){
		$form_errors->add('field', 'Some of required form fields are missing');
	}
}

function complete_vps_form($data = [],$page_id){

	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = get_option('ief_success');
	$error = get_option('ief_error');
	
	
	if(isset($data['cv']) && !empty($data['cv'])) {
		$document = '';
		$count = count($data['cv']['name']);
		for($i = 0; $i < $count; $i++){
			$ext = pathinfo($data['cv']['name'][$i], PATHINFO_EXTENSION);
			$getDocumentName = md5(rand()) . '-isk.' . $ext;
			$target_dir = WP_CONTENT_DIR . '/uploads/doc/'.$getDocumentName;
			move_uploaded_file( $data['cv']['tmp_name'][$i],$target_dir);
			$document .= $getDocumentName.',';
		}
		$data['cv'] = rtrim($document,',');
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
    $query = $wpdb->insert( $wpdb->prefix.'story_requests', $data);
    
   // print_r($query);exit;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }

     ief_send_user_email($data,$page_id);
     ief_send_admin_email($data);

    echo json_encode(['status' => true, 'message' => $success]);exit;

}

// //Email Template
function ief_send_user_email($data = [],$page_id){
	$mail 			= new SendEmail;	
	$from 			= get_option('ief_email_from');
	$to 			= $data['wb_email'];
	//$cc 			= get_option('ief_email_cc');
	$subject 		= get_option('ief_user_email_subject');
	$template_body 	= get_option('ief_user_email_body');
	$first_name 	= $data['wb_fullname'];

	$template_body 	= str_replace("{your_name}",$first_name,$template_body);
	$template_body 	= str_replace("{position}",$data['wb_position'],$template_body);
	
	$body 			= $mail->template($template_body);
	
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from
	];
	

	//print_r($attachments);die();
	//$attachments = array(ABSPATH . '/uploads/doc/');
	wp_mail( $to, $subject, $body, $headers );		
}	

//Admin Email Template
function ief_send_admin_email($data = []){
	//print_r(123); exit;
	$mail 			= new SendEmail;	
	$from 			= get_option('ief_email_from');
	$to 			= get_option('ief_email_to');
	$cc 			= get_option('ief_email_cc');
	$subject 		= get_option('ief_admin_email_subject');
	$template_body 	= get_option('ief_admin_email_body');

	
	$template_body 	= str_replace("{name}",$data['wb_fullname'],$template_body);
	
	$template_body 	= str_replace("{email}",$data['wb_email'],$template_body);
	
	$template_body 	= str_replace("{contact_number}",$data['wb_phone'],$template_body);
		
	$template_body 	= str_replace("{location}",$data['wb_location'],$template_body);
	
	$template_body 	= str_replace("{message}",$data['wb_message'],$template_body);
		
	$template_body 	= str_replace("{ip}",$data['ip_addr'],$template_body);
	
	$template_body 	= str_replace("{date}",date('m-d-y',strtotime($data['added_on'])),$template_body);

	$body 			= $mail->template($template_body);
	
	$attachments 	= array(WP_CONTENT_DIR . '/uploads/cv/'.$data['cv']);
	
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from, 
    	'Cc: '.$cc, 
    	'Bcc:'.$bcc, 
    	'Reply-To: '.$data["name"].' <'.$data["email"].'>'
	];

	$attachments = [];
	$attachs = explode(',',$data['cv']);
	foreach ($attachs as $attach) {
		array_push($attachments, ABSPATH . 'wp-content/uploads/doc/'.$attach);
	}
	wp_mail( $to, $subject, $body, $headers, $attachments );
}
?>