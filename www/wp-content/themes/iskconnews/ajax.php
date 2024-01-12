<?php 
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'wp-load.php');

define("BCC", "");
define("CC", "");
$url = site_url();
$pattern = '/\blocalhost\b/';
if (preg_match($pattern,$url) == true) {
	define("ADMIN_TO", "dipanjan@sundewsolutions.com");
}else{
	//define("ADMIN_TO", "mailto:info@diadem.in");
	define("ADMIN_TO", "dipanjan@sundewsolutions.com");
}


// //request submission for zimbra only
	add_action( 'wp_ajax_nopriv_see_cost_breakup', 'see_cost_breakup' );
	add_action( 'wp_ajax_see_cost_breakup', 'see_cost_breakup' );
// //end
//request submission for office 365 only  
	add_action( 'wp_ajax_nopriv_see_cost_breakup_office', 'see_cost_breakup_office' );
	add_action( 'wp_ajax_see_cost_breakup_office', 'see_cost_breakup_office' );
//end

//request submission for Gsuite only  
	add_action( 'wp_ajax_nopriv_see_cost_breakup_gsuite', 'see_cost_breakup_gsuite' );
	add_action( 'wp_ajax_see_cost_breakup_gsuite', 'see_cost_breakup_gsuite' );
//end
	

//request submission for zimbra + office 365 only  
	add_action( 'wp_ajax_nopriv_see_cost_breakup_office_zimbra', 'see_cost_breakup_office_zimbra' );
	add_action( 'wp_ajax_see_cost_breakup_office_zimbra', 'see_cost_breakup_office_zimbra' );
//end


// //request submission for zimbra + gsuite only  
	add_action( 'wp_ajax_nopriv_see_cost_breakup_gsuite_zimbra', 'see_cost_breakup_gsuite_zimbra' );
	add_action( 'wp_ajax_see_cost_breakup_gsuite_zimbra', 'see_cost_breakup_gsuite_zimbra' );
// //end 

// //request submission for zimbra + gsuite only  
	add_action( 'wp_ajax_nopriv_see_cost_breakup_jelastic', 'see_cost_breakup_jelastic' );
	add_action( 'wp_ajax_see_cost_breakup_jelastic', 'see_cost_breakup_jelastic' );
// //end

//Job apploction request submission
	add_action( 'wp_ajax_nopriv_dm_ja_get_job_details', 'dm_ja_get_job_details' );
	add_action( 'wp_ajax_dm_ja_get_job_details', 'dm_ja_get_job_details' );
//end


/*Zimbra form submission functionalities*/
function see_cost_breakup() {
//print_r(123); die();
	$data = [];
    // sanitize user form input	   
	$data['brkup_full_name'] 						= sanitize_text_field( $_POST['brkup_full_name'] );
	$data['brkup_email'] 							= sanitize_text_field( $_POST['brkup_email'] );
	$data['brkup_phone'] 							= $_POST['brkup_phone'];
	$data['brkup_num1'] 							= $_POST['brkup_num1'];
	$data['brkup_num2']								= $_POST['brkup_num2'];
	$data['brkup_captcha']							= $_POST['brkup_captcha'];
	$data['added_on']								= date('Y-m-d h:i:s');
    //Validate the data     
    $data['business_email_id'] 						= $_POST['business_email_id'];
    $data['business_email_plus_id'] 				= $_POST['business_email_plus_id'];
    $data['zimbra_standard_ids'] 					= $_POST['zimbra_standard_id'];
    $data['zimbra_professional_id'] 				= $_POST['zimbra_professional_id'];

    $data['business_email_cost_per_id'] 					= $_POST['business_email_cost_per_id'];
    $data['business_email_plus_cost_per_id'] 				= $_POST['business_email_plus_cost_per_id'];
    $data['zimbra_standard_cost_per_id'] 					= $_POST['zimbra_standard_cost_per_id'];
    $data['zimbra_professional_cost_per_id'] 				= $_POST['zimbra_professional_cost_per_id'];

    $data['business_email_cost_per_month'] 						= $_POST['business_email_cost_per_month'];
    $data['business_email_plus_cost_per_month'] 				= $_POST['business_email_plus_cost_per_month'];
    $data['zimbra_standard_cost_per_month'] 					= $_POST['zimbra_standard_cost_per_month'];
    $data['zimbra_professional_cost_per_month'] 				= $_POST['zimbra_professional_cost_per_month'];

    $data['zimb_totalcost'] 						= $_POST['zimb_totalcost'];
   	see_cost_breakup_form_validation($data);

	//Save the data and send emails	 
    complete_see_cost_breakup_form($data);
}	
//Server side validation
function  see_cost_breakup_form_validation($data = []){
	//print_r($data); die();
	global $form_errors;
	$form_errors = new WP_Error;
	//validating the input received as argument
	if (
		empty( $data['brkup_full_name'] )	||
		empty( $data['brkup_email'] )		||
		empty( $data['brkup_phone'] )		||
		empty( $data['brkup_num1'] )		||
		empty( $data['brkup_num2'] )		||
		empty( $data['brkup_captcha'] )

	){
		$form_errors->add('field', 'Some of required form fields are missing');
	}else{
		$x = $data['brkup_num1'];
		$y = $data['brkup_num2'];
		if($x + $y != $data['brkup_captcha']){
			$form_errors->add('field', 'Invalid Captcha');
		}
	}
}
function complete_see_cost_breakup_form($data = []){
	//print_r($data); die();
	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = "Request Submitted Successfully. Details of cost break up has been sent through your registered mail id";
	$error = "Some error occurred. Please try again";

	$emailData = $data;
	unset($data['brkup_num1']);
	unset($data['brkup_num2']);
	unset($data['brkup_captcha']);

	$insertData['brkup_full_name'] 	= $data['brkup_full_name'];
	$insertData['brkup_email'] 		= $data['brkup_email'];
	$insertData['brkup_phone'] 		= $data['brkup_phone'];
	$insertData['added_on']		  	= date('Y-m-d h:i:s');

    $query = $wpdb->insert( $wpdb->prefix.'cost_breakup_requests', $insertData);

   // print_r($query);exit;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }
    //print_r($emailData); die();
    
    $subject_zimbra 		= 'Zimbra Cost Breakup';
    $template_body_user 	= get_zimbra_cost_beakup_template(['type'=>'user']);
    $replaces_user = [
    					'{name}' => $emailData['brkup_full_name'], 

	                    '{business_email_id}'=>$emailData['business_email_id'],
	                    '{business_email_plus_id}'=>$emailData['business_email_plus_id'],
	                    '{zimbra_standard_id}'=>$emailData['zimbra_standard_ids'],
	                    '{zimbra_professional_id}'=>$emailData['zimbra_professional_id'],

	                    '{business_email_cost_per_id}'=>$emailData['business_email_cost_per_id'],
	                    '{business_email_plus_cost_per_id}'=>$emailData['business_email_plus_cost_per_id'],
	                    '{zimbra_standard_cost_per_id}'=>$emailData['zimbra_standard_cost_per_id'],
	                    '{zimbra_professional_cost_per_id}'=>$emailData['zimbra_professional_cost_per_id'],

	                    '{business_email_cost_per_month}'=>$emailData['business_email_cost_per_month'],
	                    '{business_email_plus_cost_per_month}'=>$emailData['business_email_plus_cost_per_month'],
	                    '{zimbra_standard_cost_per_month}'=>$emailData['zimbra_standard_cost_per_month'],
	                    '{zimbra_professional_cost_per_month}'=>$emailData['zimbra_professional_cost_per_month'],

	                    '{zimbra_total_cost}'=>$emailData['zimb_totalcost'],
    				];
    $to_user = $emailData['brkup_email'];
    see_brkup_send_email_commom($template_body_user,$replaces_user,$subject_zimbra,$to_user,'user');
    $replaces_admin = [
    					'{name}' => $emailData['brkup_full_name'], 
	                    '{email}' => $emailData['brkup_email'],
	                    '{phone}' => $emailData['brkup_phone']
    				];
    $template_body_admin 	= get_zimbra_cost_beakup_template(['type'=>'admin']);
    $to_admin = constant("ADMIN_TO");
    see_brkup_send_email_commom($template_body_admin,$replaces_admin,$subject_zimbra,$to_admin,'admin');

    echo json_encode(['status' => true, 'message' => $success]);exit;

}

