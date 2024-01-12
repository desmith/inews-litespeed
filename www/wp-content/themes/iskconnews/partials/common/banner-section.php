<?php
	$image  = get_field('banner_image'); 
?>

<figure>
  <?php 
           printf( '<img src="%s" alt="%s" title="%s" />', 
           $image['url'], $image['alt'], $image['title'] ); 
  ?>
</figure>