<?php
/*
* Template Name: Submit News Page
*/

//header

 get_header();
 get_template_part('partials/common/menu');
?>

<main class="pg_about">
            <section class="most_popular iskcon_submit">
                <div class="main-container">
                    <div class="inner-container">

								<?php
									get_template_part('partials/submit-news/content-details');
									// get_template_part('partials/submit-news/right-section');
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
