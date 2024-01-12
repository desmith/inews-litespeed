jQuery(document).ready(function(){
	//Form validation
		jQuery.validator.addMethod("customemail", function (value, element, params) {
	        //var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]+\.[a-zA-Z.]{2,5}$/i;

	        var re = /^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}$/;
	        return re.test(value);
	    }, "Please enter a valid email address.");

		jQuery("#rhl_cr_fname,#rhl_cr_lname,#rhl_cr_message").keypress(function(e) {
				   if (e.which === 32 && !this.value.length) {
					   e.preventDefault();
				   }
		});
	    //Captcha Validation
	    	/*jQuery.validator.addMethod("captcha_validation_contact", function(value, element, params) {
			    //console.log('123');return false;
			    console.log(value); 
			        var num1 = jQuery("#cr_num1").val(),
			            num2 = jQuery("#cr_num2").val();
			            console.log(num1,num2);
			        if (isNaN(value)) return false;
			        var i = parseInt(num1) + parseInt(num2);
			        return i == value ? true : false;
			    }, jQuery.validator.format("Invalid captcha"));*/
	    //End Of captcha validation

	    jQuery('#rhl_contact_form').validate({
	        errorElement:"i",
	        errorClass:"error",
	        rules:{
	            rhl_cr_fname:{
	            	required:true,
	            	minlength:2
	            },

	             rhl_cr_lname:{
	            	required:true,
	            	minlength:2
	            },
	            
	            rhl_cr_phone:{
	            	number:true,
	            	//required:true,
	            	minlength:8,
					maxlength:12
	            },
	           
	            rhl_cr_email:{
	            	required:true,
	            	customemail:true
	            },
				
	            rhl_cr_message:{
	            	required:true
	            }
				
	            /*captcha:{
	            	required:true,
	            	captcha_validation_contact:true
	            }*/
	            
	        },
	        messages:{
	            rhl_cr_fname:{
	            	required:"Please enter your first name",
	            	minlength:"First name should contain minimum two characters"
	            },

	             rhl_cr_lname:{
	            	required:"Please enter your last name",
	            	minlength:"Last name should contain minimum two characters"
	            },
	            
	            rhl_cr_email:{
	            	required:"Please enter your email address",
	            	customemail:"Please enter valid email",
	            },
	            rhl_cr_phone:{
	            	//required:"Please enter your phone number",
	            	minlength:"Phone no must be of minimum 8 digits",
					maxlength:"Phone number contain maximum 12 digits",
	            	number:"Phone number must contain only numbers"
	            },
	            rhl_cr_message:{
	            	required:"Please write down your message",
	            }
				
	            /*captcha:{
	            	required:"Please enter the result",
	            	captcha_validation_contact:"Invalid captcha"
	            }*/
	        },
    		submitHandler: function(form) {
    			show_loader(form);
    			jQuery("#rhl_cr_sbmt_btn").prop('disabled',true);
        	    jQuery("#rhl_cr_sbmt_btn").text('Please wait');
	        	let allData = new FormData(form);
				allData.append('action', 'rhl_cr_contact_form_submission');
				jQuery.ajax({
			        url: rhl_cr_form_submission.ajax_url,
			        type:'POST',
			        data: allData,
			        dataType: "json",
			        cache: false,
		            processData: false, 
		            contentType: false,
			        success:function(response) {
			        	jQuery("#rhl_cr_sbmt_btn").prop('disabled',false);
        	    		jQuery("#rhl_cr_sbmt_btn").text('Submit');
			        	jQuery('#rhl_contact_form')[0].reset();
    					hide_loader(form);
    					show_message(response,form);
    					/*var x = Math.floor((Math.random()*10)+1);
		                var y = Math.floor((Math.random()*10)+1);
		                jQuery('#cr_n1').text(x); 
	                    jQuery('#cr_n2').html(y+'<b class="starmark">*</b>');
	                    jQuery(form).find('input[name="cr_num1"]').val(x);
	                    jQuery(form).find('input[name="cr_num2"]').val(y);*/
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
    jQuery(form).find('.btn-submit').attr('disabled',false).val('Submit');

}

