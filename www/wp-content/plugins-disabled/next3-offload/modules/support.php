<?php 
namespace Next3Offload\Modules;
defined( 'ABSPATH' ) || exit;

class Support{
    private static $instance;

    const CACHE_KEY = 'next3_cache';

    const POST_CACHE_GROUP = 'post_next3_cache';

    const OPTION_CACHE_GROUP = 'option_next3_cache';

    const OPTION_KEY_ARR = '_next3';

    protected $query_cache = array();

    public function init() { 
        // EDD
		add_filter( 'edd_download_files', [ $this, 'edd_download_files' ], 99);
		add_filter( 'edd_metabox_save_edd_download_files', [ $this, 'edd_download_files' ], 99);

        // Customizer
		add_filter( 'theme_mod_background_image', [ $this, 'theme_mod_background_image' ], 99, 2);
		add_filter( 'pre_set_theme_mod_background_image', [ $this, 'theme_mod_background_image' ], 99, 2);
		add_filter( 'theme_mod_header_image', [ $this, 'theme_mod_background_image' ], 99, 2 );
		add_filter( 'pre_set_theme_mod_header_image', [ $this, 'theme_mod_background_image' ], 99, 2 );
		add_filter( 'pre_set_theme_mod_header_image_data', [ $this, 'theme_mod_header_image_data' ], 99, 2 );
		// css
		add_filter( 'customize_value_custom_css', [ $this, 'customize_value_custom_css' ], 10, 2 );
		add_filter( 'update_custom_css_data', array( $this, 'update_custom_css_data' ), 10, 2 );
		add_filter( 'wp_get_custom_css',[ $this, 'wp_get_custom_css' ], 10, 2 );

        // Posts
        add_action( 'the_post', [ $this, 'filter_post_data' ], 119);
        add_filter( 'content_pagination', [ $this, 'filter_content_pagination' ], 119);

        add_filter( 'the_content', [ $this, 'the_content' ], 119 );
		add_filter( 'the_excerpt', [ $this, 'the_content' ], 119 );
		add_filter( 'rss_enclosure', [ $this, 'the_content' ], 119 );
		add_filter( 'content_edit_pre', [ $this, 'the_content' ], 119 );
		add_filter( 'excerpt_edit_pre', [ $this, 'the_content' ], 119 );
		add_filter( 'elementor/frontend/the_content', [ $this, 'the_content' ], 119 );

		add_filter( 'content_save_pre', array( $this, 'the_content' ) );
		add_filter( 'excerpt_save_pre', array( $this, 'the_content' ) );

        // widgets
		add_filter( 'widget_form_callback', [ $this, 'filter_widget_display' ], 99);
		add_filter( 'widget_display_callback', [ $this, 'filter_widget_display' ], 99 );
		add_filter( 'widget_update_callback', array( $this, 'filter_widget_display' ) );
		add_filter( 'pre_update_option_widget_block', array( $this, 'filter_widget_block_save' ) );


        if ( function_exists( 'is_wp_version_compatible' ) && is_wp_version_compatible( '5.8' ) ) {
			add_filter( 'customize_value_widget_block', [ $this, 'filter_widget_display' ], 99 );
			add_filter( 'widget_block_content', [ $this, 'widget_block_content' ], 99 );
		}

        // Edit Media page
		add_filter( 'set_url_scheme', [ $this, 'set_url_scheme' ], 99, 3 );
		add_filter( 'next3_wp_get_attachment_url', [ $this, 'next3_wp_get_attachment_url' ], 99, 3 );
		add_filter( 'next3_wp_get_attachment_image_attributes', [ $this, 'next3_wp_get_attachment_image_attributes' ], 99, 3 );
		add_filter( 'next3_wp_get_attachment_image_src', [ $this, 'next3_wp_get_attachment_image_src' ], 99, 3 );
		add_filter( 'next3_image_get_intermediate_size', [ $this, 'next3_image_get_intermediate_size' ], 99, 3 );

        // Blocks
		if ( function_exists( 'is_wp_version_compatible' ) && is_wp_version_compatible( '5.9' ) ) {
			add_filter( 'render_block',[ $this, 'the_content' ], 119 );
			add_filter( 'get_block_templates', [ $this, 'filter_get_block_templates' ], 119, 3 );
			add_filter( 'get_block_template', [ $this, 'filter_get_block_template' ], 119, 3 );
		}

		// acf custom fields
		add_filter( 'acf/load_value/type=text', array( $this, 'the_content' ) );
		add_filter( 'acf/load_value/type=textarea', array( $this, 'the_content' ) );
		add_filter( 'acf/load_value/type=wysiwyg', array( $this, 'the_content' ) );
		add_filter( 'acf/update_value/type=text', array( $this, 'the_content' ) );
		add_filter( 'acf/update_value/type=textarea', array( $this, 'the_content' ) );
		add_filter( 'acf/update_value/type=wysiwyg', array( $this, 'the_content' ) );

    }

