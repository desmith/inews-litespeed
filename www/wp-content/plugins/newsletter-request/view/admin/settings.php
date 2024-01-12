<div class="wrap ils_pre_settings">
    <form method="post" action="options.php" enctype='multipart/form-data' >
        <?php
            settings_fields("ils-pre-emails-settings");
            do_settings_sections("ils_pre_settings");      
            submit_button(); 
        ?>          
    </form>
</div>