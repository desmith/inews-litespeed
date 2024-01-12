<?php 
  $facebook_link =  get_option('facebook_link');
  $instra_link =  get_option('instra_link');
  $twitter_link =  get_option('twitter_link');
  $footer_content =  get_option('footer_content');
?>

<footer class="isk_footer">
    <div class="top">
        <div class="main-container">
            <div class="inner-container">
                <div class="list">
                    <div class="footer_logo">
                        <figure>
                            <img src="<?php bloginfo('template_url'); ?>/assets/img/logo.png" alt="" title="">
                        </figure>
                    </div>
                    <div class="f_menu">
                        <div class="menu_list">
                            <?php wp_nav_menu( array( 'theme_location' => 'footer_menu' ) ); ?>
                        </div>
                        <div class="social">
                            <div class="socials">
                                <a href="<?php echo $facebook_link; ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/facebook.svg" alt="facebook" title="Facebook"></a>
                                <a href="<?php echo $twitter_link; ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/twitter.svg" alt="twitter" title="Twitter"></a>
                                <a href="<?php echo $instra_link; ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/instagram.svg" alt="instragram" title="Instagram"></a>
                            </div>
                            <?php echo $footer_content; ?>
                        </div>
                    </div>
                    <div class="iskc">
                        <figure>
                            <img src="<?php bloginfo('template_url'); ?>/assets/img/iskcon_seal.png" alt="" title="">
                        </figure>
                    </div>
                    <a href="#top" class="back2top"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/up.png"></a>
                </div>
            </div>
        </div>
    </div>
    <div class="footerlinkwrap">
        <div class="footerlinkbox">
            <div class="hflinks">
                <p>OTHER HELPFUL LINKS</p>
            </div>
            <div class="hf_items">
                <div class="main-container">
                    <div class="inner-container">
                        <ul>
                            <li><a href="http://www.founderacharya.com/" target="_blank">Founder Acharya.com </a></li>
                            <li><a href="http://www.prabhupada.net/" target="_blank">Prabhupada.net</a></li>
                            <li><a href="http://directory.krishna.com/" target="_blank">ISKCON Centers</a></li>
                            <li><a href="http://gbc.iskcon.org/" target="_blank">ISKCON Documents</a></li>
                            <li><a href="https://www.iskconcommunications.org/iskcon-journal/vol-1" target="_blank">ISKCON Comunications Journal</a></li>
                            <li><a href="https://iskconcommunications.org/" target="_blank">ISKCON Comunications</a></li>
                            <li><a href="http://www.krishna.com/" target="_blank">Krishna.com</a></li>
                            <li><a href="https://gbc.iskcon.org/" target="_blank">ISKCON GBC</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom">

    </div>
    
</footer>
<div id="vidBox" style="display:none">
    <div class="vbxcwrap">
        <div id="videCont">
            <div id="closer_videopopup"></div>
            <iframe id="serVid" width="100%" height="400" src="" frameborder="0" allow="accelerometer; autoplay; loop; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>


<div class="pop-up-overlay" style="display:none;">
	<div class="pop-up-msg">
		<span>
			<svg class="ico-svg checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"></circle>
				<path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path>
			</svg>
			<svg class="ico-svg cross" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
				<circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"></circle>
				<path class="checkmark__check" fill="none" d="M16 16 36 36 M36 16 16 36"></path>
			</svg>
		</span>
		<div class="content">
			<h6></h6>
			<p></p>
		</div>
	</div>
</div>

</script>
  <script type="text/javascript" src="<?php bloginfo('template_url'); ?>/assets/js/custom-file-input.js?v=<?= rand(); ?>"></script>
     <?php wp_footer(); ?>
    </body>
</html>