function get_zimbra_cost_beakup_template($param=[]){
	//print_r($param); die();
	$html = '';
	$type = $param['type']; 
	//print_r($type); die();
	switch ($type) {
		case 'user':
			$html.= 
					'<table>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
				      </tr>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 20px;">
				            <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				               <!-- hello user -->
				               <tbody>
				                  <tr style="width: 610px; margin: 0px; padding: 0px;">
				                     <td style="width: 610px; margin: 0px; padding: 0px;">
				                        <table style="width: 610px; margin: 0px; padding: 0px;">
				                           <tbody>
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
				                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello {name},</p>
				                                 </td>
				                              </tr>
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
				                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">Thank you for your interest.</p>
				                                 </td>
				                              </tr>
				                              <!-- user details -->
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
				                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				                                       <tbody>
				                                          <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
				                                                <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
				                                             </td>
				                                          </tr>
				                                       </tbody>
				                                    </table>
				                                 </td>
				                              </tr>
				                              <!-- user details -->
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                
				                                 <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
				                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				                                       <tbody>
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px;">Item</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Cost (Per Year/ID)</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">IDs taken</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Total Cost</p>
				                                             </td>
				                                          </tr>
				                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Business Email:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_email_cost_per_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{business_email_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_email_cost_per_month}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Business Email Plus:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_email_plus_cost_per_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{business_email_plus_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_email_plus_cost_per_month}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Zimbra Standard:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_standard_cost_per_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{zimbra_standard_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_standard_cost_per_month}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Zimbra Professional:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_professional_cost_per_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{zimbra_professional_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_professional_cost_per_month}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 20px; width: 610px; margin: 0px; padding: 4px; height: 1px; background: #eeeeee;" colspan="4" align="right">Total Cost:&nbsp;<span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_total_cost} Annually</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">*GST as applicable</td>
				                                          </tr>
				                                       </tbody>
				                                    </table>
				                                 </td>
				                              </tr>
				                           </tbody>
				                        </table>
				                     </td>
				                  </tr>
				               </tbody>
				            </table>
				         </td>
				      </tr>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
				      </tr>
					</table>';
			break;
		
		case 'admin':
			$html.= 
					'<table>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 20px;">
					         <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					            <!-- hello user -->
					            <tbody>
					               <tr style="width: 610px; margin: 0px; padding: 0px;">
					                  <td style="width: 610px; margin: 0px; padding: 0px;">
					                     <table style="width: 610px; margin: 0px; padding: 0px;">
					                        <tbody>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello Admin,</p>
					                              </td>
					                           </tr>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">{name} has successfully sent a request to get the cost break up for the plan selected for Zimbra.</p>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                       <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
					                                             <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
					                                          </td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                    <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Full Name:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{name}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Email Address:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{email}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Contact Number:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{phone}</p>
					                                          </td>
					                                       </tr>
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                  </td>
					               </tr>
					            </tbody>
					         </table>
					      </td>
					   </tr>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
					   </tr>
					</table>' ;
			break;
	}

	//print_r($html); die();
	return $html;
}
/*Zimbr aform submission functionalities*/

/*----------------Office form submission functionalities------------------------------
--------------------------------------------------------------------------------------*/
function see_cost_breakup_office() {
//print_r(123); die();
	$data = [];
    // sanitize user form input	   
	$data['brkup_full_name'] 						= sanitize_text_field( $_POST['brkup_full_name'] );
	$data['brkup_email'] 							= sanitize_text_field( $_POST['brkup_email'] );
	$data['brkup_phone'] 							= $_POST['brkup_phone'];
	$data['brkup_num1'] 							= $_POST['brkup_num1'];
	$data['brkup_num2']								= $_POST['brkup_num2'];
	$data['brkup_captcha']							= $_POST['brkup_captcha'];
	$data['added_on']								= date('Y-m-d h:i:s');
    //Validate the data     
    $data['busi_ess_initial_cost'] 					= $_POST['busi_ess_initial_cost'];
    $data['busi_initial_cost'] 						= $_POST['busi_initial_cost'];
    $data['busi_prem_initial_cost'] 				= $_POST['busi_prem_initial_cost'];

    $data['selected_busi_no'] 						= $_POST['selected_busi_no']; 
    $data['selected_busi_prem_no'] 					= $_POST['selected_busi_prem_no'];
    $data['selected_busi_ess_no'] 					= $_POST['selected_busi_ess_no'];

    $data['busi_ess_final_cost'] 					= $_POST['busi_ess_final_cost'];
    $data['busi_final_cost'] 						= $_POST['busi_final_cost'];
    $data['busi_prem_final_cost'] 					= $_POST['busi_prem_final_cost'];

    $data['total_cost'] 							= $_POST['total_cost'];
    see_cost_breakup_form_validation_office($data);

	//Save the data and send emails	 
    complete_see_cost_breakup_form_office($data);
}	
//Server side validation
function  see_cost_breakup_form_validation_office($data = []){
	//print_r($data); die();
	global $form_errors;
	$form_errors = new WP_Error;
	//validating the input received as argument
	if (
		empty( $data['brkup_full_name'] )	||
		empty( $data['brkup_email'] )		||
		empty( $data['brkup_phone'] )		||
		empty( $data['brkup_num1'] )		||
		empty( $data['brkup_num2'] )		||
		empty( $data['brkup_captcha'] )

	){//print_r(1);die();
		$form_errors->add('field', 'Some of required form fields are missing');
	}else{
		$x = $data['brkup_num1'];
		$y = $data['brkup_num2'];
		if($x + $y != $data['brkup_captcha']){
			$form_errors->add('field', 'Invalid Captcha');
		}
	}
}
function complete_see_cost_breakup_form_office($data = []){
	//print_r($data); die();
	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = "Request Submitted Successfully. Details of cost break up has been sent through your registered mail id";
	$error = "Some error occurred. Please try again";

	

	unset($data['brkup_num1']);
	unset($data['brkup_num2']);
	unset($data['brkup_captcha']);

	$emailData = $data;

	$insertData['brkup_full_name'] 	= $data['brkup_full_name'];
	$insertData['brkup_email'] 		= $data['brkup_email'];
	$insertData['brkup_phone'] 		= $data['brkup_phone'];
	$insertData['added_on']		  	= date('Y-m-d h:i:s');

    $query = $wpdb->insert( $wpdb->prefix.'cost_breakup_requests', $insertData);

   // print_r($query);exit;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }
   //print_r($emailData); die();
     $replaces_1 = [ 
                    '{name}' => $emailData['brkup_full_name'],
                    '{selected_busi_no}' => $emailData['selected_busi_no'],
                    '{selected_busi_prem_no}' => $emailData['selected_busi_prem_no'],
                    '{selected_busi_ess_no}' => $emailData['selected_busi_ess_no'],
                    '{busi_initial_cost}' => $emailData['busi_initial_cost'],
                    '{busi_ess_initial_cost}' => $emailData['busi_ess_initial_cost'],
                    '{busi_prem_initial_cost}' => $emailData['busi_prem_initial_cost'],
                    '{busi_ess_final_cost}' => $emailData['busi_ess_final_cost'],
                    '{busi_final_cost}' => $emailData['busi_final_cost'],
                    '{busi_prem_final_cost}' => $emailData['busi_prem_final_cost'],
                    '{total_cost}' => $emailData['total_cost']
                ]; // For user
    $to_user = $emailData['brkup_email'];

    $replaces_2 = [ 
                    '{name}' => $emailData['brkup_full_name'], 
                    '{email}' => $emailData['brkup_email'],
                    '{phone}' => $emailData['brkup_phone']
                ]; //For admin
    $to_admin = constant("ADMIN_TO");

    //Template
    $template_body_admin 	= get_cost_beakup_template_office(['type'=>'admin']);
    $template_body_user 	= get_cost_beakup_template_office(['type'=>'user']);

    //Template
    $subject_user = 'Office 365 cost breakup';
    $subject_admin = 'Office 365 cost breakup request';
    see_brkup_send_email_commom($template_body_user, $replaces_1,$subject_user,$to_user,'user');
    see_brkup_send_email_commom($template_body_admin, $replaces_2,$subject_admin,$to_admin,'admin');
     

    echo json_encode(['status' => true, 'message' => $success]);exit;

}

//Admin Email Template
function get_cost_beakup_template_office($param=[]){
	//print_r($param); die();
	$html = '';
	$type = $param['type']; 
	//print_r($type); die();
	switch ($type) {
		case 'user':
			$html.= 
					'<table>
					      <tr style="width: 650px; margin: 0px; padding: 0px;">
					         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
					      </tr>
					      <tr style="width: 650px; margin: 0px; padding: 0px;">
					         <td style="width: 610px; margin: 0px; padding: 0px 20px;">
					            <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					               <!-- hello user -->
					               <tbody>
					                  <tr style="width: 610px; margin: 0px; padding: 0px;">
					                     <td style="width: 610px; margin: 0px; padding: 0px;">
					                        <table style="width: 610px; margin: 0px; padding: 0px;">
					                           <tbody>
					                              <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                 <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
					                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello {name},</p>
					                                 </td>
					                              </tr>
					                              <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                 <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
					                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">Thank you for your interest.</p>
					                                 </td>
					                              </tr>
					                              <!-- user details -->
					                              <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                 <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
					                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                       <tbody>
					                                          <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
					                                             <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
					                                                <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
					                                             </td>
					                                          </tr>
					                                       </tbody>
					                                    </table>
					                                 </td>
					                              </tr>
					                              <!-- user details -->
					                              <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                
					                                 <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
					                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                       <tbody>
					                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px;">Item</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Cost (Per Month/ID)</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">IDs taken</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Total Cost</p>
					                                             </td>
					                                          </tr>
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Business Basic:</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{busi_initial_cost}</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_no}</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{busi_final_cost}</p>
					                                             </td>
					                                          </tr>
					                                
					                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
					                                          </tr>

					                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Business Standard:</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{busi_prem_initial_cost}</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_prem_no}</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{busi_prem_final_cost}</p>
					                                             </td>
					                                          </tr>
					                                
					                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
					                                          </tr>

					                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Business Premium:</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{busi_ess_initial_cost}</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_ess_no}</p>
					                                             </td>
					                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{busi_ess_final_cost}</p>
					                                             </td>
					                                          </tr>
					                                
					                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
					                                          </tr>

					                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
					                                             <td style="font-size: 20px; width: 610px; margin: 0px; padding: 4px; height: 1px; background: #eeeeee;" colspan="4" align="right">Total Cost:&nbsp;<span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{total_cost}</td>
					                                          </tr>
					                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
					                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">*GST as applicable</td>
					                                          </tr>
					                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
					                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">Annual Commitment Required</td>
					                                          </tr>
					                                       </tbody>
					                                    </table>
					                                 </td>
					                              </tr>
					                           </tbody>
					                        </table>
					                     </td>
					                  </tr>
					               </tbody>
					            </table>
					         </td>
					      </tr>
					      <tr style="width: 650px; margin: 0px; padding: 0px;">
					         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
					      </tr>
					</table>';
			break;
		
		case 'admin':
			$html.= 
					'<table>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 20px;">
					         <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					            <!-- hello user -->
					            <tbody>
					               <tr style="width: 610px; margin: 0px; padding: 0px;">
					                  <td style="width: 610px; margin: 0px; padding: 0px;">
					                     <table style="width: 610px; margin: 0px; padding: 0px;">
					                        <tbody>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello Admin,</p>
					                              </td>
					                           </tr>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">{name} has successfully sent a request to get the cost break up for the plan selected for Office 365.</p>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                       <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
					                                             <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
					                                          </td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                    <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Full Name:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{name}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Email Address:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{email}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Contact Number:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{phone}</p>
					                                          </td>
					                                       </tr>
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                  </td>
					               </tr>
					            </tbody>
					         </table>
					      </td>
					   </tr>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
					   </tr>
					</table>' ;
			break;
	}

	//print_r($html); die();
	return $html;
}
/*----------------Office form submission functionalities------------------------------
--------------------------------------------------------------------------------------*/

