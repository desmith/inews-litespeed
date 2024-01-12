<?php
namespace Next3Offload\Utilities;
defined( 'ABSPATH' ) || exit;

class N3aws_Admin{
	
    private static $instance;

    public $menulist = [];

    protected static $is_installing_or_updating_plugins;

    public function init() { 

        if(current_user_can('manage_options')){
            add_filter( 'plugin_action_links_' . plugin_basename( next3_core()->plugin::plugin_file() ), [ $this , '_action_links'] );
            add_filter( 'plugin_row_meta', [ $this, '_plugin_row_meta'], 10, 2 );
            
            add_action( 'admin_enqueue_scripts', [ $this , 'load_script'] );
            // admin script
            add_action( 'admin_enqueue_scripts', [ $this , '_admin_scripts'] );

            // Valid Check
            if( class_exists('\Next3Offload\Utilities\Check\N3aws_Valid') ){
                Check\N3aws_Valid::instance()->init();
            }
            if ( is_multisite() && NEXT3_MULTI_SITE ) {
                add_action( 'network_admin_menu', [$this, 'admin_menu'] );
            } else {
                add_action( 'admin_menu', [$this, 'admin_menu'] );
            }
            add_action( 'admin_init', [$this, 'handle_tools_action'] );

            // dashboard
            N3aws_Dashboard::instance()->_init();

            // new setup
            add_filter( 'network_admin_plugin_action_links', [ $this, 'plugin_actions_settings_link' ], 10, 2 );
		    add_filter( 'pre_get_space_used', [ $this, 'mlsite_get_space_used' ] );

            add_action( 'admin_notices', [ $this, 'hook_admin_notices' ] );
			add_action( 'network_admin_notices', [ $this, 'hook_admin_notices' ] );

            // admin menu bar
            add_action( 'admin_bar_menu', [ $this, 'admin_bar_item'], 500 );
        }
        add_action( 'wp_enqueue_scripts', [ $this , 'load_script'] );   
        // public script
        add_action( 'wp_enqueue_scripts', [ $this , '_public_scripts'] );

        // plugin updater
        add_action( 'admin_init', array( $this, 'init_plugin_updater' ), 0 );
        
        // schedule cron job
        add_filter('cron_schedules', function( $schedules ){
            if( !isset($schedules['per_10_sec'])){
                $schedules['per_10_sec'] = [
                    'interval' => apply_filters( 'next3_update_cron_interval', 10 ) * 60,
                    'display'  => sprintf( __( 'Every Per %d Seconds', 'next3-offload' ), apply_filters( 'next3_update_cron_interval', 10 ) ),
                ];
            }
            return $schedules;
        });
        //start cron hook
        if (! wp_next_scheduled ( 'next3_cron_hook' )) {
            wp_schedule_event( time(), 'per_10_sec', 'next3_cron_hook' );
        }
        if (! wp_next_scheduled ( 'next3_cron_license' )) {
            wp_schedule_event( time(), 'weekly', 'next3_cron_license' );
        }
        
        // end cron hook
        register_deactivation_hook( next3_core()->plugin::plugin_file(), function(){
            wp_clear_scheduled_hook( 'next3_cron_hook' );
            wp_clear_scheduled_hook( 'next3_cron_license' );
        } );

        // action cron hook
        add_action( 'next3_cron_license', [ $this, 'next3_schedule_action_license'] );
        add_action( 'next3_cron_hook', [ $this, 'next3_schedule_action'] );

    }
    public function admin_menu(){
        $capability = nx3aws_admin_role();

        $this->menulist[] = add_menu_page(
            esc_html__('Next3 Offload', 'next3-offload'),
            esc_html__('Next3 Offload', 'next3-offload'),
            $capability,
            'next3aws',
            [$this, 'setupOffload'],
            esc_url( next3_core()->plugin::plugin_url() . 'assets/img/icon.svg'),
            55
        );
        $setup = $this->check_setup();
        $step = ($setup['step']) ?? 'license';
    
        if( in_array($step, ['license', 'provider', 'config']) ){
            remove_submenu_page( 'next3aws', 'next3aws');
            $this->menulist[] = add_submenu_page( 'next3aws', esc_html__( 'Setup', 'next3-offload' ), esc_html__( 'Setup', 'next3-offload' ), $capability, 'next3aws', [ $this ,'setupOffload'], 1);
            
        } else {
            do_action( 'next3aws/admin_menu/top', $this->menulist);
            //remove_submenu_page( 'next3aws', 'next3config');

            $get_package = next3_license_package();
            
            $this->menulist[] = add_submenu_page( 'next3aws', esc_html__( 'File manager', 'next3-offload' ), esc_html__( 'File manager', 'next3-offload' ), $capability, 'next3aws-file', [ $this ,'setupOffload'], 1);
            
            remove_submenu_page( 'next3aws', 'next3aws');
            
            $this->menulist[] = add_submenu_page( 'next3aws', esc_html__( 'Storage', 'next3-offload' ), esc_html__( 'Storage', 'next3-offload' ), 'manage_options', 'next3aws#ntab=settings', '__return_null');
            $this->menulist[] = add_submenu_page( 'next3aws', esc_html__( 'Delivery', 'next3-offload' ), esc_html__( 'Delivery', 'next3-offload' ), 'manage_options', 'next3aws#ntab=delivery', '__return_null');
            
            do_action( 'next3aws/admin_menu/middle', $this->menulist);

            if( in_array($get_package, ['business', 'developer', 'extended']) ){
                add_submenu_page( 'next3aws', esc_html__( 'Optimization', 'next3-offload' ), esc_html__( 'Optimization', 'next3-offload' ), 'manage_options', 'admin.php?page=next3aws#ntab=optimization', '', 3);
            }
            if( in_array($get_package, ['developer', 'extended']) ){
                add_submenu_page( 'next3aws', esc_html__( 'Assets', 'next3-offload' ), esc_html__( 'Assets', 'next3-offload' ), 'manage_options', 'admin.php?page=next3aws#ntab=assets', '', 4);
            }

            add_submenu_page( 'next3aws', esc_html__( 'Offload', 'next3-offload' ), esc_html__( 'Offload', 'next3-offload' ), 'manage_options', 'admin.php?page=next3aws#ntab=offload', '', 5);
            
            add_submenu_page( 'next3aws', esc_html__( 'Tools', 'next3-offload' ), esc_html__( 'Tools', 'next3-offload' ), 'manage_options', 'admin.php?page=next3aws#ntab=tools', '', 6);
            //add_submenu_page( 'next3aws', esc_html__( 'Export & Import', 'next3-offload' ), esc_html__( 'Export & Import', 'next3-offload' ), 'manage_options', 'admin.php?page=next3aws#ntab=imports', '', 7);
            
            do_action( 'next3aws/admin_menu/bottom', $this->menulist);

            if( !NEXT3_SELF_MODE ){
                add_submenu_page( 'next3aws', esc_html__( 'Active License', 'next3-offload' ), esc_html__( 'Active License', 'next3-offload' ), 'manage_options', 'admin.php?page=next3aws#ntab=license', '', 100);
            }

            //add_submenu_page( 'next3aws', esc_html__( 'Addons', 'next3-offload' ), esc_html__( 'Addons', 'next3-offload' ), 'manage_options', 'admin.php?page=next3aws#ntab=addons', '', 101);

            apply_filters('next3aws/admin_menu', $this->menulist);
        }
    }

