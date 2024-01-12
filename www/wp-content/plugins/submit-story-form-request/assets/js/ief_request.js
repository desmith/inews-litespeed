jQuery(document).ready(function(){
	jQuery.validator.addMethod('filesize', function(value, element, param) {
    // param = size (en bytes) 
    // element = element to validate (<input>)
    // value = value of the element (file name)
    return this.optional(element) || (element.files[0].size <= param) 
	});
	//Form validation
		jQuery.validator.addMethod("customemail", function (value, element, params) {
	        //var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]+\.[a-zA-Z.]{2,5}$/i;

	        var re = /^[a-z0-9]+([-._][a-z0-9]+)*@([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,4}$/;
	        return re.test(value);
	    }, "Please enter a valid email address.");

		jQuery("#wb_fullname").keypress(function(e) {
				   if (e.which === 32 && !this.value.length) {
					   e.preventDefault();
				   }
		});

	    //Captcha VAlidation
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
	
		 jQuery('#doc').on('click',function(){

	     });

	    jQuery('#ief_register_form').validate({
	        errorElement:"i",
	        errorClass:"error",
	        rules:{
	            wb_fullname:{
	            	required:true,
	            	minlength:2
	            },
	            
	            wb_phone:{
	            	number:true,
	            	//required:true,
	            	minlength:8,
					maxlength:12
	            },
	           
	            wb_email:{
	            	required:true,
	            	customemail:true
	            },
				
				cv:{
	            	required:true,
	            	//accept: "png|jpe?g|gif", 
	            	filesize: 2097152
	            }
								
	            /*captcha:{
	            	required:true,
	            	captcha_validation_contact:true
	            }*/
	            
	        },
	        messages:{
	            wb_fullname:{
	            	required:"Please enter your name.",
	            	minlength:"Name should contain minimum two characters"
	            },
	            
	            wb_email:{
	            	required:"Please enter your email.",
	            	customemail:"Please enter valid email"
	            },
				
	            wb_phone:{
	            	//required:"Please enter your phone number.",
	            	minlength:"Phone number must be of minimum 8 digits",
					maxlength:"Phone number contain maximum 12 digits",
	            	number:"Phone number must contain only numbers"
	            },
	            
				cv:{
	            	required:"Please upload your file (.doc /.docx /.pdf /.jpeg /.jpg)",
	            	filesize:"2MB"
	            	//accept:".doc /.docx / .pdf"

	            }
				
	            /*captcha:{
	            	required:"Please enter the result.",
	            	captcha_validation_contact:"Invalid captcha."
	            }*/
	        },
    		submitHandler: function(form) {
    			show_loader(form);
    			jQuery("#ief_sbmt_btn").prop('disabled',true);
        	    jQuery("#ief_sbmt_btn").text('Please wait');
	        	let allData = new FormData(form);
				allData.append('action', 'ief_form_submission');
				jQuery.ajax({
			        url: ief_form_submission.ajax_url,
			        type:'POST',
			        data: allData,
			        dataType: "json",
			        cache: false,
		            processData: false, 
		            contentType: false,
			        success:function(response) {
			        	jQuery("#ief_sbmt_btn").prop('disabled',false);
        	    		jQuery("#ief_sbmt_btn").text('Submit Now');
			        	jQuery('#ief_register_form')[0].reset();
    					hide_loader(form);
    					show_message(response,form);
    					jQuery('#ief_register_form .form-element').removeClass('has-value');
    					jQuery('.fileName').text('');
						jQuery('#doc').val('');
						jQuery('.form-elementfile span').html('');
						jQuery('.form-elementfile span').html('Upload Your Doc File');
						
    					//resetCaptcha();
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
    jQuery(form).find('.btn-submit').attr('disabled',false).val('Submit Now');

}


/*function resetCaptcha(){
	jQuery('#ief_register_form')[0].reset();
	jQuery('.fileName').text('');
	var x = Math.floor((Math.random()*10)+1);
    var y = Math.floor((Math.random()*10)+1);
    jQuery('#cr_n1').text(x); 
    jQuery('#cr_n2').html(y+'<b class="starmark">*</b>');
    jQuery('#captcha').val('');
    jQuery('#captcha-error').text('');
    jQuery('.form-element i.error').hide();
    jQuery('#ief_register_form').find('input[name="cr_num1"]').val(x);
    jQuery('#ief_register_form').find('input[name="cr_num2"]').val(y);
}*/