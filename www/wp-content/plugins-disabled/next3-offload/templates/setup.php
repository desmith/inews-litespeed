<div class="next3offload-settings-wrap">
    <h2> <?php esc_html_e('Next3 Setup', 'next3-offload');?> </h2>
    <?php
    $credentials = next3_credentials();
    ?>
    <div class="next3offload-body">
        <div class="next3offload-steps" >
            <div class="next3-progressbars-wrap">
                <div class="next3-progressbar-show">
                <?php 
                if($stepno == 1){
                    echo esc_html('STEP 1/3: Active License');
                }else if( $stepno == 2){
                    echo esc_html('STEP 2/3: Choose Provider');
                } else if( $stepno == 3 ){
                    echo esc_html('STEP 3/3: Configuration');
                }
                ?>
                </div>
                <?php if( !empty($step_msg) ){?><p class="error-print"><?php echo esc_html($step_msg);?></p><?php }?>
                
            </div>
            <div class="offload-content">
                <?php 
                switch($step){

                    case 'license':
                        $status = \Next3Offload\Utilities\Check\N3aws_Valid::instance()->_get_action();
                        $key_data = next3_get_option('__validate_author_next3aws_keys__', '');
                        $data = \Next3Offload\Utilities\Check\N3aws_Valid::instance()->get_pro($key_data);
                        $typeitem = isset($data->typeitem) ? $data->typeitem : '';

                        include( next3_core()->plugin::plugin_dir().'templates/step/license.php' );
                        break;
                    
                    case 'provider':
                        include( next3_core()->plugin::plugin_dir().'templates/step/providers.php' );
                        break;
                    
                    case 'config':
                        include( next3_core()->plugin::plugin_dir().'templates/step/config.php' );
                        break;
                    
                    default:
                        include( next3_core()->plugin::plugin_dir().'templates/step/dashboard.php' );
                        break;
                }
                ?>
            </div>
        </div>     
    </div>        
</div>