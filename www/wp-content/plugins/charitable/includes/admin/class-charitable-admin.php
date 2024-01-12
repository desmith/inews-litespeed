<?php
/**
 * Class that sets up the Charitable Admin functionality.
 *
 * @package   Charitable/Classes/Charitable_Admin
 * @author    David Bisset
 * @copyright Copyright (c) 2023, WP Charitable LLC
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * @version   1.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Admin' ) ) :

	/**
	 * Charitable_Admin
	 *
	 * @final
	 * @since 1.0.0
	 */
	final class Charitable_Admin {

		/**
		 * The single instance of this class.
		 *
		 * @var Charitable_Admin|null
		 */
		private static $instance = null;

		/**
		 * Donation actions class.
		 *
		 * @var Charitable_Donation_Admin_Actions
		 */
		private $donation_actions;

		/**
		 * Set up the class.
		 *
		 * Note that the only way to instantiate an object is with the charitable_start method,
		 * which can only be called during the start phase. In other words, don't try
		 * to instantiate this object.
		 *
		 * @since  1.0.0
		 */
		protected function __construct() {
			$this->load_dependencies();

			$this->donation_actions = new Charitable_Donation_Admin_Actions();

			do_action( 'charitable_admin_loaded' );

			add_action( 'wp_ajax_charitable_lite_settings_upgrade', array( $this, 'dismiss_lite_cta' ) );

			add_filter( 'post_row_actions', array( $this, 'campaign_action_row' ), 10, 2 );
			add_filter( 'get_edit_post_link', array( $this, 'campaign_link' ), 99, 3 );
			add_filter( 'preview_post_link', array( $this, 'campaign_preview_link' ), 10, 2 );
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @since  1.2.0
		 *
		 * @return Charitable_Admin
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Include admin-only files.
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		private function load_dependencies() {
			$admin_dir = charitable()->get_path( 'includes' ) . 'admin/';

			require_once $admin_dir . 'charitable-core-admin-functions.php';
			require_once $admin_dir . 'campaigns/charitable-admin-campaign-hooks.php';
			require_once $admin_dir . 'dashboard-widgets/charitable-dashboard-widgets-hooks.php';
			require_once $admin_dir . 'donations/charitable-admin-donation-hooks.php';
			require_once $admin_dir . 'settings/charitable-settings-admin-hooks.php';
		}

		/**
		 * Get Charitable_Donation_Admin_Actions class.
		 *
		 * @since  1.5.0
		 *
		 * @return Charitable_Donation_Admin_Actions
		 */
		public function get_donation_actions() {
			return $this->donation_actions;
		}

		/**
		 * Do an admin action.
		 *
		 * @since  1.5.0
		 *
		 * @return boolean|WP_Error WP_Error in case of error. Mixed results if the action was performed.
		 */
		public function maybe_do_admin_action() {
			if ( ! array_key_exists( 'charitable_admin_action', $_GET ) ) {
				return false;
			}

			if ( count( array_diff( array( 'action_type', '_nonce', 'object_id' ), array_keys( $_GET ) ) ) ) {
				return new WP_Error( __( 'Action could not be executed.', 'charitable' ) );
			}

			if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'donation_action' ) ) { // phpcs:ignore.
				return new WP_Error( __( 'Action could not be executed. Nonce check failed.', 'charitable' ) );
			}

			if ( ! isset( $_GET['action_type'] ) || 'donation' !== $_GET['action_type'] ) {
				return new WP_Error( __( 'Action from an unknown action type executed.', 'charitable' ) );
			}

			return $this->donation_actions->do_action( $_GET['charitable_admin_action'], $_GET['object_id'] );
		}

		/**
		 * Loads admin-only scripts and stylesheets.
		 *
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function admin_enqueue_scripts() {

			$min     = ''; // charitable_get_min_suffix(); // todo: undo this.
			$version = charitable()->get_version();

			$assets_dir = charitable()->get_path( 'assets', false );

			/* Menu styles are loaded everywhere in the WordPress dashboard. */
			wp_register_style(
				'charitable-admin-menu',
				$assets_dir . 'css/charitable-admin-menu' . $min . '.css',
				array(),
				$version
			);

			wp_enqueue_style( 'charitable-admin-menu' );

			wp_register_script(
				'charitable-admin-pages-all',
				$assets_dir . 'js/charitable-admin-pages' . $min . '.js',
				array( 'jquery' ),
				$version,
				false
			);

			wp_enqueue_script( 'charitable-admin-pages-all' );

			/* Admin page styles are registered but only enqueued when necessary. */
			wp_register_style(
				'charitable-admin-pages',
				$assets_dir . 'css/charitable-admin-pages' . $min . '.css',
				array(),
				$version
			);

			/* The following styles are only loaded on Charitable screens. */
			$screen = get_current_screen();

			/* This check covers the category and tags pages, only load the main admin css (upgrade banner, etc.) */
			if ( ! is_null( $screen ) && in_array( $screen->base, array( 'edit-tags', 'edit-categories' ) ) ) {

				wp_register_style(
					'charitable-admin',
					$assets_dir . 'css/charitable-admin' . $min . '.css',
					array(),
					$version
				);

				wp_enqueue_style( 'charitable-admin' );

				wp_register_script(
					'charitable-admin-notice',
					$assets_dir . 'js/charitable-admin-notice' . $min . '.js',
					array( 'jquery' ),
					$version,
					false
				);

			}

			// v 1.8.0.
			wp_register_style(
				'charitable-admin-2.0',
				$assets_dir . 'css/charitable-admin-2.0' . $min . '.css',
				array(),
				$version
			);

			wp_enqueue_style( 'charitable-admin-2.0' );

			if ( ! is_null( $screen ) && in_array( $screen->id, $this->get_charitable_screens() ) ) {

				wp_register_style(
					'charitable-admin',
					$assets_dir . 'css/charitable-admin' . $min . '.css',
					array(),
					$version
				);

				wp_enqueue_style( 'charitable-admin' );

				$dependencies   = array( 'jquery-ui-datepicker', 'jquery-ui-tabs', 'jquery-ui-sortable' );
				$localized_vars = array(
					'suggested_amount_placeholder' => __( 'Amount', 'charitable' ),
					'suggested_amount_description_placeholder' => __( 'Optional Description', 'charitable' ),
				);

				if ( 'donation' === $screen->id ) {
					wp_register_script(
						'accounting',
						$assets_dir . 'js/libraries/accounting' . $min . '.js',
						array( 'jquery' ),
						$version,
						true
					);

					$dependencies[] = 'accounting';
					$localized_vars = array_merge(
						$localized_vars,
						array(
							'currency_format_num_decimals' => esc_attr( charitable_get_currency_helper()->get_decimals() ),
							'currency_format_decimal_sep'  => esc_attr( charitable_get_currency_helper()->get_decimal_separator() ),
							'currency_format_thousand_sep' => esc_attr( charitable_get_currency_helper()->get_thousands_separator() ),
							'currency_format'              => esc_attr( charitable_get_currency_helper()->get_accounting_js_format() ),
						)
					);
				}

				wp_register_script(
					'charitable-admin',
					$assets_dir . 'js/charitable-admin' . $min . '.js',
					$dependencies,
					$version,
					false
				);

				wp_enqueue_script( 'charitable-admin' );

				// v 1.8.0.

				wp_enqueue_style(
					'jquery-confirm',
					charitable()->get_path( 'directory', false ) . 'assets/lib/jquery.confirm/jquery-confirm.min.css',
					null,
					'3.3.4'
				);

				wp_enqueue_style(
					'charitable-font-awesome',
					charitable()->get_path( 'directory', false ) . 'assets/lib/font-awesome/font-awesome.min.css',
					null,
					'4.7.0'
				);

				wp_enqueue_script(
					'jquery-confirm',
					charitable()->get_path( 'directory', false ) . 'assets/lib/jquery.confirm/jquery-confirm.min.js',
					[ 'jquery' ],
					'3.3.4',
					false
				);

				wp_register_script(
					'charitable-admin-2.0',
					$assets_dir . 'js/charitable-admin-2.0' . $min . '.js',
					$dependencies,
					$version,
					false
				);

				wp_enqueue_script( 'charitable-admin-2.0' );

				/**
				 * Filter the admin Javascript vars.
				 *
				 * @since 1.0.0
				 *
				 * @param array $localized_vars The vars.
				 */
				$localized_vars = apply_filters( 'charitable_localized_javascript_vars', $localized_vars );

				if ( ! empty( $localized_vars ) ) {
					wp_localize_script( 'charitable-admin', 'CHARITABLE', $localized_vars );
				}

				/* color picker for admin settings */
				wp_enqueue_script( 'wp-color-picker', false, false, false, true ); // phpcs:ignore.
				wp_enqueue_style( 'wp-color-picker' );

			} // end if

			wp_register_script(
				'charitable-admin-notice',
				$assets_dir . 'js/charitable-admin-notice' . $min . '.js',
				array( 'jquery' ),
				$version,
				false
			);

			wp_register_script(
				'charitable-admin-media',
				$assets_dir . 'js/charitable-admin-media' . $min . '.js',
				array( 'jquery' ),
				$version,
				false
			);

			wp_register_script(
				'lean-modal',
				$assets_dir . 'js/libraries/leanModal' . $min . '.js',
				array( 'jquery' ),
				$version,
				true
			);

			wp_register_style(
				'lean-modal-css',
				$assets_dir . 'css/modal' . $min . '.css',
				array(),
				$version
			);

			wp_register_script(
				'charitable-admin-tables',
				$assets_dir . 'js/charitable-admin-tables' . $min . '.js',
				array( 'jquery', 'lean-modal' ),
				$version,
				true
			);

			wp_register_script(
				'select2',
				$assets_dir . 'js/libraries/select2' . $min . '.js',
				array( 'jquery' ),
				'4.0.12',
				true
			);

			wp_register_style(
				'select2-css',
				$assets_dir . 'css/libraries/select2' . $min . '.css',
				array(),
				'4.0.12'
			);

			do_action( 'after_charitable_admin_enqueue_scripts', $min, $version, $assets_dir );
		}

		/**
		 * Set admin body classes.
		 *
		 * @since  1.5.0
		 *
		 * @param  string $classes Existing list of classes.
		 * @return string
		 */
		public function set_body_class( $classes ) {
			$screen = get_current_screen();

			if ( 'donation' === $screen->post_type && ( 'add' === $screen->action || isset( $_GET['show_form'] ) ) ) { // phpcs:ignore.
				$classes .= ' charitable-admin-donation-form';
			}

			if ( isset( $_GET['page'] ) && 'charitable-settings' === $_GET['page'] ) { // phpcs:ignore.
				$classes .= ' charitable-admin-settings';
			}

			if ( isset( $_GET['page'] ) && 'charitable-settings' === $_GET['page'] && isset( $_GET['tab'] ) && '' !== $_GET['tab'] ) { // phpcs:ignore.
				$classes .= ' charitable-admin-settings-' . esc_attr( $_GET['tab'] ); // phpcs:ignore.
			}

			return $classes;
		}

		/**
		 * Add notices to the dashboard.
		 *
		 * @since  1.4.0
		 *
		 * @return void
		 */
		public function add_notices() {
			/*
			Get any version update notices first.
			*/
			// $this->add_version_update_notices(); // depreciated

			$this->add_third_party_notices();

			/* Render notices. */
			charitable_get_admin_notices()->render();
		}

		/**
		 * Add notices for potential third party warnings.
		 *
		 * @since 1.7.0.8
		 */
		public function add_third_party_notices() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			// Allow this feature to be disabled in case unforeseen problems come up.
			if ( false === apply_filters( 'charitable_donations_export_args', true ) ) {
				return;
			}

			$notices = array(
				/* translators: %s: link */
				'code-snippets/code-snippets.php' => sprintf(
					__( "You appear to be using a code snippet plugin. Please <a href='%s' target='_blank'>review best practices</a> when using snippets to avoid problems when upgrading or deactivating Charitable.", 'charitable' ),
					'https://wpcharitable.com/code-snippet-best-practices/'
				),
			);

			// Allow this list to be altered by a third party or charitable addon.
			$notices = apply_filters( 'charitable_admin_third_party_notices', $notices );

			$helper = charitable_get_admin_notices();

			foreach ( $notices as $notice => $message ) {
				if ( false === charitable_get_third_party_warning_option( 'code-snippets/code-snippets.php' ) || 'dismissed' === charitable_get_third_party_warning_option( 'code-snippets/code-snippets.php' ) ) {
					continue;
				}

				$helper->add_third_party_warning( $message, $notice );
			}
		}

		/**
		 * Add version update notices to the dashboard.
		 *
		 * @since  1.4.6
		 *
		 * @return void
		 */
		public function add_version_update_notices() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$notices = array(
				/* translators: %s: link */
				'release-150' => sprintf(
					__( "Charitable 1.5 is packed with new features and improvements. <a href='%s' target='_blank'>Find out what's new</a>.", 'charitable' ),
					'https://wpcharitable.com/charitable-1-5-release-notes/?utm_source=WordPress&utm_campaign=WP+Charitable&utm_medium=Admin+Notice&utm_content=Version+One+Five+Whats+New'
				),
				/* translators: %s: link */
				'release-160' => sprintf(
					__( 'Charitable 1.6 introduces important new user privacy features and other improvements. <a href="%s" target="_blank">Find out what\'s new</a>.', 'charitable' ),
					'https://www.wpcharitable.com/charitable-1-6-user-privacy-gdpr-better-refunds-and-a-new-shortcode/?utm_source=WordPress&utm_campaign=WP+Charitable&utm_medium=Admin+Notice&utm_content=Version+One+Six+Whats+New'
				),
			);

			$helper = charitable_get_admin_notices();

			foreach ( $notices as $notice => $message ) {
				if ( ! get_transient( 'charitable_' . $notice . '_notice' ) ) {
					continue;
				}

				$helper->add_version_update( $message, $notice );
			}
		}

		/**
		 * Dismiss a notice.
		 *
		 * @since  1.4.0
		 *
		 * @return void
		 */
		public function dismiss_notice() {
			if ( ! isset( $_POST['notice'] ) ) { // phpcs:ignore.
				wp_send_json_error();
			}

			$ret = ( 'noted' === charitable_get_third_party_warning_option( 'code-snippets/code-snippets.php' ) ) ? charitable_set_third_party_warning_option( 'code-snippets/code-snippets.php', 'dismissed' ) : delete_transient( 'charitable_' . filter_input( INPUT_POST, 'notice', FILTER_SANITIZE_STRING ) . '_notice', true );

			do_action( 'charitable_dismiss_notice', $_POST ); // phpcs:ignore.

			if ( ! $ret ) {
				wp_send_json_error( $ret );
			}

			wp_send_json_success();
		}

		/**
		 * Dismiss a banner.
		 *
		 * @since  1.7.0
		 *
		 * @return void
		 */
		public function dismiss_banner() {
			if ( ! isset( $_POST['banner_id'] ) ) { // phpcs:ignore.
				wp_send_json_error();
			}

			$ret = set_transient( 'charitable_' . esc_attr( $_POST['banner_id'] ) . '_banner', 1, WEEK_IN_SECONDS ); // phpcs:ignore.

			if ( ! $ret ) {
				wp_send_json_error( $ret );
			}

			wp_send_json_success();
		}

		/**
		 * Dismiss a banner.
		 *
		 * @since  1.8.0
		 *
		 * @return void
		 */
		public function dismiss_list_banner() {
			if ( ! isset( $_POST['banner_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				wp_send_json_error();
			}

			$ret = set_transient( 'charitable_' . esc_attr( $_POST['banner_id'] ) . '_list_banner', 1, WEEK_IN_SECONDS ); // phpcs:ignore.

			if ( ! $ret ) {
				wp_send_json_error( $ret );
			}

			wp_send_json_success();
		}

		/**
		 * Dismiss a five star rating.
		 *
		 * @since  1.7.0
		 *
		 * @return void
		 */
		public function dismiss_five_star_rating() {
			if ( ! isset( $_POST['banner_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				wp_send_json_error();
			}

			$check = get_transient( 'charitable_' . esc_attr( $_POST['banner_id'] ) . '_banner' ); // phpcs:ignore.

			if ( ! $check ) {
				$ret = set_transient( 'charitable_' . esc_attr( $_POST['banner_id'] ) . '_banner', true ); // phpcs:ignore.
				if ( ! $ret ) {
					wp_send_json_error( $ret );
				}
			}

			wp_send_json_success();
		}

		/**
		 * Dismiss a five star rating.
		 *
		 * @since  1.7.0
		 *
		 * @return void
		 */
		public function dismiss_lite_cta() {
			if ( ! isset( $_POST['chartiable_action'] ) ) { // phpcs:ignore.
				wp_send_json_error();
			}

			$updated = update_option( 'charitable_lite_settings_upgrade', true );

			if ( $updated ) {
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		}

		/**
		 * Adds one or more classes to the body tag in the dashboard.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $classes Current body classes.
		 * @return string Altered body classes.
		 */
		public function add_admin_body_class( $classes ) {
			$screen = get_current_screen();

			if ( in_array( $screen->post_type, array( Charitable::DONATION_POST_TYPE, Charitable::CAMPAIGN_POST_TYPE ) ) ) {
				$classes .= ' post-type-charitable';
			}

			return $classes;
		}

		/**
		 * Add custom links to the plugin actions.
		 *
		 * @since  1.0.0
		 *
		 * @param  string[] $links Plugin action links.
		 * @return string[]
		 */
		public function add_plugin_action_links( $links ) {
			$links[] = '<a href="' . admin_url( 'admin.php?page=charitable-settings' ) . '">' . __( 'Settings', 'charitable' ) . '</a>';
			return $links;
		}

		/**
		 * Add Extensions link to the plugin row meta.
		 *
		 * @since  1.2.0
		 *
		 * @param  string[] $links Plugin action links.
		 * @param  string   $file  The plugin file.
		 * @return string[] $links
		 */
		public function add_plugin_row_meta( $links, $file ) {
			if ( plugin_basename( charitable()->get_path() ) != $file ) {
				return $links;
			}

			// Use an updated UTM.
			$extensions_link = charitable_ga_url(
				'https://wpcharitable.com/extensions/',
				urlencode( 'Admin Plugin Page' ),
				urlencode( 'Extensions' )
			);

			$links[] = '<a href="' . $extensions_link . '" target="_blank" rel="noopener">' . __( 'Extensions', 'charitable' ) . '</a>';

			return $links;
		}

		/**
		 * Remove the jQuery UI styles added by Ninja Forms.
		 *
		 * @since  1.2.0
		 *
		 * @param  string $context Media buttons context.
		 * @return string
		 */
		public function remove_jquery_ui_styles_nf( $context ) {
			wp_dequeue_style( 'jquery-smoothness' );
			return $context;
		}

		/**
		 * Export donations.
		 *
		 * @since  1.3.0
		 *
		 * @return false|void Returns false if the export failed. Exits otherwise.
		 */
		public function export_donations() {

			if ( ! wp_verify_nonce( $_GET['_charitable_export_nonce'], 'charitable_export_donations' ) ) {
				return false;
			}

			/**
			 * Filter the donation export arguments.
			 *
			 * @since 1.3.0
			 *
			 * @param array $args Export arguments.
			 */
			$export_args = apply_filters(
				'charitable_donations_export_args',
				array(
					'start_date'  => isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '',
					'end_date'    => isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '',
					'status'      => isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '',
					'campaign_id' => isset( $_GET['campaign_id'] ) ? sanitize_text_field( wp_unslash( $_GET['campaign_id'] ) ) : '',
					'report_type' => isset( $_GET['report_type'] ) ? sanitize_text_field( wp_unslash( $_GET['report_type'] ) ) : '',
				)
			);

			/**
			 * Filter the export class name.
			 *
			 * @since 1.3.0
			 *
			 * @param string $report_type The type of report.
			 * @param array  $args        Export arguments.
			 */
			$export_class = apply_filters( 'charitable_donations_export_class', 'Charitable_Export_Donations', $_GET['report_type'], $export_args );

			new $export_class( $export_args );

			die();
		}

		/**
		 * Export campaigns.
		 *
		 * @since  1.6.0
		 *
		 * @return false|void Returns false if the export failed. Exits otherwise.
		 */
		public function export_campaigns() {

			if ( ! isset( $_GET['_charitable_export_nonce'] ) || ! wp_verify_nonce( $_GET['_charitable_export_nonce'], 'charitable_export_campaigns' ) ) {
				return false;
			}

			/**
			 * Filter the campaign export arguments.
			 *
			 * @since 1.6.0
			 *
			 * @param array $args Export arguments.
			 */
			$export_args = apply_filters(
				'charitable_campaigns_export_args',
				array(
					'start_date_to' => isset( $_GET['start_date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date_to'] ) ) : '',
					'end_date_from' => isset( $_GET['end_date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date_from'] ) ) : '',
					'end_date_to'   => isset( $_GET['end_date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date_to'] ) ) : '',
					'status'        => isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '',
					'report_type'   => isset( $_GET['report_type'] ) ? sanitize_text_field( wp_unslash( $_GET['report_type'] ) ) : '',
				)
			);

			/**
			 * Filter the export class name.
			 *
			 * @since 1.6.0
			 *
			 * @param string $report_type The type of report.
			 * @param array  $args        Export arguments.
			 */
			$export_class = apply_filters( 'charitable_campaigns_export_class', 'Charitable_Export_Campaigns', $_GET['report_type'], $export_args );

			new $export_class( $export_args );

			die();
		}

		/**
		 * Returns an array of screen IDs where the Charitable scripts should be loaded.
		 *
		 * @uses   charitable_admin_screens
		 *
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_charitable_screens() {
			/**
			 * Filter admin screens where Charitable styles & scripts should be loaded.
			 *
			 * @since 1.8.0
			 *
			 * @param string[] $screens List of screen ids.
			 */
			return apply_filters(
				'charitable_admin_screens',
				array(
					'campaign',
					'donation',
					'charitable_page_charitable-settings',
					'edit-campaign',
					'edit-donation',
					'dashboard',
					'toplevel_page_charitable',
					'charitable_page_charitable-addons',
				)
			);
		}

		/**
		 * Updates the "action row" of campaigns to add the ability to edit with builder.
		 *
		 * @since  1.8.0
		 *
		 * @param array $actions Actions.
		 * @param array $post   Post data.
		 *
		 * @return array
		 */
		public function campaign_action_row( $actions, $post ) {

			$actions_first = array();

			$suffix = ( charitable_is_debug() || charitable_is_script_debug() ) ? '#charitabledebug' : false;

			// Check for the post type.

			if ( is_object( $post ) && isset( $post->post_type ) && 'campaign' === $post->post_type && ( ! isset( $_GET['post_status'] ) || ( isset( $_GET['post_status'] ) && 'trash' !== strtolower( $_GET['post_status'] ) ) ) ) {

				$post_id = intval( $post->ID );

				// Determine if this is a "legacy" campaign.
				$is_legacy_campaign = charitable_is_campaign_legacy( $post_id );

				if ( isset( $actions['edit'] ) && 0 !== $post_id ) {

					$actions_show_id     = array( 'id' => esc_html__( 'ID: ', 'charitable' ) . $post_id );
					$actions_edit_legacy = array( 'edit' => $actions['edit'] );

					unset( $actions['edit'] );

				}

				// if ( defined( 'CHARITABLE_BUILDER_SHOW_LEGACY_EDIT_LINKS' ) && CHARITABLE_BUILDER_SHOW_LEGACY_EDIT_LINKS === true ) {

					// Add "edit" link that goes to the builder. The campaign can still be edited w/ legacy via direct link (example https://website.test/wp-admin/post.php?post=246&action=edit).
					$actions_edit_legacy = array( 'Edit' => '<a href="' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '">' . esc_html__( 'Legacy Edit', 'charitable' ) . '</a>' );

					$actions = ( false === $is_legacy_campaign )
					? $actions_show_id + $actions_edit_legacy + array( 'charitable_builder' => '<a href="' . admin_url( 'admin.php?page=charitable-campaign-builder&campaign_id=' . $post_id ) . $suffix . '">' . esc_html__( 'Edit With Builder', 'charitable' ) . '</a>' ) + $actions
					: $actions_show_id + $actions_edit_legacy + $actions;

				// } else {

				// 	$actions = ( false === $is_legacy_campaign )
				// 	? $actions_show_id + array( 'charitable_builder' => '<a href="' . admin_url( 'admin.php?page=charitable-campaign-builder&campaign_id=' . $post_id ) . $suffix . '">' . esc_html__( 'Edit', 'charitable' ) . '</a>' ) + $actions
				// 	: $actions_show_id + $actions_edit_legacy + $actions;

				// }

			}

			return $actions;
		}

		/**
		 * Updates the "post link" of campaigns to add the ability to edit with builder.
		 *
		 * @since  1.8.0
		 *
		 * @param string  $link The current link URL.
		 * @param integer $post_id Post ID.
		 * @param array   $context Context.
		 *
		 * @return array
		 */
		public function campaign_link( $link, $post_id, $context ) { // phpcs:ignore

			$screen  = get_current_screen();
			$post_id = intval( $post_id );

			if ( 0 === $post_id || ! isset( $screen->id ) || $screen->id !== 'edit-campaign' ) {
				return $link;
			}

			// Determine if this is a "legacy" campaign.
			$is_legacy_campaign = charitable_is_campaign_legacy( $post_id );

			$suffix = ( charitable_is_debug() || charitable_is_script_debug() ) ? '#charitabledebug' : false;

			$new_link = ( false === $is_legacy_campaign )
						? admin_url( 'admin.php?page=charitable-campaign-builder&campaign_id=' . $post_id ) . $suffix
						: esc_url( $link );

			return $new_link;
		}

		/**
		 * Filters the URL used for a campaign preview.
		 *
		 * @since 1.8.0
		 *
		 * @param string  $preview_link URL used for the post preview.
		 * @param WP_Post $post         Post object.
		 */
		public function campaign_preview_link( $preview_link, $post ) {

			if ( ! empty( $post ) && 'campaign' === $post->post_type ) {

				$preview_link = add_query_arg(
					array(
						'charitable_campaign_preview' => $post->ID,
					),
					$preview_link
				);

			}

			return $preview_link;
		}
	}




endif;
