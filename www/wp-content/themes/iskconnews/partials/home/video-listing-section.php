<section class="isk_ourvid">
        <div class="main-container">
            <div class="inner-container">
                    <h5 class="title">Our Videos</h5>
                    <!-- <a href="videos/" class="viewall">View All</a> -->

                <div class="vidbox">
                    <div class="our_vidcarousel single_slide owl-carousel">
                        <?php 
                           $args = array(
                                   'post_type'         => 'post', 
                                   'category_name'     => 'video',
                                   'post_status'       => 'publish',
                                   'posts_per_page'    => 10,
                                   'orderby'           => 'post_date'
                                   );
                           $the_query = new WP_Query( $args );
                        ?> 
                        <?php if ( $the_query->have_posts() ) : ?>
                        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>   
                            <?php //$banner_image = chk_for_img(get_field('banner_image')); ?>
                            <?php $banner_image = get_field('banner_image') ? get_field('banner_image') : get_stylesheet_directory_uri().'/assets/img/placeholder.jpg'; ?>
                            <div class="item">
                                <figure>
                                <a href="javascript:void(0);" class="play_btn videoBoxBtn" data-vlink="<?php the_field('video_link'); ?>"><img src="<?php echo $banner_image; ?>" alt="big-img" /></a>
                                </figure>
                                <div class="content">
                                    <div class="cat"><?php echo wp_kses_post(get_field('author_name')); ?></div>
                                    <h5 class="sttl"><?php the_title() ?></h5>
                                    <a href="javascript:void(0);" class="play_btn videoBoxBtn" data-vlink="<?php the_field('video_link'); ?>">
                                      <span>Play</span>
                                    </a>
                                </div>
                            </div>

                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>  
                         <!-- end of the loop -->
                         <?php else:  ?>
                         <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
</section>
