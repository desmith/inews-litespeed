<?php 
namespace Next3Offload\Modules;
defined( 'ABSPATH' ) || exit;

class Optimizer{
    private static $instance;

    public $default_max_width_sizes = array(
		array(
			'label' => '2560px',
			'value' => 2560,
			'selected' => 0,
		),
		array(
			'label' => '2048px',
			'value' => 2048,
			'selected' => 0,
		),
		array(
			'label' => '1920px',
			'value' => 1920,
			'selected' => 0,
		),
		array(
			'label' => 'Disabled',
			'value' => 0,
			'selected' => 0,
		),
	);

    const BATCH_LIMIT = 200;

	const PNGS_SIZE_LIMIT = 1048576;

	public $type = 'image';

	public $non_optimized = 'next3_optimizer_total_unoptimized_images';

	public $batch_skipped = 'next3_optimizer_is_optimized';

	public $process_map = array(
		'filter'   => 'next3_optimizer_image_optimization_timeout',
		'attempts' => 'next3_optimizer_optimization_attempts',
		'failed'   => 'next3_optimizer_optimization_failed',
	);

    public $options_map = array(
		'completed' => 'next3_optimizer_image_optimization_completed',
		'status'    => 'next3_optimizer_image_optimization_status',
		'stopped'   => 'next3_optimizer_image_optimization_stopped',
	);
	
	public $compression_level_map = array(
		// IMAGETYPE_GIF.
		1 => array(
			'1'    => '-O1', // Low.
			'2' => '-O2', // Medium.
			'3'   => '-O3', // High.
		),
		// IMAGETYPE_JPEG.
		2 => array(
			'1'    => '-m85', // Low.
			'2' => '-m60', // Medium.
			'3'   => '-m20', // High.
		),
		// IMAGETYPE_PNG.
		3 => array(
			'1'    => '-o1',
			'2' => '-o2',
			'3'   => '-o3',
		),
	);


    public function init() {
        // Get the resize_images option
        $settings_options = next3_options();
        $optimizer_resize_images = ($settings_options['optimization']['optimizer_resize_images']) ?? 2560;

		$resize_images = apply_filters( 'next3_set_max_image_width', intval( $optimizer_resize_images ) );
		// Resize newly uploaded images
		if ( 2560 !== $resize_images ) {
			add_filter( 'big_image_size_threshold', array( $this, 'resize' ) );
		}
        $compression_enable = ($settings_options['optimization']['compression']) ?? 'no';
        if( $compression_enable != 'yes'){
            return;
        }
        $compression_level = ($settings_options['optimization']['compression_level']) ?? '0';

        // Optimize newly uploaded images.
		if ( '0' !== $compression_level ) {
			add_action( 'delete_attachment', array( $this, 'delete_backups' ) );
			add_action( 'wp_generate_attachment_metadata', array( $this, 'optimize_new_image' ), 10, 2 );
		} else {
			//add_action( 'wp_generate_attachment_metadata', array( $this, 'maybe_update_total_unoptimized_images' ) );
		}

        add_action( 'edit_attachment', array( $this, 'custom_attachment_compression_level' ) );
		add_filter( 'attachment_fields_to_edit', array( $this, 'custom_attachment_compression_level_field' ), null, 2 );

    }

    public function resize( $image_data ) {
		// Getting the option value from the db and applying additional filters, if any.
		$settings_options = next3_options();
        $optimizer_resize_images = ($settings_options['optimization']['optimizer_resize_images']) ?? 2560;

		// Disable resize, if it's set so in the DB and no filters are found.
		if ( 0 === intval ( $optimizer_resize_images ) ) {
			return false;
		}

		// Adding a min value.
		$optimizer_resize_images = intval( $optimizer_resize_images ) < 1200 ? 1200 : intval( $optimizer_resize_images );

		return intval( $optimizer_resize_images );
	}

