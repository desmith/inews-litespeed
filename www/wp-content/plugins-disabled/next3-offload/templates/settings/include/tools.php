<?php
    $msg = isset($_GET['msg']) ? sanitize_text_field($_GET['msg']) : '';
    switch($msg){
        case 'settings_cleared':
            echo next3_print('<h4> '. esc_html__('Successfully reset plugin settings...', 'next3-offload') .' </h4>');
        break;
        case 'cache_cleared':
            echo next3_print('<h4> '. esc_html__('Successfully reset credentials...', 'next3-offload') .' </h4>');
        break;
        case 'css_unoffload':
            echo next3_print('<h4> '. esc_html__('Successfully deleted CSS offload information...', 'next3-offload') .' </h4>');
        break;
        case 'js_unoffload':
            echo next3_print('<h4> '. esc_html__('Successfully deleted JS offload information...', 'next3-offload') .' </h4>');
        break;
        case 'restore_compress':
            echo next3_print('<h4> '. esc_html__('Successfully restored all backup...', 'next3-offload') .' </h4>');
        break;
        case 'webp_remove':
            echo next3_print('<h4> '. esc_html__('Successfully deleted all WebP...', 'next3-offload') .' </h4>');
        break;
        case 'wpoffload_action':
            echo next3_print('<h4> '. esc_html__('Successfully restored to Next3...', 'next3-offload') .' </h4>');
        break;
        
    }

    $offload_store = next3_core()->action_ins->get_offload_count();
    $wpoffload = ($offload_store['wpoffload']) ?? 0;
    $wpoffload_done = ($offload_store['wpoffload_done']) ?? 0;
    $wpoffload_per = ($offload_store['wpoffload_per']) ?? 0;
    $total_optimize = ($offload_store['total_optimize']) ?? 0;
    $total_webp_done = ($offload_store['total_webp_done']) ?? 0;
    $total_compress_done = ($offload_store['total_compress_done']) ?? 0;
    $webp_per = ($offload_store['webp_per']) ?? 0;
    $compress_per = ($offload_store['compress_per']) ?? 0;
    

    $offload_data = next3_get_option('_next3_offload_data', []);
     
    $get_package = next3_license_package();
?>

<?php do_action('next3aws-tools-content-before');?>  


<?php
$persent_offload = '0';

