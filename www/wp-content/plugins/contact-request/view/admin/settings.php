<div class="wrap rhl_cr_settings">
    <form method="post" action="options.php" enctype='multipart/form-data' >
        <?php
            settings_fields("rhl-cr-emails-settings");
            do_settings_sections("rhl_cr_settings");      
            submit_button(); 
        ?>          
    </form>
</div>