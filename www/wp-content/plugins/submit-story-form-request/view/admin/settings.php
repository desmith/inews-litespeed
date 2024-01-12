<div class="wrap ief_settings">
    <form method="post" action="options.php" enctype='multipart/form-data' >
        <?php
            settings_fields("ief-emails-settings");
            do_settings_sections("ief_settings");      
            submit_button(); 
        ?>          
    </form>
</div>