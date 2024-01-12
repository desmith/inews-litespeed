<?php 
$get_package = next3_license_package();
?>
<section class="next3aws-section"> 

    <div class="next3aws-content">
        <div class="next3aws-nav-menu">
            <ul class="nav-setting">
                <li class="logo_area"><a></a></li>

                <?php do_action('next3aws/navtab/before');?>
                
                <li class="active"><a href="#ntab=settings" id="nv-settings"> <i class="dashicons dashicons-cloud-upload"></i> <?php echo esc_html__('Storage Settings', 'next3-offload');?></a></li>
                <li><a href="#ntab=delivery" id="nv-delivery"> <i class="dashicons dashicons-cloud-saved"></i> <?php echo esc_html__('Delivery Settings', 'next3-offload');?></a></li>
                <?php if( in_array($get_package, ['business', 'developer', 'extended']) ){?>
                <li><a href="#ntab=optimization" id="nv-optimization"> <i class="dashicons dashicons-update"></i> <?php echo esc_html__('Optimization', 'next3-offload');?></a></li>
                <?php }?>
                <?php if( in_array($get_package, ['developer', 'extended']) ){?>
                <li><a href="#ntab=assets" id="nv-assets"> <i class="dashicons dashicons-filter"></i> <?php echo esc_html__('Assets', 'next3-offload');?></a></li>
                <?php }?>
                <li><a href="#ntab=offload" id="nv-offload"> <i class="dashicons dashicons-cloud"></i> <?php echo esc_html__('Offload Settings', 'next3-offload');?></a></li>

                <li><a href="#ntab=tools" id="nv-tools"> <i class="dashicons dashicons-admin-tools"></i> <?php echo esc_html__('Tools', 'next3-offload');?></a></li>
                <!--li><a href="#ntab=imports" id="nv-imports"> <i class="dashicons dashicons-database-add"></i> <?php echo esc_html__('Export & Import', 'next3-offload');?></a></li-->
                
                <?php do_action('next3aws/navtab/after');?>
                
                <?php if( !NEXT3_SELF_MODE ){?>
                <li><a href="#ntab=license" id="nv-license"> <i class="dashicons dashicons-lock"></i> <?php echo esc_html__('Active License', 'next3-offload');?></a></li>
                <?php }?>
                
                <!--li><a href="#ntab=addons" id="nv-addons"> <i class="dashicons dashicons-plugins-checked"></i> <?php echo esc_html__('Addons', 'next3-offload');?></a></li-->
                
                <li><a href="<?php echo esc_url('//www.themedev.net/next3-offload/?utm_source=next3&utm_medium=Dashboard&utm_campaign=Plugin+Dashboard&utm_id=plugin');?>" target="_blank" id="nv-livepreview"> <i class="dashicons dashicons-welcome-view-site"></i> <?php echo esc_html__('Live Demos', 'next3-offload');?></a></li>
                
                <li><a></a></li>
            </ul>
        </div>

        <div class="next3aws-content-area">
            <h1> <?php echo esc_html__('Next3 Panel', 'next3-offload');?> <span class="next3aws-version"><?php echo esc_html__('Version: ', 'next3-offload');?> <?php _e( \Next3Offload\N3aws_Plugin::version());?></span></h1>
            
            <a href="<?php echo esc_url(next3_admin_url().'admin.php?page=next3aws-file');?>" class="nxbutton submit-button right-submit <?php if( next3_get_option('__validate_author_next3aws__') != 'active'){?>hide-template<?php }?>"><?php echo esc_html__('File Manager', 'next3-offload');?></a>
             
            <div class="message-view <?php if( next3_get_option('__validate_author_next3aws__') == 'active'){?>hide-message<?php }?>"> <?php echo esc_html__('Please active your license ', 'next3-offload');?></div>
            <div class="message-view <?php if( !NEXT3_SELF_MODE ){?>hide-message<?php }?>"> <?php echo esc_html__('Trial Mode Enabled. Just you can view mode.', 'next3-offload');?></div>

            <div class="settings-content">

                <?php do_action('next3aws/content/before');?>  

                <div id="settings" class="ncode-tabs-settings active">
                    <div class="heading-label ">
                        <h3> <?php esc_html_e('Storage Settings', 'next3-offload');?> </h3>      
                        <?php include( __DIR__ .'/include/settings.php');?>
                    </div> 
                </div>

                <div id="delivery" class="ncode-tabs-delivery">
                    <div class="heading-label ">
                        <h3> <?php esc_html_e('Delivery Settings', 'next3-offload');?> </h3>      
                        <?php include( __DIR__ .'/include/delivery.php');?>
                    </div> 
                </div>
                <?php if( in_array($get_package, ['business', 'developer', 'extended']) ){?>
                <div id="optimization" class="ncode-tabs-optimization">
                    <div class="heading-label ">
                        <h3> <?php esc_html_e('Optimization Settings', 'next3-offload');?> </h3>      
                        <?php include( __DIR__ .'/include/optimization.php');?>
                    </div> 
                </div>
                <?php }?>

                <div id="offload" class="ncode-tabs-offload">
                    <div class="heading-label ">
                        <h3> <?php esc_html_e('Offload Settings', 'next3-offload');?> </h3>      
                        <?php include( __DIR__ .'/include/offload.php');?>
                    </div> 
                </div>

                <?php if( in_array($get_package, ['developer', 'extended']) ){?>
                <div id="assets" class="ncode-tabs-assets">
                    <div class="heading-label ">
                        <h3> <?php esc_html_e('Assets Tools', 'next3-offload');?> </h3>      
                        <?php include( __DIR__ .'/include/assets.php');?>
                    </div> 
                </div>
                <?php }?>


                <?php if( !NEXT3_SELF_MODE ){?>
                <div id="license" class="ncode-tabs-license">
                    <div class="heading-label ">
                        <h3> <?php esc_html_e('Active License', 'next3-offload');?> </h3> 
                        <?php include( __DIR__ .'/include/active-pro.php');?>
                    </div> 
                </div>
                <?php }?>

                <div id="tools" class="ncode-tabs-tools">
                    <div class="heading-label ">
                        <h3> <?php esc_html_e('Tools', 'next3-offload');?> </h3> 
                        <?php 
                        include( __DIR__ .'/include/tools.php');
                        ?> 
                    </div> 
                </div>
                <!--div id="imports" class="ncode-tabs-imports">
                    <div class="heading-label ">
                        <h3> <?php esc_html_e('Export & Import Media', 'next3-offload');?> </h3> 
                        
                        <?php 
                        //include( __DIR__ .'/include/export.php');
                        ?> 
                    </div> 
                </div>
                <div id="addons" class="ncode-tabs-addons">
                     <div class="heading-label ">
                        <h3> <?php esc_html_e('Add-ons', 'next3-offload');?> </h3> 
                        <span> <?php esc_html_e('Extra Addons of Next3 Plugins. You can use for extra features to extend Next3 Offload.', 'next3-offload');?></span> 
                        <?php include( __DIR__ .'/include/addons.php');?> 
                    </div> 
                </div-->
                
                <?php do_action('next3aws/content/after');?>

            </div>

        </div>
    </div>

</section>
