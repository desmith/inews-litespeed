<?php
// get some info about the term queried
$queried_object = get_queried_object();
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id;
?>

<?php //Get the correct taxonomy ID by id
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = array(
    'post_type'         => 'post',
    'post_status'       => 'publish',
    'posts_per_page'    => 9,
    'paged'             => $paged,
    'tax_query'         => array(
        array(
            'taxonomy'  => $taxonomy,
            'field'     => 'term_id',
            'terms'     => $term_id
        )
    )
);

$the_query = new WP_Query( $args );
?>

      <section class="most_popular">
               <div class="main-container">
                    <div class="inner-container">
                       <h5 class="title">Latest Content</h5>
                        <div class="card_list colm--4">

                           <?php if ( $the_query->have_posts() ) :
                              $ads = 0; ?>
                           <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                           <?php the_post();
                             $term_ids = wp_list_pluck(get_the_terms(get_the_ID(),'category'), 'term_id');
                             ?>

                              <?php if($ads == 6){ ?>
                                 <div class="card card_ads">
                                   <?php echo wp_kses_post(get_field('latest_content_section_ad', 5)); ?>
                                   <div class="ad_desc">AD</div>
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
                                                <div class="name"><?php echo wp_kses_post(get_field('author_name')); ?></div>
                                                <div class="cat">
                                                     <?php
                                                         $cat_name = 'category';
                                                         $cat = '';
                                                         $categories = get_the_terms( $post->ID, $cat_name );
                                                         foreach($categories as $category) {
                                                          if($category->parent){
                                                              //$cat .= $category->name.' | ';
                                                             $cat .= '<a class="linktag" href="'.esc_attr( esc_url( get_category_link( $category->term_id ) ) ).'">'.$category->name . '</a>';
                                                           }
                                                         }
                                                         echo $cat;
                                                     ?>
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
                                    </figure>

                                    <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h6>
                                    <div class="name"><?php echo wp_kses_post(get_field('author_name')); ?></div>
                                    <div class="cat">
                                      <?php
                                          $cat_name = 'category';
                                          $cat = '';
                                          $categories = get_the_terms( $post->ID, $cat_name );
                                          foreach($categories as $category) {
                                           if($category->parent){
                                               //$cat .= $category->name.' | ';
                                              $cat .= '<a class="linktag" href="'.esc_attr( esc_url( get_category_link( $category->term_id ) ) ).'">'.$category->name . '</a>';
                                            }
                                          }
                                       echo $cat;
                                      ?>
                                    </div>
                                 </div>
                              </div>
                              <?php } ?>
                           <!-- post -->

                           <?php $ads++; endwhile; ?>
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
               </div>
      </section>