    public function edd_download_files( $value ) {
		$status = next3_check_rewrite('rewrite');
        
		if ( empty( $value ) ) {
			return $value;
		}

		foreach ( $value as $key => $attachment ) {
            $attachment_id = ($attachment['attachment_id']) ?? 0;
			if( !$status && false === next3_check_post_meta($attachment_id, 'next3_optimizer_is_converted_to_webp')){
				continue;
			}
			$url = next3_core()->action_ins->get_attatchment_url_preview($attachment_id, 'full');
			if ( !empty($url) ) {
				$value[ $key ]['file'] = $url;
			}
		}
		return $value;
	}

	public function theme_mod_header_image_data( $value, $old_value = false ) {
		$attachment_id = ($value->attachment_id) ?? 0;
		$status = next3_check_rewrite('rewrite');
        if( !$status && false === next3_check_post_meta($attachment_id, 'next3_optimizer_is_converted_to_webp')){
            return $value;
        }
		
		$url = next3_core()->action_ins->get_attatchment_url_preview($attachment_id, 'full');

		if ( $url ) {
			$value->url           = $url;
			$value->thumbnail_url = $url;
		}

		return $value;
	}

    public function theme_mod_background_image( $value, $old_value = false){
        if ( empty( $value ) || is_a( $value, 'stdClass' ) ) {
			return $value;
		}

        if ( empty( $value ) ) {
			return $value;
		}
		$value    = $this->process_content( $value );

        return $value;
    }

	public function customize_value_custom_css( $value, $setting ) {
		return $this->content_custom_css( $value, $setting->stylesheet );
	}

	public function update_custom_css_data( $data, $args ) {
		$data['css'] = $this->content_custom_css( $data['css'], $args['stylesheet'] );
		return $data;
	}

	public function wp_get_custom_css( $css, $stylesheet ) {
		return $this->content_custom_css( $css, $stylesheet );
	}

	protected function content_custom_css( $css, $stylesheet ) {
		if ( empty( $css ) ) {
			return $css;
		}

		$post_id  = $this->get_custom_css_post_id( $stylesheet );
		$cache    = $this->get_post_cache( $post_id );

		$css      = $this->process_content( $css, $cache);
		return $css;
	}

    public function filter_post_data( $post ) {
		global $pages;

		if ( is_array( $pages ) && 1 === count( $pages ) && ! empty( $pages[0] ) ) {
			$post->post_content = $pages[0];
		} else {
			$post->post_content = $this->process_content( $post->post_content);
		}
		$post->post_excerpt = $this->process_content( $post->post_excerpt);

	}

    public function the_content( $content ){
        if ( empty( $content ) ) {
			return $content;
		}
		
        $content  = $this->process_content( $content );
		
        return $content;
    }

    public function filter_content_pagination( $pages ) {
		foreach ( $pages as $key => $page ) {
			$pages[ $key ] = $this->process_content( $page );
		}
		return $pages;
	}

