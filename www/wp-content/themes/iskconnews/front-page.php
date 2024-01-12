<?php
/*
* Template Name: Home Page
*/

//header
get_header();
get_template_part('partials/common/menu');
?>
	
<main class="home">
        <?php 
            get_template_part('partials/home/top-section');
            // get_template_part('partials/home/latest-content-section');
            get_template_part('partials/home/video-listing-section');
            get_template_part('partials/home/ad-section');
            get_template_part('partials/home/news-listing-section');
            get_template_part('partials/home/people-section');
            get_template_part('partials/common/newsletter-section');
        ?>       
</main>
	
<?php
	get_footer(); 
?>
