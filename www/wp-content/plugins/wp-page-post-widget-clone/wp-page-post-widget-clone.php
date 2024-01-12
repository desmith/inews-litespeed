<?php
/*
Plugin Name: WP Page Post Widget Clone
Plugin URI: https://wordpress.org/plugins/wp-page-post-widget-clone
Description: Wordpress page post widget plugin to generate duplicate post or page and widget with all the contents and it's settings.
Author: Mahesh Vora
Author URI: https://paypal.me/maheshvorahelp
Version: 1.0.1
Text Domain: wp-page-post-widget-clone
Requires at least: 4.0
Tested up to: 4.8
Domin Path: Languages
License: GPLv2 or later

/* Exit if accessed directly*/
if (!defined('ABSPATH')) {
    exit;
}

/* Define wordpress Constant variables */
define('MPWP_PAGE_POST_WIDGET_CLONE_URL', plugins_url() . '/wp-page-post-widget-clone');


if(!function_exists('mpwp_page_post_clone')) {
    /* Function for clone page and post as draft */
    function mpwp_page_post_clone(){

            global $wpdb;
            if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'mpwp_page_post_clone' == $_REQUEST['action'] ) ) ) {
                    wp_die('Not any post or page to clone has been supplied!');
            }

			$post_id=isset($_GET['post']) ? $_GET['post'] : $_POST['post'];
            $post_id = (int)$post_id;
            $post = get_post( $post_id );
            $current_user = wp_get_current_user();
            $post_author = $current_user->ID;

            if (isset( $post ) && $post != null) {

                    $mp_pp_args = array(
                            'comment_status' => $post->comment_status,
                            'ping_status'    => $post->ping_status,
                            'post_author'    => $post_author,
                            'post_content'   => $post->post_content,
                            'post_excerpt'   => $post->post_excerpt,
                            'post_name'      => $post->post_name,
                            'post_parent'    => $post->post_parent,
                            'post_password'  => $post->post_password,
                            'post_status'    => 'draft',
                            'post_title'     => $post->post_title,
                            'post_type'      => $post->post_type,
                            'to_ping'        => $post->to_ping,
                            'menu_order'     => $post->menu_order
                    );

                    $clone_pp_id = wp_insert_post( $mp_pp_args );

                    $taxonomies = get_object_taxonomies($post->post_type);
                    foreach ($taxonomies as $taxonomy) {
                            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
                            wp_set_object_terms($clone_pp_id, $post_terms, $taxonomy, false);
                    }

                    $post_meta_data = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
                    if (count($post_meta_data)!=0) {
                            $clone_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                            foreach ($post_meta_data as $meta_data) {
                                    $meta_key = $meta_data->meta_key;
                                    $meta_value = addslashes($meta_data->meta_value);
                                    $clone_query_select[]= "SELECT $clone_pp_id, '$meta_key', '$meta_value'";
                            }
                            $clone_query.= implode(" UNION ALL ", $clone_query_select);
                            $wpdb->query($clone_query);
                    }

                    wp_redirect( admin_url( 'post.php?action=edit&post=' . $clone_pp_id ) );
                    exit;

            } else {

                    wp_die(__('Post or Page clone failed, could not find original data:', 'wp-page-post-widget-clone') . $post_id);

            }
    }

}

add_action( 'admin_action_mpwp_page_post_clone', 'mpwp_page_post_clone' );


if(!function_exists('mpwp_page_post_link')) {

    /**
     * Add the clone link to action list for post row action
     * @param string $actions
     * @param type $post
     * @return string
     */
    function mpwp_page_post_link( $actions, $post ) {
            if (current_user_can('edit_posts')) {
                    $actions['clone'] = '<a href="admin.php?action=mpwp_page_post_clone&amp;post=' . $post->ID . '" title="'.__('Make It Duplicate', 'wp-page-post-widget-clone').'" rel="permalink">'.__('Clone Me', 'wp-page-post-widget-clone').'</a>';
            }
            return $actions;
    }

}

/**
 * Filter for post and  page row actions
 */
add_filter( 'post_row_actions', 'mpwp_page_post_link', 10, 2 );
add_filter('page_row_actions', 'mpwp_page_post_link', 10, 2);


