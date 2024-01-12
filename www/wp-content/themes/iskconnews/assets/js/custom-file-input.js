/*
	By Osvaldas Valutis, www.osvaldas.info
	Available for use under the MIT License
*/

'use strict';

;( function ( document, window, index )
{
	var inputs = document.querySelectorAll( '.inputfile' );
	Array.prototype.forEach.call( inputs, function( input )
	{
		var label	  = input.nextElementSibling,
			 labelVal  = label.innerHTML;

		input.addEventListener( 'change', function( e )
		{
			var fileNames = '';
			for(var i = 0; i < this.files.length; i++){
				var fileName = '';
				//console.log(this.files[i].name);
				if(/\.(doc|docx|pdf|jpg|jpeg|png)$/i.test(this.value)){
                    var filesize = this.files[0].size;
                    console.log(filesize);
                    if((filesize/(1024*1024)) > 2){
                        alert('Document size should be less than 2 MB.');
                         jQuery(this).val('');
                         jQuery('.fileName').text('');
                        return false;
                    }
                }else{
                   alert('Invalid File Format. Please Select a doc / docx / pdf / jpeg / jpg file format.');
                   jQuery(this).val('');
                   jQuery('.fileName').text('');
                   return false;
                }
				fileNames += this.files[i].name+',';
			 }
			 console.log(fileNames.slice(0,-1));
			 //return false;
			 fileNames = fileNames.slice(0,-1);
			
			if( this.files && this.files.length > 1 )
				fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
			else
				fileName = e.target.value.split( '\\' ).pop();

			if( fileNames )
				label.querySelector( 'span' ).innerHTML = fileNames;
			else
				label.innerHTML = labelVal;
		});

		// Firefox bug fix
		input.addEventListener( 'focus', function(){ input.classList.add( 'has-focus' ); });
		input.addEventListener( 'blur', function(){ input.classList.remove( 'has-focus' ); });
	});
}( document, window, 0 ));