    public function admin_bar_item( \WP_Admin_Bar $admin_bar ){
        if ( ! current_user_can( 'manage_options' ) || is_admin()) {
            $admin_bar->add_menu( array(
                'id'    => 'next3-settings',
                'parent' => null,
                'group'  => null,
                'title' => '<img src="'. esc_url( next3_core()->plugin::plugin_url() . 'assets/img/icon.svg') .'" style="margin-top: 5px;">', 
                'href'  => admin_url( 'admin.php?page=next3aws#ntab=settings' ),
                'meta' => [
                    'title' => __( 'Next3 Offload Settings', 'next3-offload' ),
                ]
            ) );
            return;
        }
        $admin_bar->add_menu( array(
            'id'    => 'next3-cache',
            'parent' => null,
            'group'  => null,
            'title' => '<img src="'. esc_url( next3_core()->plugin::plugin_url() . 'assets/img/icon.svg') .'" style="margin-top: 5px;">', 
            'href'  => '?next3-cache=true',
            'meta' => [
                'title' => __( 'Clear Next3 Offload Cache', 'next3-offload' ),
            ]
        ) );
    }

    /*tools*/
    public function handle_tools_action() {
        if ( !isset( $_GET['nx3_action'] ) ) {
            return;
        }

        if ( !current_user_can( 'manage_options' ) ) {
            return;
        }
        
        $action  = isset( $_GET['nx3_action'] ) ? sanitize_text_field( wp_unslash( $_GET['nx3_action'] ) ) : '';
        $message = '';
        $link = 'tools';
        switch ( $action ) {

            case 'clear_settings':
                next3_delete_option( next3_options_key() );
                $message = 'settings_cleared';
            break;
            
            case 'clear_cache':
                next3_delete_option( next3_credentials_key() );
                $message = 'cache_cleared';
            break;

            case 'css_settings':
                next3_delete_option( 'next3_offload_styles');
                $message = 'css_unoffload';
            break;

            case 'js_settings':
                next3_delete_option( 'next3_offload_scripts' );
                $message = 'js_unoffload';
            break;

            case 'restore_backup':
                next3_core()->optimizer_ins->restore_originals();
                $message = 'restore_compress';
            break;

            case 'delete_webp':
                next3_core()->webp_ins->delete_webp_files();
                $message = 'webp_remove';
            break;

            case 'wpoffload_next3':
                next3_core()->action_ins->wpoffload_to_next3();
                $message = 'wpoffload_action';
            break;
        }

        wp_redirect( add_query_arg( [ 'msg' => $message ], next3_admin_url( 'admin.php?page=next3aws#ntab=' . $link ) ) );
        exit;
    }
    public static function _version(){
        return next3_core()->plugin::version();
    }
    public static function _plugin_url(){
        return next3_core()->plugin::plugin_url();
    }
    public static function _plugin_dir(){
        return next3_core()->plugin::plugin_dir();
    }
    public function check_setup(){
        $stepGet = ($_GET['step']) ?? '';
        $setup = [
            'step' => 'dashboard', // license, provider, config, dashboard
            'stepno' => 4,
            'msg' => ''
        ];
        // license check
        $status = \Next3Offload\Utilities\Check\N3aws_Valid::instance()->_get_action();
        $key_data = next3_get_option('__validate_author_next3aws_keys__', '');

        if( $status == 'inactive' || empty($key_data) || $stepGet == 'license'){
            $setup['step'] = 'license';
            $setup['stepno'] = 1;
            if($stepGet != 'license'){
                $setup['msg'] = esc_html__('Please activate your license!', 'next3-offload');
            }
            return $setup;
        }
        $credentials = next3_credentials();
        $provider = ($credentials['settings']['provider']) ?? '';
        
        // provider data
        $prodiver_data = ($credentials['settings'][$provider]) ?? [];
        $type = ($prodiver_data['type']) ?? '';

        if( $stepGet == 'provider' ){
            $setup['step'] = 'provider';
            $setup['stepno'] = 2;
            $setup['msg'] = '';
            return $setup;
        }
        if( empty($type) || empty($provider) ){
            $setup['step'] = 'provider';
            $setup['stepno'] = 2;
            $setup['msg'] = esc_html__('Please choose prodiver and enter valid credentails.', 'next3-offload');
            return $setup;
        }
        
        if($type == 'wp'){
            if( !defined('NEXT3_SETTINGS') ){
                $setup['step'] = 'provider';
                $setup['stepno'] = 2;
                $setup['msg'] = esc_html__('Sorry!! not define access keys in wp-config.php', 'next3-offload');
                return $setup;
            }
            $config = defined('NEXT3_SETTINGS') ? unserialize(NEXT3_SETTINGS) : [];
            if( $provider == 'google'){
                $path = ($config['key-file-path']) ?? '';
                if( empty($path) || strrpos($path, '.json') == 0 ){
                    $setup['step'] = 'provider';
                    $setup['stepno'] = 2;
                    $setup['msg'] = esc_html__('Sorry!! please define you .json file path', 'next3-offload');
                    return $setup;
                }
            } else if( $provider == 'bunny'){
                $access = ($config['api-key']) ?? '';
                if( strlen($access) < 10 ){
                    $setup['step'] = 'provider';
                    $setup['stepno'] = 2;
                    $setup['msg'] = esc_html__('Sorry!! please enter Bunny API key or password.', 'next3-offload');   
                    return $setup; 
                }
            } else if( in_array($provider, ['aws', 'digital', 'wasabi']) ){
                $access = ($config['access-key-id']) ?? '';
                $secret = ($config['secret-access-key']) ?? '';
                if( strlen($access) < 10  || strlen($secret) < 10 ){
                    $setup['step'] = 'provider';
                    $setup['stepno'] = 2;
                    $setup['msg'] = esc_html__('Sorry!! please enter your valid credentails.', 'next3-offload'); 
                    return $setup;   
                }
            }
        }else{
            
            $config = ($prodiver_data['credentails']) ?? [];
            if( $provider == 'google'){
                $path = ($config['json_data']) ?? '';
                if( empty($path) ){
                    $setup['step'] = 'provider';
                    $setup['stepno'] = 2;
                    $setup['msg'] = esc_html__('Sorry!! please enter json data', 'next3-offload');
                    return $setup;
                }
            } else if( $provider == 'bunny'){
                $access = ($config['api_key']) ?? '';
                if( strlen($access) < 10 ){
                    $setup['step'] = 'provider';
                    $setup['stepno'] = 2;
                    $setup['msg'] = esc_html__('Sorry!! please enter Bunny API key or password.', 'next3-offload');   
                    return $setup; 
                }
            } else if( in_array($provider, ['aws', 'digital', 'wasabi']) ){
                $access = ($config['access_key']) ?? '';
                $secret = ($config['secret_key']) ?? '';
                if( strlen($access) < 10  || strlen($secret) < 10 ){
                    $setup['step'] = 'provider';
                    $setup['stepno'] = 2;
                    $setup['msg'] = esc_html__('Sorry!! please enter your credentails data', 'next3-offload');   
                    return $setup; 
                }
            }
        }
        
        $default_bucket = ($prodiver_data['default_bucket']) ?? '';
        $default_region = ($prodiver_data['default_region']) ?? '';
        if( $stepGet == 'config' ){
            $setup['step'] = 'config';
            $setup['stepno'] = 3;
            $setup['msg'] = '';
            return $setup;
        }
        if( empty($default_bucket) && in_array($provider, ['aws', 'digital', 'wasabi'])){
            $setup['step'] = 'config';
            $setup['stepno'] = 3;
            $setup['msg'] = esc_html__('Steup your Bucket and Region', 'next3-offload');
            return $setup;
        } else if( empty($default_bucket) && in_array($provider, ['bunny'])){
            $setup['step'] = 'config';
            $setup['stepno'] = 3;
            $setup['msg'] = esc_html__('Steup your Storage and Region', 'next3-offload');
            return $setup;
        }
        
        $public_access = ($prodiver_data['public_access']) ?? false;
        if( $public_access == false && in_array($provider, ['aws', 'digital', 'wasabi']) ){
            $setup['step'] = 'config';
            $setup['stepno'] = 3;
            $setup['msg'] = esc_html__('Wraning: Block All Public Access is currently enabled. Login provider dashboard and disable all public access. ', 'next3-offload');
            return $setup;
        }

        $file_permission = ($prodiver_data['file_permission']) ?? false;
        if( $file_permission == false && in_array($provider, ['aws', 'digital', 'wasabi', 'bunny']) ){
            $setup['step'] = 'config';
            $setup['stepno'] = 3;
            $setup['msg'] = esc_html__('Wraning: File upload permission disabled. Login provider dashboard and enable public access permission.', 'next3-offload');
            return $setup;
        }

        return apply_filters('next3/setup/condition', $setup);
    }

