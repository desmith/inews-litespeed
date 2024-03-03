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
                                                <p class="name"><?php echo wp_kses_post(get_field('author_name')); ?></p>
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
                                                <p class="name"><?php echo wp_kses_post(get_field('author_name')); ?></p>
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
