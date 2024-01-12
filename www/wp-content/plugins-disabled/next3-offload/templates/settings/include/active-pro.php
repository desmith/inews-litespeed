<div class="nextactive-license">
    <div class="nextactive_container" >
        <form action="" method="POST" class="nextactive-form" id="nextactive-form">
            <div class="ins-active-license">
                <ol class="nextul">
                    <li> <?php echo esc_html__('At first go to ', 'next3-offload');?> <a href="http://account.themedev.net/" target="_blank"> <?php echo esc_html('ThemeDev Account', 'next3-offload');?> </a></li>
                    <li> <?php echo esc_html__('Login your account ', 'next3-offload');?> <a href="http://account.themedev.net/?views=login" target="_blank"> <?php echo esc_html('Login', 'next3-offload');?> </a> </li>
                    <li> <?php echo esc_html__('Go to Products page ', 'next3-offload');?> <a href="http://account.themedev.net/?views=products" target="_blank"> <?php echo esc_html(' Product List', 'next3-offload');?></a> </li>
                    <li> <?php echo esc_html__('Click licenses button and get license key after add your domain', 'next3-offload');?> </li>
                    <li> <?php echo esc_html__('Copy licenses key and paste key in license filed. Then click "Active License" Button', 'next3-offload');?> </li>
                    <li> <?php echo esc_html__('If don\'t have any license key, Just buy click now button and collect license key.', 'nextcode');?> <a href="https://www.themedev.net/next3-offload-media/" target="_blank"> <?php echo esc_html(' Buy Now', 'nextcode');?> </a></li>
                </ol>
            </div>
            <div class="ins-active-license">
                <?php if($status == 'active'){?>
                    <div class="nextactive-message next-success"><a href="" class="__revoke_license" data-keys="<?php echo esc_attr($key_data);?>" >  <?php echo esc_html('Click to Revoke License. ', 'nextcode');?> <?php if($typeitem == 'check'){ echo esc_html__('This license work only for 10 days.', 'nextcode');}?></a></div>
                <?php }else{?>
                    <div class="license-key">
                    <label for="_license_key">
                        <input type="text" name="key_license" id="key_license" class="license-input" placeholder="<?php echo esc_attr('Please paste your license key', 'next3-offload'); ?>" value="<?php echo esc_attr($key_data);?>">
                    </label>
                    <button type="submit" name="_active_license" class="next_active_license-button"><?php echo esc_html('Active License', 'next3-offload');?> </button>
                </div>
                <div class="nextactive-message"></div>
                 <?php }?>  
            </div>
        </form>
    </div>  
</div>