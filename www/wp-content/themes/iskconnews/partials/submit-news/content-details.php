<div class="pepoleOp_wrap">
    <div class="title_wrap">
        <h5 class="title">How to Submit Articles to ISKCON News</h5>
        <a href="mailto:<?php the_field('submit_email_id'); ?>" target="_blank" class="btn hvr:outline bg--yellow c--white hvr:bg--blue hvr:c--white"><img src="<?php bloginfo('template_url') ?>/assets/img/icons/mailW.svg">Email Us</a>
    </div>
                        
    <div class="about_content submit_cont submit_container_new">
        <div class="iskn">
            <?php
                get_template_part('partials/common/banner-section'); 
            ?> 
            <h5 class="title">Submit Your Story!</h5>
            <?php the_field('main_section_content'); ?>                                   
        </div>

        <p class="mailbtn"> <a href="mailto:<?php the_field('submit_email_id'); ?>" target="_blank" class="btn hvr:outline bg--yellow c--white hvr:bg--blue hvr:c--white"><img src="<?php bloginfo('template_url') ?>/assets/img/icons/mailW.svg">Email Us</a></p>
    </div>
</div>
