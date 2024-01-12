<?php
/*
* Data Migration
* Added : 08.11.2021
*/

add_action('rest_api_init', function () {

  register_rest_route('data-migration/article', '/posts', array(
    'methods' => 'GET',
    'callback' => 'in_migrate_article',
  ));

});

function in_migrate_article() {
  ini_set('max_execution_time', 3000); //3000 seconds = 50 mins
  $endpoint = 'http://127.0.0.1:8000/';
  $start = $_GET['start'] ?? 0;
  $limit = $_GET['limit'] ?? 10;
  $article = wp_remote_get($endpoint . 'content_article_new?skip=' . $start . '&take=' . $limit);
  $article_body = wp_remote_retrieve_body($article);
  $article_data = json_decode($article_body);
  //print_r($article_data);exit;
  $res = process_insertion($article_data);

  echo json_encode($res);
  exit;
}

add_action('rest_api_init', function () {

  register_rest_route('data-migration/video', '/posts', array(
    'methods' => 'GET',
    'callback' => 'in_migrate_video',
  ));

});

function in_migrate_video() {
  ini_set('max_execution_time', 3000); //3000 seconds = 50 mins
  $endpoint = 'http://127.0.0.1:8000/';
  $start = $_GET['start'] ?? 0;
  $limit = $_GET['limit'] ?? 10;
  $video = wp_remote_get($endpoint . 'content_video_new?skip=' . $start . '&take=' . $limit);
  $video_body = wp_remote_retrieve_body($video);
  $video_data = json_decode($video_body);

  //print_r($video_data);exit;

  $res = process_insertion($video_data);

  echo json_encode($res);
  exit;
}

add_action('rest_api_init', function () {

  register_rest_route('data-migration/audio', '/posts', array(
    'methods' => 'GET',
    'callback' => 'in_migrate_audio',
  ));

});

function in_migrate_audio() {
  ini_set('max_execution_time', 3000); //3000 seconds = 50 mins
  $endpoint = 'http://127.0.0.1:8000/';
  $start = $_GET['start'] ?? 0;
  $limit = $_GET['limit'] ?? 10;
  $audio = wp_remote_get($endpoint . 'content_audio?skip=' . $start . '&take=' . $limit);
  $audio_body = wp_remote_retrieve_body($audio);
  $audio_data = json_decode($audio_body);
  print_r($audio_data);
  exit;
}

function process_insertion($data): array {

  if (!$data || !count($data)) {
    return (['status' => false, 'message' => 'Data not available']);
  }

  foreach ($data as $key => $item) {
    $postdate = $item->publish_date;
    $post_id = wp_insert_post(array(
                                'post_title' => $item->title,
                                'post_type' => 'post',
                                'post_content' => $item->content,
                                'post_date' => $postdate,
                                'post_status' => 'publish'
                              ));

    //print_r($post_id); exit;

    //Insert ACF fields
    //author
    $author = 'field_615c32f0a08f2';
    update_field($author, $item->author, $post_id);

    //video embed
    if (isset($item->iframe_src)) {
      $video = 'field_615c32f0a45ff';
      update_field($video, $item->iframe_src, $post_id);
    }

    //short description
    $teaser = 'field_615c33356c36e';
    if (isset($item->teaser)) {
      $teaser_data = $item->teaser;
    } else {
      $teaser_data = substr(strip_tags($item->content), 0, 200);
    }
    update_field($teaser, $teaser_data, $post_id);

    //Listing image
    $listing_image = 'field_615c32f0a835f';
    $image = site_url() . '/media/' . $item->image;
    //$image = get_stylesheet_directory_uri().'/assets/img/placeholder.jpg';
    update_field($listing_image, $image, $post_id);

    //Details image
    $details_image = 'field_615c32f0ac03d';
    $image = site_url() . '/media/' . $item->image;
    //$image = get_stylesheet_directory_uri().'/assets/img/placeholder.jpg';
    update_field($details_image, $image, $post_id);

    //categorize posts
    if (isset($item->sections) && count($item->sections)) {
      $category_relation_arr = [
        'world-news' => 'news',
        'opinion' => 'opinion',
        'events' => 'news',
        'lifestyle' => 'lifestyle',
        'people' => 'people',
        'activism' => 'outreach-activism',
        'arts' => 'arts',
        'health-science' => 'health-wellness',
        'humor' => 'fine-arts',
        'obituary' => 'obituaries',
        'covid-19' => 'covid-19',
        'legacy' => 'news'
      ];
      foreach ($item->sections as $k => $cat) {
        if (isset($cat->section) && !empty($cat->section)) {
          $category_slug = $category_relation_arr[$cat->section->slug];
          $catid = get_category_by_slug($category_slug);
          wp_set_post_categories($post_id, $catid, false);
        }
      }
    }

    //Check if video
    if (isset($item->type) && !empty($item->type)) {
      if ($item->type->id == 8) {
        $catid = 58;
        wp_set_post_categories($post_id, $catid, false);
      }
    }

    //add tags to posts
    $tags_arr = [];
    if (isset($item->tags) && count($item->tags)) {
      foreach ($item->tags as $k => $tag) {
        if (isset($tag->tag) && !empty($tag->tag)) {
          array_push($tags_arr, $tag->tag->name);
        }
      }
      wp_set_post_tags($post_id, $tags_arr); // Set tags to Post
    }
  }

  return (['status' => true, 'message' => 'Data migrated successfully']);

}

add_action('rest_api_init', function () {

  register_rest_route('data-migration/article', '/update', array(
    'methods' => 'GET',
    'callback' => 'in_migrate_article_update',
  ));

});

function in_migrate_article_update() {
  ini_set('max_execution_time', 3000); //3000 seconds = 50 mins
  $endpoint = 'http://127.0.0.1:8000/';
  $start = $_GET['start'] ?? 0;
  $limit = $_GET['limit'] ?? 10;
  $article = wp_remote_get($endpoint . 'content_article?skip=' . $start . '&take=' . $limit);
  $article_body = wp_remote_retrieve_body($article);
  $article_data = json_decode($article_body);
  //print_r($article_data);exit;
  $res = update_image($article_data);

  echo json_encode($res);
  exit;
}

add_action('rest_api_init', function () {

  register_rest_route('data-migration/video', '/update', array(
    'methods' => 'GET',
    'callback' => 'in_migrate_video_update',
  ));

});

function in_migrate_video_update() {
  ini_set('max_execution_time', 3000); //3000 seconds = 50 mins
  $endpoint = 'http://127.0.0.1:8000/';
  $start = $_GET['start'] ?? 0;
  $limit = $_GET['limit'] ?? 10;
  $video = wp_remote_get($endpoint . 'content_video?skip=' . $start . '&take=' . $limit);
  $video_body = wp_remote_retrieve_body($video);
  $video_data = json_decode($video_body);
  //print_r($video_data);exit;
  $res = update_image($video_data);

  echo json_encode($res);
  exit;
}
