<div class="pepoleOp_wrap">
        <div class="title_wrap">
            <h5 class="title">How to Submit Articles to ISKCON News</h5>
            <a href="mailto:<?php the_field('submit_email_id'); ?>" target="_blank" class="btn hvr:outline bg--yellow c--white hvr:bg--blue hvr:c--white"><img src="<?php bloginfo('template_url') ?>/assets/img/icons/mailW.svg">Email Us</a>
        </div>
                        
                        <div class="about_content submit_cont">
                                <div class="iskn">
                                    <?php
                                        get_template_part('partials/common/banner-section'); 
                                    ?> 
        <h5 class="title">Submit Your Story!</h5>
                                    <?php the_field('top_section_content'); ?>                                   


        <?php //echo do_shortcode('[story_form_request]'); ?>
                                </div>

                                <div class="submit_accord accord">
                                    <?php 
                                        if( have_rows('accord_section_content', 99) ):
                                        while ( have_rows('accord_section_content', 99) ) : the_row();
                                    ?>
                                        <div class="box">
                                            <h5 class="accord-btn"><?php echo get_sub_field('question'); ?></h5>
                                            <div class="accord-target textblt">
                                                <?php echo get_sub_field('answer'); ?>
                                            </div>
                                        </div>
                                    <?php 
                                        endwhile;
                                        endif;
                                    ?> 
                                    
                                </div>

                                <?php the_field('bottom_section_content'); ?>

                                <p class="mailbtn"> <a href="mailto:<?php the_field('submit_email_id'); ?>" target="_blank" class="btn hvr:outline bg--yellow c--white hvr:bg--blue hvr:c--white"><img src="<?php bloginfo('template_url') ?>/assets/img/icons/mailW.svg">Email Us</a></p>
                            </div>
</div>