$msg_offload = esc_html__('Compress: '.$compress_per .'% and WebP: '.$webp_per.'% files need to optimize', 'next3-offload');
if( $total_webp_done != 0 && $total_compress_done != 0){
    $msg_offload = esc_html__( 'Compress: '.$compress_per .'% and WebP: '.$webp_per.'% files has been optimized', 'next3-offload');
}
if( !empty($offload_data)){
    $status_offload = ($offload_data['status']) ?? '';
    $total_offload = ($offload_data['total']) ?? 0;
    $start_offload = ($offload_data['start']) ?? 0;
    $type_offload = ($offload_data['type']) ?? '';

    if($status_offload == 'pause' && $type_offload == 'compress'){
        $msg_offload = esc_html__( 'Compress: '.$compress_per .'% and WebP: '.$webp_per.'% files need to optimize, Paused!', 'next3-offload');
    }

    $start_offload = ($total_offload < $start_offload) ? $total_offload : $start_offload;
    if( $start_offload != 0  && $total_offload != 0){
        $persent_offload = floor(( $start_offload * 100) / $total_offload);
    }
    $txt_offload = $persent_offload . '% ('.$start_offload.'/'. $total_offload .')';
    if( $total_offload <= $start_offload || $type_offload != 'compress'){
        $status_offload = '';
        $persent_offload = 0;
        $txt_offload = '0% (0/0)';
    }
    
}
?>
<?php if( in_array($get_package, ['business', 'developer', 'extended']) ){?>

<div class="next3aws-admin-toolbox-item next3-offload-wrap"  data-action="compress">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Compress & WebP convert: only local storage media files', 'next3-offload');?></h3>
    <div class="next3aws-footer-content next3-offload-section">
        <p class="show-process"><?php echo esc_html__($msg_offload, 'next3-offload');?></p>
        <div class="next3-settings-progressbars next3-offload-bar <?php echo esc_attr( ($status_offload != '') ? 'nxopen' : '' );?>">
            <div class="complate-process" style="width:<?php echo esc_attr($persent_offload);?>%;"></div>
            <div class="next3-progressbar next3-progress-process" data-title="Process offload media"><span><?php echo esc_html($txt_offload);?></span></div>
        </div>
        <button class="action-button next3-offload-target next3-offload-action <?php echo esc_attr( ($status_offload == '') ? 'nxopen' : '' );?>" data-type="compress" href="#"><?php echo esc_html__('Start now', 'next3-offload');?></button>
        <button class="action-button next3-offload-target next3-offload-pause <?php echo esc_attr( ($status_offload != '') ? 'nxopen' : '' );?>" data-type="<?php echo esc_attr( ($status_offload == 'pause') ? 'resume' : 'pause' );?>" href="#"><?php echo esc_html( ($status_offload == 'pause') ? 'Resume' : 'Pause' );?> </button>
        <button class="action-button next3-offload-target next3-offload-cancel <?php echo esc_attr( ($status_offload != '') ? 'nxopen' : '' );?>" data-type="cancel" href="#"><?php echo esc_html__('Cancel', 'next3-offload');?></button>
    </div>
    <div class="next3-offload-pie">
        <p class="compress-data"><strong><?php echo esc_html__('Compress files:', 'next3-offload');?></strong> <span><?php echo esc_html($compress_per);?>% (<?php echo esc_html($total_compress_done);?>/<?php echo esc_html($total_optimize);?>)</span></p>
        <p class="webp-data"><strong><?php echo esc_html__('WebP files:', 'next3-offload');?></strong> <span><?php echo esc_html($webp_per);?>% (<?php echo esc_html($total_webp_done);?>/<?php echo esc_html($total_optimize);?>)</span></p>  
    </div>
</div>

<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Restore compress backup', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('This tool will restore backup all compress files in local storage and back to orginal files.', 'next3-offload');?></p>
        <a class="reset-button" href="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=restore_backup#ntab=tools' ));?>"><?php echo esc_html__('Restore', 'next3-offload');?></a>
    </div>
</div>

<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Restore WebP files', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('This tool will delete all WebP files in local storage and back to orginal files.', 'next3-offload');?></p>
        <a class="reset-button" href="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=delete_webp#ntab=tools' ));?>"><?php echo esc_html__('Restore', 'next3-offload');?></a>
    </div>
</div>

<?php }?>

<?php