    public function setupOffload(){
        $page        = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : null;
        $action = ($page == 'next3aws') ? 'settings' : $page;

        $setup = $this->check_setup();
        $step = ($setup['step']) ?? 'license';
        $stepno = ($setup['stepno']) ?? 1;
        $step_msg = ($setup['msg']) ?? '';

        if( in_array($step, ['license', 'provider', 'config']) ){
            $action = 'setup';
        }

        switch ( $action ) {
            case 'settings':
               
                $status = \Next3Offload\Utilities\Check\N3aws_Valid::instance()->_get_action();
                $key_data = next3_get_option('__validate_author_next3aws_keys__', '');
                $data = \Next3Offload\Utilities\Check\N3aws_Valid::instance()->get_pro($key_data);
                $typeitem = isset($data->typeitem) ? $data->typeitem : '';

                $addons = $this->_getaddons();
                $settings_options = next3_options();
                
                include( next3_core()->plugin::plugin_dir().'templates/settings/dashboard.php' );

                break;
            case 'next3aws-file':

                $credentials = next3_credentials();
                $provider = ($credentials['settings']['provider']) ?? '';
            
                $prodiver_data = ($credentials['settings'][$provider]) ?? [];
                $default_bucket = ($prodiver_data['default_bucket']) ?? '';
                $default_region = ($prodiver_data['default_region']) ?? 'us-east-1';

                if($provider == 'bunny'){
                    $default_bucket = '/'. $default_bucket .'/';
                }

                $getBucket = isset($_GET['nx3buckets']) ? sanitize_text_field($_GET['nx3buckets']) : $default_bucket;
                $folder = isset($_GET['folder']) ? sanitize_text_field($_GET['folder']) : '';
                $refresh = isset($_GET['refresh']) ? sanitize_text_field($_GET['refresh']) : true;

                include( next3_core()->plugin::plugin_dir().'templates/amazon/file-manager.php' );
                break;    
            default:
                include( next3_core()->plugin::plugin_dir().'templates/setup.php' );
                break;
        }
    }

