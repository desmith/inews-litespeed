<?php
    $url = site_url();
?>
<section class="most_popular isk_people">
       <div class="main-container">
           <div class="inner-container">
               <div class="title_wrap">
                        <h5 class="title">People</h5>
                        <a href="<?php echo $url; ?>/category/people/" class="viewall">View All</a>
                    </div>

               <div class="card_list colm--4">
                    <?php
                        $args = array(
                                 'post_type' => 'post',
                                 'category_name' => 'people',
                                 'post_status' => 'publish',
                                 'order' => 'Desc',
                                 'posts_per_page' => 4
                            );
                       $the_query = new WP_Query( $args );
                    ?>
                    <?php if ( $the_query->have_posts() ) : ?>
                    <?php while ( $the_query->have_posts() ) : $the_query->the_post();
                        $term_ids = wp_list_pluck(get_the_terms(get_the_ID(),'category'), 'term_id');
                        if(in_array('58', $term_ids)){ ?>
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

                                    <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                    <div class="name"><?php echo wp_kses_post(get_field('author_name')); ?></div>
                                    <!--<p class="cat">
                                        <?php
                                               $cat_name = 'category';
                                               $cat = '';
                                               $categories = get_the_terms( $post->ID, $cat_name );
                                               foreach($categories as $category) {
                                                 if($category->parent){
                                                    $cat .= $category->name.' | ';
                                                 }
                                               }
                                               echo rtrim($cat,' | ');
                                        ?>
                                    </p>-->
                                </div>
                            </div>
                        <?php }else{ ?>
                            <div class="card">
                                <div class="box">
                                    <figure>
                                        <a href="<?php the_permalink() ?>">
                                            <?php
                                                get_template_part('partials/common/listing-image');
                                            ?>
                                        </a>
                                    </figure>

                                    <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                    <div class="name"><?php echo wp_kses_post(get_field('author_name')); ?></div>
                                    <!--<p class="cat">
                                        <?php
                                           $cat_name = 'category';
                                           $cat = '';
                                           $categories = get_the_terms( $post->ID, $cat_name );
                                           foreach($categories as $category) {
                                             if($category->parent){
                                                $cat .= $category->name.' | ';
                                             }
                                           }
                                           echo rtrim($cat,' | ');
                                        ?>
                                    </p>-->
                                </div>
                            </div>
                        <?php } 
                   
			//$i++; 
			endwhile; 
		  
                    wp_reset_postdata(); ?>
                     <!-- end of the loop -->
                    <?php else:  ?>
                      <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                    <?php endif; ?>

               </div>
           </div>
       </div>
</section>
