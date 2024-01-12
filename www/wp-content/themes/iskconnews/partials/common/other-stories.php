<?php 
// get some info about the term queried
$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id; 
?>
<div class="related_content">
    <div class="title_wrap">
      <h5 class="title">Other Stories</h5>
</div>
            <div class="related_carousel relatedsingle owl-carousel">
              <?php 
                 global $paged;
                 $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                 $args = array('post_type' => 'post', 'posts_per_page' => 5, 'paged' => $paged);
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
                            <p class="cat">
                                <span><?php echo get_the_date( 'M d, Y' ); ?></span>
                            </p>
                            <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                            <p class="name"><?php the_field('author_name'); ?></p>
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
                            </figure>
                            <p class="cat"><span><?php echo get_the_date( 'M d, Y' ); ?></span></p>
                            <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                            <p class="name"><?php the_field('author_name'); ?></p>
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