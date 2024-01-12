<?php
/*
* Template Name: 404 page
*/
get_header();
get_template_part('partials/common/menu'); ?>

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

<?php
get_footer();
?>