/*----------------Gsuite form submission functionalities------------------------------
--------------------------------------------------------------------------------------*/
function see_cost_breakup_gsuite() {
//print_r(123); die();
	$data = [];
    // sanitize user form input	   
	$data['brkup_full_name'] 						= sanitize_text_field( $_POST['brkup_full_name'] );
	$data['brkup_email'] 							= sanitize_text_field( $_POST['brkup_email'] );
	$data['brkup_phone'] 							= $_POST['brkup_phone'];
	$data['brkup_num1'] 							= $_POST['brkup_num1'];
	$data['brkup_num2']								= $_POST['brkup_num2'];
	$data['brkup_captcha']							= $_POST['brkup_captcha'];
	$data['added_on']								= date('Y-m-d h:i:s');

    //Validate the data     
    $data['basic_per_id_cost'] 						= $_POST['basic_per_id_cost'];
    $data['business_per_id_cost'] 					= $_POST['business_per_id_cost'];
    $data['enterprise_per_id_cost'] 				= $_POST['enterprise_per_id_cost'];

    $data['selected_basic_no'] 						= $_POST['selected_basic_no']; 
    $data['selected_business_no'] 					= $_POST['selected_business_no'];
    $data['selected_enterprise_no'] 				= $_POST['selected_enterprise_no'];

    $data['basic_final_cost'] 						= $_POST['basic_final_cost']; 
    $data['business_final_cost'] 					= $_POST['business_final_cost'];
    $data['enterprice_final_cost'] 					= $_POST['enterprice_final_cost']; 

    $data['gsuite_totalCost'] 						= $_POST['gsuite_totalCost'];
   // see_cost_breakup_form_validation_gsuite($data);

	//Save the data and send emails	 
    complete_see_cost_breakup_form_gsuite($data);
}	
//Server side validation
function  see_cost_breakup_form_validation_gsuite($data = []){
	//print_r($data['brkup_captcha']); die();
	global $form_errors;
	$form_errors = new WP_Error;
	//validating the input received as argument
	if (
		empty( $data['brkup_full_name'] )	||
		empty( $data['brkup_email'] )		||
		empty( $data['brkup_phone'] )		||
		empty( $data['brkup_num1'] )		||
		empty( $data['brkup_num2'] )		||
		empty( $data['brkup_captcha'] )

	){
		$form_errors->add('field', 'Some of required form fields are missing');
	}else{
		$x = $data['brkup_num1'];
		$y = $data['brkup_num2'];
		if($x + $y != $data['brkup_captcha_gsuite']){
			$form_errors->add('field', 'Invalid Captcha');
		}
	}
}
function complete_see_cost_breakup_form_gsuite($data = []){
	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = "Request Submitted Successfully. Details of cost break up has been sent through your registered mail id";
	$error = "Some error occurred. Please try again";

	
	unset($data['brkup_num1']);
	unset($data['brkup_num2']);
	unset($data['brkup_captcha']);

	$insertData['brkup_full_name'] 	= $data['brkup_full_name'];
	$insertData['brkup_email'] 		= $data['brkup_email'];
	$insertData['brkup_phone'] 		= $data['brkup_phone'];
	$insertData['added_on']		  	= date('Y-m-d h:i:s');
	
	$emailData = $data;

    $query = $wpdb->insert( $wpdb->prefix.'cost_breakup_requests', $insertData);

    //print_r($query);die;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }
    //print_r($emailData); die();
     $replaces_1 = [ 
                    '{name}' => $emailData['brkup_full_name'],
                    '{selected_basic_no}' => $emailData['selected_basic_no'],
                    '{selected_enterprise_no}' => $emailData['selected_enterprise_no'],
                    '{selected_business_no}' => $emailData['selected_business_no'],
                    '{basic_per_id_cost}' => $emailData['basic_per_id_cost'],
                    '{business_per_id_cost}' => $emailData['business_per_id_cost'],
                    '{enterprise_per_id_cost}' => $emailData['enterprise_per_id_cost'],
                    '{basic_final_cost}' => $emailData['basic_final_cost'],
                    '{business_final_cost}' => $emailData['business_final_cost'],
                    '{enterprice_final_cost}' => $emailData['enterprice_final_cost'], 
                    '{gsuite_totalCost}' => $emailData['gsuite_totalCost']
                ]; // For user
     $to_user = $emailData['brkup_email'];

     $replaces_2 = [ 
                    '{name}' => $emailData['brkup_full_name'], 
                    '{email}' => $emailData['brkup_email'],
                    '{phone}' => $emailData['brkup_phone']
                ]; //For admin
    $to_admin = constant("ADMIN_TO");

    //Template
    $template_body_admin 	= get_cost_beakup_template_gsuite(['type'=>'admin']);
    $template_body_user 	= get_cost_beakup_template_gsuite(['type'=>'user']);

    //Template
    $subject_user = 'G-Suite cost breakup';
    $subject_admin = 'G-Suite cost breakup request';

    see_brkup_send_email_commom($template_body_user, $replaces_1,$subject_user,$to_user,'user');
    see_brkup_send_email_commom($template_body_admin, $replaces_2,$subject_admin,$to_admin,'admin');
     

    echo json_encode(['status' => true, 'message' => $success]);exit;

}
//Admin Email Template
function get_cost_beakup_template_gsuite($param=[]){
	//print_r($param); die();
	$html = '';
	$type = $param['type']; 
	//print_r($type); die();
	switch ($type) {
		case 'user':
			$html.= 
					'<table>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
				      </tr>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 20px;">
				            <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				               <!-- hello user -->
				               <tbody>
				                  <tr style="width: 610px; margin: 0px; padding: 0px;">
				                     <td style="width: 610px; margin: 0px; padding: 0px;">
				                        <table style="width: 610px; margin: 0px; padding: 0px;">
				                           <tbody>
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
				                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello {name},</p>
				                                 </td>
				                              </tr>
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
				                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">Thank you for your interest.</p>
				                                 </td>
				                              </tr>
				                              <!-- user details -->
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
				                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				                                       <tbody>
				                                          <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
				                                                <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
				                                             </td>
				                                          </tr>
				                                       </tbody>
				                                    </table>
				                                 </td>
				                              </tr>
				                              <!-- user details -->
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                
				                                 <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
				                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				                                       <tbody>
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px;">Item</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Cost (Per Month/ID)</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">IDs taken</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Total Cost</p>
				                                             </td>
				                                          </tr>
				                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">G-Suite Basic:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{basic_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_basic_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{basic_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">G-Suite Business:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_business_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">G-Suite Enterprice:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{enterprise_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_enterprise_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{enterprice_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 20px; width: 610px; margin: 0px; padding: 4px; height: 1px; background: #eeeeee;" colspan="4" align="right">Total Cost:&nbsp;<span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{gsuite_totalCost}</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">Annual Commitment Required</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">*GST as applicable</td>
				                                          </tr>
				                                       </tbody>
				                                    </table>
				                                 </td>
				                              </tr>
				                           </tbody>
				                        </table>
				                     </td>
				                  </tr>
				               </tbody>
				            </table>
				         </td>
				      </tr>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
				      </tr>
				</table>';
			break;
		
		case 'admin':
			$html.= 
					'<table>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 20px;">
					         <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					            <!-- hello user -->
					            <tbody>
					               <tr style="width: 610px; margin: 0px; padding: 0px;">
					                  <td style="width: 610px; margin: 0px; padding: 0px;">
					                     <table style="width: 610px; margin: 0px; padding: 0px;">
					                        <tbody>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello Admin,</p>
					                              </td>
					                           </tr>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">{name} has successfully sent a request to get the cost break up for the plan selected for G-Suite.</p>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                       <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
					                                             <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
					                                          </td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                    <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Full Name:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{name}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Email Address:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{email}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Contact Number:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{phone}</p>
					                                          </td>
					                                       </tr>
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                  </td>
					               </tr>
					            </tbody>
					         </table>
					      </td>
					   </tr>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
					   </tr>
					</table>' ;
			break;
	}

	//print_r($html); die();
	return $html;
}
/*----------------Office form submission functionalities------------------------------
--------------------------------------------------------------------------------------*/

