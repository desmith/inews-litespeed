<?php

if( !function_exists('next3_core') ){
    function next3_core(){

        $obj = new \stdClass();
        $obj->plugin = '\Next3Offload\N3aws_Plugin';
        $obj->plugin_ins = \Next3Offload\N3aws_Plugin::instance();
       
        $obj->admin = '\Next3Offload\Utilities\N3aws_Admin';
        $obj->admin_ins = \Next3Offload\Utilities\N3aws_Admin::instance();
       
        $obj->load_module = '\Next3Offload\Modules\Load';
        $obj->load_module_ins = \Next3Offload\Modules\Load::instance();
       
        $obj->pro_init = '\Next3Offload\Modules\Proactive\N3aws_Init';
        $obj->pro_init_ins = \Next3Offload\Modules\Proactive\N3aws_Init::instance();
       
        $obj->provider = '\Next3Offload\Modules\Provider';
        $obj->provider_ins = \Next3Offload\Modules\Provider::instance();

        $obj->action = '\Next3Offload\Modules\Action';
        $obj->action_ins = \Next3Offload\Modules\Action::instance();

        $obj->compatiblility = '\Next3Offload\Modules\Compatiblility';
        $obj->compatiblility_ins = \Next3Offload\Modules\Compatiblility::instance();
       
        $obj->support = '\Next3Offload\Modules\Support';
        $obj->support_ins = \Next3Offload\Modules\Support::instance();

        $obj->optimizer = '\Next3Offload\Modules\Optimizer';
        $obj->optimizer_ins = \Next3Offload\Modules\Optimizer::instance();

        $obj->webp = '\Next3Offload\Modules\Webp';
        $obj->webp_ins = \Next3Offload\Modules\Webp::instance();

        $obj->database = '\Next3Offload\Modules\Database\Action';
        $obj->database_ins = \Next3Offload\Modules\Database\Action::instance();

        $obj->assets = '\Next3Offload\Modules\Assets';
        $obj->assets_ins = \Next3Offload\Modules\Assets::instance();

        return $obj;
    }
}


if( !function_exists('nx3aws_admin_role') ){
    function nx3aws_admin_role(){
        return apply_filters( 'nx3aws_admin_role', 'manage_options' );
    }
}

if( !function_exists('next3_print') ){
    function next3_print( $content ){
        return $content;
    }
}

if( !function_exists('next3_same_values') ){
    function next3_same_values( $array = [], $status =false ){
        if( $status ){
            return $array;
        }
        if( !empty($array) && is_array($array) ){
            $new = [];
            foreach($array as $same){
                if( !in_array($same, $new)){
                    $new[] = $same;
                }
            }
            $array = $new;
        }
        return $array;
    }
}

if( !function_exists('next3_admin_url') ){
    function next3_admin_url( $page='', $multi = NEXT3_MULTI_SITE, $url_method = 'self' ){

        if ( is_multisite() && $multi) {
            $url_method = 'network';
        }
        
        switch ( $url_method ) {
			case 'self':
				$base_url = admin_url( $page );
				break;
			default:
				
                if( NEXT3_MULTI_SITE ){
                    $base_url = network_admin_url( $page );
                } else {
                    $base_url = admin_url( $page );
                }
		}
        return $base_url;
    }
}


if( !function_exists('next3_home_url') ){
    function next3_home_url( $page ='', $multi = NEXT3_MULTI_SITE, $url_method = 'self' ){

        if ( is_multisite()  && $multi ) {
            $url_method = 'network';
        }
        
        switch ( $url_method ) {
			case 'self':
				$base_url = home_url( $page );
				break;
			default:
				
                if( NEXT3_MULTI_SITE ){
                    $base_url = network_home_url( $page );
                } else {
                    $base_url = home_url( $page );
                }
		}
        return $base_url;
    }
}


