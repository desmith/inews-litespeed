jQuery(document).ready(function(){
	jQuery('#get_in_touch').DataTable( {
	    autoFill: true
	} );
})

//Show the alert
show_alert = (e) => {
	jQuery(e).next('.alert').slideDown();
}
//Confirm delete
confirm_delete = (e) => {
	
	let id = jQuery(e).parents('.alert').attr('data-id');
	let _this = jQuery(e);

	if(id == '' || id == null || id == undefined || id == isNaN(id)) return;

	jQuery.ajax({
        url: ils_pre_actions.ajax_url,
        type:'POST',
        data: {id,'action': 'ils_pre_remove'},
        dataType: "json",
        success:function(response) {
			show_message(response);
			hide_alert();
			response.status == 'success' ? _this.parents('.single-row').remove() : '';
        },
        error: function(xhr){
			hide_alert();
        	console.log(xhr.responseText);
        }
    });
}
//Hide the alert
hide_alert = (e) => {
    jQuery(e).parents('.alert').slideUp();
}
//Show success/error message
show_message = (response) => {
	let cls = 'updated notice notice-'+response.status;	
	jQuery('#message').attr('class',cls).show().find('p').text(response.message);

	setTimeout(() => {
		location.reload();
	},2000);
} 