    public function load_script(){

        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        wp_register_script( 'nextJs', self::_plugin_url() . 'assets/script/nextJs.min.js', [], self::_version(), true );
       
        // setup
        wp_register_style( 'next3aws-setup', self::_plugin_url() . 'assets/setup/setupCss.min.css', false, self::_version() );
        wp_register_script( 'next3aws-setup', self::_plugin_url() . 'assets/setup/setupJs.min.js', ['nextJs'], self::_version(), true );
        
        // select2
        wp_register_style( 'hJsSelect2', self::_plugin_url() . 'assets/select2/haziSelect2.css', false, self::_version() );
        wp_register_script( 'hJsSelect2', self::_plugin_url() . 'assets/select2/haziSelect2.js', ['nextJs'], self::_version(), true );
       
        wp_localize_script(
            'next3aws-setup',
            'next3config',
            array(
                'ajax_url' => next3_admin_url('admin-ajax.php', false),
                'admin_url' => get_admin_url('post.php'),
                'rest_url'           => get_rest_url(),
                'nonce'              => wp_create_nonce( 'wp_rest' ),
                'backup'              => wp_create_nonce( 'next3_config' ),
                'savenonce'              => wp_create_nonce( 'next3_savesystem' ),
            )
        );

        // settings admin
        wp_register_style( 'next3aws-admin', self::_plugin_url() . 'assets/css/admin-setting.css', false, self::_version() );
        wp_register_script( 'next3aws-admin', self::_plugin_url() . 'assets/script/admin-setting.min.js', ['nextJs', 'hJsSelect2'], self::_version(), true );
        
        $aws_upload = $this->getJsonFile('upload-to-aws');
        $aws_upload_status = 'no';
        if( isset($aws_upload['post']) && !empty($aws_upload['post'])){
            $post = $aws_upload['post'];
            $id = ($post[0]) ? $post[0] : 0;
            if( $id != 0 ){
                $aws_upload_status = 'yes';
            }
        }

        $wpmedia = $this->getJsonFile('upload-to-wpmedia');
        $wpmedia_status = 'no';
        if( isset($wpmedia['post']) && !empty($wpmedia['post'])){
            $post = $wpmedia['post'];
            $id = ($post[0]) ? $post[0] : 0;
            if( $id != 0 ){
                $wpmedia_status = 'yes';
            }
            
        }
        $import = $this->getJsonFile('import-to-aws');
        $import_status = 'no';
        if( isset($import['post']) && !empty($import['post'])){
            $post = $import['post'];
            $id = ($post[0]) ? $post[0] : 0;
            if( $id != 0 ){
                $import_status = 'yes';
            }
        }

        $status_offload = '';
        $type_offload = '';
        $start_offload = 0;
        $offload_data = next3_get_option('_next3_offload_data', []);
        if( !empty($offload_data)){
            $status_offload = ($offload_data['status']) ?? '';
            $total_offload = ($offload_data['total']) ?? 0;
            $start_offload = ($offload_data['start']) ?? 0;
            $type_offload = ($offload_data['type']) ?? 'offload';

            if( $total_offload <= $start_offload){
                $status_offload = '';
            }
        }

        wp_localize_script('next3aws-admin', 'nxaws3', 
            array( 
                'siteurl' => next3_get_option('siteurl'), 
                'pluginurl' => self::_plugin_url() . 'modules/amazon/save/', 
                'nonce' => wp_create_nonce( 'wp_rest' ), 
                'resturl' => get_rest_url(), 
                'ajax_url' => next3_admin_url('admin-ajax.php', false), 
                'aws_upload_status' => $aws_upload_status, 
                'wpmedia_status' => $wpmedia_status, 
                'import_status' => $import_status,

                'status_offload' => $status_offload,
                'start_offload' => $start_offload,
                'type_offload' => $type_offload,
            )
        );
        
        
        // settings plubic
        wp_register_style( 'next3aws-filemanager', self::_plugin_url() . 'assets/css/file-manager.css', false, self::_version() );
        wp_register_style( 'next3aws-public', self::_plugin_url() . 'assets/css/public-setting.css', false, self::_version() );
        wp_register_script( 'next3aws-public', self::_plugin_url() . 'assets/script/public-setting.min.js', ['jquery', 'nextJs'], self::_version(), true );
        wp_localize_script('next3aws-public', 'nxaws3', array( 
            'siteurl' => next3_get_option('siteurl'), 
            'nonce' => wp_create_nonce( 'wp_rest' ), 
            'uploadnonce' => wp_create_nonce( 'next3_upload' ), 
            'resturl' => get_rest_url(), 
            'ajax_url' => next3_admin_url('admin-ajax.php', false) 
        ));
        
        wp_register_script( 'next3aws-media', self::_plugin_url() . 'assets/script/media.min.js', ['jquery', 'media-views', 'media-grid', 'wp-util', 'nextJs'], self::_version(), true );
        wp_localize_script( 'next3aws-media', 'next3_media', array(
			'strings' => next3_media_action_strings(),
			'nonces'  => array(
				'get_attachment' => wp_create_nonce( 'get-attachment-next3-details' ),
			),
            'siteurl' => next3_get_option('siteurl'),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'resturl' => get_rest_url(), 
            'ajax_url' => next3_admin_url('admin-ajax.php', false)
		));
        
    }
 