    public function filter_widget_display( $instance ) {
		return $this->handle_widget( $instance );
	}

	
	public function filter_widget_block_save( $value ) {
		if ( empty( $value ) || ! is_array( $value ) ) {
			return $value;
		}

		foreach ( $value as $idx => $section ) {
			$value[ $idx ] = $this->handle_widget( $section );
		}

		return $value;
	}
    public function widget_block_content( $content ) {
		
		if ( empty( $content ) ) {
			return $content;
		}
		
		$changed_content = $this->process_content( $content );

		if ( ! empty( $changed_content ) && $changed_content !== $content ) {
			$content = $changed_content;
		}
		
		return $content;
	}

    public function set_url_scheme( $url, $scheme, $orig_scheme ) {

		$settings_options = next3_options();
        $force_https = isset($settings_options['delivery']['force_https']) ? true : false;
		if (
			'http' === $scheme && empty( $orig_scheme ) &&
			$force_https 
		) {
			$parts = self::parse_url( $url );

			if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || 'http' !== $parts['scheme'] ) {
				return $url;
			}
			$ours = false;
			if ( $ours ) {
				return substr_replace( $url, 'https', 0, 4 );
			}
		}

		return $url;
	}

	public function next3_wp_get_attachment_url( $url, $post_id){
		$status = next3_check_rewrite('rewrite');
        if( !$status ){
            return $url;
        }
		$settings_options = next3_options();
        $force_https = isset($settings_options['delivery']['force_https']) ? true : false;
		
		$parts = self::parse_url( $url );
		if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || 'http' !== $parts['scheme'] ) {
			return $url;
		}
		if( $force_https ){
			return substr_replace( $url, 'https', 0, 4 );
		}
		return $url;
	}

	public function next3_wp_get_attachment_image_attributes( $attr, $attachment, $size ){
		$post_id = ($attachment->ID) ?? 0;
		$status = next3_check_rewrite('rewrite');
        if( !$status ){
            return $attr;
        }
		$src = ($attr['src']) ?? '';
		if( empty($src) ){
			return $attr;
		}

		$settings_options = next3_options();
        $force_https = isset($settings_options['delivery']['force_https']) ? true : false;
		
		$parts = self::parse_url( $src );
		if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || 'http' !== $parts['scheme'] ) {
			return $attr;
		}
		if( $force_https ){
			$attr['src'] = substr_replace( $src, 'https', 0, 4 );
		}
		return $attr;
	}

	public function next3_wp_get_attachment_image_src( $image, $attachment_id, $size ){
		$status = next3_check_rewrite('rewrite');
        if( !$status ){
            return $image;
        }

		if ( isset( $image[0] ) ) {
			$src = ($image[0]) ?? '';
			$settings_options = next3_options();
			$force_https = isset($settings_options['delivery']['force_https']) ? true : false;
			
			$parts = self::parse_url( $src );
			if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || 'http' !== $parts['scheme'] ) {
				return $image;
			}
			if( $force_https ){
				$image[0] = substr_replace( $src, 'https', 0, 4 );
			}
		}
		return $image;
	}

	public function next3_image_get_intermediate_size( $data, $post_id, $meta ){
		$status = next3_check_rewrite('rewrite');
        if( !$status ){
            return $data;
        }
		$src = ($data['url']) ?? '';
		if( empty($src) ){
			return $data;
		}

		$settings_options = next3_options();
        $force_https = isset($settings_options['delivery']['force_https']) ? true : false;
		
		$parts = self::parse_url( $src );
		if ( empty( $parts['scheme'] ) || empty( $parts['host'] ) || 'http' !== $parts['scheme'] ) {
			return $data;
		}
		if( $force_https ){
			$data['url'] = substr_replace( $src, 'https', 0, 4 );
		}
		return $data;
	}

    public function filter_get_block_templates( $query_result, $query, $template_type ) {
		if ( empty( $query_result ) ) {
			return $query_result;
		}

		foreach ( $query_result as $block_template ) {
			$block_template = $this->filter_get_block_template( $block_template, $block_template->id, $template_type );
		}

		return $query_result;
	}

    public function filter_get_block_template( $block_template, $id, $template_type ) {
		if ( empty( $block_template ) ) {
			return $block_template;
		}

		$content = $block_template->content;

		if ( empty( $content ) ) {
			return $block_template;
		}

		$content = $this->the_content( $content );

		if ( ! empty( $content ) && $content !== $block_template->content ) {
			$block_template->content = $content;
		}

		return $block_template;
	}

    protected function process_content( $content, $cache = []) {
		if ( empty( $content ) ) {
			return $content;
		}

		$type = 'cloud';
		$status = next3_check_rewrite('rewrite');
        if( !$status ){
            $type = 'wp';
        }
		
		$to_cache = isset($_GET['next3-cache']) ? $cache : $this->get_post_cache();

		$content = $this->pre_replace_content( $content );

        $url_pairs = $this->get_urls_from_img_src( $content, $to_cache );

		$url_pairs = $this->get_urls_from_content( $content, $url_pairs );

		$content   = $this->replace_urls( $content, $url_pairs, $type);

		return $content;
	}

    protected function pre_replace_content( $content ) {
		$uploads  = wp_upload_dir();
		$base_url = self::remove_scheme( $uploads['baseurl'] );
		return $this->remove_aws_query_strings( $content, $base_url );
	}

    public static function remove_aws_query_strings( $content, $base_url = '' ) {
		$pattern = '\?[^\s"<\?]*(?:X-Amz-Algorithm|AWSAccessKeyId|Key-Pair-Id|GoogleAccessId)=[^\s"<\?]+';
		$group   = 0;
		if ( ! is_string( $content ) ) {
			return $content;
		}
		if ( ! empty( $base_url ) ) {
			$pattern = preg_quote( $base_url, '/' ) . '[^\s"<\?]+(' . $pattern . ')';
			$group   = 1;
		}
		if ( ! preg_match_all( '/' . $pattern . '/', $content, $matches ) || ! isset( $matches[ $group ] ) ) {
			return $content;
		}
		$matches = array_unique( $matches[ $group ] );
		foreach ( $matches as $match ) {
			$content = str_replace( $match, '', $content );
		}
		return $content;
	}

    protected function get_urls_from_img_src( $content, $to_cache  ) {
		$url_pairs = $to_cache;

		if ( ! is_string( $content ) ) {
			return $url_pairs;
		}
		
		if ( ! preg_match_all( '/<img [^>]+>/', $content, $matches ) || ! isset( $matches[0] ) ) {
			return $url_pairs;
		}
        
		$matches      = array_unique( $matches[0] );
       
		foreach ( $matches as $image ) {
			if ( ! preg_match( '/wp-image-([0-9]+)/i', $image, $class_id ) || ! isset( $class_id[1] ) ) {
				continue;
			}
			if ( ! preg_match( '/src=\\\?["\']+([^"\'\\\]+)/', $image, $src ) || ! isset( $src[1] ) ) {
				continue;
			}
			$url = ($src[1]) ?? '';
            $id = absint( $class_id[1] );

			if( empty($id) || $id == 0){
				continue;
			}

			if( isset($to_cache[ $id . self::OPTION_KEY_ARR ]) ){
				continue;
			}

			if( 
				next3_get_post_meta($id, '_next3_attached_url') === false &&
				false === next3_check_post_meta($id, 'next3_optimizer_is_converted_to_webp')
			){
				continue;
			}

			$url_pairs[ $id . self::OPTION_KEY_ARR ] = [
                'wp' => next3wp_get_attachment_url($id),
                'cloud' => next3_core()->action_ins->get_attatchment_url_preview($id, 'full'),
				'id' => $id
            ];
		}
		if ( count( $url_pairs ) > 1 ) {
			$this->maybe_update_post_cache( $url_pairs );
		}
		
		return $url_pairs;
	}

	protected function get_urls_from_content( $content, $to_cache ) {
		$url_pairs = $to_cache;

		if ( ! is_string( $content ) ) {
			return $url_pairs;
		}

		if ( ! preg_match_all( '/(http|https)?:?\/\/[^"\'\s<>()\\\]*/', $content, $matches ) || ! isset( $matches[0] ) ) {
			return $url_pairs;
		}

		$matches = array_unique( $matches[0] );
		$urls    = array();

		foreach ( $matches as $url ) {
			$url = preg_replace( '/[^a-zA-Z0-9]$/', '', $url );

			if ( ! $this->url_needs_replacing( $url ) ) {
				continue;
			}
			$parts = self::parse_url( $url );

			if ( ! isset( $parts['path'] ) ) {
				continue;
			}

			if ( ! pathinfo( $parts['path'], PATHINFO_EXTENSION ) ) {
				continue;
			}

			$id = attachment_url_to_postid($url);
			if( empty($id) || $id == 0){
				continue;
			}

			if( isset($to_cache[ $id . self::OPTION_KEY_ARR ])){
				continue;
			}

			if( 
				next3_get_post_meta($id, '_next3_attached_url') === false &&
				false === next3_check_post_meta($id, 'next3_optimizer_is_converted_to_webp')
			){
				continue;
			}

			$url_pairs[ $id . self::OPTION_KEY_ARR ] = [
                'wp' => next3wp_get_attachment_url($id),
                'cloud' => next3_core()->action_ins->get_attatchment_url_preview($id, 'full'),
				'id' => $id
            ];
			
		}
		if ( count( $url_pairs ) > 1 ) {
			$this->maybe_update_post_cache( $url_pairs );
		}

		return $url_pairs;
	}

    protected function replace_urls( $content, $url_pairs, $type = 'cloud' ) {
		if ( empty( $url_pairs ) ) {
			return $content;
		}
		
		foreach ( $url_pairs as $key => $replace ) {
			$postid = ($replace['id']) ?? 0;
			if( $postid == 0 ){
				continue;
			}
			
			$wp = ($replace['wp']) ?? '';
			$cloud = ($replace['cloud']) ?? '';

			if( $type == 'cloud' && empty($cloud) ){
				continue;
			}
			if( $type == 'wp' && empty($wp) ){
				continue;
			}

			$find = ($type == 'cloud') ? $wp : $cloud;
			$replace = ($type == 'cloud') ? $cloud : $wp;

			// validation process end
            if( !empty($replace) ){
				$replace = self::encode_filename_in_path( $replace, $postid );
				$content = str_replace( $find, $replace, $content );
			}
			
		}

		return $content;
	}

	public function item_matches_src( $id, $url ) {
		$id = absint( $id );

		if ( $id == 0 || $id == '' ) {
			return false;
		}
		$meta = next3_get_post_meta( $id, '_wp_attachment_metadata');

		if ( ! isset( $meta['sizes'] ) ) {
			return false;
		}

		$base_url = self::encode_filename_in_path( next3wp_get_attachment_url($id), $id);
		$basename = wp_basename( $base_url );
		
		// Add full size URL
		$base_urls[] = $base_url;

		// Add additional image size URLs
		foreach ( $meta['sizes'] as $size ) {
			$base_urls[] = str_replace( $basename, self::encode_filename_in_path( $size['file'], $id), $base_url );
		}

		$url = self::encode_filename_in_path( self::reduce_url( $url ), $id);

		if ( in_array( $url, $base_urls ) ) {
			return true;
		}

		return false;
	}

    public static function encode_filename_in_path( $file, $id = 0) {
		if(empty($file)){
			return $file;
		}
		if( $id != 0  && true === next3_check_post_meta($id, 'next3_optimizer_is_converted_to_webp') ){
            if(strpos($file, ".webp") === false){
                $file .= '.webp';
            }
        }
        $url = parse_url( $file );

        if ( ! isset( $url['path'] ) ) {
            return $file;
        }

        if ( isset( $url['query'] ) ) {
            $file_name = wp_basename( str_replace( '?' . $url['query'], '', $file ) );
        } else {
            $file_name = wp_basename( $file );
        }

        if ( false !== strpos( $file_name, '%' ) ) {
            return $file;
        }

        $encoded_file_name = rawurlencode( $file_name );

        if ( $file_name === $encoded_file_name ) {
            return $file;
        }

        return str_replace( $file_name, $encoded_file_name, $file );
    }

    protected function handle_widget( $instance ) {
		if ( empty( $instance ) || ! is_array( $instance ) ) {
			return $instance;
		}
		
		$update_cache = true;

		if ( isset( $_POST['wp_customize'] ) && 'on' === $_POST['wp_customize'] ) {
			$update_cache = false;
		}

		foreach ( $instance as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			if ( in_array( $key, array( 'text', 'content' ) ) || self::is_url( $value )) {
				$instance[ $key ] = $this->process_content( $value );
			}
		}
		
		return $instance;
	}

	protected function maybe_update_post_cache( $to_cache, $post_id = false ) {
		$post_id = self::get_post_id( $post_id );
		if ( ! $post_id || empty( $to_cache ) ) {
			return;
		}
		
		$cached = $this->get_post_cache( $post_id, false );
		$urls   = static::merge_cache( $cached, $to_cache );

		$settings_options = next3_options();
        $disable_cache = ($settings_options['delivery']['disable_cache']) ?? 'no';

		if($disable_cache == 'yes' || isset($_GET['next3-cache']) ){
			if ( wp_using_ext_object_cache() ) {
				wp_cache_delete( $post_id, self::POST_CACHE_GROUP);
			}else{
				next3_delete_post_meta( $post_id, self::CACHE_KEY );
			}
		}

		if ( $urls !== $cached ) {
			$this->set_post_cache( $post_id, $urls );
		}
	}

    protected function set_post_cache( $post, $data ) {
		$post_id = self::get_post_id( $post );
		if ( ! $post_id ) {
			return;
		}
		if ( wp_using_ext_object_cache() ) {
			$expires = apply_filters( 'next3_' . self::POST_CACHE_GROUP . '_expires', DAY_IN_SECONDS, $post_id, $data );
			wp_cache_set( $post_id, $data, self::POST_CACHE_GROUP, $expires );
		} else {
			next3_update_post_meta( $post_id, self::CACHE_KEY, $data );
		}
	}

    public function get_post_cache( $post = null, $transform_ints = true ) {
		$post_id = self::get_post_id( $post );
		if ( ! $post_id ) {
			return array();
		}
		if ( wp_using_ext_object_cache() ) {
			$cache = wp_cache_get( $post_id, self::POST_CACHE_GROUP );
		} else {
			$cache = next3_get_post_meta( $post_id, self::CACHE_KEY);
		}

		$settings_options = next3_options();
        $disable_cache = ($settings_options['delivery']['disable_cache']) ?? 'no';

		if ( empty( $cache ) || $disable_cache == 'yes') {
			$cache = array();
		}

		if ( ! $transform_ints ) {
			return $cache;
		}
		return $cache;
	}

    protected function set_option_cache( $data ) {
		if ( wp_using_ext_object_cache() ) {
			$expires = apply_filters( 'next3_' . self::OPTION_CACHE_GROUP . '_expires', DAY_IN_SECONDS, self::CACHE_KEY, $data );
			wp_cache_set( self::CACHE_KEY, $data, self::OPTION_CACHE_GROUP, $expires );
		} else {
			next3_update_option( self::CACHE_KEY, $data );
		}
	}

	protected function get_option_cache() {
		if ( wp_using_ext_object_cache() ) {
			$cache = wp_cache_get( self::CACHE_KEY, self::OPTION_CACHE_GROUP );
		} else {
			$cache = next3_get_option( self::CACHE_KEY, array() );
		}

		if ( empty( $cache ) ) {
			$cache = array();
		}
		return $cache;
	}

    public static function get_post_id( $post = null ) {
        return (int) get_post_field( 'ID', $post );
    }

	protected function get_custom_css_post_id( $stylesheet ) {
		$post = wp_get_custom_css_post( $stylesheet );

		if ( ! $post ) {
			return 0;
		}

		return $post->ID;
	}

	public static function merge_cache( $existing_cache, $merge_cache ) {
		if ( ! empty( $existing_cache ) ) {
			$post_cache_keys = array_map( 'self::reduce_url', array_keys( $existing_cache ) );
			$existing_cache  = array_combine( $post_cache_keys, $existing_cache );
		}

		if ( ! empty( $merge_cache ) ) {
			$add_cache_keys = array_map( 'self::reduce_url', array_keys( $merge_cache ) );
			$merge_cache    = array_combine( $add_cache_keys, $merge_cache );
		}

		return array_merge( $existing_cache, $merge_cache );
	}
    
	public static function remove_scheme( $url ) {
        return preg_replace( '/^(?:http|https):/', '', $url );
    }

    public static function parse_url( $url, $component = -1 ) {
        $url       = trim( $url );
        $no_scheme = 0 === strpos( $url, '//' );
        if ( $no_scheme ) {
            $url = 'http:' . $url;
        }
        $parts = parse_url( $url, $component );
        if ( 0 < $component ) {
            return $parts;
        }
        if ( $no_scheme && is_array( $parts ) ) {
            unset( $parts['scheme'] );
        }
        return $parts;
    }

	public static function is_url( $string ) {
		if ( ! is_string( $string ) ) {
			return false;
		}

		if ( preg_match( '@^(?:https?:)?//[a-zA-Z0-9\-]+@', $string ) ) {
			return true;
		}

		return false;
	}

    public static function reduce_url( $url ) {
		return $url;

        $parts = self::parse_url( $url );
        $host  = isset( $parts['host'] ) ? $parts['host'] : '';
        $port  = isset( $parts['port'] ) ? ":{$parts['port']}" : '';
        $path  = isset( $parts['path'] ) ? $parts['path'] : '';

        return '//' . $host . $port . $path;
    }

	public function url_needs_replacing( $url ) {
		if ( str_replace( $this->get_bare_upload_base_urls(), '', $url ) === $url ) {
			return false;
		}
		return true;
	}

	public function get_bare_upload_base_urls( $refresh = false ){

		$base_urls = array();
		if ( $refresh || empty( $base_urls ) ) {
			$domains = array();

			$uploads     = wp_upload_dir();
			$base_url    = self::remove_scheme( $uploads['baseurl'] );
			$orig_domain = self::parse_url( $base_url, PHP_URL_HOST );
			$port        = self::parse_url( $base_url, PHP_URL_PORT );
			if ( ! empty( $port ) ) {
				$orig_domain .= ':' . $port;
			}

			$domains[] = $orig_domain;
			$base_urls = array( $base_url );

			$base_url    = next3_core()->action_ins->local_subsite_url( $uploads['baseurl'] );
			$base_url    = self::remove_scheme( $base_url );
			$curr_domain = self::parse_url( $base_url, PHP_URL_HOST );
			$port        = self::parse_url( $base_url, PHP_URL_PORT );
			if ( ! empty( $port ) ) {
				$curr_domain .= ':' . $port;
			}

			if ( $curr_domain !== $orig_domain ) {
				$domains[] = $curr_domain;
			}

			$domains = apply_filters( 'next3_local_domains', $domains );

			if ( ! empty( $domains ) ) {
				foreach ( array_unique( $domains ) as $match_domain ) {
					$base_urls[] = substr_replace( $base_url, $match_domain, 2, strlen( $curr_domain ) );
				}
			}
		}

		return array_unique( $base_urls );
	}


    public static function instance(){
		if (!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
	}
}