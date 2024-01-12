<?php
	$image1  = get_field('ad_image'); 
    $image2  = get_field('2nd_ad_image');
?>

<div class="pepoleOp_wrap">
        <?php the_field('top_section_content'); ?>

        <!--<div class="card">
            <figure>
                <a href="<?php the_field('ad_link'); ?>" target="_blank" style="display:block;">
                    <?php 
                        printf( '<img src="%s" alt="%s" title="%s" />', 
                        $image1['url'], $image1['alt'], $image1['title'] );
                    ?>
                </a>
            </figure>
            <div class="ad_desc">ISKCON News AD</div>
        </div>-->
         <?php the_field('table_section_content'); ?>
         <div class="card-2">
            <figure class="center_ad">
                <a href="<?php the_field('ad_link_2'); ?>" target="_blank" style="display:block;">
                    <?php 
                        printf( '<img src="%s" alt="%s" title="%s" />', 
                        $image2['url'], $image2['alt'], $image2['title'] );
                    ?>
                </a>
            </figure>
            <div class="ad_desc">Godhead Subscribe</div> 
        </div>
        <?php the_field('bottom_content'); ?>
</div>