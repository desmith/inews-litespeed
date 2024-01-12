<?php 
// get some info about the term queried
$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id; 
?>
<div class="pepoleOp_wrap">
    <div class="title_wrap">
        <h5 class="title">Video</h5>
    </div>
        <div class="big_vidarea">
            <?php //Get the correct taxonomy ID by id
               $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

               $args = array(
                   'post_type'         => 'post',
                   'category_name'     => 'video',
                   'posts_per_page'    => 1,
                   'paged'             => $paged,
                   'orderby'           => 'post_date'
               );
               $the_query = new WP_Query( $args );?> 
            <?php if ( $the_query->have_posts() ) : ?>
            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                <?php the_post(); ?>
               <?php $banner_image = chk_for_img(get_field('banner_image')); ?>

                <div class="item">
                    <figure>
                        <img src="<?php echo $banner_image; ?>" alt="big-img" />
                    </figure>
                    <div class="content">
                        <div class="cat"><?php the_field('author_name'); ?></div>
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

    <div class="card_list colm--3 video--card">

        <?php //Get the correct taxonomy ID by id
           $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

           $args = array(
               'post_type'         => 'post',
               'category_name'     => 'video',
               'post_status'       => 'publish',
               'posts_per_page'    => 9,
               'paged'             => $paged,
			   'orderby' 		   => 'post_date'
           );
           $the_query = new WP_Query( $args );
        ?> 
        <?php if ( $the_query->have_posts() ) : ?>
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				
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
                    </div>
                </div>
        
        <?php endwhile; ?>
        <?php wp_reset_postdata(); ?>  
         <!-- end of the loop -->
         <?php else:  ?>
         <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
        <?php endif; ?>  
        </div> 
        <!----------------------- Pagination ----------------------------->
        <div class="pages">
            <?php 
                $big = 999999999; // need an unlikely integer
                echo paginate_links( array(
                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $the_query->max_num_pages,
                'show_all' => FALSE, //this will make paginate not to show all links.
                'end_size' => 2, //will show 2 numbers on either the start and the end list edges. 
                'mid_size' => 0 //so that you won't have 1,2...,3,...,7,8
            ) );
            ?>
        </div>

<!---------------------------------------------------------------->           
         
</div>
