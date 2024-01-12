<div class="pepoleOp_wrap isk_opinion about__opinion">
    <div class="title_wrap">
      <h5 class="title"><?php the_title(); ?></h5>
    </div>
    <div class="about_content list">
        <div class="left">
            <div class="main_vidcarousel single_slide owl-carousel about__slide">
                <?php 
                    $id = get_the_ID();
                    if( have_rows('add_slider') ):
                    while ( have_rows('add_slider') ) : the_row();
                        
                    $video_link  = get_sub_field('video_link',$id);
                    $video_banner_image  = get_sub_field('video_banner_image',$id);


                 /* if($video_banner_image != '') { ?>
                    <div class="item">
                            <div class="box">
                                <?php
                                    printf( '<img src="%s" alt="%s" title="%s" />', 
                                    $video_banner_image['url'], $video_banner_image['alt'], $video_banner_image['title'] );
                                ?>
                                <div class="content_wrap">
                                    <div class="btnG">
                                        <a href="javascript:void(0);" class="play_btn videoBoxBtn" data-vlink="<?php echo $video_link; ?>">
                                            <span>Play</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                    </div>
                    
		    <?php } else {  */ ?>

                    <!--
                    <div class="item">
                            <div class="box">
                                <?php
		    
                                    $bannerimage  = get_sub_field('banner_image');
                                    printf( '<img src="%s" alt="%s" title="%s" />', 
                                    $bannerimage['url'], $bannerimage['alt'], $bannerimage['title'] );
		               ?>
                                <div class="content_wrap">
                                    <h1 class="title"><?php echo get_sub_field('banner_text'); ?></h1>
                                </div>
                            </div>
		    </div>
                    -->


                <?php  // }
                    endwhile;
                    endif;
                ?> 
                
            </div>
        </div>

        <div class="about__description">
            <div class="submit_accord accord">
                <div class="box">
                    <h5 class="accord-btn actv">ISKCON</h5>
                    <div class="accord-target textblt" style="display: block;">
                        <?php the_field('top_section_content'); ?>
                    </div>
                </div>
                <div class="box">
                    <h5 class="accord-btn">Founder</h5>
                    <div class="accord-target textblt">
                        <?php the_field('middle_section_content'); ?>
                    </div>
                </div>
                <div class="box">
                    <h5 class="accord-btn">Our Team</h5>
                    <div class="accord-target textblt">
                        <?php the_field('team_section_top_content'); ?>
                        <div class="team">
                            <?php 
                                if( have_rows('team_section_main_content') ):
                                while ( have_rows('team_section_main_content') ) : the_row();
                            ?>

                                <div class="member">
                                    <figure>
                                        <?php
                                            $image  = get_sub_field('nwimage');
                                            printf( '<img src="%s" alt="%s" title="%s" />', 
                                            $image['url'], $image['alt'], $image['title'] );
                                        ?>
                                    </figure>
                                    <article>
                                        <h2><?php echo get_sub_field('name'); ?></h2>
                                        <h3><?php echo get_sub_field('designation'); ?></h3>
                                        <?php echo get_sub_field('details_content'); ?>
                                    </article>
                                </div>
                            
                            <?php 
                                endwhile;
                                endif;
                            ?>  

                            <?php //the_field('buttom_section_content'); ?>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