$msg_offload = esc_html__($wpoffload_per .'% Files need to migrate to Next3 Offload', 'next3-offload');
if( $wpoffload != 0){
    $msg_offload = esc_html__( $wpoffload_per .'% Files have been migrated', 'next3-offload');
}
if( !empty($offload_data)){
    $status_offload = ($offload_data['status']) ?? '';
    $total_offload = ($offload_data['total']) ?? 0;
    $start_offload = ($offload_data['start']) ?? 0;
    $type_offload = ($offload_data['type']) ?? '';

    if($status_offload == 'pause' && $type_offload == 'wpoffload'){
        $msg_offload = esc_html__( $wpoffload_per .'% Files need to migrate to Next3 Offload, Paused!', 'next3-offload');
    }

    $start_offload = ($total_offload < $start_offload) ? $total_offload : $start_offload;
    if( $start_offload != 0  && $total_offload != 0){
        $persent_offload = floor(( $start_offload * 100) / $total_offload);
    }
    $txt_offload = $persent_offload . '% ('.$start_offload.'/'. $total_offload .')';
    if( $total_offload <= $start_offload || $type_offload != 'wpoffload'){
        $status_offload = '';
        $persent_offload = 0;
        $txt_offload = '0% (0/0)';
    }
    
}
?>
<div class="next3aws-admin-toolbox-item next3-offload-wrap"  data-action="wpoffload">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('WP Offload Media to Next3 Offload migration', 'next3-offload');?></h3>
    <div class="next3aws-footer-content next3-offload-section">
        <p><?php echo esc_html__($msg_offload, 'next3-offload');?></p>
        <div class="next3-settings-progressbars next3-offload-bar <?php echo esc_attr( ($status_offload != '') ? 'nxopen' : '' );?>">
            <div class="complate-process" style="width:<?php echo esc_attr($wpoffload_per);?>%;"></div>
            <div class="next3-progressbar next3-progress-process" data-title="Process offload media"><span><?php echo esc_html($txt_offload);?></span></div>
        </div>
        <?php 
            if ( class_exists( '\WP_Offload_Media_Autoloader' ) ) {
        ?>
        <button class="action-button next3-offload-target next3-offload-action <?php echo esc_attr( ($status_offload == '') ? 'nxopen' : '' );?>" data-type="wpoffload" href="#"><?php echo esc_html__('Restore Now', 'next3-offload');?></button>
        <button class="action-button next3-offload-target next3-offload-pause <?php echo esc_attr( ($status_offload != '') ? 'nxopen' : '' );?>" data-type="<?php echo esc_attr( ($status_offload == 'pause') ? 'resume' : 'pause' );?>" href="#"><?php echo esc_html( ($status_offload == 'pause') ? 'Resume' : 'Pause' );?> </button>
        <button class="action-button next3-offload-target next3-offload-cancel <?php echo esc_attr( ($status_offload != '') ? 'nxopen' : '' );?>" data-type="cancel" href="#"><?php echo esc_html__('Cancel', 'next3-offload');?></button>
        <?php }else{?>
            <p class="wpoffload-data"><i><?php echo esc_html__('Must be activate the WP Offload Media plugin, then you can start the migration process.', 'next3-offload');?></i>
                <!--a href="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=wpoffload_next3#ntab=tools' ));?>"><?php echo esc_html__('Restore All', 'next3-offload');?></a-->      
            </p>
        <?php }?>
        
    </div>
    <div class="next3-offload-pie">
        <p class="wpoffload-data"><strong><?php echo esc_html__('WP Offload Media:', 'next3-offload');?></strong> <span><?php echo esc_html($wpoffload_per);?>% (<?php echo esc_html($wpoffload_done);?>/<?php echo esc_html($wpoffload);?>)</span></p>
    </div>
</div>
<?php 
if( in_array($get_package, ['developer', 'extended']) ){
?>
<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Restore offloaded CSS files', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('This tool will delete all CSS offloaded files information.', 'next3-offload');?></p>
        <a class="reset-button" href="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=css_settings#ntab=tools' ));?>"><?php echo esc_html__('Delete', 'next3-offload');?></a>
    </div>
</div>

<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Restore offloaded JS files', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('This tool will delete all JS offloaded files information.', 'next3-offload');?></p>
        <a class="reset-button" href="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=js_settings#ntab=tools' ));?>"><?php echo esc_html__('Delete', 'next3-offload');?></a>
    </div>
</div>

<?php }?>
<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Reset credentials', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('This tool will remove provider credentials of Next3 Offload', 'next3-offload');?></p>
        <a class="reset-button" href="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=clear_cache#ntab=tools' ));?>"><?php echo esc_html__('Reset', 'next3-offload');?></a>
    </div>
   
</div>

<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Reset settings', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('This tool will delete all the plugin settings of Next3 Offload', 'next3-offload');?></p>
        <a class="reset-button" href="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=clear_settings#ntab=tools' ));?>"><?php echo esc_html__('Reset', 'next3-offload');?></a>
    </div>
</div>

<?php do_action('next3aws-tools-content-after');?>  