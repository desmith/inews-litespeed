<?php 
    $url = site_url();
?>
<section class="isk_news">
    <div class="main-container">
        <div class="inner-container">
            <div class="title_wrap">
                <h5 class="title">ISKCON News</h5>
                  <a href="<?php echo $url; ?>/category/news/" class="viewall">View All</a>
            </div>

            <div class="list">
                    <?php //Get the correct taxonomy ID by id
                       $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                       $args = array(
                           'post_type' => 'post', 
                           'category_name' => 'news',
                           'post_status'       => 'publish',
                           'posts_per_page'    => 1,
                           'paged'             => $paged,
    					   'orderby' 		   => 'post_date'
                       );

                       $the_query = new WP_Query( $args );
                    ?> 
                    <?php if ( $the_query->have_posts() ) : ?>
                    <?php 
                    while ( $the_query->have_posts() ) : $the_query->the_post(); 
                         $banner_image = chk_for_img(get_field('banner_image'));
                    ?>   

                        <div class="left">
                            <figure style="background-image: url(<?php echo $banner_image; ?>);"></figure>
                               <div class="content">
                                   <div class="card">
                                       <div class="box">
                                           <div class="cat"><?php echo get_the_date( 'M d, Y' ); ?></span></div>
                                           <h6 class="ttl"><?php the_title(); ?></h6>
                                           <div class="name"><?php echo wp_kses_post(get_field('author_name')); ?></div>
                                           <div class="desc"><?php echo wp_kses_post(get_field('news_short_description')); ?></div>
                                       </div>
                                   </div>
                               </div>
                            <a href="<?php the_permalink() ?>" class="link"><img src="<?php bloginfo('template_url') ?>/assets/img/icons/blue-arrow.png"></a>
                        </div>

                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>  
                     <!-- end of the loop -->
                     <?php else:  ?>
                     <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                    <?php endif; ?>

                <div class="right">    
                    <div class="our_newscarousel single_slide owl-carousel">
                        <?php //Get the correct taxonomy ID by id
                           $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                           $args = array(
                               'post_type' => 'post', 
                               'category_name' => 'news',
                               'post_status'       => 'publish',
                               'posts_per_page'    => 5,
                               'paged'             => $paged,
                               'offset'			   => 1,
        					   'orderby' 		   => 'post_date'
                           );
                           $the_query = new WP_Query( $args );
                        ?> 
                        <?php if ( $the_query->have_posts() ) : ?>
                        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); 
                            $term_ids = wp_list_pluck(get_the_terms(get_the_ID(),'category'), 'term_id');
                            if(in_array('58', $term_ids)){ ?>

                            <div class="item">
                                <div class="card">
                                    <div class="box">
                                        <figure>
                                         <a href="javascript:void(0);" class="videoBoxBtn" data-vlink="<?php the_field('video_link'); ?>">
                                            <?php 
                                                get_template_part('partials/common/listing-image'); 
                                            ?> 
                                            <p class="play_btn">Play</p>                                                
                                          </a>
                                        </figure>
                                        <div class="cat">
                                            <span><?php echo get_the_date( 'M d, Y' ); ?></span>
                                        </div>
                                        <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                        <div class="name"><?php echo wp_kses_post(get_field('author_name')); ?></div>
                                    </div>
                                </div>
                            </div>

                          <?php }else{ ?>
                               <div class="item">
                                   <div class="card">
                                       <div class="box">
                                            <figure>
                                              <a href="<?php the_permalink() ?>">
                                               	<?php 
                                                    get_template_part('partials/common/listing-image'); 
                                                ?>													
											  </a>
                                            </figure>
                                            <div class="cat">
                                                <span><?php echo get_the_date( 'M d, Y' ); ?></span>
											</div>
                                           <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                           <div class="name"><?php echo wp_kses_post(get_field('author_name')); ?></div>
                                       </div>
                                   </div>
                               </div>
                          <?php } ?>  
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
    </div>
</section>