    public function delete_backups( $id ) {
		//global $wp_filesystem;

        /*require_once ( ABSPATH . '/wp-admin/includes/file.php' );
        WP_Filesystem();
		*/
		$main_image = next3_get_attached_file( $id, true);
		$metadata   = wp_get_attachment_metadata( $id, true);
		$basename   = basename( $main_image );
        $backup_file = preg_replace( '~.(png|jpg|jpeg|gif)$~', '.bak.$1', $main_image );
        if ( !file_exists( $backup_file ) ) {
            return;
        }
		//$wp_filesystem->delete( preg_replace( '~.(png|jpg|jpeg|gif)$~', '.bak.$1', $main_image ) );
		@unlink( preg_replace( '~.(png|jpg|jpeg|gif)$~', '.bak.$1', $main_image ) );

		if ( ! empty( $metadata['sizes'] ) ) {
			// Loop through all image sizes and optimize them as well.
			foreach ( $metadata['sizes'] as $size ) {
				//$wp_filesystem->delete( preg_replace( '~.(png|jpg|jpeg|gif)$~', '.bak.$1', str_replace( $basename, $size['file'], $main_image ) ) );
				@unlink( preg_replace( '~.(png|jpg|jpeg|gif)$~', '.bak.$1', str_replace( $basename, $size['file'], $main_image ) ) );
			}
		}
		
	}

    public function optimize_new_image( $data, $attachment_id ) {
		// Optimize the image.
		$this->optimize( $attachment_id, $data );

		// Return the attachment data.
		return $data;
	}

    public function optimize( $id, $metadata ) {
		// Load the uploads dir.
		$upload_dir = wp_get_upload_dir();
		// Get path to main image.
		$main_image = next3_get_attached_file( $id, true);

        $settings_options = next3_options();
        $compression_level = ($settings_options['optimization']['compression_level']) ?? 0;
        $overwrite_custom = ($settings_options['optimization']['overwrite_custom']) ?? 'no';
        $webp_enable = ($settings_options['optimization']['webp_enable']) ?? 'no';

		// Bail if the override is disabled and the image has a custom compression level.
        if (
			'yes' !== $overwrite_custom &&
			! empty( next3_get_post_meta( $id, 'next3_optimizer_compression_level') )
		) {
			return false;
		}

		// Get the basename.
		$basename = basename( $main_image );
		// Get the command placeholder. It will be used by main image and to optimize the different image sizes.
		$status = $this->execute_optimization_command( $main_image );

		// Optimization failed.
		if ( true === boolval( $status ) ) {
			next3_update_post_meta( $id, 'next3_optimizer_optimization_failed', 1 );
			return false;
		}

		// Check if there are any sizes.
		if ( ! empty( $metadata['sizes'] ) ) {
			// Loop through all image sizes and optimize them as well.
			foreach ( $metadata['sizes'] as $size ) {
				// Replace main image with the cropped image and run the optimization command.
				$status = $this->execute_optimization_command( str_replace( $basename, $size['file'], $main_image ) );

				// Optimization failed.
				if ( true === boolval( $status ) ) {
					next3_update_post_meta( $id, 'next3_optimizer_optimization_failed', 1 );
					return false;
				}
			}
		}

		// Save the original filesize in new post meta.
		next3_update_post_meta( $id, 'next3_optimizer_original_filesize', $metadata['filesize'] ) ;
		// Replace the filesize in the metadata.
		$metadata['filesize'] = filesize( $main_image );

		// Update the attachment metadata.
		wp_update_attachment_metadata( $id, $metadata );

		// Everything ran smoothly.
		next3_update_post_meta( $id, 'next3_optimizer_is_optimized', 1 );

        if ( $webp_enable == 'yes' && file_exists( $main_image . '.webp' ) ) {
            next3_update_post_meta( $id, 'next3_optimizer_is_converted_to_webp', 1 );
        }

		return true;
	}

