<!DOCTYPE html>
<html lang="en">
	<head>

			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<meta name="theme-color" content="">
			<?php if(!is_single()) : ?>
			<meta property="og:image" content="<?php bloginfo('template_url'); ?>/assets/img/schema.jpg" />
			<?php else :?>
			<meta property="og:image" content="<?php !empty(get_field('banner_image')) ? the_field('banner_image') : the_field('migrated_image_path') ?>" />
			<?php endif; ?>
			<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/assets/img/favicon.png"/>
			<title><?php bloginfo('name'); ?> | <?php echo is_front_page() ? 'Home' : wp_title(''); ?></title>
			

			<!-- Google Sitelinks Search Box -->
			<script type="application/ld+json">
			  {
			    "@context": "https://schema.org",
			    "@type": "WebSite",
			    "url": "https://iskconnews.org",
			    "potentialAction": {
			      "@type": "SearchAction",
			      "target": "https://iskconnews.org/search?q={search_term_string}",
			      "query-input": "required name=search_term_string"
			    }
			  }
			</script>
		<?php wp_head() ?>
	</head>
<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TCCFCHM"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