/*-------------- Functions related to zimbra + office----- --------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

function see_cost_breakup_office_zimbra(){
	//print_r(123); die();
	$data = [];
    // sanitize user form input	   
	$data['brkup_full_name'] 						= sanitize_text_field( $_POST['brkup_full_name'] );
	$data['brkup_email'] 							= sanitize_text_field( $_POST['brkup_email'] );
	$data['brkup_phone'] 							= $_POST['brkup_phone'];
	$data['brkup_num1'] 							= $_POST['brkup_num1'];
	$data['brkup_num2']								= $_POST['brkup_num2'];
	$data['brkup_captcha']							= $_POST['brkup_captcha'];
	$data['added_on']								= date('Y-m-d h:i:s');
    //Validate the data     
    $data['if_no_hybrid'] 							= $_POST['if_no_hybrid'];
    $data['saved'] 									= $_POST['saved'];

    $data['selected_busi_ess_no'] 					= $_POST['selected_busi_ess_no'];
    $data['selected_busi_no'] 				        = $_POST['selected_busi_no'];
    $data['selected_busi_prem_no'] 					= $_POST['selected_busi_prem_no'];
    $data['selected_hybrid_zimbra_no'] 				= $_POST['selected_hybrid_zimbra_no'];

    $data['basic_per_id_cost'] 				        = $_POST['basic_per_id_cost'];
    $data['business_per_id_cost'] 					= $_POST['business_per_id_cost'];
    $data['enterprise_per_id_cost'] 				= $_POST['enterprise_per_id_cost'];
    $data['zimbra_email_cost_per_id'] 				= $_POST['zimbra_email_cost_per_id'];

    $data['basic_final_cost'] 				        = $_POST['basic_final_cost'];
    $data['business_final_cost'] 					= $_POST['business_final_cost'];   
    $data['enterprice_final_cost'] 					= $_POST['enterprice_final_cost'];
    $data['zimbra_final_cost'] 						= $_POST['zimbra_final_cost'];

    $data['total_cost'] 				            = $_POST['total_cost'];
    $data['total_no_of_id_taken'] 					= $_POST['total_no_of_id_taken'];
   
    see_cost_breakup_form_validation_office_zimbra($data);

	//Save the data and send emails	 
    complete_see_cost_breakup_form_office_zimbra($data);
}
function  see_cost_breakup_form_validation_office_zimbra($data = []){
	//print_r($data); die();
	global $form_errors;
	$form_errors = new WP_Error;
	//validating the input received as argument
	if (
		 empty( $data['brkup_full_name'] )	||
		 empty( $data['brkup_email'] )		||
		empty( $data['brkup_phone'] )		||
		empty( $data['brkup_num1'] )		||
		empty( $data['brkup_num2'] )		||
		empty( $data['brkup_captcha'] )

	){//print_r(1);die();
		$form_errors->add('field', 'Some of required form fields are missing');
	}else{
		//print_r(1);die();
		$x = $data['brkup_num1'];
		$y = $data['brkup_num2'];
		if($x + $y != $data['brkup_captcha']){
			$form_errors->add('field', 'Invalid Captcha');
		}
	}
}

function complete_see_cost_breakup_form_office_zimbra($data = []){
	//print_r($data); die();
	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = "Request Submitted Successfully. Details of cost break up has been sent through your registered mail id";
	$error = "Some error occurred. Please try again";

	
	unset($data['brkup_num1']);
	unset($data['brkup_num2']);
	unset($data['brkup_captcha']);

	$emailData = $data;

	$insertData['brkup_full_name'] 	= $data['brkup_full_name'];
	$insertData['brkup_email'] 		= $data['brkup_email'];
	$insertData['brkup_phone'] 		= $data['brkup_phone'];
	$insertData['added_on']		  	= date('Y-m-d h:i:s');

    $query = $wpdb->insert( $wpdb->prefix.'cost_breakup_requests', $insertData);

   // print_r($query);exit;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }
    //print_r($emailData); die();

     see_brkup_send_user_email_office_zimbra($emailData);
     see_brkup_send_admin_email_office_zimbra($emailData);

    echo json_encode(['status' => true, 'message' => $success]);exit;
}
function see_brkup_send_user_email_office_zimbra($data = []){
	//print_r($data); die();
	$mail 			= new SendEmail;	
	$from 			= 'projectteam@sds.com';
	$to 			= $data['brkup_email'];
	$subject 		= 'Office 365 Plus Zimbra Cost Breakup';
	$template_body 	= get_zimbra_cost_breakup_template_office_zimbra(['type'=>'user']);
	
	$full_name 								= $data['brkup_full_name'];
	$phone 									= $data['brkup_phone'];
	$brkup_email 							= $data['brkup_email'];
	$if_no_hybrid 						    = $data['if_no_hybrid'];
	$total_cost 						    = $data['total_cost']; 
    $saved									= $data['saved'];
    $total_no_of_id_taken					= $data['total_no_of_id_taken'];

    $selected_busi_ess_no					= $data['selected_busi_ess_no'];
    $selected_busi_no					    = $data['selected_busi_no'];
    $selected_busi_prem_no					= $data['selected_busi_prem_no'];
    $selected_hybrid_zimbra_no				= $data['selected_hybrid_zimbra_no'];

    $basic_per_id_cost						= $data['basic_per_id_cost'];
    $business_per_id_cost					= $data['business_per_id_cost'];
    $enterprise_per_id_cost					= $data['enterprise_per_id_cost'];
    $zimbra_email_cost_per_id				= $data['zimbra_email_cost_per_id'];

    $basic_final_cost						= $data['basic_final_cost'];
    $business_final_cost					= $data['business_final_cost'];
    $enterprice_final_cost					= $data['enterprice_final_cost'];
    $zimbra_final_cost						= $data['zimbra_final_cost'];

    $substruction                           = $if_no_hybrid.' - '.$total_cost;
    $subs                                   = $if_no_hybrid - $total_cost; 
    $savepercentage                         = (($subs / $if_no_hybrid) * 100);
    $save_percentage 						= number_format($savepercentage,2);
    

	$template_body 	= str_replace("{name}",$full_name,$template_body);
	$template_body 	= str_replace("{email}",$brkup_email,$template_body);
	$template_body 	= str_replace("{phone}",$phone,$template_body);

	$template_body 	= str_replace("{total_cost}",$total_cost,$template_body);
	$template_body 	= str_replace("{Value}",$saved,$template_body);
	$template_body 	= str_replace("{total_no_of_id_taken}",$total_no_of_id_taken,$template_body);
	$template_body 	= str_replace("{substruction}",$substruction,$template_body);
	$template_body 	= str_replace("{save_percentage}",$save_percentage,$template_body);

	$template_body 	= str_replace("{selected_busi_ess_no}",$selected_busi_ess_no,$template_body);
	$template_body 	= str_replace("{selected_busi_no}",$selected_busi_no,$template_body);
	$template_body 	= str_replace("{selected_busi_prem_no}",$selected_busi_prem_no,$template_body);
	$template_body 	= str_replace("{selected_hybrid_zimbra_no}",$selected_hybrid_zimbra_no,$template_body);
	

	$template_body 	= str_replace("{basic_per_id_cost}",$basic_per_id_cost,$template_body);
	$template_body 	= str_replace("{business_per_id_cost}",$business_per_id_cost,$template_body);
	$template_body 	= str_replace("{enterprise_per_id_cost}",$enterprise_per_id_cost,$template_body);
	$template_body 	= str_replace("{zimbra_email_cost_per_id}",$zimbra_email_cost_per_id,$template_body);

	$template_body 	= str_replace("{basic_final_cost}",$basic_final_cost,$template_body);
	$template_body 	= str_replace("{business_final_cost}",$business_final_cost,$template_body);
	$template_body 	= str_replace("{enterprice_final_cost}",$enterprice_final_cost,$template_body);
	$template_body 	= str_replace("{zimbra_final_cost}",$zimbra_final_cost,$template_body);


	$body 			= $mail->template($template_body);
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from
	];

	wp_mail( $to, $subject, $body, $headers );
}
function see_brkup_send_admin_email_office_zimbra($data = []){
	//print_r(123); exit;
	$mail 			= new SendEmail;	
	$from 			= $data['brkup_email'];
	$to 			= constant("ADMIN_TO");
	$bcc 			= constant("BCC");
	$cc 			= constant("CC");
	$subject 		= 'Office 365 Plus Zimbra Cost Breakup';
	$template_body 	= get_zimbra_cost_breakup_template_office_zimbra(['type'=>'admin']);

	$full_name 								= $data['brkup_full_name'];
	$phone 									= $data['brkup_phone'];
	$brkup_email 							= $data['brkup_email'];
	$business_email_id 						= $data['business_email_id'];
    $business_email_plus_id					= $data['business_email_plus_id'];
    $zimbra_standard_id						= $data['zimbra_standard_ids'];
    $zimbra_professional_id					= $data['zimbra_professional_id'];

	$template_body 	= str_replace("{name}",$full_name,$template_body);
	$template_body 	= str_replace("{email}",$brkup_email,$template_body);
	$template_body 	= str_replace("{phone}",$phone,$template_body);
	$template_body 	= str_replace("{business_email_id}",$business_email_id,$template_body);
	$template_body 	= str_replace("{business_email_plus_id}",$business_email_plus_id,$template_body);
	$template_body 	= str_replace("{zimbra_standard_id}",$zimbra_standard_id,$template_body);
	$template_body 	= str_replace("{zimbra_professional_id}",$zimbra_professional_id,$template_body);

	$body 			= $mail->template($template_body);

  	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from, 
    	'Cc: '.$cc,
    	'Bcc:'.$bcc, 
    	'Reply-To: '.$data["brkup_full_name"]." ".$data["last_name"].' <'.$data["email"].'>'
	];
	wp_mail( $to, $subject, $body, $headers );
}
function get_zimbra_cost_breakup_template_office_zimbra($param=[]){
	//print_r($param); die();
	$html = '';
	$type = $param['type']; 
	//print_r($type); die();
	switch ($type) {
		case 'user':
			$html.= 
					'<table>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
				      </tr>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 20px;">
				            <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				               <!-- hello user -->
				               <tbody>
				                  <tr style="width: 610px; margin: 0px; padding: 0px;">
				                     <td style="width: 610px; margin: 0px; padding: 0px;">
				                        <table style="width: 610px; margin: 0px; padding: 0px;">
				                           <tbody>
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
				                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello {name},</p>
				                                 </td>
				                              </tr>
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
				                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">Thank you for your interest.</p>
				                                 </td>
				                              </tr>
				                              <!-- user details -->
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
				                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				                                       <tbody>
				                                          <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
				                                                <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
				                                             </td>
				                                          </tr>
				                                       </tbody>
				                                    </table>
				                                 </td>
				                              </tr>
				                              <!-- user details -->
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                
				                                 <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
				                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				                                       <tbody>
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px;">Item</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Cost (Per month)</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">IDs taken</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Total Cost</p>
				                                             </td>
				                                          </tr>
				                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Total Number of IDs Taken:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>N/A</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{total_no_of_id_taken}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>N/A</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Zimbra:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_email_cost_per_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_hybrid_zimbra_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Business Basic:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{basic_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{basic_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Business Standard:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_prem_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Business Premium:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{enterprise_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_ess_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{enterprice_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 20px; width: 610px; margin: 0px; padding: 4px; height: 1px; background: #eeeeee;" colspan="4" align="right">Total Cost:&nbsp;<span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{total_cost} (Monthly)</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#676767; background: #eeeeee;" colspan="4" align="right">Saved : {substruction}</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">= {Value} ({save_percentage}%)</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">*GST as applicable</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">Annual Commitment Required</td>
				                                          </tr>
				                                       </tbody>
				                                    </table>
				                                 </td>
				                              </tr>
				                           </tbody>
				                        </table>
				                     </td>
				                  </tr>
				               </tbody>
				            </table>
				         </td>
				      </tr>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
				      </tr>
				</table>';
			break;
		
		case 'admin':
			$html.= 
					'<table>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 20px;">
					         <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					            <!-- hello user -->
					            <tbody>
					               <tr style="width: 610px; margin: 0px; padding: 0px;">
					                  <td style="width: 610px; margin: 0px; padding: 0px;">
					                     <table style="width: 610px; margin: 0px; padding: 0px;">
					                        <tbody>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello Admin,</p>
					                              </td>
					                           </tr>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">{name} has successfully sent a request to get the cost break up for the plan selected for Hybrid Email Office + Zimbra.</p>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                       <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
					                                             <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
					                                          </td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                    <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Full Name:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{name}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Email Address:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{email}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Contact Number:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{phone}</p>
					                                          </td>
					                                       </tr>
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                  </td>
					               </tr>
					            </tbody>
					         </table>
					      </td>
					   </tr>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
					   </tr>
					</table>' ;
			break;
	}

	//print_r($html); die();
	return $html;
}
/*----------------------END---------------------------------------------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/


/*-------------------Functions related to zimbra + G-suite--------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

function see_cost_breakup_gsuite_zimbra(){
	//print_r(123); die();
	$data = [];
    // sanitize user form input	   
	$data['brkup_full_name'] 						= sanitize_text_field( $_POST['brkup_full_name'] );
	$data['brkup_email'] 							= sanitize_text_field( $_POST['brkup_email'] );
	$data['brkup_phone'] 							= $_POST['brkup_phone'];
	$data['brkup_num1'] 							= $_POST['brkup_num1'];
	$data['brkup_num2']								= $_POST['brkup_num2'];
	$data['brkup_captcha']							= $_POST['brkup_captcha_gsuite'];
	$data['added_on']								= date('Y-m-d h:i:s');

    //Validate the data     
    $data['if_no_hybrid'] 							= $_POST['if_no_hybrid'];
    $data['saved'] 									= $_POST['saved'];

    $data['total_no_of_id_taken'] 					= $_POST['total_no_of_id_taken'];

    $data['selected_busi_ess_no'] 					= $_POST['selected_enterprise_no'];
    $data['selected_busi_no'] 				        = $_POST['selected_basic_no'];
    $data['selected_busi_prem_no'] 					= $_POST['selected_business_no'];
    $data['selected_hybrid_zimbra_no'] 				= $_POST['selected_hybrid_zimbra_no'];

    $data['basic_per_id_cost'] 						= $_POST['basic_per_id_cost'];
    $data['business_per_id_cost'] 					= $_POST['business_per_id_cost'];
    $data['enterprise_per_id_cost'] 				= $_POST['enterprise_per_id_cost'];
    $data['zimbra_email_cost_per_id'] 				= $_POST['zimbra_email_cost_per_id'];

    $data['basic_final_cost'] 						= $_POST['basic_final_cost'];
    $data['business_final_cost'] 					= $_POST['business_final_cost'];
    $data['enterprice_final_cost'] 					= $_POST['enterprice_final_cost'];
    $data['zimbra_final_cost'] 						= $_POST['zimbra_final_cost'];

    $data['total_cost'] 				            = $_POST['total_cost'];
   
    see_cost_breakup_form_validation_gsuite_zimbra($data);

	//Save the data and send emails	 
    complete_see_cost_breakup_form_gsuite_zimbra($data);
}
function  see_cost_breakup_form_validation_gsuite_zimbra($data = []){
	//print_r($data); die();
	global $form_errors;
	$form_errors = new WP_Error;
	//validating the input received as argument
	if (
		 empty( $data['brkup_full_name'] )	||
		 empty( $data['brkup_email'] )		||
		empty( $data['brkup_phone'] )		||
		empty( $data['brkup_num1'] )		||
		empty( $data['brkup_num2'] )		||
		empty( $data['brkup_captcha'] )

	){//print_r(1);die();
		$form_errors->add('field', 'Some of required form fields are missing');
	}else{
		//print_r(1);die();
		$x = $data['brkup_num1'];
		$y = $data['brkup_num2'];
		if($x + $y != $data['brkup_captcha']){
			$form_errors->add('field', 'Invalid Captcha');
		}
	}
}

function complete_see_cost_breakup_form_gsuite_zimbra($data = []){
	//print_r($data); die();
	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = "Request Submitted Successfully. Details of cost break up has been sent through your registered mail id";
	$error = "Some error occurred. Please try again";

	
	unset($data['brkup_num1']);
	unset($data['brkup_num2']);
	unset($data['brkup_captcha']);

	$emailData = $data;

	$insertData['brkup_full_name'] 	= $data['brkup_full_name'];
	$insertData['brkup_email'] 		= $data['brkup_email'];
	$insertData['brkup_phone'] 		= $data['brkup_phone'];
	$insertData['added_on']		  	= date('Y-m-d h:i:s');

    $query = $wpdb->insert( $wpdb->prefix.'cost_breakup_requests', $insertData);

   // print_r($query);exit;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }
    //print_r($emailData); die();

     see_brkup_send_user_email_gsuite_zimbra($emailData);
     see_brkup_send_admin_email_gsuite_zimbra($emailData);

    echo json_encode(['status' => true, 'message' => $success]);exit;
}
function see_brkup_send_user_email_gsuite_zimbra($data = []){
	//print_r($data); die();
	$mail 			= new SendEmail;	
	$from 			= 'projectteam@sds.com';
	$to 			= $data['brkup_email'];
	$subject 		= 'G-Suite Plus Zimbra Cost Breakup';
	$template_body 	= get_zimbra_cost_breakup_template_gsuite_zimbra(['type'=>'user']);
	
	$full_name 								= $data['brkup_full_name'];
	$phone 									= $data['brkup_phone'];
	$brkup_email 							= $data['brkup_email'];
	$if_no_hybrid 						    = $data['if_no_hybrid'];
	$total_cost 						    = $data['total_cost'];
    $saved									= $data['saved'];

    $total_no_of_id_taken					=$data['total_no_of_id_taken'];

    $selected_busi_ess_no					= $data['selected_busi_ess_no'];
    $selected_busi_no					    = $data['selected_busi_no'];
    $selected_busi_prem_no					= $data['selected_busi_prem_no'];
    $selected_hybrid_zimbra_no				= $data['selected_hybrid_zimbra_no'];

    $basic_per_id_cost						= $data['basic_per_id_cost'];
    $business_per_id_cost					= $data['business_per_id_cost'];
    $enterprise_per_id_cost					= $data['enterprise_per_id_cost'];
    $zimbra_email_cost_per_id				= $data['zimbra_email_cost_per_id'];

    $basic_final_cost						= $data['basic_final_cost'];
    $business_final_cost					= $data['business_final_cost'];
    $enterprice_final_cost					= $data['enterprice_final_cost'];
    $zimbra_final_cost						= $data['zimbra_final_cost'];

    $substruction                           = $if_no_hybrid.' - '.$total_cost;
    $subs                                   = $if_no_hybrid - $total_cost;
    $savepercentage                         = (($subs / $if_no_hybrid) * 100);
    $save_percentage 						= number_format($savepercentage,2);

	$template_body 	= str_replace("{name}",$full_name,$template_body);
	$template_body 	= str_replace("{email}",$brkup_email,$template_body);
	$template_body 	= str_replace("{phone}",$phone,$template_body);
	$template_body 	= str_replace("{business_email_id}",$business_email_id,$template_body);
	$template_body 	= str_replace("{Value}",$saved,$template_body);
	$template_body 	= str_replace("{save_percentage}",$save_percentage,$template_body);
	$template_body 	= str_replace("{substruction}",$substruction,$template_body);
	$template_body 	= str_replace("{total_cost}",$total_cost,$template_body);

	$template_body 	= str_replace("{total_no_of_id_taken}",$total_no_of_id_taken,$template_body);

	$template_body 	= str_replace("{selected_busi_ess_no}",$selected_busi_ess_no,$template_body);
	$template_body 	= str_replace("{selected_busi_no}",$selected_busi_no,$template_body);
	$template_body 	= str_replace("{selected_busi_prem_no}",$selected_busi_prem_no,$template_body);
	$template_body 	= str_replace("{selected_hybrid_zimbra_no}",$selected_hybrid_zimbra_no,$template_body);

	$template_body 	= str_replace("{basic_per_id_cost}",$basic_per_id_cost,$template_body);
	$template_body 	= str_replace("{business_per_id_cost}",$business_per_id_cost,$template_body);
	$template_body 	= str_replace("{enterprise_per_id_cost}",$enterprise_per_id_cost,$template_body);
	$template_body 	= str_replace("{zimbra_email_cost_per_id}",$zimbra_email_cost_per_id,$template_body);

	$template_body 	= str_replace("{basic_final_cost}",$basic_final_cost,$template_body);
	$template_body 	= str_replace("{business_final_cost}",$business_final_cost,$template_body);
	$template_body 	= str_replace("{enterprice_final_cost}",$enterprice_final_cost,$template_body);
	$template_body 	= str_replace("{zimbra_final_cost}",$zimbra_final_cost,$template_body);

	$body 			= $mail->template($template_body);
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from
	];

	wp_mail( $to, $subject, $body, $headers );
}
function see_brkup_send_admin_email_gsuite_zimbra($data = []){
	//print_r(123); exit;
	$mail 			= new SendEmail;	
	$from 			= $data['brkup_full_name'];
	$to 			= constant("ADMIN_TO");
	$bcc 			= constant("BCC");
	$cc 			= constant("CC");
	$subject 		= 'G-Suite Plus Zimbra Cost Breakup';
	$template_body 	= get_zimbra_cost_breakup_template_gsuite_zimbra(['type'=>'admin']);

	$full_name 								= $data['brkup_full_name'];
	$phone 									= $data['brkup_phone'];
	$brkup_email 							= $data['brkup_email'];
	$business_email_id 						= $data['business_email_id'];
    $business_email_plus_id					= $data['business_email_plus_id'];
    $zimbra_standard_id						= $data['zimbra_standard_ids'];
    $zimbra_professional_id					= $data['zimbra_professional_id'];

	$template_body 	= str_replace("{name}",$full_name,$template_body);
	$template_body 	= str_replace("{email}",$brkup_email,$template_body);
	$template_body 	= str_replace("{phone}",$phone,$template_body);
	$template_body 	= str_replace("{business_email_id}",$business_email_id,$template_body);
	$template_body 	= str_replace("{business_email_plus_id}",$business_email_plus_id,$template_body);
	$template_body 	= str_replace("{zimbra_standard_id}",$zimbra_standard_id,$template_body);
	$template_body 	= str_replace("{zimbra_professional_id}",$zimbra_professional_id,$template_body);

	$body 			= $mail->template($template_body);

  	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from, 
    	'Cc: '.$cc, 
    	'Bcc:'.$bcc,
    	'Reply-To: '.$data["brkup_full_name"]." ".$data["last_name"].' <'.$data["email"].'>'
	];
	wp_mail( $to, $subject, $body, $headers );
}
function get_zimbra_cost_breakup_template_gsuite_zimbra($param=[]){
	//print_r($param); die();
	$html = '';
	$type = $param['type']; 
	//print_r($type); die();
	switch ($type) {
		case 'user':
			$html.= 
					'<table>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
				      </tr>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 20px;">
				            <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				               <!-- hello user -->
				               <tbody>
				                  <tr style="width: 610px; margin: 0px; padding: 0px;">
				                     <td style="width: 610px; margin: 0px; padding: 0px;">
				                        <table style="width: 610px; margin: 0px; padding: 0px;">
				                           <tbody>
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
				                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello {name},</p>
				                                 </td>
				                              </tr>
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
				                                    <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">Thank you for your interest.</p>
				                                 </td>
				                              </tr>
				                              <!-- user details -->
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                 <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
				                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				                                       <tbody>
				                                          <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
				                                                <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
				                                             </td>
				                                          </tr>
				                                       </tbody>
				                                    </table>
				                                 </td>
				                              </tr>
				                              <!-- user details -->
				                              <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                
				                                 <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
				                                    <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
				                                       <tbody>
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px;">Item</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Cost (Per month)</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">IDs taken</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Total Cost</p>
				                                             </td>
				                                          </tr>
				                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Total Number of IDs Taken:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>N/A</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{total_no_of_id_taken}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>N/A</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Zimbra:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_email_cost_per_id}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_hybrid_zimbra_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{zimbra_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">G-Suite Basic:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{basic_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{basic_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">G-Suite Business:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_prem_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{business_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">G-Suite Enterprice:</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{enterprise_per_id_cost}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{selected_busi_ess_no}</p>
				                                             </td>
				                                             <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
				                                                <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{enterprice_final_cost}</p>
				                                             </td>
				                                          </tr>
				                                
				                                          <tr style="width: 610px; margin: 0px; padding: 0px;">
				                                             <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
				                                          </tr>

				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 20px; width: 610px; margin: 0px; padding: 4px; height: 1px; background: #eeeeee;" colspan="4" align="right">Total Cost:&nbsp;<span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{total_cost} (Monthly)</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#676767; background: #eeeeee;" colspan="4" align="right">Saved : {substruction}</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">= {Value} ({save_percentage}%)</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">*GST as applicable</td>
				                                          </tr>
				                                          <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
				                                             <td style="font-size: 16px; width: 610px; margin: 0px; padding: 4px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">Annual Commitment Required</td>
				                                          </tr>
				                                       </tbody>
				                                    </table>
				                                 </td>
				                              </tr>
				                           </tbody>
				                        </table>
				                     </td>
				                  </tr>
				               </tbody>
				            </table>
				         </td>
				      </tr>
				      <tr style="width: 650px; margin: 0px; padding: 0px;">
				         <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
				      </tr>
				</table>';
			break;
		
		case 'admin':
			$html.= 
					'<table>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 20px;">
					         <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					            <!-- hello user -->
					            <tbody>
					               <tr style="width: 610px; margin: 0px; padding: 0px;">
					                  <td style="width: 610px; margin: 0px; padding: 0px;">
					                     <table style="width: 610px; margin: 0px; padding: 0px;">
					                        <tbody>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello Admin,</p>
					                              </td>
					                           </tr>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">{name} has successfully sent a request to get the cost break up for the plan selected for Hybrid email G-Suite + Zimbra.</p>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                       <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
					                                             <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
					                                          </td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                    <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Full Name:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{name}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Email Address:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{email}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Contact Number:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{phone}</p>
					                                          </td>
					                                       </tr>
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                  </td>
					               </tr>
					            </tbody>
					         </table>
					      </td>
					   </tr>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
					   </tr>
					</table>' ;
			break;
	}

	//print_r($html); die();
	return $html;
}
/*---------------------------END-------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

/*-------------------Jelastic--------------------------------------------------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/

function see_cost_breakup_jelastic(){
	//print_r(123); die();
	$data = [];
    // sanitize user form input	   
	$data['brkup_full_name'] 						= sanitize_text_field( $_POST['brkup_full_name'] );
	$data['brkup_email'] 							= sanitize_text_field( $_POST['brkup_email'] );
	$data['brkup_phone'] 							= $_POST['brkup_phone'];
	$data['brkup_num1'] 							= $_POST['brkup_num1'];
	$data['brkup_num2']								= $_POST['brkup_num2'];
	$data['brkup_captcha']							= $_POST['brkup_captcha'];
	$data['added_on']								= date('Y-m-d h:i:s');
    //Validate the data     
    $data['per_hour_cost']  						= $_POST['per_hour_cost'];
    $data['per_hour_cost_cloudlet'] 				= $_POST['per_hour_cost_cloudlet'];
    $data['no_of_cloudlets'] 						= $_POST['no_of_cloudlets'];
    $data['ip_cost_per_month'] 						= $_POST['ip_cost_per_month'];

    $data['ip_cost_per_hour'] 						= $_POST['ip_cost_per_hour'];
    $data['total_no_of_server'] 					= $_POST['total_no_of_server'];
    $data['ssd'] 									= $_POST['ssd'];
    $data['net_traffic'] 							= $_POST['net_traffic'];
    $data['public_ip'] 								= $_POST['public_ip'];
    $data['ip_cost'] 								= $_POST['ip_cost'];
    $data['ssd_hourly_cost'] 						= $_POST['ssd_hourly_cost'];
    $data['ssd_monthly_cost'] 						= $_POST['ssd_monthly_cost'];
    $data['net_trf_hourly_cost'] 					= $_POST['net_trf_hourly_cost'];

    $data['net_traffic_final_cost'] 				= $_POST['net_traffic_final_cost'];
    $data['ssd_final_cost'] 						= $_POST['ssd_final_cost'];
    $data['daily_usage_val'] 						= $_POST['daily_usage_val'];

    
    see_cost_breakup_form_validation_jelastic($data);

	//Save the data and send emails	 
    complete_see_cost_breakup_form_jelastic($data);
}
function  see_cost_breakup_form_validation_jelastic($data = []){
	//print_r($data); die();
	global $form_errors;
	$form_errors = new WP_Error;
	//validating the input received as argument
	if (
		empty( $data['brkup_full_name'] )	||
		empty( $data['brkup_email'] )		||
		empty( $data['brkup_phone'] )		||
		empty( $data['brkup_num1'] )		||
		empty( $data['brkup_num2'] )		||
		empty( $data['brkup_captcha'] )

	){//print_r(1);die();
		$form_errors->add('field', 'Some of required form fields are missing');
	}else{
		//print_r(1);die();
		$x = $data['brkup_num1'];
		$y = $data['brkup_num2'];
		if($x + $y != $data['brkup_captcha']){
			$form_errors->add('field', 'Invalid Captcha');
		}
	}
}

function complete_see_cost_breakup_form_jelastic($data = []){
	//print_r($data); die();
	global 	$wpdb,
			$form_errors; 
    if(count( $form_errors->errors) > 0){
    	echo json_encode(['status' => false, 'message' => 'Some of required form fields are missing']);exit;
    }   

    $success = "Request Submitted Successfully. Details of cost break up has been sent through your registered mail id";
	$error = "Some error occurred. Please try again";

	


	unset($data['brkup_num1']);
	unset($data['brkup_num2']);
	unset($data['brkup_captcha']);

	$insertData['brkup_full_name'] 	= $data['brkup_full_name'];
	$insertData['brkup_email'] 		= $data['brkup_email'];
	$insertData['brkup_phone'] 		= $data['brkup_phone'];
	$insertData['added_on']		  	= date('Y-m-d h:i:s');
	
	$emailData = $data;

    $query = $wpdb->insert( $wpdb->prefix.'cost_breakup_requests', $insertData);

    //print_r($query);exit;

    if(!$query){
    	echo json_encode(['status' => false, 'message' => $error]);exit;
    }
    //print_r($emailData); die();

     see_brkup_send_user_email_jelastic($emailData);
     see_brkup_send_admin_email_jelastic($emailData);

    echo json_encode(['status' => true, 'message' => $success]);exit;
}
function see_brkup_send_user_email_jelastic($data = []){
	//print_r($data); die();
	$mail 			= new SendEmail;	
	$from 			= 'projectteam@sds.com';
	$to 			= $data['brkup_email'];
	$subject 		= 'Jelastic Cost Break Up';
	
	$template_body 	= get_cost_breakup_template_jelastic(['type'=>'user']);
	
	$full_name 								= $data['brkup_full_name'];
	$phone 									= $data['brkup_phone'];
	$brkup_email 							= $data['brkup_email'];
	$per_hour_cost_cloudlet 				= $data['per_hour_cost_cloudlet'];
	$no_of_cloudlets 						= $data['no_of_cloudlets'];
    $ip_cost_per_month						= $data['ip_cost_per_month'];
    $ip_cost_per_hour						= $data['ip_cost_per_hour'];
    $total_no_of_server					    = $data['total_no_of_server'];
    $ssd									= $data['ssd'];
    $net_traffic							= $data['net_traffic'];
    $public_ip								= $data['public_ip'] == 'yes' ? 'Included' : 'Not included';
    $public_ip_final_cost					= $data['public_ip'] == 'yes' ? $ip_cost_per_hour : 0; 
    $ip_cost								= $data['ip_cost'];
    $ssd_hourly_cost						= $data['ssd_hourly_cost'];
    $ssd_monthly_cost						= $data['ssd_monthly_cost'];
    $net_trf_hourly_cost					= $data['net_trf_hourly_cost'];
    $ssd_final_cost 						= $data['ssd_final_cost'];
    $net_traffic_final_cost 				= $data['net_traffic_final_cost']; 
    $daily_usage_val 						= $data['daily_usage_val'];
    $multiplication_percentage_factor       = number_format(($daily_usage_val / 100),2); 


    //Calculation Logic//
	$hourly = '(cost_of_cloudlet_for_one_hour + size_of_ssd * ssd_hourly_cost + ip_cost_per_hour + network_traffic_size * net_traffic_hourly_cost) * total_no_of_server_taken';
	$monthly = '((cost_of_cloudlet_for_one_month) + size_of_ssd * ssd_monthly_cost + ip_cost_per_month + network_traffic_size * net_traffic_monthly_cost) * total_no_of_server_taken';
	//Calculation Logic
	$per_hour_cost_brkup = '('.$per_hour_cost_cloudlet.' + '.$ssd.' * '.$ssd_hourly_cost.' + '.$public_ip_final_cost.' + '.$net_traffic.' * '.$net_trf_hourly_cost.') * '.$total_no_of_server;
	$per_month_cost_brkup = '(('.$per_hour_cost_cloudlet.' * 24*30) + '.$ssd.' * '.$ssd_monthly_cost.' + '.$ip_cost_per_month.' + '.$net_traffic.' * '.$net_trf_hourly_cost.' * 24*30) * '.$total_no_of_server;

	$perhourcost = $data['per_hour_cost'];

	$permonthcost = (($per_hour_cost_cloudlet * 24*30) + $ssd * $ssd_monthly_cost +$ip_cost_per_month + $net_traffic * $net_trf_hourly_cost * 24*30) * $total_no_of_server;
	
	$per_hour_cost = number_format($perhourcost,2);
	$cost_per_server = number_format(($perhourcost / $total_no_of_server),4);
	$per_month_cost = number_format($permonthcost,2);

	//print_r($per_month_cost); die();

	$template_body 	= str_replace("{name}",$full_name,$template_body);
	$template_body 	= str_replace("{no_of_cloudlets}",$no_of_cloudlets,$template_body);
	$template_body 	= str_replace("{per_hour_cost_cloudlet}",$per_hour_cost_cloudlet,$template_body);
	$template_body 	= str_replace("{net_traffic}",$net_traffic,$template_body);
	$template_body 	= str_replace("{net_trf_hourly_cost}",number_format($net_trf_hourly_cost,2),$template_body);
	$template_body 	= str_replace("{ssd}",$ssd,$template_body);
	$template_body 	= str_replace("{public_ip}",$public_ip,$template_body);
	$template_body 	= str_replace("{total_no_of_server}",$total_no_of_server,$template_body);

	$template_body 	= str_replace("{net_traffic_final_cost}",number_format($net_traffic_final_cost,2),$template_body);
	$template_body 	= str_replace("{ssd_final_cost}",number_format($ssd_final_cost,4),$template_body);
	$template_body 	= str_replace("{ssd_hourly_cost}",$ssd_hourly_cost,$template_body);
	$template_body 	= str_replace("{ip_cost_per_hour}",$ip_cost_per_hour,$template_body); 
	$template_body 	= str_replace("{public_ip_final_cost}",number_format($public_ip_final_cost,4),$template_body);
	$template_body 	= str_replace("{total_cost_cloudlet}",number_format(($per_hour_cost_cloudlet*$no_of_cloudlets*$multiplication_percentage_factor),4),$template_body);

	$template_body 	= str_replace("{daily_usage_val}",$daily_usage_val,$template_body);
	$template_body 	= str_replace("{cost_per_server}",$cost_per_server,$template_body);

	 


	$template_body 	= str_replace("{cost_hrly}",$per_hour_cost,$template_body);
	$body 			= $mail->template($template_body);
	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from
	];

	wp_mail( $to, $subject, $body, $headers );
}
function see_brkup_send_admin_email_jelastic($data = []){
	//print_r(123); exit;
	$mail 			= new SendEmail;	
	$from 			= $data['brkup_email'];
	$to 			= constant("ADMIN_TO");
	$bcc 			= constant("BCC");
	$cc 			= constant("CC");
	$subject 		= 'Jelastic Cost Break Up';
	$template_body 	= get_cost_breakup_template_jelastic(['type'=>'admin']);

	$full_name 								= $data['brkup_full_name'];
	$phone 									= $data['brkup_phone'];
	$brkup_email 							= $data['brkup_email'];
	$per_hour_cost_cloudlet 				= $data['per_hour_cost_cloudlet'];
	$no_of_cloudlets 						= $data['no_of_cloudlets'];
    $ip_cost_per_month						= $data['ip_cost_per_month'];
    $ip_cost_per_hour						= $data['ip_cost_per_hour'];
    $total_no_of_server					    = $data['total_no_of_server'];
    $ssd									= $data['ssd'];
    $net_traffic							= $data['net_traffic'];
    $public_ip								= $data['public_ip'];
    $ssd_hourly_cost						= $data['ssd_hourly_cost'];
    $ssd_monthly_cost						= $data['ssd_monthly_cost'];
    $net_trf_hourly_cost					= $data['net_trf_hourly_cost'];

	$template_body 	= str_replace("{name}",$full_name,$template_body);
	$template_body 	= str_replace("{email}",$brkup_email,$template_body);
	$template_body 	= str_replace("{phone}",$phone,$template_body);

	$body 			= $mail->template($template_body);

  	$headers 		= [
		'Content-Type: text/html; charset=UTF-8',
		'From: '.$from, 
    	'Cc: '.$cc, 
    	'Bcc:'.$bcc,
    	'Reply-To: '.$data["brkup_full_name"]." ".$data["last_name"].' <'.$data["email"].'>'
	];
	wp_mail( $to, $subject, $body, $headers );
}
function get_cost_breakup_template_jelastic($param=[]){
	//print_r($param); die();
	$html = '';
	$type = $param['type']; 
	//print_r($type); die();
	switch ($type) {
		case 'user':
			$html.= 
					'<table>
   <tr style="width: 650px; margin: 0px; padding: 0px;">
      <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
   </tr>
   <tr style="width: 650px; margin: 0px; padding: 0px;">
      <td style="width: 610px; margin: 0px; padding: 0px 20px;">
         <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
            <!-- hello user -->
            <tbody>
               <tr style="width: 610px; margin: 0px; padding: 0px;">
                  <td style="width: 610px; margin: 0px; padding: 0px;">
                     <table style="width: 610px; margin: 0px; padding: 0px;">
                        <tbody>
                           <tr style="width: 610px; margin: 0px; padding: 0px;">
                              <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello {name},</p>
                              </td>
                           </tr>
                           <tr style="width: 610px; margin: 0px; padding: 0px;">
                              <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">Thank you for your interest.</p>
                              </td>
                           </tr>
                           <!-- user details -->

                           <tr style="width: 610px; margin: 0px; padding: 0px;">
                              <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
                                    <tbody>
                                       <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
                                          <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
                                             <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                           <!-- user details -->
                           <tr style="width: 610px; margin: 0px; padding: 0px;">
                             
                              <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
                                    <tbody>
                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px;">Item</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Cost (Per Day/Unit)</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Unit taken</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #585858; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #fff; margin: 0px; text-align: left;">Total Cost</p>
                                          </td>
                                       </tr>
                                    <tr style="width: 610px; margin: 0px; padding: 0px;">
                             
                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
                                       </tr>

                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Cloudlets:</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{per_hour_cost_cloudlet}</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{no_of_cloudlets}</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{total_cost_cloudlet}</p>
                                          </td>
                                       </tr>
                             
                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
                                       </tr>

                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Network Traffic:</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{net_trf_hourly_cost}</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{net_traffic} GB</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{net_traffic_final_cost}</p>
                                          </td>
                                       </tr>
                             
                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
                                       </tr>

                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">SSD Storage:</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{ssd_hourly_cost}</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{ssd} GB</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{ssd_final_cost}</p>
                                          </td>
                                       </tr>
                             
                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
                                       </tr>

                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Public IP Address(Per Server):</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{ip_cost_per_hour}</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">{public_ip}</p>
                                          </td>
                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{public_ip_final_cost}</p>
                                          </td>
                                       </tr>
                             
                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="font-family: verdana; width: 610px; margin: 0px; padding: 15px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
                                       </tr>

                                       <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
                                          <td style="font-family: verdana; font-size: 13px; width: 610px; margin: 0px; padding: 10px; height: 1px; background: #eeeeee;" colspan="4" align="right"><p style="margin: 0; padding: 0; width:74%; float:left;">Cost Per Server (Per Day) :</p><p style="width:24%; float: right; margin: 0; padding: 0;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{cost_per_server}</p></td>
                                       </tr>
                                       <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
                                          <td style="font-family: verdana; font-size: 13px; width: 610px; margin: 0px; padding: 10px; height: 1px; background: #eeeeee;" colspan="4" align="right"><p style="margin: 0; padding: 0; width:74%; float:left;">Daily Usage (%) :</p><p style="width:24%; float: right; margin: 0; padding: 0;">{daily_usage_val}</p></td>
                                       </tr>

                                       <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
                                          <td style="font-family: verdana; font-size: 13px; width: 610px; margin: 0px; padding: 10px; height: 1px; background: #eeeeee;" colspan="4" align="right"><p style="margin: 0; padding: 0; width:74%; float:left;">Number of Servers: </p><p style="width:24%; float: right; margin: 0; padding: 0;">{total_no_of_server}</p></td>
                                       </tr>

                                       <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
                                          <td style="font-family: verdana; font-weight:bold; font-size: 13px; width: 610px; margin: 0px; padding: 10px; height: 1px; background: #eeeeee;" colspan="4" align="right"><p style="margin: 0; padding: 0; width:74%; float:left;">Estimated Cost of Server (Per Day) :</p><p style="width:24%; float: right; margin: 0; padding: 0;"><span style="color:#e07914; padding-right:5px;">&#x20b9;</span>{cost_hrly}</p></td>
                                       </tr>
                                       <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
                                          <td style="font-family: verdana; font-size: 13px; width: 610px; margin: 0px; padding: 10px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">*GST as applicable
                                          </td>
                                       </tr>
                                       <tr style="width: 610px; margin: 0px; padding: 10px; margin-bottom:0px;">
                                          <td style="font-family: verdana; font-size: 13px; width: 610px; margin: 0px; padding: 10px; height: 1px; color:#e07914; background: #eeeeee;" colspan="4" align="right">No Commitment Required
                                          </td>
                                       </tr>
                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
                                          <td style="font-family: verdana; width: 610px; margin: 0px; padding: 15px; height: 1px; background: #eeeeee;" colspan="4" align="left"></td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
               </tr>
            </tbody>
         </table>
      </td>
   </tr>
   <tr style="width: 650px; margin: 0px; padding: 0px;">
      <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
   </tr>
</table>';
			break;
		
		case 'admin':
			$html.= 
					'<table>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 20px;">
					         <table style="width: 610px; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					            <!-- hello user -->
					            <tbody>
					               <tr style="width: 610px; margin: 0px; padding: 0px;">
					                  <td style="width: 610px; margin: 0px; padding: 0px;">
					                     <table style="width: 610px; margin: 0px; padding: 0px;">
					                        <tbody>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 20px 0px 12px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 14px; color: #676767; margin: 0px; padding: 0px; font-weight: bold;">Hello Admin,</p>
					                              </td>
					                           </tr>
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px 0px 8px 0px;">
					                                 <p style="float: left; width: 610px; font-family:verdana; font-size: 13px; color: #676767; margin: 0px; padding: 0px;">{name} has successfully sent a request to get the cost break up for the plan selected for Jelastic.</p>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; padding-top: 26px;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                       <tr style="background-color: #e3e3e3; width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 588px; margin: 0px; padding: 10px; border: 1px solid #eeeeee;" align="left">
					                                             <p style=" font-family: verdana; font-size: 13px; color: #676767; margin: 0px; font-weight: bold;">Cost breakup for your selected plan is as follows: </p>
					                                          </td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                           <!-- user details -->
					                           <tr style="width: 610px; margin: 0px; padding: 0px;">
					                              <td style="width: 610px; margin: 0px; padding: 0px; background: #ffffff;" align="left">
					                                 <table style="width: 610px; background-color: #ffffff; margin: 0px; padding: 0px;" border="0" cellspacing="0" cellpadding="0">
					                                    <tbody>
					                                    <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Full Name:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{name}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Email Address:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{email}</p>
					                                          </td>
					                                       </tr>
					                             
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>

					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee; border-left: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px;">Contact Number:</p>
					                                          </td>
					                                          <td style="margin: 0px; padding: 10px; background: #ffffff; border-right: 1px solid #eeeeee;" align="left">
					                                             <p style="font-family: verdana; font-size: 13px; color: #676767; margin: 0px; text-align: left;">{phone}</p>
					                                          </td>
					                                       </tr>
					                                       <tr style="width: 610px; margin: 0px; padding: 0px;">
					                                          <td style="width: 610px; margin: 0px; padding: 0px; height: 1px; background: #eeeeee;" colspan="2" align="left"></td>
					                                       </tr>
					                                    </tbody>
					                                 </table>
					                              </td>
					                           </tr>
					                        </tbody>
					                     </table>
					                  </td>
					               </tr>
					            </tbody>
					         </table>
					      </td>
					   </tr>
					   <tr style="width: 650px; margin: 0px; padding: 0px;">
					      <td style="width: 610px; margin: 0px; padding: 0px 0px 0px 0px; height: 20px;" align="left"></td>
					   </tr>
					</table>' ;
			break;
	}

	//print_r($html); die();
	return $html;
}
/*---------------------------END-------------------------------------------------------------------------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/




/*-------------------------------------------Common Function To send Email---------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------*/
function see_brkup_send_email_commom($template, $replaces,$subject, $to, $context) {  
	$mail 			= new SendEmail;
	if ($context == 'user') {
		$from 			= 'projectteam@sds.com';
	}
	if ($context == 'admin') {
		$from 			= $to;
	}
	$to 			=  $to;
	$bcc 			= constant("BCC");
	$cc 			= constant("CC");
	$subject 		= $subject;
    
    if (! empty($replaces)) { 
        foreach($replaces as $key => $replacer) {
            $template = str_replace($key, $replacer, $template);
        }
        //print_r($template);die();
    }

    $body 			= $mail->template($template);

    if ($context == 'user') {
    	$headers 		= [
			'Content-Type: text/html; charset=UTF-8',
			'From: '.$from,
	    	'Reply-To: '.$data["brkup_full_name"].' <'.$data["email"].'>'
		];
    }

    if ($context == 'admin') {
    	$headers 		= [
			'Content-Type: text/html; charset=UTF-8',
			'From: '.$from,
			'Cc:'.$cc, 
	    	'Bcc:'.$bcc,
	    	'Reply-To: '.$data["brkup_full_name"].' <'.$data["email"].'>'
		];
    }


	wp_mail( $to, $subject, $body, $headers );
}
/*-------------------------------------------Common Function To send Email---------------------------------------------------------------------------------
-----------------------------------------------------------------------------------------------------------------------------------------------------------*/

/*--------------------------------------- JOb details--------------------------------*/
function dm_ja_get_job_details(){
	//print_r(1); die();
	$id	= $_POST['post_id'];
	$args = ['p'=>$id];
	$args = [
	    'post_type'              => 'current_openings',
	    'post_id'     		     => $id
	];
	$current_openings = new WP_Query( $args );
	
	$x = get_field('job_details', $id);
	if ($x != '') {
		echo json_encode(['status' => true, 'data' => $x]);exit;
	}else{
		echo json_encode(['status' => false, 'data' => $x]);exit;
	}
	
} 
?>