    public function _admin_scripts(){

        $screen = get_current_screen();
        
        if( in_array($screen->id, [ 'next3_page_next3config', 'toplevel_page_next3aws', 'toplevel_page_next3aws-network']) ){
            wp_enqueue_style('next3aws-admin');
            wp_enqueue_style('next3aws-setup');
            wp_enqueue_script('next3aws-setup');
        }

        if( in_array($screen->id, [ 'toplevel_page_next3aws', 'toplevel_page_next3aws-network']) ){
            wp_enqueue_style('hJsSelect2');
            wp_enqueue_script('hJsSelect2');

            wp_enqueue_style('next3aws-admin');
            wp_enqueue_script('next3aws-admin');
        }

        if( in_array($screen->id, [ 'next3-offload_page_next3aws-file', 'next3-offload_page_next3aws-file-network']) ){
            // next js load
            wp_enqueue_script('nextJs');
            wp_enqueue_script('next3aws-public');
            wp_enqueue_style('next3aws-filemanager');
        }
        if( in_array($screen->id, [ 'toplevel_page_next3aws', 'upload', 'toplevel_page_next3aws-network']) ){
            wp_enqueue_script('next3aws-media'); 
        }
    }

    public function _public_scripts(){
       
    }

    public function getJsonFile( $file = '' ){
        $file = next3_core()->plugin::modules_dir() . 'amazon/save/'.$file.'.json';
        if(is_readable($file) ){
            return json_decode(
                file_get_contents($file),
                true
            );
        }
        return;
    }

    public function mlsite_get_space_used( $space_used ) {
		global $wpdb;
		$sql = "SELECT SUM( meta_value ) AS bytes_total
				FROM {$wpdb->postmeta}
				WHERE meta_key = '_next3_filesize_total'";

		$space_used = $wpdb->get_var( $sql );

		$upload_dir = wp_upload_dir();
		$space_used += get_dirsize( $upload_dir['basedir'] );

		if ( $space_used > 0 ) {
			$space_used = $space_used / 1024 / 1024;
		}
		return $space_used;
	}

    public function plugin_actions_settings_link( $links, $file ) {
        $setup = $this->check_setup();
        $step = ($setup['step']) ?? 'license';
        if( in_array($step, ['license', 'provider', 'config']) ){
            $settings_link = '<a href="' . next3_admin_url( 'admin.php?page=next3aws&step=' . $step , 'network') . '">' . esc_html( 'Setup' ) . '</a>';
        }else{
            $settings_link = '<a href="' . next3_admin_url( 'admin.php?page=next3aws#ntab=settings', 'network') . '">' . esc_html( 'Dashboard' ) . '</a>';
        }
		
		if ( $file == plugin_basename( next3_core()->plugin::plugin_file() ) ) {
			array_unshift( $links, $settings_link );
		}

		return $links;
	}

    public function _action_links($links){
        $setup = $this->check_setup();
        $step = ($setup['step']) ?? 'license';
        if( in_array($step, ['license', 'provider', 'config']) ){
            $links[] = '<a class="next-highlight-b" href="' . next3_admin_url( 'admin.php?page=next3aws&step=' . $step ).'"> '. __('Setup', 'next3-offload').'</a>';
        } else {
            $links[] = '<a class="next-highlight-b" href="' . next3_admin_url( 'admin.php?page=next3aws#ntab=settings' ).'"> '. __('Dashboard', 'next3-offload').'</a>';
        }
        return $links;
    }


    public function _plugin_row_meta(   $links, $file  ){
        if ( strpos( $file, plugin_basename( next3_core()->plugin::plugin_file() )) !== false ) {
            $new_links = array(
                'demo' => '<a class="next-highlight-b" href="https://themedev.net/next3-offload/features/" target="_blank"><span class="dashicons dashicons-welcome-view-site"></span>'. __('Features', 'next3-offload').'</a>',
                'doc' => '<a class="next-highlight-b" href="https://support.themedev.net/docs/next3-offload/" target="_blank"><span class="dashicons dashicons-media-document"></span>'. __('User Guideline', 'next3-offload').'</a>',
                'support' => '<a class="next-highlight-b" href="https://support.themedev.net/support-tickets/" target="_blank"><span class="dashicons dashicons-editor-help"></span>'. __('Support', 'next3-offload').'</a>'
            );
            $links = array_merge( $links, $new_links );
        }
        return $links;
    }

    public function _check_php_version(){
        N3aws_Notice::push(
			[
				'id'          => 'unsupported-php-version',
				'type'        => 'error',
				'dismissible' => true,
				'message'     => sprintf( esc_html__( 'Next3 Offload requires PHP version %1$s+, which is currently NOT RUNNING on this server.', 'next3-offload' ), '5.6'),
			]
		);
    }

