<!--<?php
    $news_post           = get_posts( array(
        'fields'         => 'ids', // only return post ID´s
        'posts_per_page' => '1',
        'post_type'      => 'post',
        'category_name' => 'news',
    ));

    $outreach_activism_post = get_posts( array(
        'fields'            => 'ids', // only return post ID´s
        'posts_per_page'    => '1',
        'post_type'         => 'post',
        'category_name' => 'outreach-activism',
    ));

    $people_post         = get_posts( array(
        'fields'         => 'ids', // only return post ID´s
        'posts_per_page' => '1',
        'post_type'      => 'post',
        'category_name' => 'people',
    ));

    $lifestyle_post = get_posts( array(
        'fields'         => 'ids', // only return post ID´s
        'posts_per_page' => '1',
        'post_type'      => 'post',
        'category_name' => 'lifestyle',
    ));

    $arts_post = get_posts( array(
        'fields'         => 'ids', // only return post ID´s
        'posts_per_page' => '1',
        'post_type'      => 'post',
        'category_name' => 'arts',
    ));

    $opinion_post = get_posts( array(
        'fields'         => 'ids', // only return post ID´s
        'posts_per_page' => '1',
        'post_type'      => 'post',
        'category_name' => 'opinion',
    ));

    $videos_post = get_posts( array(
        'fields'         => 'ids', // only return post ID´s
        'posts_per_page' => '1',
        'post_type'      => 'post',
        'category_name' => 'video',
    ));

    $merged_post_ids = array_merge( $news_post, $outreach_activism_post, $people_post, $lifestyle_post, $arts_post, $opinion_post, $videos_post );

    $wp_query = new WP_Query( array(
    'post_type' => 'post', // any post type
    'post__in'  => $merged_post_ids, // our merged queries
) );
?>-->

<section class="isk_opinion">
   <div class="main-container">
       <div class="inner-container">
           <div class="list">
                <div class="left">
                    <div class="main_vidcarousel single_slide owl-carousel">

                        <?php
                            $args = array(
                                     'post_type' => 'post',
                                     'posts_per_page' => 10,
                                     'post_status' => 'publish',
                                     'order' => 'post_date'
                                    );
                               $the_query = new WP_Query( $args );

                                if ( $wp_query->have_posts() ) :
                                while ( $wp_query->have_posts() ) : $wp_query->the_post();
                                    //$term = get_the_terms(get_the_ID(),'category');
                                    //$link = get_term_link($term[0]->term_id);
                                    //$banner_image = get_field('banner_image') ? get_field('banner_image') : get_stylesheet_directory_uri().'/assets/img/placeholder.jpg';
                                    $banner_image = chk_for_img(get_field('banner_image'));


                                    if($term[0]->term_id == '58'){ ?>
                                        <div class="item">
                                            <div class="box box_vid">
                                                <div class="video_wrapper video_wrapper_full js-videoWrapper flex-video">
                                                    <iframe class="videoIframe js-videoIframe" src="" frameborder="0" allowtransparency="true" allowfullscreen="" data-src="<?php the_field('video_link'); ?>?enablejsapi=1&amp;autoplay=1&amp;modestbranding=1&amp;rel=0&amp;hl=en&amp;showinfo=0&amp;color=white"></iframe>
                                                    <button class="videoPoster js-videoPoster" style="background-image:url(<?php echo $banner_image; ?>);"></button>
                                                </div>
                                                <div class="content_wrap">
                                                    <h1 class="title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
                                                    <div class="btnG">
                                                      <a href="<?php the_permalink() ?>" class="btn hvr:outline bg--yellow c--black hvr:bg--transparent hvr:c--yellow">View Article</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="item">
                                            <div class="box">
                                                <img src="<?php echo $banner_image; ?>" alt="big-img" />
                                                <div class="content_wrap">
                                                    <h1 class="title"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
                                                    <div class="btnG">
                                                        <a href="<?php the_permalink() ?>" class="btn hvr:outline bg--yellow c--black hvr:bg--transparent hvr:c--yellow">View Article</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                        <?php endwhile; ?>
                        <?php wp_reset_postdata(); ?>
                         <?php else:  ?>
                         <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="right opside">
                    <h1 class="title">Most Popular</h1>
                        <div class="opauth_list">

                            <?php
                                $args = array(
                                     'post_type' => 'post',
                                     'posts_per_page' => 10,
                                     'post_status' => 'publish',
                                     'meta_key' => 'wpb_post_views_count',
                                     'orderby' => 'meta_value_num',
                                     'order' => 'DESC'
                                    );
                               $the_query = new WP_Query( $args );
                            ?>
                            <?php if ( $the_query->have_posts() ) : ?>
                            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                                <?php
                                    $term_ids = wp_list_pluck(get_the_terms(get_the_ID(),'category'), 'term_id');
                                    if(in_array('58', $term_ids)){ ?>

                                        <div class="box">
                                            <div class="img">
                                                <figure>
                                                    <a href="javascript:void(0);" class="videoBoxBtn" data-vlink="<?php the_field('video_link'); ?>">
                                                        <?php
                                                            get_template_part('partials/common/listing-image');
                                                        ?>
                                                        <p class="play_btn">Play</p>
                                                    </a>
                                                </figure>
                                            </div>
                                            <div class="t_name">
                                                <h1 class="sttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
                                                <p class="name"><?php the_field('author_name'); ?></p>
                                            </div>
                                            <span class="related_ic">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M15 8v8H5V8h10m1-2H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4V7c0-.55-.45-1-1-1z"/></svg>
                                            </span>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="box">
                                            <div class="img">
                                                <figure>
                                                    <a href="<?php the_permalink() ?>">
                                                        <?php
                                                            get_template_part('partials/common/listing-image');
                                                        ?>
                                                    </a>
                                                </figure>
                                            </div>
                                            <div class="t_name">
                                                <h1 class="sttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>
                                                <p class="name"><?php the_field('author_name'); ?></p>
                                            </div>
                                            <span class="related_ic">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg>
                                            </span>
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

                <section class="most_popular">
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
                </section>

            </div>
       </div>
   </div>
</section>
