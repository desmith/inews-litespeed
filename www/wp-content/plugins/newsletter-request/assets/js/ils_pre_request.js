jQuery(document).ready(function(){
	//Form validation
		jQuery.validator.addMethod("customemail", function (value, element, params) {
	        //var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]+\.[a-zA-Z.]{2,5}$/i;

	        var re = /^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}$/;
	        return re.test(value);
	    }, "Please enter a valid email address.");

	
	    jQuery('#get_in_touch').validate({
	        errorElement:"i",
	        errorClass:"error",
	        rules:{            	           
	            gt_email:{
	            	required:true,
	            	customemail:true
	            }
	        },
	        messages:{
	            
	            gt_email:{
	            	required:"Please enter your email",
	            	customemail:"Please enter valid email",
	            	companyemail:"Please provide company email only."
	            }
	            
	        },
    		submitHandler: function(form) {
    			show_loader(form);
    			jQuery("#ils_pre_sbmt_btn").prop('disabled',true);
        	    jQuery("#ils_pre_sbmt_btn").text('Please wait');
	        	let allData = new FormData(form);
				allData.append('action', 'ils_pre_get_in_touch_form_submission');
				jQuery.ajax({
			        url: ils_pre_form_submission.ajax_url,
			        type:'POST',
			        data: allData,
			        dataType: "json",
			        cache: false,
		            processData: false, 
		            contentType: false,
			        success:function(response) {
						jQuery("#ils_pre_sbmt_btn").prop('disabled',false);
        	    		jQuery("#ils_pre_sbmt_btn").text('Subscribe');
			        	jQuery('#get_in_touch')[0].reset();
    					hide_loader(form);
    					show_message(response,form);
			        },
			        error: function(xhr){
    					hide_loader(form);
			        	console.log(xhr.responseText);
			        }
			    });
	        }
    	});
})

//show the loader
function show_loader(form){
    jQuery(form).find('.btn-submit').attr('disabled',true).val('Please Wait');
}
//hide the loader
function hide_loader(form){
    jQuery(form).find('.btn-submit').attr('disabled',false).val('Subscribe');

}

