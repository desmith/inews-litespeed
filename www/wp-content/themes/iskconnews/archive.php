<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

get_header();
get_template_part('partials/common/menu');

$queried_object = get_queried_object();
$parent_term_id = $queried_object->parent;
$term_id = $queried_object->term_id;

if($term_id == '1' || $parent_term_id == '1'){ // This condition for News
?>
    <main class="pg_news">
        <?php
          get_template_part('partials/news-taxonomy-page/top-section-code');
          get_template_part('partials/news-taxonomy-page/post-listing');
          get_template_part('partials/common/newsletter-section');
        ?>
    </main>
<?php }
elseif($term_id == '35' || $parent_term_id == '35'){ // This condition for Outreach & Activism
?>
    <main class="pg_arts pg_OA pg_video">
       <section class="most_popular">
            <div class="main-container">
                <div class="inner-container">
                <?php
                   get_template_part('partials/outreach-activism-taxonomy-page/left-section');
                   get_template_part('partials/outreach-activism-taxonomy-page/right-section');
                ?>
             </div>
            </div>
       </section>
       <?php
          get_template_part('partials/common/newsletter-section');
       ?>
    </main>
<?php }
elseif($term_id == '42' || $parent_term_id == '42'){ // This condition for People
?>
    <main class="pg_people pg_video">
       <section class="most_popular">
            <div class="main-container">
                <div class="inner-container">
                <?php
                   get_template_part('partials/people-taxonomy-page/left-section');
                   get_template_part('partials/people-taxonomy-page/right-section');
                ?>
             </div>
            </div>
       </section>
                <?php
                   get_template_part('partials/common/newsletter-section');
                ?>
    </main>
<?php }
elseif($term_id == '46' || $parent_term_id == '46'){ // This condition for Lifestyle
?>
    <main class="pg_arts pg_lifestyle pg_video">
       <section class="most_popular">
            <div class="main-container">
                <div class="inner-container">
                <?php
                   get_template_part('partials/lifestyle-taxonomy-page/left-section');
                   get_template_part('partials/lifestyle-taxonomy-page/right-section');
                ?>
             </div>
            </div>
       </section>
        <?php
           get_template_part('partials/common/newsletter-section');
        ?>
    </main>
<?php }
elseif($term_id == '51' || $parent_term_id == '51'){ // This condition for Arts
?>
    <main class="pg_arts pg_video">
       <section class="most_popular">
            <div class="main-container">
                <div class="inner-container">
                <?php
                   get_template_part('partials/arts-taxonomy-page/left-section');
                   get_template_part('partials/arts-taxonomy-page/right-section');
                ?>
             </div>
            </div>
       </section>
       <?php
          get_template_part('partials/common/newsletter-section');
       ?>
    </main>
<?php }
elseif($term_id == '57' || $parent_term_id == '57'){ // This condition for Opinion
?>
    <main class="pg_opinion pg_video">
        <section class="most_popular">
            <div class="main-container">
                <div class="inner-container">
                    <?php
                        get_template_part('partials/opinion-taxonomy-page/left-section');
                        get_template_part('partials/opinion-taxonomy-page/right-section');
                    ?>
                </div>
            </div>
        </section>
        <?php
            get_template_part('partials/common/newsletter-section');
        ?>
    </main>
<?php }
elseif($term_id == '58' || $parent_term_id == '58'){ // This condition for Video
?>
    <main class="pg_video">
        <section class="most_popular">
            <div class="main-container">
                <div class="inner-container">
                    <?php
                        get_template_part('partials/video-taxonomy-page/left-section');
                        get_template_part('partials/video-taxonomy-page/right-section');
                    ?>
                </div>
            </div>
        </section>
        <?php
            get_template_part('partials/common/newsletter-section');
        ?>
    </main>
<?php }
else  { ?>
    <main class="pg_about">
        <section class="most_popular">
            <div class="wrap_404">
                <div class="main-container">
                    <div class="r_404 text-center mb60">
                        <h5 class="main_title"><span>404</span></h5>
                        <p class="large">Oops!! Page Not Found</p>
                        <p class="desc">The page you are looking for was removed or might never existed.</p>
                        <p class="desc"><a class="btn dark" href="<?php echo get_site_url(); ?>/home">Go to Home Page</a></p>
                    </div>
                </div>
            </div>
        </section>
        <?php
           get_template_part('partials/common/newsletter-section');
        ?>
    </main>

<?php } ?>

<?php get_footer(); ?>
