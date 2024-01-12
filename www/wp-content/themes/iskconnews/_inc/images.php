<?php

add_theme_support('post-thumbnails');
//set_post_thumbnail_size( $width, $height, $crop );


function update_image($data) {
  $url = site_url();
  $path = 'field_619e1d6feff99';
  if (!$data || !count($data)) {
    return (['status' => false, 'message' => 'Data not available']);
  }
  foreach ($data as $key => $item) {
    $title = $item->title;
    $post_data = get_page_by_title($title, OBJECT, 'post');
//print_r($post_data);exit;
    $post_id = $post_data->ID;
    $image = $url . '/media/' . $item->image;
    update_field($path, $image, $post_id);
  }

  return (['status' => true, 'message' => 'Data migrated successfully']);
}


function chk_for_img($img, $title = '') {
//get_field('banner_image') ? get_field('banner_image') : get_stylesheet_directory_uri().'/assets/img/placeholder.jpg';
  $placeholder = get_stylesheet_directory_uri() . '/assets/img/placeholder.jpg';
  $img_tag = `<img src="` . $placeholder . `" alt="Iskcon" title="Iskcon"/>`;
//$image  = get_field($img);
  if (!empty($img)) {

    if ($img) {
      $img_tag = `<img src="` . $img . `" alt="" title=""/>`;
    }

  } else {
    $image = get_field('migrated_image_path');

    if (!empty($image)) {
      $wp_root_path = str_replace('/wp-content/themes', '', get_theme_root());
      $segments_arr = explode('/media/', $image);
      $img_exists = $wp_root_path . '/media/' . $segments_arr[1];
      if (file_exists($img_exists) && !empty($segments_arr[1])) {
        $img_tag = `<img src="` . $image . `" alt="Iskcon" title="Iskcon"/>`;
      }
    }
  }

  return $img_tag;
}


/**** Image auto W&H delete ****/
function remove_width_attribute($html) {
  $html = preg_replace('/(width|height)="\d*"\s/', '', $html);
  return $html;
}

add_filter('post_thumbnail_html', 'remove_width_attribute', 10);
add_filter('image_send_to_editor', 'remove_width_attribute', 10);


add_shortcode('wp_caption', 'fixed_img_caption_shortcode');
add_shortcode('caption', 'fixed_img_caption_shortcode');
function fixed_img_caption_shortcode($attr, $content = null) {
  if (!isset($attr['caption'])) {
    if (preg_match('#((?:<a [^>]+>\s*)?<img [^>]+>(?:\s*</a>)?)(.*)#is', $content, $matches)) {
      $content = $matches[1];
      $attr['caption'] = trim($matches[2]);
    }
  }

  $output = apply_filters('img_caption_shortcode', '', $attr, $content);
  if ($output != '')
    return $output;

  extract(shortcode_atts(array(
                           'id' => '',
                           'align' => 'alignnone',
                           'width' => '',
                           'caption' => ''
                         ), $attr));

  if (1 > (int)$width || empty($caption))
    return $content;

  if ($id) $id = 'id="' . esc_attr($id) . '" ';

  return '
<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '">' . do_shortcode($content) . '<p>' . $caption .
    '</p></div>';
}

add_filter('wpseo_opengraph_image', 'isk_og_image', 10, 1);
add_filter('wpseo_twitter_image', 'isk_tw_image', 15, 1);

function isk_og_image($url) {
  global $post;
  $path = get_stylesheet_directory_uri() . '/assets/img/placeholder.jpg';
  $img = get_field('banner_image', $post->ID);

  if (!empty($img)) {

    if ($img) {
      $path = $img;
    }

  } else {
    $image = get_field('migrated_image_path', $post->ID);

    if (!empty($image)) {
      $wp_root_path = str_replace('/wp-content/themes', '', get_theme_root());
      $segments_arr = explode('/media/', $image);
      $img_exists = $wp_root_path . '/media/' . $segments_arr[1];
      if (file_exists($img_exists) && !empty($segments_arr[1])) {
        $path = $image;
      }
    }
  }

  return $path;
}

function isk_tw_image($url) {
  global $post;
  $path = get_stylesheet_directory_uri() . '/assets/img/placeholder.jpg';
  $img = get_field('banner_image', $post->ID);

  if (!empty($img)) {

    if ($img) {
      $path = $img;
    }

  } else {
    $image = get_field('migrated_image_path', $post->ID);

    if (!empty($image)) {
      $wp_root_path = str_replace('/wp-content/themes', '', get_theme_root());
      $segments_arr = explode('/media/', $image);
      $img_exists = $wp_root_path . '/media/' . $segments_arr[1];
      if (file_exists($img_exists) && !empty($segments_arr[1])) {
        $path = $image;
      }
    }
  }

  return $path;
}