    private function execute_optimization_command( $filepath, $compression_level = null ) {
		// Bail if the file doens't exists.
		if ( ! file_exists( $filepath ) ) {
			return true;
		}
        
        $settings_options = next3_options();
        $compression_level_option = ($settings_options['optimization']['compression_level']) ?? 0;
        $backup_orginal = ($settings_options['optimization']['backup_orginal']) ?? 'no';
        $webp_enable = ($settings_options['optimization']['webp_enable']) ?? 'no';

        $compression_level = is_null( $compression_level ) ? intval( $compression_level_option ) : $compression_level;

		// Bail if compression level is set to None.
		if ( 0 == $compression_level ) {
			return true;
		}
        
		$backup_filepath = preg_replace( '~.(png|jpg|jpeg|gif)$~', '.bak.$1', $filepath );

		if (
			$backup_orginal == 'yes' &&
			! file_exists( $backup_filepath )
		) {
			copy( $filepath, $backup_filepath );
		}
        
		$status = $this->optimize_image(
			file_exists( $backup_filepath ) ? $backup_filepath : $filepath,
			$compression_level
		);
        
		// Create webp copy of the webp is enabled.
		if ( $webp_enable == 'yes' ) {
			next3_core()->webp_ins->generate_webp_file( $filepath  );
		}

		return $status;
	}


    public function optimize_image( $filepath, $level ) {
		// Get image type.
		$type = exif_imagetype( $filepath );

		$output_filepath = preg_replace( '~\.bak.(png|jpg|jpeg|gif)$~', '.$1', $filepath );
        
		switch ( $type ) {
			case IMAGETYPE_GIF:
				$placeholder = 'gifsicle %s --careful %s -o %s 2>&1';
				break;

			case IMAGETYPE_JPEG:
				// DO NOT REMOVE THE LINE BELOW!
				// The jpegoptim doesn't support input/output params, so we need to create a backup of the original image.
				// However, if the filepaths are the same, this is skipped.
				if ( $filepath !== $output_filepath ) {
					copy( $filepath, $output_filepath );
				}
				$placeholder = 'jpegoptim %1$s %3$s 2>&1';
				break;

			case IMAGETYPE_PNG:
				// Bail if the image is bigger than 500k.
				// PNG usage is not recommended and images bigger than 500kb
				// hit the limits.
				if ( filesize( $filepath ) > self::PNGS_SIZE_LIMIT ) {
					return true;
				}
				$placeholder = 'optipng %s %s -out=%s 2>&1';
				break;

			default:
				// Bail if the image type is not supported.
				return true;
		}
        
		// Optimize the image.
		exec(
			sprintf(
				$placeholder, // The command.
				$this->compression_level_map[ $type ][ $level ], // The compression level.
				$filepath, // Image path.
				$output_filepath // New Image path.
			),
			$output,
			$status
		);
        
		return $status;
	}

    public function maybe_update_total_unoptimized_images( $data ) {
		if ( next3_check_options( $this->options_map['status'] ) ) {
			return $data;
		}
		next3_update_option(
			$this->non_optimized,
			next3_get_option( $this->non_optimized, 0 ) + 1
		);
		// Return the attachment data.
		return $data;
	}

    public function custom_attachment_compression_level( $attachment_id ) {
		
        if ( ! isset( $_REQUEST['next3_compression_level'] ) ) {
			return $attachment_id;
		}
        
        // Get attachment's filepath.
		$filepath = next3_get_attached_file( $attachment_id, true);

        if ( ! file_exists( $filepath ) ) {
			return $attachment_id;
		}
		// Update the attachment's meta.
		next3_update_post_meta( $attachment_id, 'next3_optimizer_compression_level', $_REQUEST['next3_compression_level'] ); // phpcs:ignore
        
		// Find backup image path.
		$backup_filepath = preg_replace( '~.(png|jpg|jpeg|gif)$~', '.bak.$1', $filepath );

		// Check if backup file exists, if so, replace the file with the original one.
		if ( file_exists( $backup_filepath ) ) {
			copy( $backup_filepath, $filepath );
		}

		// Compress the image only if the compression level is different than none.
		if ( 0 !== intval( $_REQUEST['next3_compression_level'] ) ) {
			// Optimize the image with the new compression level.
			return $this->execute_optimization_command( $filepath, intval( $_REQUEST['next3_compression_level'] ) );
		}
	}
    public function custom_attachment_compression_level_field( $form_fields, $post ) {
		// Get current attachment compression level.
		$field_value = next3_get_post_meta( $post->ID, 'next3_optimizer_compression_level');

		// If field value is empty - fallback to site global option.
		if ( ! is_numeric( $field_value ) ) {
			$settings_options = next3_options();
            $field_value = ($settings_options['optimization']['compression_level']) ?? 0;
		}

		// The field html.
		$html = '<select name="next3_compression_level">';

		// Select options.
		$options = apply_filters('next3/selected/compression/level', next3_allowed_compression_level());

		// Add the select options to the html.
		foreach ( $options as $key => $value ) {
			$html .= '<option' . selected( $field_value, $key, false ) . ' value="' . $key . '">' . $value . '</option>';
		}

		$html .= '</select>';

		$form_fields['compression_level'] = array(
			'value' => $field_value ? intval( $field_value ) : '',
			'label' => __( 'Compression Level', 'next3-offload' ),
			'input' => 'html',
			'html'  => $html,
		);

		return $form_fields;
	}

