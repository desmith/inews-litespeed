<?php 
    $image  = get_field('listing_image'); 
    if(!empty($image) && isset($image['url'])){

        if($image){
            printf( '<img src="%s" alt="%s" title="%s" />', 
            $image['url'], $image['alt'], $image['title'] ); 
        }else{
            printf( '<img src="%s" alt="Iskcon" title="Iskcon" />', 
            get_stylesheet_directory_uri().'/assets/img/placeholder.jpg' ); 
        }

    }else{
        $image  = get_field('migrated_image_path');

        if(!empty($image)){
            $wp_root_path = str_replace('/wp-content/themes', '', get_theme_root());
            $segments_arr = explode('/media/', $image);
            $img_exists = $wp_root_path.'/media/'.$segments_arr[1];
            if(file_exists($img_exists) && !empty($segments_arr[1])){
                printf( '<img src="%s" alt="%s" title="%s" />', 
                $image, 'Iskcon', 'Iskcon' );
            }else{
                printf( '<img src="%s" alt="Iskcon" title="Iskcon" />', 
                get_stylesheet_directory_uri().'/assets/img/placeholder.jpg' ); 
             
            }
        }
    }
?>