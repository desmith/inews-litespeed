<?php
/*
Template Name: About Us Page
*/

//header

 get_header();
 get_template_part('partials/common/menu');
?>

<main class="pg_about">
	<section class="about-us">
        <div class="main-container">
            <div class="inner-container">
					<?php
						get_template_part('partials/about/about-description');
						//get_template_part('partials/about/about-right-section');
					?>
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
