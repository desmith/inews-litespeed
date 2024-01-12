<?php
/*
* Template Name: Contact Page
*/

//header

 get_header();
 get_template_part('partials/common/menu');
?>

<main class="pg_about">
            <section class="most_popular contact_us">
                <div class="main-container">
                    <div class="inner-container">

								<?php
									get_template_part('partials/contact/contact-details');
									get_template_part('partials/contact/right-section');
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
