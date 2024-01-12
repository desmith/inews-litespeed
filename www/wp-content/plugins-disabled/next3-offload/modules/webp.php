<?php 
namespace Next3Offload\Modules;
defined( 'ABSPATH' ) || exit;

class Webp{
    private static $instance;

    const PNGS_SIZE_LIMIT = 1048576;

    public $options_map = array(
      'completed' => 'next3_optimizer_webp_conversion_completed',
      'status'    => 'next3_optimizer_webp_conversion_status',
      'stopped'   => 'next3_optimizer_webp_conversion_stopped',
    );

    public $non_optimized = 'next3_optimizer_total_non_converted_images';

    public $batch_skipped = 'next3_optimizer_is_converted_to_webp';

    public function init() {
      $settings_options = next3_options();

      $webp_enable = ($settings_options['optimization']['webp_enable']) ?? 'no';
      $compression_enable = ($settings_options['optimization']['compression']) ?? 'no';
      if( $webp_enable == 'yes' && $compression_enable != 'yes'){
        add_action( 'delete_attachment', array( $this, 'delete_webp_copy' ) );
        add_action( 'edit_attachment', array( $this, 'regenerate_webp_copy' ) );
        add_action( 'wp_generate_attachment_metadata', array( $this, 'optimize_new_image' ), 10, 2 );
      }else{
        //add_action( 'wp_generate_attachment_metadata', array( $this, 'maybe_update_total_unoptimized_images' ) );
      }

    }
    
    public function delete_webp_files() {
      $basedir = self::get_uploads_dir();
      exec( "find $basedir -name '*.webp' -type f -print0 | xargs -L 500 -0 rm", $output, $result );
  
      $this->reset_image_optimization_status();
  
      return $result;
    }

    public function delete_webp_copy( $id ) {
      
      $main_image = next3_get_attached_file( $id, true);
      $metadata   = next3_wp_get_attachment_metadata( $id, true);
      $basename   = basename( $main_image );
  
      if ( !file_exists( $main_image . '.webp' ) ) {
          return;
      }
      @unlink( $main_image . '.webp' );
  
      if ( ! empty( $metadata['sizes'] ) ) {
        // Loop through all image sizes and optimize them as well.
        foreach ( $metadata['sizes'] as $size ) {
          @unlink( str_replace( $basename, $size['file'], $main_image ) . '.webp' );
        }
      }
      
    }

    public function regenerate_webp_copy( $id ) {
      $this->delete_webp_copy( $id );
      $metadata = next3_wp_get_attachment_metadata( $id, true);
  
      $this->optimize( $id, $metadata );
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
      // Get the basename.
      $basename = basename( $main_image );
  
      // Get the command placeholder. It will be used by main image and to optimize the different image sizes.
      $status = $this->generate_webp_file( $main_image );
  
      // conversion failed.
      if ( true === boolval( $status ) ) {
        return false;
      }
  
      if ( ! empty( $metadata['sizes'] ) ) {
        // Loop through all image sizes and optimize them as well.
        foreach ( $metadata['sizes'] as $size ) {
          // Replace main image with the cropped image and run the conversion command.
          $status = $this->generate_webp_file( str_replace( $basename, $size['file'], $main_image ) );
  
          // conversion failed.
          if ( true === boolval( $status ) ) {
            return false;
          }
        }
      }
  
      // Everything ran smoothly.
      if ( true !== boolval( $status ) ) {
        next3_update_post_meta( $id, 'next3_optimizer_is_converted_to_webp', 1 );
      }
      return true;
    }

    public function generate_webp_file( $filepath ) {
      // Bail if the file doens't exists or if the webp copy already exists.
      if ( ! file_exists( $filepath ) ) {
        return true;
      }
  
      if ( file_exists( $filepath . '.webp' ) ) {
        @unlink( $filepath . '.webp' ); //phpcs:ignore
      }
  
      // Get image type.
      $type = exif_imagetype( $filepath );
  
      $quality      = apply_filters( 'next3_webp_quality', 80 );
      $quality_type = intval( apply_filters( 'next3_webp_quality_type', 0 ) );
  
      switch ( $type ) {
        case IMAGETYPE_GIF:
          // Default quality type for GIF is lossless.
          $quality_type = 1 !== $quality_type ? '' : '-lossy';
          $placeholder  = 'gif2webp -q %1$s %2$s %3$s -o %3$s.webp 2>&1';
          break;
  
        case IMAGETYPE_JPEG:
          // Default quality type for JPEG is lossy.
          $quality_type = 1 !== $quality_type ? '' : '-lossless';
          $placeholder  = 'cwebp -q %1$s %2$s %3$s -o %3$s.webp 2>&1';
          break;
  
        case IMAGETYPE_PNG:
          // Bail if the image is bigger than 500k.
          // PNG usage is not recommended and images bigger than 500kb
          // hit the limits.
          if ( filesize( $filepath ) > self::PNGS_SIZE_LIMIT ) {
            return true;
          }
          // Default quality type for PNG is lossy.
          $quality_type = 1 !== $quality_type ? '' : '-lossless';
          $placeholder  = 'cwebp -q %1$s %2$s %3$s -o %3$s.webp 2>&1';
          break;
  
        default:
          // Bail if the image type is not supported.
          return true;
      }
  
      // Optimize the image.
      exec(
        sprintf(
          $placeholder, // The command.
          $quality, // The quality %.
          $quality_type, // The quality type -lossless or -lossy.
          $filepath // Image path.
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

    public function reset_image_optimization_status() {
      global $wpdb;
  
      $wpdb->query(
        "
          DELETE FROM $wpdb->postmeta
          WHERE `meta_key` = '" . $this->batch_skipped . "'
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