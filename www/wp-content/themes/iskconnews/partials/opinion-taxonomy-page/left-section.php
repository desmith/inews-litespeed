<?php
// get some info about the term queried
$queried_object = get_queried_object();
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id;
?>
<div class="pepoleOp_wrap">
    <div class="title_wrap">
        <h5 class="title">Opinion</h5>
    </div>

    <div class="card_list colm--3">

        <?php //Get the correct taxonomy ID by id
           $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

           $args = array(
               'post_type'         => 'post',
               'category_name'     => 'opinion',
               'post_status'       => 'publish',
               'posts_per_page'    => 9,
               'paged'             => $paged,
			   'orderby' 		   => 'post_date'
           );
           $the_query = new WP_Query( $args );
        ?>
        <?php if ( $the_query->have_posts() ) : ?>
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
			<?php the_post();
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
                              <?php
                                /*** We don't need the contributor image displayed at this time.
                              ?>
                              <div class="auth">
                                <?php
                                    $image  = get_field('author_image');
                                    if($image){
                                        printf( '<img src="%s" alt="%s" title="%s" />',
                                        $image['url'], $image['alt'], $image['title'] );
                                    }else{
                                        printf( '<img src="%s" alt="Iskcon" title="Iskcon" />',
                                        get_stylesheet_directory_uri().'/assets/img/placeholder-logo.jpg' );
                                    }
                                ?>
                               </div>
                               <?php */ ?>
                            </figure>

                            <div class="inner_box">
                                <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                <div class="name"><?php the_field('author_name'); ?></div>
                                <div class="desc"><?php the_field('news_short_description'); ?></div>
                            </div>
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
                              <?php
                              /*** We don't need the contributor image displayed at this time.
                              ?>
                              <div class="auth">
                                <?php
                                    $image  = get_field('author_image');
                                    if($image){
                                        printf( '<img src="%s" alt="%s" title="%s" />',
                                        $image['url'], $image['alt'], $image['title'] );
                                    }else{
                                        printf( '<img src="%s" alt="Iskcon" title="Iskcon" />',
                                        get_stylesheet_directory_uri().'/assets/img/placeholder-logo.jpg' );
                                    }
                                ?>
                               </div>
                               <?php */ ?>
                            </figure>
                            <div class="inner_box">
                                <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                <div class="name"><?php the_field('author_name'); ?></div>
                                <div class="desc"><?php the_field('news_short_description'); ?></div>
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