//-----------------Constructor for widgets or sidebar content clone
	/**
	 * Function for widget clone
	 * @global type $pagenow
	 * @return type
	 */
	function mpwp_clone_widget_contents() {

		global $pagenow;

		if( $pagenow != 'widgets.php' ) {
			return;
        }

            ?>
            <style>
				.mpcwc_wrap{
					background-color: #7d807f;
					float: left;
					padding-right: 12px;
				}
                .mpcwc_wrap a.mp-clone-widget-action {
                    text-decoration: none;
                    border-radius: 3px;
                    color: #ffffff;
                    padding: 5px 10px;
                    vertical-align: middle;
                    margin-left: 10px;
                }

                .mpcwc_wrap a.mp-clone-widget-action {
                    background: #0073aa none repeat scroll 0 0;
					display: -webkit-inline-box;
                }

                .mpcwc_wrap a.mp-clone-widget-action:hover,
                .mpcwc_wrap a.clear-sidebar:hover {
                    opacity: 0.8;
                }
                .mpcwc_wrap .mpcwc_selection_div {
                    display: inline-block;
					padding: 10px;
					text-align: center;
					font-size: larger;
                }
				.mpcwc_wrap .mpcwc_selection_div span{
					color : #ffffff;
				}
                .mpcwc_wrap .mpcwc_selection_div span {
                    display: inline-block;
                    font-weight: bold;
                    margin-bottom: 7px;
                    padding-left: 5px;
                }

            </style>
            <script>
            /**
             * Js code for clone widget contents
             * @returns {undefined}
             */
            jQuery(document).ready(function(event) {
                    var $sbc_content = '';
                    $sbc_content += '<div class="mpcwc_wrap">';
                    $sbc_content += '<div class="mpcwc_selection_div">';
                    $sbc_content += '<span><?php _e('Copy From Sidebar', 'mpwp_clone_widget_contents'); ?></span><br/>';
                    $sbc_content += '<select class="mpcwc_copy_from"><option value=""><?php _e('Select Sidebar', 'mpwp_clone_widget_contents'); ?></option><?php foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) { ?><option value="<?php echo $sidebar['id']; ?>"><?php echo ucwords($sidebar['name']); ?></option><?php } ?></select>';
                    $sbc_content += '</div>';
                    $sbc_content += '<div class="mpcwc_selection_div">';
                    $sbc_content += '<span><?php _e('Paste In Sidebar', 'mpwp_clone_widget_contents'); ?></span><br/>';
                    $sbc_content += '<select class="mpcwc_paste_in"><option value=""><?php _e('Select Sidebar', 'mpwp_clone_widget_contents'); ?></option><?php foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) { ?><option value="<?php echo $sidebar['id']; ?>"><?php echo ucwords($sidebar['name']); ?></option><?php } ?></select>';
                    $sbc_content += '</div>';
                    $sbc_content += '<a href="#" class="mp-clone-widget-action sbc-clone-action"><?php _e('Clone Widgets', 'mpwp_clone_widget_contents'); ?></a>';
                    $sbc_content += '</div>';
                    jQuery('#widgets-right').before($sbc_content);
                    
                    jQuery('.mp-clone-widget-action').live('click', function() {
                        var $main_wrap = jQuery('#widgets-right');
                        var $source_sidebar_name = jQuery('.mpcwc_copy_from').val();
                        var $main_container = $main_wrap.find('#'+$source_sidebar_name);
                        var $dest_sidebar_name = jQuery('.mpcwc_paste_in').val();
                        jQuery($main_container.find('.widget').get()).each(function() {

                            var $sidebar = jQuery(this).clone();
                            var id_base = $sidebar.find('input[name="id_base"]').val();
                            var number = $sidebar.find('input[name="widget_number"]').val();
                            var maximum = 0;
                            jQuery('input.widget-id[value|="' + id_base + '"]').each(function() {
                                    var match = this.value.match(/-(\d+)$/);
                                    if(match && parseInt(match[1]) > maximum)
                                            maximum = parseInt(match[1]);
                            });
                            var newest_num = maximum + 1;
                            $sidebar.find('.widget-content').find('input,select,textarea').each(function () {
                                    if (jQuery(this).attr('name'))
                                        jQuery(this).attr('name', jQuery(this).attr('name').replace(number, newest_num));
                                });
                                jQuery('.widget').each(function () {
                                    var match = this.id.match(/^widget-(\d+)/);
                                    if (match && parseInt(match[1]) > maximum)
                                        maximum = parseInt(match[1]);
                                });
                                var widgetid = maximum + 1;
                                var add = jQuery('#widget-list .id_base[value="' + id_base + '"]').siblings('.add_new').val();
                                $sidebar[0].id = 'widget-' + widgetid + '_' + id_base + '-' + newest_num;
                                $sidebar.find('input.widget-id').val(id_base + '-' + newest_num);
                                $sidebar.find('input.widget_number').val(newest_num);
                                $main_wrap.find('#' + $dest_sidebar_name).append($sidebar);
                                $sidebar.fadeIn();
                                $sidebar.find('.multi_number').val(newest_num);
                                wpWidgets.save($sidebar, 0, 0, 1);
                                jQuery(document).trigger('widget-added', [$sidebar]);
                            });
                            event.preventDefault();
                        });
                    });
           
            </script>
            <?php
	}
	
	add_filter( 'admin_head', 'mpwp_clone_widget_contents' );


?>