    public function get_human_readable_size( $filepath ) {
		// Get the size of the file in bytes.
		$size = filesize( $filepath );
		// Possible unit types.
		$units = array( 'B', 'kB', 'MB' );
		$step = 1024;
		$i = 0;

		// Divide the size until it's less than 1 to find the correct unit.
		while ( ( $size / $step ) > 0.9 ) {
			$size = $size / $step;
			$i++;
		}
		// Return the human readable string.
		return round( $size, 2 ) . $units[ $i ];
	}

    public static function check_for_unoptimized_images( $type ) {

		$meta = array(
			'image' => array(
				'next3_optimizer_is_optimized',
				'next3_optimizer_optimization_failed',
			),
			'webp'  => array(
				'next3_optimizer_is_converted_to_webp',
				'next3_optimizer_webp_conversion_failed',
			),
		);

		$images = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => array(
					// Skip optimized images.
					array(
						'key'     => $meta[ $type ][0],
						'compare' => 'NOT EXISTS',
					),
					// Also skip failed optimizations.
					array(
						'key'     => $meta[ $type ][1],
						'compare' => 'NOT EXISTS',
					),
				),
			)
		);

		return count( $images );
	}

	public function restore_originals() {
		$basedir = self::get_uploads_dir();

		exec( "find $basedir -regextype posix-extended -type f -regex '.*bak.(png|jpg|jpeg|gif)$' -exec rename '.bak' '' {} \;", $output, $result );

		// Reset the images metadata.
		$this->reset_images_filesize_meta();
		// Reset the optimization status.
		$this->reset_image_optimization_status();

		return $result;
	}
	public function reset_images_filesize_meta() {
		// Get all images with backup filesize metadata available.
		$images = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_mime_type' => 'image',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'     => 'next3_optimizer_original_filesize',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		// Bail if we have no images with backup filesize metadata.
		if ( empty( $images ) ) {
			return;
		}

		// Restore the filesize metadata.
		foreach( $images as $image_id ) {
			// Get the image metadata.
			$metadata = next3_wp_get_attachment_metadata( $image_id, true);
			// Restore the original filesize metdata.
			$metadata['filesize'] = next3_get_post_meta( $image_id, 'next3_optimizer_original_filesize');
			// Update the attachment metadata.
			wp_update_attachment_metadata( $image_id, $metadata );
		}
	}

	public function reset_image_optimization_status() {
		global $wpdb;

		$wpdb->query(
			"
				DELETE FROM $wpdb->postmeta
				WHERE `meta_key` = '" . $this->batch_skipped . "'
				OR `meta_key` = '" . $this->process_map['failed'] . "'
				OR `meta_key` = 'next3_optimizer_original_filesize'
			"
		);
	}

	public static function get_uploads_dir() {
		// Get the uploads dir.
		$upload_dir = wp_upload_dir();

		$base_dir = $upload_dir['basedir'];

		if ( defined( 'UPLOADS' ) ) {
			$base_dir = ABSPATH . UPLOADS;
		}

		return $base_dir;
	}
    public static function instance(){
		if (!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
	}
}