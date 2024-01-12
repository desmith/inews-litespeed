<?php 
// get some info about the term queried
$queried_object = get_queried_object(); 
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id; 
?>

<section class="isk_news">
    <div class="main-container">
        <div class="inner-container">
            <div class="title_wrap">
                <h5 class="title"><?php echo get_queried_object()->name; ?></h5>
                  <form class="allForm">
                    <div class="form-element form-select">
                        <?php $terms = get_terms('category', array( 'parent' => 1, 'orderby' => 'slug', 'hide_empty' => false ) ); 
                         ?>
                          <select onchange="location = this.value;">
                                <option value="<?php bloginfo('url'); ?>/category/news/">Select topic</option>
                            <?php foreach ($terms as $term) { ?>
                                <option value = "<?= get_term_link($term->term_id); ?>" <?= ($term->term_id == $term_id) ? 'selected':''; ?>><?= $term->name; ?></option>
                            <?php } ?>
                          </select>
                    </div>
                  </form>
            </div>

        <div class="list">
                    <?php //Get the correct taxonomy ID by id
                       $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                       $args = array(
                                        'post_type'         => 'post',
                                        'post_status'       => 'publish',
                                        'posts_per_page'    => 1,
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
                    <?php if ( $the_query->have_posts() ) : ?>
                    <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>   
                        <?php  
                            $term_ids = wp_list_pluck(get_the_terms(get_the_ID(),'category'), 'term_id');
                            $banner_image = chk_for_img(get_field('banner_image'));
                            if(in_array('58', $term_ids)){ ?>
                                
                                <div class="left">
                                        <figure>
                                         <a href="javascript:void(0);" class="videoBoxBtn" data-vlink="<?php the_field('video_link'); ?>">
                                            <img src="<?php echo $banner_image; ?>" alt="vid-img"> 
                                            <p class="play_btn">Play</p>                                                
                                          </a>
                                        </figure>
                                        <div class="content">
                                            <div class="card">
                                                <div class="box">
                                                   <div class="cat"><?php echo get_the_date( 'M d, Y' ); ?></span></div>
                                                   <h6 class="ttl"><?php the_title(); ?></h6>
                                                   <div class="name"><?php the_field('author_name'); ?></div>
                                                   <div class="desc"><?php the_field('news_short_description'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="<?php the_permalink() ?>" class="link"><img src="<?php bloginfo('template_url') ?>/assets/img/icons/blue-arrow.png" alt="arrow"></a>
                                </div>
                            <?php }else{ ?>

                            <div class="left">
                                <figure>
                                    <img src="<?php echo $banner_image; ?>" alt="News Image">
                                </figure>
                                   <div class="content">
                                       <div class="card">
                                           <div class="box">
                                               <div class="cat"><?php echo get_the_date( 'M d, Y' ); ?></span></div>
                                               <h6 class="ttl"><?php the_title(); ?></h6>
                                               <div class="name"><?php the_field('author_name'); ?></div>
                                               <div class="desc"><?php the_field('news_short_description'); ?></div>
                                           </div>
                                       </div>
                                   </div>
                                <a href="<?php the_permalink() ?>" class="link"><img src="<?php bloginfo('template_url') ?>/assets/img/icons/blue-arrow.png" alt="arrow"></a>
                            </div>

                        <?php } ?>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>  
                     <!-- end of the loop -->
                     <?php else:  ?>
                     <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                    <?php endif; ?>

                    <div class="right">
                        <div class="our_newscarousel">
                           <div class="item">
                                <?php //Get the correct taxonomy ID by id
                                   $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

                                   $args = array(
                                                'post_type'         => 'post',
                                                'post_status'       => 'publish',
                                                'posts_per_page'    => 1,
                                                'offset' => 1,
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
                                <?php if ( $the_query->have_posts() ) : ?>
                                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                                <?php  
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
                                                <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                                <div class="name"><?php the_field('author_name'); ?></div>
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
                                           <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                           <div class="name"><?php the_field('author_name'); ?></div>
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

    </div>
</section>