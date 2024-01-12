<?php 
	/*

	* Video data migration

	* Added on : 07.04.2021

	*/

	add_action( 'rest_api_init', function () {

	  	register_rest_route( 'data-migration/v1', '/videos', array(

		    'methods' => 'GET',

		    'callback' => 'cttv_migrate_videos',

	  	));

	});

	function cttv_migrate_videos(){

		global $wpdb;

		$table_name = 'ct0321_videos';

		//Select the available videos

		$result = $wpdb->get_results("SELECT * FROM $table_name WHERE flag=0");

		$data = [];

		$error = 0;

		//print_r($result);exit;

		//Form the array to be inserted

		if(!count($result)){

			echo json_encode(['status' => 'error', 'message' => 'No data available for migration']);exit;

		}



		// begin transaction

  		$wpdb->query('START TRANSACTION');



		foreach ($result as $key => $value) {

			$post_id = wp_insert_post(array (

			   'post_type' => 'cttv-videos',

			   'post_title' => $value->title,

			   'post_content' => $value->description,

			   'post_status' => 'publish',

			));



			if(!$post_id){

				$error++;

				echo json_encode(['status' => 'error', 'message' => 'Error while creating the video']);exit;

			}



			//Insert ACF fields

			$video_id = 'field_6061a3cd633bf';	

			update_field( $video_id, $value->video_id, $post_id );



			$date_recorded = 'field_6061a0343a10a';	

			update_field( $date_recorded, $value->date_recorded, $post_id );



			$date_uploaded = 'field_6061a0733a10b';

			update_field( $date_uploaded, $value->date_uploaded, $post_id );



			$location_recorded = 'field_6061a0893a10c';

			update_field( $location_recorded, $value->location_recorded, $post_id );



			$video_url = 'field_6061a48cca7cc';

			update_field( $video_url, wp_upload_dir()['url'].'/'.$value->video_id.'.mp4', $post_id );



			if(!empty($value->channel)){

				$category = get_term_by('name', $value->channel, 'cttv_video_channels');

				//print_r($value);exit;

				if(!$category){

					$error++;

					echo json_encode(['status' => 'error', 'message' => 'Channel not found']);exit;

				}

				$term_taxonomy_ids = wp_set_object_terms( $post_id, $category->id, 'cttv_video_channels');

			}

			



			//Insert meta

			add_post_meta( $post_id, 'rating_number_votes', $value->rating_number_votes );

			add_post_meta( $post_id, 'rating_total_points', $value->rating_total_points );

			add_post_meta( $post_id, 'updated_rating', $value->updated_rating );

			add_post_meta( $post_id, 'number_of_views', $value->number_of_views );

			add_post_meta( $post_id, 'chef', $value->chef );



			//Set the tags

			$tags = explode(',', $value->tags);

			wp_set_post_tags( $post_id, $tags);



			//Update the flag of the video table

			$update = $wpdb->update(

				$table_name,

				['flag'=>1],

			    ['video_id'=>$value->video_id],

			    ['%s'],

			    ['%s']

			);

		}



		if($error){

			$wpdb->query('ROLLBACK');

			echo json_encode(['status' => 'error', 'message' => 'Migration failed']);exit;

		}

		$wpdb->query('COMMIT');

		echo json_encode(['status' => 'success', 'message' => 'Migration successful']);exit;

	}



	/*

	* Enqueue scripts

	* Added on: 11.04.2021

	*/

	function cttv_scripts() {

		$ver = '1.0.5';

	    wp_enqueue_style( 'swiper-style', get_stylesheet_directory_uri() . '/assets/vendors/swiper@7.0.1/swiper-bundle.min.css' );
	    wp_enqueue_style( 'compare-style', get_stylesheet_directory_uri() . '/assets/vendors/image-comparison-slider/img-compare.css' );
	    wp_enqueue_style( 'main-style', get_stylesheet_directory_uri() . '/assets/css/style.css?v=2.35' );


	    wp_enqueue_script( 'videojs-js','http://vjs.zencdn.net/7.11.4/video.min.js');

	    wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/assets/js/main.js', ['jquery', 'videojs-js'], $ver, true );
	    wp_enqueue_script( 'likes-js', get_stylesheet_directory_uri() . '/assets/js/likes.js', ['jquery'], $ver, true );
	    wp_enqueue_script( 'comparison-js', get_stylesheet_directory_uri() . '/assets/vendors/image-comparison-slider/img-compare.js', ['jquery'], $ver, true );
        wp_enqueue_script( 'swiper-js', get_stylesheet_directory_uri() . '/assets/vendors/swiper@7.0.1/swiper-bundle.min.js', ['jquery'], $ver, true );
       	wp_enqueue_script( 'frontend-js', get_stylesheet_directory_uri() . '/assets/js/frontend.js', ['jquery', 'comparison-js', 'swiper-js'], $ver, true );
       	wp_enqueue_script( 'video-js', get_stylesheet_directory_uri() . '/assets/js/video.js', ['jquery', 'comparison-js', 'swiper-js'], $ver, true );
	    wp_localize_script( 'likes-js', 'cttv_likes', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
	    wp_localize_script( 'video-js', 'cttv_videos', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));


	}

	add_action( 'wp_enqueue_scripts', 'cttv_scripts' );



	/*

	* Pagination function

	* Added on : 18.04.2021

	*/



	function pagination_bar( $custom_query ) {

	    $total_pages = $custom_query->max_num_pages;

	    $big = 999999999; // need an unlikely integer



	    if ($total_pages > 1){

	        $current_page = max(1, get_query_var('paged'));



	        echo paginate_links(array(

	            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),

	            'format' => '?paged=%#%',

	            'current' => $current_page,

	            'total' => $total_pages,

	        ));

	    }

	}


	add_action( 'wp_ajax_cttv_likes_ajax', 'cttv_likes_ajax' );
	add_action( 'wp_ajax_nopriv_cttv_likes_ajax', 'cttv_likes_ajax' );

	function cttv_likes_ajax() {
	   	$key = 'video_like_count';
		$post_id = $_POST['id'];
		$count = (int) get_post_meta( $post_id, $key, true );
		$count++;
		update_post_meta( $post_id, $key, $count );
		echo json_encode([$post_id => $count]);exit;
	}






	/*-------------------------------------
	  Load more
	  Added on : 20.10.2021
	---------------------------------------*/
	add_action( 'wp_ajax_nopriv_cttv_load_more', 'cttv_load_more' );
	add_action( 'wp_ajax_cttv_load_more', 'cttv_load_more' );

	function cttv_load_more() {
		$offset = $_POST['offset'];
		$per_page = $_POST['per_page'];
		$country_id = $_POST['country_id'];
		$query = new WP_Query([ 
			'post_type' => 'cttv-videos',
			'meta_query' => [
				[
					'key' 		=> 'country',
					'value' 	=> $country_id,
					'compare' 	=> '='
				]
			],
			'posts_per_page' => $per_page, 
			'offset' => $offset 
		]);
		$posts = [];
		$total = new WP_Query([ 
			'post_type' => 'cttv-videos',
			'meta_query' => [
				[
					'key' 		=> 'country',
					'value' 	=> $country_id,
					'compare' 	=> '='
				]
			]
		]);
		if(count($query->posts)){
			foreach ($query->posts as $key => $post) {
				$id = $post->ID;
				$posts[$key]->title = get_the_title($id);
				$posts[$key]->video_id = get_field('video_id', $id);
				$posts[$key]->channel = get_field('channel', $id);
				$posts[$key]->date_recorded = get_field('date_recorded', $id);
				$posts[$key]->date_uploaded = get_field('date_uploaded', $id);
				$posts[$key]->location_recorded = get_field('location_recorded', $id);
				$posts[$key]->country = get_field('country', $id);
				$posts[$key]->video_uploadurl = get_field('video_uploadurl', $id);
				$posts[$key]->video_url = get_field('video_url', $id);
				$posts[$key]->video_upload = get_field('video_upload', $id);
				$posts[$key]->poster = get_field('poster', $id);
				$posts[$key]->new_video = get_field(' new_video', $id);
			}
		}
	    echo json_encode(['posts' => $posts, 'total' => count($total->posts) ]);exit;
	}

?>