    // auto update plugin
    public function init_plugin_updater() {

        $key = next3_get_option('__validate_author_next3aws_keys__', '');
        if( empty($key) || !class_exists('\Next3Offload\Utilities\Check\N3aws_Valid') ){
            return;
        }
        $data = \Next3Offload\Utilities\Check\N3aws_Valid::instance()->get_pro($key);
        $datalicense = isset($data->datalicense) ? $data->datalicense : '';
        if( empty($datalicense) ){
            return;
        }
        
        if ( ! self::is_registered( $datalicense) || self::has_license_expired( $datalicense ) ) {
			return;
		}

		// Require the updater class, if not already present.
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) )  {
			require_once __DIR__ . '/edd/EDD_SL_Plugin_Updater.php';
		}

		// Retrieve our license key from the DB.
		$license_key = isset($data->key) ? $data->key : '';
		// Setup the updater.
		$edd_updater = new \EDD_SL_Plugin_Updater( 'https://account.themedev.net/', next3_core()->plugin::plugin_file(), array(
				'version' 	=> next3_core()->plugin::version(),
				'license' 	=> $license_key,
				'item_name' => 'Next3 Offload',
				'item_id' => 286,
				'author' 	  => 'ThemeDev',
				'beta'		  => false
			)
		);
      
	}

    private static function has_license_expired( $data = ''){
        if ( !isset($data->expires) || empty($data->expires ) ) {
			return true;
		}

		if ( 'lifetime' == $data->expires ) {
			return false;
		}

        $now             = new \DateTime();
		$expiration_date = new \DateTime( $data->expires );
		$is_expired = $now > $expiration_date;
		if ( ! $is_expired ) {
			return false;
		}

        $prevent_check = get_transient( 'next3aws-dont-check-license' );
		if ( $prevent_check ) {
			return true;
		}
        set_transient( 'next3aws-dont-check-license', true, DAY_IN_SECONDS );
        return true;
    }

    private static function is_registered( $data) {
		if ( empty( $data ) ) {
			return false;
		}

        if ( ! empty( $data->success ) && ! empty( $data->license ) && 'valid' === $data->license ) {
			return true;
		}

		return false;
	}

    private function _getaddons(){
        $data = ['error' => false];
        $parmas['tab'] = 'next3';

        $demo   = get_transient( '__next3awsdemo_addons__', '');
		
		if(  true == $demo && !empty($demo) && isset($demo['success']) ){
            return $demo;
        }
        $url = 'https://api.themedev.net/addons/next3?'. http_build_query($parmas, '&');
        $args = array(
            'timeout'     => 60,
            'redirection' => 3,
            'httpversion' => '1.0',
            'blocking'    => true,
            'sslverify'   => true,
        ); 
		$res = wp_remote_get( $url, $args );
		
		if ( is_wp_error( $res ) ) {
            $data['error'] = true;
            $data['message'] = 'Api error';
            return $data;
		}
		if(!isset($res['body'])){
			$data['error'] = true;
            $data['message'] = 'Not found any demos';
            return $data;
        }
        
        $data['success'] = true;
        $data['message'] = (object) json_decode(
            (string) $res['body']
        ); 
   
        if(!empty($data['message']) ){
            set_transient( '__next3awsdemo_addons__', $data , 86400 );
        }
		
        return $data;
    }

    public function check_capabilities() {
        if ( is_multisite() ) {
            if ( ! current_user_can( 'manage_network_plugins' ) ) {
                return false; 
            }
        } else {
            $caps = array( 'activate_plugins', 'update_plugins', 'install_plugins' );
            foreach ( $caps as $cap ) {
                if ( ! current_user_can( $cap ) ) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function is_installing_or_updating_plugins() {
        if ( ! is_null( self::$is_installing_or_updating_plugins ) ) {
            return self::$is_installing_or_updating_plugins;
        }

        self::$is_installing_or_updating_plugins = false;

        global $pagenow;

        if ( 'update.php' === $pagenow && isset( $_GET['action'] ) && 'install-plugin' === $_GET['action'] ) {
            self::$is_installing_or_updating_plugins = true;
        }
        if ( 'plugins.php' === $pagenow && isset( $_POST['action'] ) ) {
            $action = $_POST['action'];
            if ( isset( $_POST['action2'] ) && '-1' !== $_POST['action2'] ) {
                $action = $_POST['action2'];
            }
            if ( 'update-selected' === $action ) {
                self::$is_installing_or_updating_plugins = true;
            }
        }
        if ( 'update-core.php' === $pagenow && isset( $_GET['action'] ) && 'do-plugin-upgrade' === $_GET['action'] ) {
            self::$is_installing_or_updating_plugins = true;
        }
        return self::$is_installing_or_updating_plugins;
    }

    public function hook_admin_notices() {
        if ( ! $this->check_capabilities() ) {
            return;
        }

        if ( self::is_installing_or_updating_plugins() ) {
            // Don't show notice when installing or updating plugins
            return;
        }

        return; // display notices data
    }

    public function next3_schedule_action(){
        
        if( NEXT3_SELF_MODE ){
            wp_send_json_success( [ 'upload_status' => 'error', 'message' => esc_html__('Sorry!!! Trial mode enable', 'next3-offload') ] );
        }
        
        global $wpdb;
        $source_type = 'media-library';

        $key = next3_get_option('__validate_author_next3aws_keys__', '');
        if( empty($key) || !class_exists('\Next3Offload\Utilities\Check\N3aws_Valid') ){
            wp_send_json_success( [ 'upload_status' => 'error', 'message' => esc_html__('Sorry!!! please active license', 'next3-offload') ] );
        }

        $status = 'success';
        $message = 'Next3 - Cron has been working';
        
        if( !next3_upload_status() ){
            wp_send_json_success( [ 'upload_status' => 'error', 'message' => esc_html__('Can\'t upload permission. Config your credentails first.', 'next3-offload') ] );
        }
       
        $key = '_next3_offload_data';
        
        $offload_data = next3_get_option($key, []);

        $status_offload = ($offload_data['status']) ?? 'push';
        $total_offload = ($offload_data['total']) ?? 0;
        $start_offload = ($offload_data['start']) ?? 0;
        $type_offload = ($offload_data['type']) ?? '';
        $post_data = ($offload_data['post']) ?? [];


       
        if( empty($offload_data) ){
            wp_send_json_success( [ 'upload_status' => 'error', 'message' => esc_html__('Not found any process', 'next3-offload') ] );
        }

        if( $status_offload == 'pause'){
            wp_send_json_success( [ 'upload_status' => 'error', 'message' => esc_html__('Offload process has been pause', 'next3-offload') ] );
        }

        if( $type_offload == 'offload' && !empty($post_data)){
            
            $settings_options = next3_options();
            $remove_local = isset($settings_options['storage']['remove_local']) ? true : false;
            $post_id = ($post_data[$start_offload]) ?? 0;
            if( next3_get_post_meta($post_id, '_next3_attached_url') === false){

                $result = next3_core()->action_ins->wpmedia_to_aws('', $post_id, $remove_local); // remove status
                if( isset(  $result['success']) ){
                    $url = ($result['message']) ?? ''; 
                }
                
            }
        
        } else if( $type_offload == 'unoffload' && !empty($post_data) ){

            $id = ($post_data[$start_offload]) ?? 0;
            if( next3_get_post_meta($id, '_next3_attached_url') && $id != 0){

                $unoffload_status  = true;
                $filepath = next3_get_post_meta( $id, '_next3_attached_file');
                $provider = next3_get_post_meta( $id, '_next3_provider');
                $bucket = next3_get_post_meta( $id, '_next3_bucket');
                $region = next3_get_post_meta( $id, '_next3_region');
                $provider_delivery = next3_get_post_meta( $id, '_next3_provider_delivery');

                $obj = next3_core()->provider_ins->load($provider)->access();

                if( !$obj || !$obj->check_configration()){
                    $unoffload_status = false;
                }
                
                $message = $obj->getStatus();
                if( $message != 'success'){
                    $unoffload_status = false;
                }

                // copy file to local
                $size = [];
                
                $source_file = next3_get_attached_file( $id, true);
                $size[] = basename($source_file);
                $webp_status = false;
                if( true === next3_check_post_meta($id, 'next3_optimizer_is_converted_to_webp') ){
                    
                    if(strpos($source_file, ".webp") === false){
                        $source_file .= '.webp';
                    }
                    if(strpos($filepath, ".webp") === false){
                        $filepath .= '.webp';
                    }
                    $webp_status = true;
                }
                
                // main file back upload
                if( !is_readable($source_file) ){
                    $source_url = next3_get_post_meta( $id, '_next3_attached_url');
                    if ( !empty($source_url) ) {
                        $source = next3_core()->action_ins->create_temp_file_from_url($source_url, pathinfo( $source_file ));
                    }
                }
                // end copy file to local

                if( $unoffload_status ){
                    $resDelete = $obj->get_deleteObjects( $bucket, $filepath);
                }
               
                $crop = next3_get_post_meta($id, '_next3_attachment_metadata');
               
                if( isset($crop['sizes']) && !empty($crop['sizes']) ){
                        
                    foreach($crop['sizes'] as $k=>$v){
                        $file = ( $v['file'] ) ?? '';
                        if( empty($file) ){
                            continue;
                        }
                        if( true === $webp_status ){
                            if(strpos($file, ".webp") === false){
                                $file .= '.webp';
                            }
                        }
                        // copy file to local
                        if( !in_array($file, $size) ){
                            $size[] = $file;

                            $path_orginal_sub = explode('/', $source_file);
                            array_pop($path_orginal_sub);
                            $path_org = ($v['org_file']) ?? $file;
                            array_push($path_orginal_sub, $path_org);

                            $source_file_sub = implode('/', $path_orginal_sub);
                            
                            // end file to local
                        
                            if( isset( $crop['sizes'][$k]['org_file'] ) ){
                                $crop['sizes'][$k]['file'] = $v['org_file'];
                                unset($crop['sizes'][$k]['org_file']);    
                            }
                            
                            if( is_readable($source_file_sub) ){
                                if( $unoffload_status ){
                                    $obj->get_deleteObjects( $bucket, $file);
                                }    
                                continue;
                            }

                            $crop_url = next3_core()->action_ins->get_attatchment_url_preview($id, $k);
                            if ( !empty($crop_url) ) {
                                $source = next3_core()->action_ins->create_temp_file_from_url($crop_url, pathinfo( $source_file_sub ));
                            }
                            if( $unoffload_status ){
                                $obj->get_deleteObjects( $bucket, $file);
                            }
                        }
                    }
                }

                if( isset( $crop['org_file'] )){
                    $crop['file'] = ($crop['org_file']) ?? '';
                    unset($crop['org_file']);
                }
                if( next3_get_post_meta($id, '_wp_attachment_metadata') === false){
                    next3_update_post_meta(  $id, '_wp_attachment_metadata', $crop);
                }

                next3_delete_post_meta( $id, '_next3_attached_file');
                next3_delete_post_meta( $id, '_next3_attached_url');
                next3_delete_post_meta( $id, '_next3_source_type');
                next3_delete_post_meta( $id, '_next3_provider');
                next3_delete_post_meta( $id, '_next3_provider_delivery');
                next3_delete_post_meta( $id, '_next3_bucket');
                next3_delete_post_meta( $id, '_next3_region');
                next3_delete_post_meta( $id, '_next3_clean_status');
                next3_delete_post_meta( $id, '_next3_rename_file');
                next3_delete_post_meta( $id, '_next3_rename_orginal');

                next3_delete_post_meta(  $id, '_next3_attachment_metadata');
            }
        
        } else if($type_offload == 'clean' && !empty($post_data) ){
            $post_id = ($post_data[$start_offload]) ?? 0;

            $source_file = next3_get_attached_file( $post_id, true);
            $webp_status = false;
            if( true === next3_check_post_meta($post_id, 'next3_optimizer_is_converted_to_webp') ){
                if(strpos($source_file, ".webp") === false){
                    $source_file .= '.webp';
                }
                $webp_status = true;
            }
           
            if( is_readable($source_file) ){
                $basename   = basename( $source_file );
                $meta         = next3_wp_get_attachment_metadata( $post_id, true);
                
                // main file
                $unlink = @unlink( $source_file );
                //size images
                if ( ! empty( $meta['sizes'] ) ) {
                    foreach ( $meta['sizes'] as $size ) {
                        $file_name = $size['file'];
                        if( $webp_status == true){
                            if(strpos($file_name, ".webp") === false){
                                $file_name .= '.webp';
                            }
                        }
                        $unlink = @unlink( str_replace( $basename, $file_name, $source_file ));
                    }
                }
                if( $unlink ){
                    next3_update_post_meta( $post_id, '_next3_clean_status', true );
                }
                
            }
        } else if($type_offload == 'wpoffload' && !empty($post_data) ){
            $post_id = ($post_data[$step]) ?? 0;

            if ( class_exists( '\WP_Offload_Media_Autoloader' ) ) {
                
                $sql = $wpdb->prepare( "SELECT * FROM " . next3_wp_offload_table() . " WHERE source_type = %s AND source_id = %d", $source_type, $post_id );
                $object = $wpdb->get_row( $sql );
                if ( !empty( $object ) && isset($object->provider)) {
                    $newpath_main = ($object->path) ?? '';
                    $default_bucket = ($object->bucket) ?? '';
                    $default_region = ($object->region) ?? '';
                    $delivery_provider = ($object->provider) ?? '';
                    $map_provider = [
                        'aws' => 'aws',
                        'spaces' => 'digital',
                        'wasabi' => 'wasabi',
                    ];
                    if( in_array($delivery_provider, $map_provider) ){
                        next3_update_post_meta( $post_id, '_next3_attached_file', $newpath_main );
                        next3_update_post_meta( $post_id, '_next3_attached_url', wp_get_attachment_url( $post_id ) );
                        next3_update_post_meta(  $post_id, '_next3_source_type', 'wp_offload' );
                        next3_update_post_meta(  $post_id, '_next3_provider', $map_provider[ $delivery_provider ] );
                        next3_update_post_meta(  $post_id, '_next3_provider_delivery', $map_provider[ $delivery_provider ] );
                        next3_update_post_meta(  $post_id, '_next3_bucket', $default_bucket );
                        next3_update_post_meta(  $post_id, '_next3_region', $default_region );

                        next3_update_post_meta( $post_id, '_next3_attachment_metadata', next3_get_post_meta( $post_id, '_wp_attachment_metadata') );
                        next3_update_post_meta( $post_id, '_next3_filesize_total', 0 );

                        $source_file = next3_get_post_meta( $post_id, '_wp_attached_file');
                        $uploads = wp_get_upload_dir();
                        if ( false === $uploads['error'] ) {
                            $source_file = $uploads['basedir'] . '/' . $source_file;
                        }
                        if( !is_readable($source_file) ){
                            next3_update_post_meta( $post_id, '_next3_clean_status', true );
                        }
                    } else{
                        $settings_options = next3_options();
                        $remove_local = isset($settings_options['storage']['remove_local']) ? true : false;
                        if( next3_get_post_meta($post_id, '_next3_attached_url') === false){
                            next3_core()->action_ins->wpmedia_to_aws('', $post_id, $remove_local); // remove status
                        }
                    }
                    
                }
            }
        } else if( in_array($type_offload, [ 'styles', 'scripts']) && !empty($post_data) ){
            $post_id = ($post_data[$step]) ?? 0;

            $get_files = next3_exclude_css_list($type_offload, false, $post_id);

            $offload_css = next3_get_option('next3_offload_' . $type_offload , []);


            if( !empty($get_files) && isset($get_files['value'])){
                $handler = ($get_files['value']) ?? '';
                $title = ($get_files['title']) ?? '';
                $group = ($get_files['group']) ?? '';

                if( !empty($handler) && !empty($title) ){
                    
                    $dir_name = get_home_path();
                    if( $group == 'plugin'){
                        $dir_name = WP_CONTENT_DIR;
                    }
                    
                    $total_path = $dir_name . $title;
                    $total_path = file_exists( $total_path ) ? $total_path : $title;
                    $data_upload = [
                        'source_file' => $total_path,
                        'key' => $title,
                        'handler' => $handler,
                        'group' => $group,
                        'type_offload' => $type_offload,
                    ];
                   
                    if( file_exists($total_path) && is_readable($total_path)){
                        $remove = false;
                        if( array_key_exists($handler, $offload_css)){
                            $remove = true;
                        }
                        next3_core()->action_ins->assets_to_aws($data_upload, $remove); // remove status
                        
                    }
                }
            }

        } else if($type_offload == 'compress' && !empty($post_data) ){
            $post_id = ($post_data[$step]) ?? 0;

            $main_image = next3_get_attached_file( $post_id, true);
            $basename = basename( $main_image );
            if ( file_exists( $main_image ) ) {

                $metadata = next3_wp_get_attachment_metadata( $post_id, true);
                    
                $settings_options = next3_options();
                $compression_enable = ($settings_options['optimization']['compression']) ?? 'no';
                // compress data
                if($compression_enable == 'yes'){
                    $status = next3_core()->optimizer_ins->optimize( $post_id, $metadata );
                    if ( false === $status ) {
                        next3_update_post_meta( $post_id, 'next3_optimizer_optimization_failed', 1 );
                    }
                }else{
                    // webp
                    $webp_enable = ($settings_options['optimization']['webp_enable']) ?? 'no';
                    if($webp_enable == 'yes'){
                        if(false === next3_check_post_meta($post_id, 'next3_optimizer_is_converted_to_webp')){
                            $status_webp =  next3_core()->webp_ins->optimize( $post_id, $metadata  );
                            if ( false == $status_webp ) {
                                next3_update_post_meta( $post_id, 'next3_optimizer_is_converted_to_webp__failed', 1 );
                            }
                        }
                    }
                }
                
            }
        } 

        $start_offload_per = $start_offload + 1;

        if( $total_offload >= $start_offload_per && !empty($type_offload)){
            $offload_data['start'] = $start_offload_per;
            next3_update_option( $key , $offload_data);    
        }

        wp_send_json_success( [ 'upload_status' => $status, 'message' => $message] );
    }

    public function next3_schedule_action_license(){
        
        $key = next3_get_option('__validate_author_next3aws_keys__', '');
        if( empty($key) || !class_exists('\Next3Offload\Utilities\Check\N3aws_Valid') ){
            return;
        }
        $data = \Next3Offload\Utilities\Check\N3aws_Valid::instance()->get_pro($key);
        $key_data = isset($data->keys) ? $data->keys : '';
        if( !empty($key_data) ){
            $config['key'] = $key_data;
            $config['eddid'] = 286;
            $config['eddv'] = next3_core()->plugin::version();
            $result = \Next3Offload\Modules\Proactive\N3aws_Init::instance()->checked_pro( $config );
            if( isset($result->status) && $result->status == 'success' ){
                return;
            }
        } 

        \Next3Offload\Utilities\Check\N3aws_Valid::instance()->revoke_save($key);
        next3_delete_option('__validate_author_next3aws__');
        next3_delete_option('__validate_author_next3aws_keys__');
        return;
    }

	public static function instance(){
		if (!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
	}

}