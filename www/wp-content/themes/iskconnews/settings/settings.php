<div class="wrap">
    <form method="post" action="options.php" enctype='multipart/form-data' >
        <?php
            settings_fields("isk_theme_section");
            do_settings_sections("theme-options");      
            submit_button(); 
        ?>          
    </form>
</div>
<style type="text/css">
	.image-preview{width: 500px; display: block;}
	.social-media input, .general-info input{width: 400px;}
    .trademark-section input,textarea{width:100%;}
    .trademark-section textarea{resize: none;}
    .wp-core-ui .quicktags-toolbar input.button.button-small{width: auto !important;}
</style>