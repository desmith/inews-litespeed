<?php

get_header();
get_template_part('partials/common/menu');
global $wp_query;
$queried_object = get_queried_object();
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id;
?>

<main class="pg_arts pg_OA">
    <section class="most_popular">
        <div class="main-container">
            <div class="inner-container">

                <div class="peopleOp_wrap">
                    <h5 class="title"> <?php echo $wp_query->found_posts; ?>
                        <?php _e('Search Results Found For', 'locale'); ?>: "<?php the_search_query(); ?>"
                    </h5>

                    <div class="card_list colm--3">
                        <?php
                        if (have_posts()) { ?>
                            <?php while (have_posts()) {
                                the_post(); ?>

                                <div class="card">
                                    <div class="box">
                                        <figure>
                                            <a href="<?php the_permalink() ?>">
                                                <?php
                                                $image = get_field('listing_image');
                                                printf('<img src="%s" alt="%s" title="%s" />',
                                                    $image['url'], $image['alt'], $image['title']);
                                                ?>
                                            </a>
                                        </figure>
                                        <p class="cat">
                                            <span><?php echo get_the_date('M d, Y'); ?></span>
                                        </p>
                                        <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                        <p class="desc"><?php the_field('news_short_description'); ?></p>
                                        <p class="name"><?php the_field('author_name'); ?></p>
                                    </div>
                                </div>

                            <?php } ?>
                        <?php } ?>
                    </div>
                    <!----------------------- Pagination ----------------------------->
                    <div class="pages">
                        <?php
                        // Previous/next page navigation.
                        the_posts_pagination(
                            array(
                                'prev_text' => __('Previous', 'Iskcon News'),
                                'next_text' => __('Next', 'Iskcon News'),
                                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page', 'Iskcon News') . ' </span>',
                            )
                        );
                        ?>
                    </div>
                    <!---------------------------------------------------------------->
                </div>

                <div class="right_side">
                    <?php
                    get_template_part('partials/common/right-side-ad');
                    get_template_part('partials/common/other-stories');
                    ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    get_template_part('partials/common/newsletter-section');
    ?>
</main>

<?php get_footer(); ?>
