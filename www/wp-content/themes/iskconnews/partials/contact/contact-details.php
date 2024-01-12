<div class="pepoleOp_wrap">
    <div class="title_wrap">
        <h5 class="title"><?php the_title(); ?></h5>
    </div>
        <div class="about_content contact">
            <div class="iskn">
                <?php
                   get_template_part('partials/common/banner-section'); 
                ?>

                <?php //echo do_shortcode('[contact_request]'); ?>
                <?php echo do_shortcode('[wpforms id="24521" title="false"]'); ?>

            </div>   
        </div>                      
</div>
