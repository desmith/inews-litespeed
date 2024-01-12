<section class="most_popular">
       <div class="main-container">
           <div class="inner-container">

               <h5 class="title">Latest Content</h5>
               <div class="card_list colm--4">

                    <?php
                        $args = array(
                                 'post_type' => 'post',
                                 'post_status' => 'publish',
                                 //'order' => 'Desc',
                                 'orderby' => 'post_date',
                                 'posts_per_page' => 10
                            );
                       $the_query = new WP_Query( $args );
                    ?>
                    <?php if ( $the_query->have_posts() ) : ?>
                    <?php $i = 0; ?>
                    <?php while ( $the_query->have_posts() ) : $the_query->the_post();
                        $term_ids = wp_list_pluck(get_the_terms(get_the_ID(),'category'), 'term_id'); ?>

                    <?php if($i == 6){ ?>
                        <div class="card card_ads">
                            <?php the_field('latest_content_section_ad', 5); ?>
                            <div class="ad_desc">New Vrindaban Guest House AD</div>
                        </div>
                    <?php } ?>

                    <?php if(in_array('58', $term_ids)){ ?>
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
                                <div class="name"><?php the_field('author_name'); ?></div>
                                <!--<p class="cat">
                                    <?php
                                       $cat_name = 'category';
                                       $cat = '';
                                       $categories = get_the_terms( $post->ID, $cat_name );
                                       foreach($categories as $category) {
                                           $cat .= $category->name.' | ';
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
                                <div class="name"><?php the_field('author_name'); ?></div>
                                <!--<p class="cat">
                                    <?php
                                       $cat_name = 'category';
                                       $cat = '';
                                       $categories = get_the_terms( $post->ID, $cat_name );
                                       foreach($categories as $category) {
                                           $cat .= $category->name.' | ';
                                       }
                                       echo rtrim($cat,' | ');
                                    ?>
                                </p>-->
                            </div>
                        </div>
                    <?php } ?>
                    <?php $i++; endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                     <!-- end of the loop -->
                    <?php else:  ?>
                      <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                    <?php endif; ?>

               </div>
           </div>
       </div>
</section>
