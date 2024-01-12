<?php 
 get_header();
 get_template_part('partials/common/menu');
 wpb_set_post_views(get_the_ID());
 $id = get_the_ID();
//$banner_image = get_field('banner_image', $id) ? get_field('banner_image', $id) : get_stylesheet_directory_uri().'/assets/img/placeholder.jpg';
 $banner_image = chk_for_img(get_field('banner_image',$id));

 $video_link = get_field('video_link',$id);

$Url = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
$Url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
?>

<main class="pg_peopleDetails">

<section class="details_area">
    <div class="main-container">
        <div class="inner-container">
            <div class="content_area">
                <h5 class="title"><?php the_title(); ?></h5>
                <div class="auth_share">
                    <h6 class="authnm">By <?php the_field('author_name'); ?> &nbsp; |&nbsp; <?php echo get_the_date( 'M d, Y' ); ?> </h6>
                    <div class="share">
                        Share
                        <div class="share_option">
                            <a class="tiw" href="https://twitter.com/share?url=<?php echo $Url; ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/twitter.svg" alt="TW" title="Twitter"></a>
                            <a class="inst" href="https://www.instagram.com/" target="_blank"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/instagram.svg" alt="ins" title="Instragram"></a>
                            <a class="fb" href="http://www.facebook.com/sharer.php?u=<?php echo $Url; ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/facebook.svg" alt="fb" title="Facebook"></a>
                            <a class="wh_app" href="https://api.whatsapp.com/send?text=<?= $Url ?>" data-action="share/whatsapp/share" target="_blank"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/whatsapp.svg" alt="wa" title="WA"></a>
                            <a class="mail" href="mailto:?subject=I wanted you to see this link&amp;body=Check out this link <?php echo $Url; ?>" target="_blank"><img src="<?php bloginfo('template_url'); ?>/assets/img/icons/email.svg" alt="mail" title="Gmail"></a>
                        </div>
                    </div>
                </div>

                <?php 
                    $term_ids = wp_list_pluck(get_the_terms(get_the_ID(),'category'), 'term_id');
                    if(in_array('58', $term_ids)) { 
                ?>
                    <div class="details">
                        <iframe width="100%" height="500" src="<?php echo $video_link; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

                        <?php the_content(); ?>

                        <?php
                            $posttags = get_the_tags(get_the_ID());
                             $tght = '';
                              if ($posttags) {
                              foreach($posttags as $tag) {
                                $tght .= '<a href="'.get_tag_link($tag->term_id).'">'.$tag->name . '</a> , ';
                                //$tght .= ' '.$tag->name . ', '; 
                              }
                                $tght = rtrim($tght, ', ');
                                $nwtag = '<div class="blogtag"> Tag:'. ' '. '<span>'. $tght .'</span> </div>';
                            	echo $nwtag;
                            }
                        ?>
                    </div>
                <?php }else{ ?>
                    <div class="details">
                        <figure>
                            <img src="<?php echo $banner_image; ?>" alt="nw" >
                        </figure>
                        <?php the_content(); ?>
                        <?php
                            $posttags = get_the_tags(get_the_ID());
                             $tght = '';
                              if ($posttags) {
                              foreach($posttags as $tag) {
                                $tght .= '<a href="'.get_tag_link($tag->term_id).'">'.$tag->name . '</a> , ';
                                //$tght .= ' '.$tag->name . ', '; 
                              }
                                $tght = rtrim($tght, ', ');
                                $nwtag = '<div class="blogtag"> Tag:'. ' '. '<span>'. $tght .'</span> </div>';
                            	echo $nwtag;
                             }
                        ?>
                    </div>
                <?php } ?>  
            </div>
        </div>
    </div>
</section>

<section class="mortopic">
    <div class="main-container">
        <div class="inner-container">
            <h5 class="title">More Topic</h5>
            <div class="moretopic_carousel fourslide owl-carousel">
            <?php
        	   $args = array('post_type' => 'post', 'posts_per_page' => 10, 'order' => 'desc', 'post__not_in' => array(get_the_ID()));
        		$the_query = new WP_Query( $args ); ?>
        		<?php if ( $the_query->have_posts() ) : ?>
        		<?php while ( $the_query->have_posts() ) : $the_query->the_post(); 
                    $term_ids = wp_list_pluck(get_the_terms(get_the_ID(),'category'), 'term_id'); 
                    if(in_array('58', $term_ids)){ ?>
                    <div class="item">
                        <div class="card">
                            <div class="box">
                                <figure>
                                 <a href="javascript:void(0);" class="videoBoxBtn" data-vlink="<?php the_field('video_link'); ?>">
                                    <?php 
                                        get_template_part('partials/common/listing-image'); 
                                    ?>  
                                    <p class="play_btn">Play</p>                                                
                                  </a>
                                </figure>
                                <p class="cat"><span><?php echo get_the_date( 'M d, Y' ); ?></span></p>
                                <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h6>
                                <p class="name"><?php the_field('author_name'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php }else{ ?>
                    <div class="item">
                        <div class="card">
                            <div class="box">
                                <figure>
                                <a href="<?php the_permalink() ?>">
                                    <?php 
                                        get_template_part('partials/common/listing-image'); 
                                    ?>
		  						</a>
                                </figure>
                                <p class="cat"><span><?php echo get_the_date( 'M d, Y' ); ?></span></p>
                                <h6 class="ttl"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h6>
                                <p class="name"><?php the_field('author_name'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                <?php endwhile; ?>  
        		 <!-- end of the loop -->
        	   <?php wp_reset_postdata(); ?>
        	   <?php else:  ?>
               <p><?php _e( 'Sorry, no news posts matched your criteria.' ); ?></p>
        	<?php endif; ?>    
            </div>
        </div>
    </div>
</section>

<?php
	get_template_part('partials/common/newsletter-section'); 
?> 
</main>

<?php get_footer(); ?>
