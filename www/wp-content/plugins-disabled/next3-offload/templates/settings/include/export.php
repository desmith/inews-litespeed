<?php
    $msg = isset($_GET['msg']) ? sanitize_text_field($_GET['msg']) : '';
    switch($msg){
        case 'export_aws_buckets':
            echo next3_print('<h4> '. esc_html__('Please select buckets...', 'next3-offload') .' </h4>');
        break;
        case 'export_aws_notfound':
            echo next3_print('<h4> '. esc_html__('Not found any media...', 'next3-offload') .' </h4>');
        break;
        case 'export_wp_types':
            echo next3_print('<h4> '. esc_html__('Please select type for export media...', 'next3-offload') .' </h4>');
        break;
        case 'imports_aws':
            echo next3_print('<h4> '. esc_html__('AWS Import process running...', 'next3-offload') .' </h4>');
        break;
        
    }

?>

<?php do_action('next3aws-export-content-before');?> 

<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Export media [AWS]', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('Export all AWS S3 files to must be select Bucket.', 'next3-offload');?></p>
        <form name="next3_wp_upload" action="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=export_aws#ntab=imports' ));?>" method="post">

            <div class="files-table">
                
                 <div class="action-section">
                     <?php 
                     $buckets = $this->get_buckets();
                     ?> 
                     <select name="tool_buckets">
                        <option value=""><?php echo esc_html__('Select any bucket', 'next3-offload');?></option>
                         <?php
                         if( !empty($buckets) ){
                            foreach($buckets as $v){
                                ?>
                                <option value="<?php echo esc_attr($v);?>"><?php echo esc_html($v);?></option>
                                <?php
                            }
                         }
                         ?>
                    </select>    
                     
                    <button type="submit" class="reset-button success"><?php echo esc_html__('Export', 'next3-offload');?></button>

                </div>
            </div>

            
        </form>
    </div>
</div>

<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Export media [WP]', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('Export all WP Media files to must be select Type(All media, Already Uploaded to AWS, Not upload to AWS).', 'next3-offload');?></p>
        <form name="next3_wp_upload" action="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=export_wp#ntab=imports' ));?>" method="post">

            <div class="files-table">
                
                 <div class="action-section">
                     <select name="tool_buckets">
                        <option value=""><?php echo esc_html__('Select Type', 'next3-offload');?></option>
                        <option value="all"><?php echo esc_html__('All', 'next3-offload');?></option>
                        <option value="unuploaded_aws"><?php echo esc_html__('!Upload AWS', 'next3-offload');?></option>
                        <option value="uploaded_aws"><?php echo esc_html__('Uploaded AWS', 'next3-offload');?></option>
                    </select>    
                     
                    <button type="submit" class="reset-button success"><?php echo esc_html__('Export', 'next3-offload');?></button>

                </div>
            </div>

            
        </form>
    </div>
</div>


<div class="next3aws-admin-toolbox-item">
    <h3 class="next3aws-toolbox-heading"><?php echo esc_html__('Import files [AWS]', 'next3-offload');?></h3>
    <div class="next3aws-footer-content">
        <p><?php echo esc_html__('Select [.json] file to upload into AWS S3 Bucket.', 'next3-offload');?></p>
        <form name="next3_wp_upload" action="<?php echo esc_url(next3_admin_url( 'admin.php?page=next3aws&nx3_action=import_json#ntab=imports' ));?>" method="post" enctype="multipart/form-data">

            <div class="files-table">
                
                 <div class="action-section">
                    <input type="file" name="import_files" accept="application/JSON">
                    <?php 
                     $buckets = $this->get_buckets();
                     ?> 
                     <select name="tool_buckets">
                        <option value=""><?php echo esc_html__('Select any bucket', 'next3-offload');?></option>
                         <?php
                         if( !empty($buckets) ){
                            foreach($buckets as $v){
                                ?>
                                <option value="<?php echo esc_attr($v);?>"><?php echo esc_html($v);?></option>
                                <?php
                            }
                         }
                         ?>
                    </select> 
                    <button type="submit" class="reset-button success"><?php echo esc_html__('Import', 'next3-offload');?></button>
                </div>
            </div>

            
        </form>
        <div class="next3-import-history"></div>
    </div>
</div>

<?php do_action('next3aws-export-content-after');?> 