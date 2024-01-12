<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

 get_header();
 get_template_part('partials/common/menu');
?>

<!-- page.php -->
<main class="pg_about privacy_plcy privacy_policypage">

   <section class="most_popular">
            <div class="main-container">
                <div class="inner-container">
                    <div class="peopleOp_wrap">
                        <h5 class="title mt0"><?php the_title(); ?></h5>
                            <div class="privacy_content">
                                <?php the_content(); ?>
                            </div>
                    </div>
                </div>
            </div>
    </section>

	<?php get_template_part('partials/common/newsletter-section'); ?>

</main>

<?php
get_footer();