if( !function_exists('next3_providers') ){
    function next3_providers(){
        $credentials = next3_credentials();
        $google_json = ($credentials['settings']['google']['credentails']['json_data']) ?? '';
        if( !empty($google_json) ){
            $google_json = stripslashes( $google_json);
        }
        

        return apply_filters('next3/providers/add', [
            'aws' => apply_filters('next3/providers/aws', [
                'label' => esc_html__('Amazon S3', 'next3-offload'),
                'img' => next3_core()->plugin::plugin_url() . 'assets/img/aws-2.svg',
                'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/aws-s3-5542/',
                'delivery_providers' => [
                    'aws' => [
                        'title' => esc_html__('Amazon S3', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/aws-2.svg',
                    ],
                    'cloudflare' => [
                        'title' => esc_html__('Cloudflare', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/cloudflare.svg',
                        'cname' => true,
                        'cname_label' => 'Enter Account ID *',
                        'cname_desc' => 'Serves media from a custom domain that has been pointed to Cloudflare.'
                    ],
                    'aws_cloudfront' => [
                        'title' => esc_html__('Amazon CloudFront', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/aws_cloudfront.svg',
                        'cname' => true,
                        'cname_label' => 'Enter Custom Domain Name (CNAME) *',
                        'cname_desc' => 'Serves media from a custom domain that has been pointed to Amazon CloudFront.'
                    ],
                    'other' => [
                        'title' => esc_html__('Other', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/other.svg',
                        'cname' => true,
                        'cname_label' => 'Enter CDN name *',
                        'cname_desc' => 'Use Another Custom Domain'
                    ],
                ],
                'field' => [
                    'wp' => [
                        'title' => esc_html__('Define access keys in wp-config.php', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/aws-s3-5542/define-your-access-keys-5549/',
                        'desc' => 'Copy this code and replace or paste into your wp-config.php page.',
                        'field' => [
                            'code_samp' => [
                                'type' => 'textarea',
                                'label' => esc_html__('Code', 'next3-offload'),
                                'readonly' => true,
                                'render' => false,
                                'default' => "define( 'NEXT3_SETTINGS', serialize( array(
    'provider' => 'aws',
    'access-key-id' => '********************',
    'secret-access-key' => '**************************************',
    'endpoint' => '',
    'region' => '',
) ) );"
                            ],
                        ]
                    ],
                    'credentails' => [
                        'title' => esc_html__('I understand, it has risks but I\'d like to store access keys in the database (not recommended)', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/aws-s3-5542/define-your-access-keys-5549/',
                        'desc' => esc_html__('Storing your access keys in the database is less secure than the options above, but if you\'re ok with that, go ahead and enter your keys in the form below.', 'next3-offload'),
                        'field' => [
                            'access_key' => [
                                'type' => 'password',
                                'label' => esc_html__('Access Key ID', 'next3-offload'),
                                'default' => ($credentials['settings']['aws']['credentails']['access_key']) ?? ''
                            ],
                            'secret_key' => [
                                'type' => 'password',
                                'label' => esc_html__('Secret Access Key', 'next3-offload'),
                                'default' => ($credentials['settings']['aws']['credentails']['secret_key']) ?? ''
                            ],
                            'default_region' => [
                                'type' => 'select',
                                'label' => esc_html__('Default Region', 'next3-offload'),
                                'default' => ($credentials['settings']['aws']['default_region']) ?? 'eu-west-2',
                                'options' => next3_aws_region('', 'aws')
                            ],
                        ]
                    ],
                ]

            ]),
           
            'digital' => apply_filters('next3/providers/digital', [
                'label' => esc_html__('DigitalOcean Spaces', 'next3-offload'),
                'img' => next3_core()->plugin::plugin_url() . 'assets/img/digital-1.svg',
                //'upcomming' => 'upcomming',
                'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                'delivery_providers' => [
                    'digital' => [
                        'title' => esc_html__('DigitalOcean Spaces', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/digital-1.svg',
                    ],
                    'keycdn' => [
                        'title' => esc_html__('KeyCDN', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/keysdn.svg',
                        'cname' => true,
                        'cname_label' => 'Enter Custom Domain Name (CNAME) *',
                        'cname_desc' => 'Serves media from a custom domain that has been pointed to KeyCDN.'
                    ],
                    
                    'digital_cdn' => [
                        'title' => esc_html__('DigitalOcean Spaces CDN', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/digital-1.svg',
                        'cname' => true,
                        'cname_label' => 'Enter Custom Domain Name (CNAME) - Optional',
                        'cname_desc' => 'Serves media from a custom domain that has been pointed to DigitalOcean CDN.'
                    ],
                    'other' => [
                        'title' => esc_html__('Other', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/other.svg',
                        'cname' => true,
                        'cname_label' => 'Enter CDN name *',
                        'cname_desc' => 'Use Another Custom Domain'
                    ],
                ],
                'field' => [
                    'wp' => [
                        'title' => esc_html__('Define access keys in wp-config.php', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => 'Copy this code and replace or paste into your wp-config.php page.',
                        'field' => [
                            'code_samp' => [
                                'type' => 'textarea',
                                'label' => esc_html__('Code:', 'next3-offload'),
                                'readonly' => true,
                                'render' => false,
                                'default' => "define( 'NEXT3_SETTINGS', serialize( array(
    'provider' => 'digital',
    'access-key-id' => '********************',
    'secret-access-key' => '**************************************',
    'region' => '',
) ) );"
                            ],
                        ]
                    ],
                    'credentails' => [
                        'title' => esc_html__('I understand, it has risks but I\'d like to store access keys in the database (not recommended)', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => esc_html__('Storing your access keys in the database is less secure than the options above, but if you\'re ok with that, go ahead and enter your keys in the form below.', 'next3-offload'),
                        'field' => [
                            'access_key' => [
                                'type' => 'password',
                                'label' => esc_html__('Spaces Access Key', 'next3-offload'),
                                'default' => ($credentials['settings']['digital']['credentails']['access_key']) ?? ''
                            ],
                            'secret_key' => [
                                'type' => 'password',
                                'label' => esc_html__('Secret Key', 'next3-offload'),
                                'default' => ($credentials['settings']['digital']['credentails']['secret_key']) ?? ''
                            ],
                            'default_region' => [
                                'type' => 'select',
                                'label' => esc_html__('Default Region', 'next3-offload'),
                                'default' => ($credentials['settings']['digital']['default_region']) ?? 'nyc3',
                                'options' => next3_aws_region('', 'digital')
                            ],
                            
                        ]
                    ],
                ]
            ]),
            'wasabi' => apply_filters('next3/providers/bunny', [
                'label' => esc_html__('Wasabi Cloud', 'next3-offload'),
                'img' => next3_core()->plugin::plugin_url() . 'assets/img/wasabi-icon.svg',
                //'upcomming' => 'upcomming',
                'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/wasabi-cloud-storage-6892/',
                'delivery_providers' => [
                    'wasabi' => [
                        'title' => esc_html__('Wasabi Cloud', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/wasabi-icon.svg',
                        'cname' => false,
                        'cname_label' => 'Enter CDN Hostname',
                        'cname_desc' => 'Go to Dashboard - console.wasabisys.com > users > create user , then copy your hostname and paste here.'
                    ],
                    'cloudflare' => [
                        'title' => esc_html__('Cloudflare', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/cloudflare.svg',
                        'cname' => true,
                        'cname_label' => 'Custom Domain (CNAME) *',
                        'cname_desc' => 'Serves media from a custom domain that has been pointed to Cloudflare.'
                    ],
                    'other' => [
                        'title' => esc_html__('Other', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/other.svg',
                        'cname' => true,
                        'cname_label' => 'Enter CDN name *',
                        'cname_desc' => 'Use Another Custom Domain'
                    ],
                ],
                'field' => [
                    'wp' => [
                        'title' => esc_html__('Define access keys in wp-config.php', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => 'Copy this code and replace or paste into your wp-config.php page.',
                        'field' => [
                            'code_samp' => [
                                'type' => 'textarea',
                                'label' => esc_html__('Code:', 'next3-offload'),
                                'readonly' => true,
                                'render' => false,
                                'default' => "define( 'NEXT3_SETTINGS', serialize( array(
    'provider' => 'wasabi',
    'access-key-id' => '********************',
    'secret-access-key' => '**************************************',
    'region' => '',
) ) );"
                            ],
                        ]
                    ],
                    'credentails' => [
                        'title' => esc_html__('I understand, it has risks but I\'d like to store access keys in the database (not recommended)', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => esc_html__('Storing your access keys in the database is less secure than the options above, but if you\'re ok with that, go ahead and enter your keys in the form below.', 'next3-offload'),
                        'field' => [
                            'access_key' => [
                                'type' => 'password',
                                'label' => esc_html__('Access Key', 'next3-offload'),
                                'default' => ($credentials['settings']['wasabi']['credentails']['access_key']) ?? ''
                            ],
                            'secret_key' => [
                                'type' => 'password',
                                'label' => esc_html__('Secret Key', 'next3-offload'),
                                'default' => ($credentials['settings']['wasabi']['credentails']['secret_key']) ?? ''
                            ],
                            'default_region' => [
                                'type' => 'select',
                                'label' => esc_html__('Default Region', 'next3-offload'),
                                'default' => ($credentials['settings']['wasabi']['default_region']) ?? 'eu-central-2',
                                'options' => next3_aws_region('', 'wasabi')
                            ],
                        ]
                    ],
                ]
            ]),
            'bunny' => apply_filters('next3/providers/bunny', [
                'label' => esc_html__('Bunny Storage', 'next3-offload'),
                'img' => next3_core()->plugin::plugin_url() . 'assets/img/bunny-icon.svg',
                'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/bunny-cdn-6863/',
                'delivery_providers' => [
                    'bunny' => [
                        'title' => esc_html__('Bunny CDN', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/bunny-icon.svg',
                        'cname' => true,
                        'cname_label' => 'Enter CDN Hostname',
                        'cname_desc' => 'Go to Dashboard - dash.bunny.net > Delivery > CDN > General > Hostname, then copy your hostname and paste here.'
                    ],
                    
                    /*'bunny_stream' => [
                        'title' => esc_html__('Bunny Stream', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/bunny-icon.svg',
                        'cname' => true,
                        'cname_label' => 'Enter CDN Hostname',
                        'cname_desc' => 'Go to Dashboard - dash.bunny.net > Delivery > Stream > API > CDN Hostname, then copy your hostname and paste here.'
                    ],*/
                    'other' => [
                        'title' => esc_html__('Other', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/other.svg',
                        'cname' => true,
                        'cname_label' => 'Enter CDN name *',
                        'cname_desc' => 'Use Another Custom Domain'
                    ],
                ],
                'field' => [
                    'wp' => [
                        'title' => esc_html__('Define access keys in wp-config.php', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => 'Copy this code and replace or paste into your wp-config.php page.',
                        'field' => [
                            'code_samp' => [
                                'type' => 'textarea',
                                'label' => esc_html__('Code:', 'next3-offload'),
                                'readonly' => true,
                                'render' => false,
                                'default' => "define( 'NEXT3_SETTINGS', serialize( array(
    'provider' => 'bunny',
    'api-key' => 'api key or password',
) ) );"
                            ],
                        ]
                    ],
                    'credentails' => [
                        'title' => esc_html__('I understand, it has risks but I\'d like to store access keys in the database (not recommended)', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => esc_html__('Storing your access keys in the database is less secure than the options above, but if you\'re ok with that, go ahead and enter your keys in the form below.', 'next3-offload'),
                        'field' => [
                            'api_key' => [
                                'type' => 'password',
                                'label' => esc_html__('API Key - Storage', 'next3-offload'),
                                'default' => ($credentials['settings']['bunny']['credentails']['api_key']) ?? ''
                            ],
                            /*'heading' => [
                                'type' => 'heading',
                                'label' => esc_html__('Or', 'next3-offload'),
                            ],
                            'stream_api' => [
                                'type' => 'password',
                                'label' => esc_html__('API Key - Stream (Optional)', 'next3-offload'),
                                'default' => ($credentials['settings']['bunny']['credentails']['stream_api']) ?? ''
                            ],
                            'video_id' => [
                                'type' => 'text',
                                'label' => esc_html__('Video Library ID (Optional)', 'next3-offload'),
                                'default' => ($credentials['settings']['bunny']['credentails']['video_id']) ?? ''
                            ],*/
                           
                        ]
                    ],
                ]
            ]),
            'objects' => apply_filters('next3/providers/objects', [
                'label' => esc_html__('S3 Object Storage', 'next3-offload'),
                'img' => next3_core()->plugin::plugin_url() . 'assets/img/object.svg',
                'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                'delivery_providers' => [
                    'objects' => [
                        'title' => esc_html__('S3 Object Storage', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/object.svg',
                    ],
                    
                    'other' => [
                        'title' => esc_html__('Other', 'next3-offload'),
                        'url' => '',
                        'img' => next3_core()->plugin::plugin_url() . 'assets/img/other.svg',
                        'cname' => true,
                        'cname_label' => 'Enter CDN name *',
                        'cname_desc' => 'Use Another Custom Domain'
                    ],
                ],
                'field' => [
                    'wp' => [
                        'title' => esc_html__('Define access keys in wp-config.php', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => 'Copy this code and replace or paste into your wp-config.php page.',
                        'field' => [
                            'code_samp' => [
                                'type' => 'textarea',
                                'label' => esc_html__('Code', 'next3-offload'),
                                'readonly' => true,
                                'render' => false,
                                'default' => "define( 'NEXT3_SETTINGS', serialize( array(
    'provider' => 'objects',
    'access-key-id' => '********************',
    'secret-access-key' => '**************************************',
    'endpoint' => '',
) ) );"
                            ],
                        ]
                    ],
                    'credentails' => [
                        'title' => esc_html__('I understand, it has risks but I\'d like to store access keys in the database (not recommended)', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/aws-s3-5542/define-your-access-keys-5549/',
                        'desc' => esc_html__('Storing your access keys in the database is less secure than the options above, but if you\'re ok with that, go ahead and enter your keys in the form below.', 'next3-offload'),
                        'field' => [
                            'access_key' => [
                                'type' => 'password',
                                'label' => esc_html__('Access Key ID', 'next3-offload'),
                                'default' => ($credentials['settings']['objects']['credentails']['access_key']) ?? ''
                            ],
                            'secret_key' => [
                                'type' => 'password',
                                'label' => esc_html__('Secret Access Key', 'next3-offload'),
                                'default' => ($credentials['settings']['objects']['credentails']['secret_key']) ?? ''
                            ],
                            'endpoint_stroage' => [
                                'type' => 'text',
                                'label' => esc_html__('Endpoint (Cluster URL)', 'next3-offload'),
                                'placeholder' => esc_html('Exam: https://worldportchapter.nl-ams1.upcloudobjects.com', 'next3-offload'),
                                'default' => ($credentials['settings']['objects']['credentails']['endpoint_stroage']) ?? '',
                            ],
                            
                        ]
                    ],
                ]

            ]),
            'google' => apply_filters('next3/providers/google', [
                'label' => esc_html__('Google Cloud Storage', 'next3-offload'),
                'img' => next3_core()->plugin::plugin_url() . 'assets/img/google-cloud.svg',
                'upcomming' => 'upcomming',
                'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                'google' => [
                    'title' => esc_html__('Google Cloud Storage', 'next3-offload'),
                    'url' => '',
                    'img' => next3_core()->plugin::plugin_url() . 'assets/img/google-cloud.svg',
                ],
                'gcp_cloud' => [
                    'title' => esc_html__('GCP Cloud CDN', 'next3-offload'),
                    'url' => '',
                    'img' => next3_core()->plugin::plugin_url() . 'assets/img/google-cloud.svg',
                    'cname' => true,
                    'cname_label' => 'Enter Custom Domain Name (CNAME) *',
                    'cname_desc' => 'Serves media from a custom domain that has been pointed to Cloudflare.'
                ],
                
                'other' => [
                    'title' => esc_html__('Other', 'next3-offload'),
                    'url' => '',
                    'img' => next3_core()->plugin::plugin_url() . 'assets/img/other.svg',
                    'cname' => true,
                    'cname_label' => 'Enter CDN name *',
                    'cname_desc' => 'Use Another CDN Service'
                ],
                'field' => [
                    'wp' => [
                        'title' => esc_html__('Define access keys in wp-config.php', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => 'Copy this code and replace or paste into your wp-config.php page',
                        'field' => [
                            'code_samp' => [
                                'type' => 'textarea',
                                'label' => esc_html__('Code:', 'next3-offload'),
                                'readonly' => true,
                                'render' => false,
                                'default' => "define( 'NEXT3_SETTINGS', serialize( array(
    'provider' => 'google',
    'key-file-path' => '/path/file.json',
) ) );"
                            ],
                        ]
                    ],
                    'credentails' => [
                        'title' => esc_html__('I understand, it has risks but I\'d like to store access keys in the database (not recommended)', 'next3-offload'),
                        'docs_link' => 'https://support.themedev.net/docs/next3-offload/setup-provider-5539/',
                        'desc' => esc_html__('Storing your access keys in the database is less secure than the options above, but if you\'re ok with that, go ahead and enter your keys in the form below.', 'next3-offload'),
                        'field' => [
                            'json_data' => [
                                'type' => 'textarea',
                                'label' => esc_html__('JSON Data', 'next3-offload'),
                                'default' => $google_json
                            ],
                        ]
                    ],
                    
                ]
            ]),
            
        ]);
    }
}

if( !function_exists('next3_credentials_default') ){
    function next3_credentials_default(){
        $credentials = next3_get_option('theme-dev-aws-credentials', true);
        $data = [
            'settings' => [
                'provider' => 'aws',
                'aws' => [
                    'type' => 'credentails',
                    'credentails' => [
                        'access_key' => ($credentials['aws_access_id']) ?? '',
                        'secret_key' => ($credentials['aws_secret_access_key']) ?? '',
                        'region' => ($credentials['region']) ?? '',
                    ]
                ]
            ]
        ];
        return $data;
    }
}


if( !function_exists('next3_credentials_key') ){
    function next3_credentials_key(){
        return '__next3_settings';
    }
}
if( !function_exists('next3_credentials') ){
    function next3_credentials(){
        return next3_get_option(next3_credentials_key(), []);
    }
}

if( !function_exists('next3_options_key') ){
    function next3_options_key(){
        return '__next3_options';
    }
}

if( !function_exists('next3_options') ){
    function next3_options(){
        $defult = [
            'storage' => [
                'copy_file' => 'yes',
                'enable_path' => 'yes',
                'folder_format' => 'yes',
                'offload_limit' => 5000,
                'offload_paged' => 1
            ],
            'delivery' => [
                'rewrite_urls' => 'yes',
            ],
            'optimization' => [
                'compression' => 'no', 
                'compression_level' => 'none', 
                'backup_orginal' => 'yes',
                'optimizer_resize_images' => '2560'
            ],
            'assets' => [
                'css_offload' => 'no',
                'js_offload' => 'no',
                'minify_css' => 'no',
                'minify_js' => 'no',
            ]
        ];
        return next3_get_option(next3_options_key(), $defult);
    }
}

if( !function_exists('next3_update_option') ){
    function next3_update_option($key, $data, $status = true, $multi = NEXT3_MULTI_SITE){
        if ( is_multisite() && $multi ) {
            //$return = update_site_option($key, $data);
            $return = update_network_option(next3_get_current_network_id(), $key, $data);
        } else{
            $return = update_option($key, $data, $status);
        }
        return $return;
    }
}

if( !function_exists('next3_get_option') ){
    function next3_get_option($key, $defalut = [], $multi = NEXT3_MULTI_SITE){
        if ( is_multisite() && $multi ) {
           //$return = get_site_option($key, $defalut);
            $return = get_network_option(next3_get_current_network_id(), $key, $defalut);
        } else{
            $return = get_option($key, $defalut);
        }
        return $return;
    }
}

if( !function_exists('next3_delete_option') ){
    function next3_delete_option($key, $multi = NEXT3_MULTI_SITE){
        if ( is_multisite() && $multi ) {
            //$return = delete_site_option($key);
            $return = delete_network_option(next3_get_current_network_id(), $key);
        } else{
            $return = delete_option($key);
        }
        return $return;
    }
}


if( !function_exists('next3_get_current_network_id') ){
    function next3_get_current_network_id(){
        return get_current_network_id();
    }
}


if( !function_exists('next3_check_options') ){
    function next3_check_options( $key, $is_multisite = false ) {
		$value = false === $is_multisite ? get_option( $key ) : get_option( $key ); //get_site_option
		if ( 1 === (int) $value ) {
			return true;
		}
		return false;
	}
}

if( !function_exists('next3_check_post_meta') ){
    function next3_check_post_meta($id, $key, $default = true ) {
		$value = next3_get_post_meta($id, $key);
		if ( 1 === (int) $value ) {
			return true;
		}
		return false;
	}
}

if( !function_exists('next3_check_webp_ext') ){
    function next3_check_webp_ext($string, $default = 'webp' ) {
		if( empty($string) ){
            return false;
        }
		if ( $default == strtolower( end( explode('.', $string)) ) ) {
			return true;
		}
		return false;
	}
}

if( !function_exists('next3_upload_status') ){
    function next3_upload_status(){
        $credentials = next3_credentials();
        $provider = ($credentials['settings']['provider']) ?? '';
        if( in_array( $provider, ['bunny', 'wasabi'])){
            //return true;
        }
        $prodiver_data = ($credentials['settings'][$provider]) ?? [];
        $file_permission = ($prodiver_data['file_permission']) ?? false;
        return $file_permission;
    }
}

if(!function_exists('next3_aws_region') ){
    function next3_aws_region( $name = '', $provider = 'aws'){
        return next3_core()->provider_ins->load($provider)->access()->get_regions( $name );
    }
}


if( !function_exists( 'next3_sanitize' ) ){
    function next3_sanitize($value, $senitize_func = 'sanitize_text_field'){
        $senitize_func = (in_array($senitize_func, [
                'sanitize_email', 
                'sanitize_file_name', 
                'sanitize_hex_color', 
                'sanitize_hex_color_no_hash', 
                'sanitize_html_class', 
                'sanitize_key', 
                'sanitize_meta', 
                'sanitize_mime_type',
                'sanitize_sql_orderby',
                'sanitize_option',
                'sanitize_text_field',
                'sanitize_title',
                'sanitize_title_for_query',
                'sanitize_title_with_dashes',
                'sanitize_user',
                'esc_url_raw',
                'wp_filter_nohtml_kses',
            ])) ? $senitize_func : 'sanitize_text_field';
        
        if(!is_array($value)){
            return $senitize_func($value);
        }else{
            return array_map(function($inner_value) use ($senitize_func){
                return next3_sanitize($inner_value, $senitize_func);
            }, $value);
        }
	}
}
if( !function_exists('next3_media_action_strings') ){

    function next3_media_action_strings( $string = null ) {
        $not_verified_value = __( 'No', 'next3-offload');
        $not_verified_value .= '&nbsp;';
        
        $strings = apply_filters( 'next3_media_action_strings', array(
            'provider'      => _x( 'Storage Provider', 'Storage provider key name', 'next3-offload'),
            'provider_name' => _x( 'Storage Provider', 'Storage provider name', 'next3-offload'),
            'bucket'        => _x( 'Bucket', 'Bucket name', 'next3-offload'),
            'key'           => _x( 'Path', 'Path to file in bucket', 'next3-offload'),
            'region'        => _x( 'Region', 'Location of bucket', 'next3-offload'),
            'acl'           => _x( 'Access', 'Access control list of the file in bucket', 'next3-offload'),
            'url'           => __( 'Preview URL', 'next3-offload'),
            'is_verified'   => _x( 'Verified', 'Whether or not metadata has been verified', 'next3-offload'),
            'not_verified'  => $not_verified_value,
        ) );
    
        if ( ! is_null( $string ) ) {
            return isset( $strings[ $string ] ) ? $strings[ $string ] : '';
        }
    
        return $strings;
    }
}

if (!function_exists('next3_post_id_by_meta')) {
	
	function next3_post_id_by_meta($key, $value) {
		global $wpdb;
		$meta = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->postmeta." WHERE meta_key=%s AND meta_value=%s", $key, $value ) );
        if (is_array($meta) && !empty($meta) && isset($meta[0])) {
			$meta = $meta[0];
		}		
		if (is_object($meta)) {
			return $meta->post_id;
		}
		else {
			return false;
		}
	}
}

if (!function_exists('next3_wp_get_attachment_url')) {
    function next3_wp_get_attachment_url($attachment_id, $size = 'full'){
        return next3_core()->action_ins->get_attatchment_url_preview($attachment_id, $size );
    }
}

if (!function_exists('next3_get_metadata_size')) {
    function next3_get_metadata_size($attachment_id, $size = 'full', $return = false){
        return next3_core()->action_ins->next3_get_metadata_size($attachment_id, $size, $return);
    }
}
if (!function_exists('next3_check_setup')) {
    function next3_check_setup( $type = 'status'){
        $setup = next3_core()->admin_ins->check_setup();
        if($type == 'array'){
            return $setup;
        }
        $step = ($setup['step']) ?? '';
        return ($step == 'dashboard') ? true : false;
    }
}
if (!function_exists('next3_check_rewrite')) {
    function next3_check_rewrite( $type = 'none' ){ // rewrite
        $result = false;
        $check_steup = next3_check_setup();
        if( !$check_steup ){
            return $result;
        }
        $settings_options = next3_options();
        
        if( $type == 'rewrite'){
            $rewrite_urls = 'yes';
            if( !empty($settings_options) ){
                $rewrite_urls = ($settings_options['delivery']['rewrite_urls']) ?? 'no';
                $remove_local = ($settings_options['storage']['remove_local']) ?? 'no';
                if( $remove_local == 'yes'){
                    $rewrite_urls = 'yes';
                }
            }
            return ($rewrite_urls == 'yes') ? true : false;
        }
        
        return $result;
    }
}

if (!function_exists('next3_allowed_mime_types')) {
    function next3_allowed_mime_types(){
        return apply_filters( 'next3_allowed_mime_types', [
            '.jpg', '.jpeg', '.png', '.txt', '.gif', '.ico',
            '.pdf', '.svg', '.json',
            '.mp4', '.avi', '.mkv', '.mov', '.flv', '.swf', '.wmv', '.m4a', '.ogg', '.wav', '.m4v', '.mpg', '.ogv', '.3gp', '.3g2',
            '.zip', '.rar', '.tar', '.iso', '.mar',
            '.ppt',
            '.pptx',
            '.pps',
            '.ppsx',
            '.odt',
            '.xls',
            '.xlsx',
            '.PSD',
            '.3ds',
            '.skp',
            '.script',
            '.mp3',
            '.webp',
            '.mpz'
        ] );
    }
}

if (!function_exists('next3__get_available_file_types')) {
    function next3__get_available_file_types(){
        $mime_types_serialized = trim( file_get_contents( next3_core()->plugin::plugin_dir() . 'assets/file-types-list.json' ) ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

	    return json_decode( $mime_types_serialized, true );
    }
}

if (!function_exists('next3__format_raw_custom_types')) {
    function next3__format_raw_custom_types( $file_data_raw ) {

        $file_data   = array();
        $description = isset( $file_data_raw['desc'] ) ? array_map( 'sanitize_text_field', $file_data_raw['desc'] ) : array();
        $mime_types  = isset( $file_data_raw['mime'] ) ? array_map( 'sanitize_text_field', $file_data_raw['mime'] ) : array();
        $extentions  = isset( $file_data_raw['ext'] ) ? array_map( 'sanitize_text_field', $file_data_raw['ext'] ) : array();

        foreach ( $description as $key => $desc ) {
            $file_data[ $key ]['desc'] = $desc;
        }

        foreach ( $mime_types as $key => $mime_type ) {
            $file_data[ $key ]['mime'] = strpos( $mime_type, ',' ) === false ? $mime_type : array_filter( array_map( 'trim', explode( ',', $mime_type ) ) );
        }

        foreach ( $extentions as $key => $extention ) {
            $file_data[ $key ]['ext'] = '.' . strtolower( ltrim( $extention, '.' ) );
        }

        return $file_data;
    }
}

if( !function_exists('next3_allowed_compression_level') ){
    function next3_allowed_compression_level(){
        return apply_filters( 'next3_allowed_compression_levels', [
            '0' => 'None',
            '1' => 'Low (25%)',
            '2' => 'Medium (60%)',
            '3' => 'High (85%)',
        ]);
    }
}
if( !function_exists('next3_exclude_css_list') ){
    function next3_exclude_css_list( $type = 'styles', $all_include = true, $get = ''){
        $assets = next3_core()->assets_ins->assets_list();
       
        $css = ($assets[$type]) ?? [];
        $css_header = ($css['header']) ?? [];
        $css_default = ($css['default']) ?? [];
        $css_dnon_minified = ($css['non_minified']) ?? [];

        $get_return = [];
        $css_load = [];
        if( $all_include == true){
            //$css_load = ['all' => 'All'];
        }
        
        foreach($css_header as $v){
            $handler = ($v['value']) ?? '';
            $title = ($v['title']) ?? '';
            $group = ($v['group']) ?? '';
            if( empty($handler) || empty($title) ){
                continue;
            }
            if( !in_array($handler, $css_load) ){
                $css_load[ $handler ] = $title;
            }
            if( !empty($get) && $handler == $get){
                $get_return = $v;
            }
        }

        foreach($css_default as $v){
            $handler = ($v['value']) ?? '';
            $title = ($v['title']) ?? '';
            $group = ($v['group']) ?? '';
            if( empty($handler) || empty($title) ){
                continue;
            }
            if( !in_array($handler, $css_load) ){
                $css_load[ $handler ] = $title;
            }
            if( !empty($get) && $handler == $get){
                $get_return = $v;
            }
        }

        foreach($css_dnon_minified as $v){
            $handler = ($v['value']) ?? '';
            $title = ($v['title']) ?? '';
            $group = ($v['group']) ?? '';
            if( empty($handler) || empty($title) ){
                continue;
            }
            if( !in_array($handler, $css_load) ){
                $css_load[ $handler ] = $title;
            }
            if( !empty($get) && $handler == $get){
                $get_return = $v;
            }
        }
        if( !empty($get)){
            return $get_return;
        }
        return apply_filters( 'next3_selected_include_'. $type, $css_load);
    }
}

if( !function_exists('next3_allowed_max_image_size') ){
    function next3_allowed_max_image_size(){
        $sizes = next3_core()->optimizer_ins->default_max_width_sizes;
        $new_arr = [];

        foreach($sizes as $v){
            $value = ($v['value']) ?? '';
            $label = ($v['label']) ?? '';
            $new_arr[ $value ] = $label;
        }

        return apply_filters( 'next3_allowed_max_image_sizes', $new_arr);
    }
}

if( !function_exists('next3_wp_offload_table')){
    function next3_wp_offload_table(){
        global $wpdb;

		return $wpdb->get_blog_prefix() . 'as3cf_items';
    }
}


if( !function_exists('next3_random_string')){
    function next3_random_string($length = 20) {
        $key = time() . '-';
        $keys = array_merge(range(0, 9), range('a', 'z'));
    
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }
    
        return $key;
    }
}

if(!function_exists('next3_license_package')){
    function next3_license_package(){
        $key = get_option('__validate_author_next3aws_keys__', '');
        if( empty($key) || !class_exists('\Next3Offload\Utilities\Check\N3aws_Valid') ){
            return 'invalid';
        }
        $data = \Next3Offload\Utilities\Check\N3aws_Valid::instance()->get_pro($key);
        $datalicense = isset($data->datalicense) ? $data->datalicense : '';
        if( empty($datalicense)){
            return 'invalid';
        }
        if( $datalicense == 'extended'){
            return 'extended';
        }
        if( isset($datalicense->license_limit) ){
            $limit = ($datalicense->license_limit) ?? 1;
            if( $limit == 1){
                return 'personal';
            } else if( $limit == 0){
                return 'developer';
            } else if( $limit == 10){
                return 'extended';
            } else{
                return 'business';
            }
        }
        return 'invalid';
    }
}


if( !function_exists('next3_update_post_meta') ){
    function next3_update_post_meta($id, $key, $data, $multi = false){
        if ( is_multisite() && $multi ) {
            $return = update_post_meta($id, $key, $data);
        } else{
            $return = update_post_meta($id, $key, $data);
        }
        return $return;
    }
}


if( !function_exists('next3_delete_post_meta') ){
    function next3_delete_post_meta($id, $key, $multi = NEXT3_MULTI_SITE){
        if ( is_multisite() && $multi ) {
            $return = delete_post_meta($id, $key);
        } else{
            $return = delete_post_meta($id, $key);
        }
        return $return;
    }
}

if( !function_exists('next3_get_post_meta')){
    function next3_get_post_meta($postid = '', $meta_key = '', $post_type = 'attachment', $post_status = 'inherit') {

        if( empty( $meta_key ) || empty($postid)){
            return false;
        }
        if( metadata_exists('post', $postid, $meta_key) === true ) {
            return get_post_meta($postid, $meta_key, true);
        }
        
        global $wpdb;
        $meta_values = $wpdb->get_col( $wpdb->prepare( "
            SELECT pm.meta_value FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = %s 
            AND pm.post_id = %s 
            AND p.post_type = %s 
            AND p.post_status = %s 
        ", $meta_key, $postid, $post_type, $post_status ) );
       
        if( !empty($meta_values) && isset($meta_values[0])){
            return maybe_unserialize( $meta_values[0] );
        }
        return false;
    }
}

if( !function_exists('next3_get_attached_file')){
    function next3_get_attached_file( $postid, $status = true){
        $source_file = get_attached_file( $postid, $status);
        if( empty($source_file) ){
            $uploads = wp_get_upload_dir();
            $source_file = next3_get_post_meta( $postid, '_wp_attached_file');
            if ( $source_file && ! str_starts_with( $source_file, '/' ) && ! preg_match( '|^.:\\\|', $source_file ) ) {
                if ( false === $uploads['error'] ) {
                    $source_file = $uploads['basedir'] . "/$source_file";
                }
            }
        }
        return $source_file;
    }
}
if( !function_exists('next3_wp_get_attachment_metadata')){
    function next3_wp_get_attachment_metadata( $attachment_id = 0, $unfiltered = false ) {
        $attachment_id = (int) $attachment_id;
    
        if ( ! $attachment_id ) {
            return false;
        }
    
        $data = next3_get_post_meta( $attachment_id, '_wp_attachment_metadata');
    
        if ( ! $data ) {
            return false;
        }
    
        if ( $unfiltered ) {
            return $data;
        }
    
        /**
         * Filters the attachment meta data.
         *
         * @since 2.1.0
         *
         * @param array $data          Array of meta data for the given attachment.
         * @param int   $attachment_id Attachment post ID.
         */
        return apply_filters( 'wp_get_attachment_metadata', $data, $attachment_id );
    }
}


if( !function_exists('next3wp_get_attachment_url') ){
    function next3wp_get_attachment_url( $attachment_id = 0 ){

        global $pagenow;

        $attachment_id = (int) $attachment_id;

        $post = get_post( $attachment_id );
    
        if ( ! $post ) {
            return false;
        }
    
        if ( 'attachment' !== $post->post_type ) {
            return false;
        }
        $url = '';
        // Get attached file.
        $file = next3_get_post_meta( $post->ID, '_wp_attached_file', true );
        if ( $file ) {
            // Get upload directory.
            $uploads = wp_get_upload_dir();
            if ( $uploads && false === $uploads['error'] ) {
                // Check that the upload base exists in the file location.
                if ( str_starts_with( $file, $uploads['basedir'] ) ) {
                    // Replace file location with url location.
                    $url = str_replace( $uploads['basedir'], $uploads['baseurl'], $file );
                } elseif ( str_contains( $file, 'wp-content/uploads' ) ) {
                    // Get the directory name relative to the basedir (back compat for pre-2.7 uploads).
                    $url = trailingslashit( $uploads['baseurl'] . '/' . _wp_get_attachment_relative_path( $file ) ) . wp_basename( $file );
                } else {
                    // It's a newly-uploaded file, therefore $file is relative to the basedir.
                    $url = $uploads['baseurl'] . "/$file";
                }
            }
        }

        /*
        * If any of the above options failed, Fallback on the GUID as used pre-2.7,
        * not recommended to rely upon this.
        */
        if ( ! $url ) {
            $url = get_the_guid( $post->ID );
        }

        // On SSL front end, URLs should be HTTPS.
        if ( is_ssl() && ! is_admin() && 'wp-login.php' !== $pagenow ) {
            $url = set_url_scheme( $url );
        }
        return $url;
    }
}