<?php
/**
 * Contains the class that is used to register and retrieve notices in the admin like errors, warnings, success messages, etc.
 *
 * @package   Charitable/Classes/Charitable_Admin_Notices
 * @author    David Bisset
 * @copyright Copyright (c) 2023, WP Charitable LLC
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.4.6
 * @version   1.6.24
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Admin_Notices' ) ) :

	/**
	 * Charitable_Admin_Notices
	 *
	 * @since 1.4.6
	 */
	class Charitable_Admin_Notices extends Charitable_Notices {

		/**
		 * Whether the script has been enqueued.
		 *
		 * @since 1.4.6
		 *
		 * @var   boolean
		 */
		private $script_enqueued;

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @since  1.4.6
		 *
		 * @return Charitable_Admin_Notices
		 */
		public static function get_instance() {
			return charitable()->registry()->get( 'admin_notices' );
		}

		/**
		 * Create class object. A private constructor, so this is used in a singleton context.
		 *
		 * @since 1.4.6
		 * @since 1.5.4 Access changed to public.
		 */
		public function __construct() {
			$this->load_notices();

			add_action( 'admin_notices', array( $this, 'render_upgrade_banner' ) );
			add_action( 'admin_notices', array( $this, 'render_five_star_rating' ) );
			add_action( 'admin_notices', array( $this, 'render_campaign_builder_notice' ) );
			add_action( 'charitable_dismiss_notice', array( $this, 'dismiss_campaign_builder_notice' ), 10, 1 );
			add_filter( 'charitable_localized_javascript_vars', array( $this, 'render_campaign_upgrade_banner_html' ), 10, 1 );
		}

		/**
		 * Adds a notice message.
		 *
		 * @since  1.4.6
		 *
		 * @param  string $message
		 * @param  string $type
		 * @param  string $key     Optional. If not set, next numeric key is used.
		 * @return void
		 */
		public function add_notice( $message, $type, $key = false, $dismissible = false ) {
			if ( false === $key ) {
				$this->notices[ $type ][] = array(
					'message'     => $message,
					'dismissible' => $dismissible,
				);
			} else {
				$this->notices[ $type ][ $key ] = array(
					'message'     => $message,
					'dismissible' => $dismissible,
				);
			}
		}

		/**
		 * Adds an error message.
		 *
		 * @since  1.4.6
		 *
		 * @param  string $message
		 * @param  string $key     Optional. If not set, next numeric key is used.
		 * @return void
		 */
		public function add_error( $message, $key = false, $dismissible = false ) {
			$this->add_notice( $message, 'error', $key, $dismissible );
		}

		/**
		 * Adds a warning message.
		 *
		 * @since  1.4.6
		 *
		 * @param  string $message
		 * @param  string $key     Optional. If not set, next numeric key is used.
		 * @return void
		 */
		public function add_warning( $message, $key = false, $dismissible = false ) {
			$this->add_notice( $message, 'warning', $key, $dismissible );
		}

		/**
		 * Adds a success message.
		 *
		 * @since  1.4.6
		 *
		 * @param  string $message
		 * @param  string $key     Optional. If not set, next numeric key is used.
		 * @return void
		 */
		public function add_success( $message, $key = false, $dismissible = false ) {
			$this->add_notice( $message, 'success', $key, $dismissible );
		}

		/**
		 * Adds an info message.
		 *
		 * @since  1.4.6
		 *
		 * @param  string $message
		 * @param  string $key     Optional. If not set, next numeric key is used.
		 * @return void
		 */
		public function add_info( $message, $key = false, $dismissible = false ) {
			$this->add_notice( $message, 'info', $key, $dismissible );
		}

		/**
		 * Adds a version update message.
		 *
		 * @since  1.4.6
		 *
		 * @param  string  $message
		 * @param  string  $key         Optional. If not set, next numeric key is used.
		 * @param  boolean $dismissible Optional. Set to true by default.
		 * @return void
		 */
		public function add_version_update( $message, $key = false, $dismissible = true ) {
			$this->add_notice( $message, 'version', $key, $dismissible );
		}

		/**
		 * Adds a third party warning message.
		 *
		 * @since  1.7.0.8
		 *
		 * @param  string  $message
		 * @param  string  $key         Optional. If not set, next numeric key is used.
		 * @param  boolean $dismissible Optional. Set to true by default.
		 * @return void
		 */
		public function add_third_party_warning( $message, $key = false, $dismissible = true ) {
			$this->add_notice( $message, 'warning', $key, $dismissible );
		}

		/**
		 * Render notices.
		 *
		 * @since  1.4.6
		 *
		 * @return void
		 */
		public function render() {
			foreach ( charitable_get_admin_notices()->get_notices() as $type => $notices ) {
				foreach ( $notices as $key => $notice ) {
					$this->render_notice( $notice['message'], $type, $notice['dismissible'], $key );
				}
			}
		}

		/**
		 * Render a notice.
		 *
		 * @since  1.4.6
		 *
		 * @param  string  $notice
		 * @param  string  $type
		 * @param  boolean $dismissible
		 * @param  string  $notice_key
		 * @return void
		 */
		public function render_notice( $notice, $type, $dismissible = false, $notice_key = '', $paragraph_tags = true ) {
			if ( ! isset( $this->script_enqueued ) ) {
				if ( ! wp_script_is( 'charitable-admin-notice' ) ) {
					wp_enqueue_script( 'charitable-admin-notice' );
				}

				$this->script_enqueued = true;
			}

			$class = 'notice charitable-notice';

			switch ( $type ) {
				case 'error':
					$class .= ' notice-error';
					break;

				case 'warning':
					$class .= ' notice-warning';
					break;

				case 'success':
					$class .= ' updated';
					break;

				case 'info':
					$class .= ' notice-info';
					break;

				case 'five-star-review':
					$class .= ' notice-info notice-five-star-review';
					break;

				case 'version':
					$class .= ' charitable-upgrade-notice';
					break;
			}

			if ( $dismissible ) {
				$class .= ' is-dismissible';
			}

			$body_text = ( $paragraph_tags ) ? '<p>%s</p>' : '%s';

			printf(
				'<div class="%s" %s>' . $body_text . '</div>',
				esc_attr( $class ),
				strlen( $notice_key ) ? 'data-notice="' . esc_attr( $notice_key ) . '"' : '',
				$notice
			);

			if ( strlen( $notice_key ) ) {
				unset( $this->notices[ $type ][ $notice_key ] );
			}
		}


		/**
		 * Render a lite to pro banner
		 *
		 * @since 1.7.0
		 */
		public function render_upgrade_banner() {
			if ( charitable_is_pro() ) {
				return;
			}
			$screen = get_current_screen();
			if ( ! is_null( $screen ) && ( in_array( $screen->id, $this->get_charitable_screens() ) || ( isset( $screen->taxonomy ) && 'campaign_category' === $screen->taxonomy ) || ( isset( $screen->taxonomy ) && 'campaign_tag' === $screen->taxonomy ) ) ) {
				$banner = get_transient( 'charitable_charitablelitetopro_banner' );
				if ( ! $banner ) {
					$utm_link = charitable_pro_upgrade_url( 'Upgrade From Lite Top Banner Link', 'To unlock more features consider upgrading to Pro.' );
					$this->render_banner(
						'You\'re using WP Charitable Lite! To unlock more features consider <a href="' . $utm_link . '" target="_blank" rel="noopener noreferrer">upgrading to Pro</a>.
				',
						'top-of-page',
						true
					);
				}

			}
		}

		/**
		 * Render a lite to pro banner in campaign lists, add it to the chartiable admin vars so it can be injected via JS.
		 *
		 * @since 1.8.0
		 */
		public function render_campaign_upgrade_banner_html( $strings = false ) {
			if ( charitable_is_pro() ) {
				return;
			}
			$screen = get_current_screen();
			if ( ! is_null( $screen ) && isset( $screen->base ) && 'edit' === $screen->base && isset( $screen->id ) && 'edit-campaign' === $screen->id ) {
				$banner = get_transient( 'charitable_charitable_ltp_lb_list_banner' );
				if ( ! $banner ) {
					$banner_text       = '<p><strong>Unlock More Donations with Peer-to-Peer Fundraising!</strong><br/>Harness the power of supporter networks and friends to reach more people and raise more money for your cause.</p>';
					$strings['banner'] = '<div data-id="charitable_ltp_lb" class="charitable-campaign-list-banner"><div class="charitable-campaign-list-banner-icon"><img src="' . charitable()->get_path( 'assets', false ) . 'images/icons/ambassador.png' . '" /></div><div class="charitable-campaign-list-banner-text">' . $banner_text . '</div><div class="charitable-campaign-list-banner-button"><a class="button-link" href="#">Learn More</a></li>
					</div>';
				} else {
					$strings['banner'] = '';
				}
			}

			return $strings;
		}

		/**
		 * Render a prompt to ask for a five star rating.
		 *
		 * @since 1.7.0
		 */
		public function render_five_star_rating() {

			$screen = get_current_screen();

			// determine if we are on the current screen.
			if ( ! is_null( $screen ) && ( in_array( $screen->id, $this->get_charitable_screens() ) || ( isset( $screen->taxonomy ) && 'campaign_category' === $screen->taxonomy ) || ( isset( $screen->taxonomy ) && 'campaign_tag' === $screen->taxonomy ) ) ) {

				$slug = 'five-star-review';

				// determine when to display this message. for now, there should be some sensible boundaries before showing the notification: a minimum of 14 days of use, created one donation form and received at least one donation.
				$activated_datetime = ( false !== get_option( 'wpcharitable_activated_datetime' ) ) ? get_option( 'wpcharitable_activated_datetime' ) : false;
				$days               = 0;
				if ( $activated_datetime ) {
					$diff = current_time( 'timestamp' ) - $activated_datetime;
					$days = abs( round( $diff / 86400 ) );
				}

				$count_campaigns = wp_count_posts( 'campaign' );
				$total_campaigns = isset( $count_campaigns->publish ) ? $count_campaigns->publish : 0;
				$count_donations = wp_count_posts( 'donation' );
				$total_donations = isset( $count_donations->{'charitable-completed'} ) ? $count_donations->{'charitable-completed'} : 0;

				if ( $days >= apply_filters( 'charitable_days_since_activated', 14 ) && $total_campaigns >= 1 && $total_donations >= 1 ) {
					// check transient.
					$star_review = get_transient( 'charitable_' . $slug . '_banner' );

					// render five star rating banner/notice.
					if ( ! $star_review ) {
						$message = charitable_admin_view( 'notices/admin-notice-five-star-review', array(), true );
						$key     = 'five-star-review';
						$this->render_notice( $message, 'five-star-review', true, $key, false );
					}

				}

			}
		}

		/**
		 * Render a banner to promote the new campaign builder in version 1.8.0. // todo: remove this in a couple of month.
		 *
		 * @since 1.8.0
		 */
		public function render_campaign_builder_notice() {

			$screen = get_current_screen();

			// determine if we are on the current screen.
			if ( ! is_null( $screen ) && ( in_array( $screen->id, $this->get_charitable_screens() ) || ( isset( $screen->taxonomy ) && 'campaign_category' === $screen->taxonomy ) || ( isset( $screen->taxonomy ) && 'campaign_tag' === $screen->taxonomy ) ) ) {

				$slug = 'campaign-builder';

				// check transient.
				$builder_notice = get_transient( 'charitable_' . $slug . '_banner' );

				// render banner/notice.
				if ( ! $builder_notice ) {
					$message = charitable_admin_view( 'notices/admin-notice-campaign-builder', array(), true );
					$key     = 'campaign-builder';
					$this->render_notice( $message, 'campaign-builder', true, $key, false );
				}

			}
		}

		/**
		 * Dismiss the new campaign builder notice.
		 *
		 * @since 1.8.0
		 * @param array $postdata $_POST data.
		 */
		public function dismiss_campaign_builder_notice( $postdata ) {

			if ( empty( $postdata['notice'] ) || 'campaign-builder' !== $postdata['notice'] ) {
				return;
			}

			$slug = 'campaign-builder';

			set_transient( 'charitable_' . $slug . '_banner', 1, YEAR_IN_SECONDS );
		}

		/**
		 * Render a banner.
		 *
		 * @since 1.7.0
		 */
		public function render_banner( $message, $type = 'top-of-page', $dismissible = false, $data_nonce = 'charitablelitetopro', $data_id = 'charitablelitetopro', $data_lifespan = false ) {
			if ( ! isset( $this->script_enqueued ) ) {
				if ( ! wp_script_is( 'charitable-admin-notice' ) ) {
					wp_enqueue_script( 'charitable-admin-notice' );
				}

				$this->script_enqueued = true;
			}

			$class = 'charitable-banner';

			switch ( $type ) {
				case 'top-of-page':
					$class .= ' charitable-admin-banner-top-of-page';
					break;
			}

			if ( $dismissible ) {
				// $class .= ' is-dismissible';
			}

			printf(
				'<div class="%s" %s %s %s>%s <button type="button" class="button-link charitable-banner-dismiss">x</button></div>',
				esc_attr( $class ),
				strlen( $data_nonce ) ? 'data-notice="' . esc_attr( $data_nonce ) . '"' : '',
				strlen( $data_id ) ? 'data-id="' . esc_attr( $data_id ) . '"' : '',
				strlen( $data_lifespan ) ? 'data-lifespan="' . esc_attr( $data_lifespan ) . '"' : '',
				$message
			);
		}


		/**
		 * When PHP finishes executing, stash any notices that haven't been rendered yet.
		 *
		 * @since  1.4.13
		 *
		 * @return void
		 */
		public function shutdown() {
			set_transient( 'charitable_notices', $this->notices );
		}

		/**
		 * Load the notices array.
		 *
		 * If there are any stuffed in a transient, pull those out. Otherwise, reset a clear array.
		 *
		 * @since  1.4.13
		 *
		 * @return void
		 */
		public function load_notices() {
			$this->notices = get_transient( 'charitable_notices' );

			if ( ! is_array( $this->notices ) ) {
				$this->clear();
			}
		}

		/**
		 * Fill admin notices from the front-end notices array.
		 *
		 * @since  1.6.24
		 *
		 * @return void
		 */
		public function fill_notices_from_frontend() {
			$notices = charitable_get_notices();

			foreach ( $notices->get_notices() as $type => $type_notices ) {
				foreach ( $type_notices as $notice ) {
					$this->add_notice( $notice, $type );
				}
			}

			$notices->clear();
		}

		/**
		 * Clear out all existing notices.
		 *
		 * @since  1.4.6
		 *
		 * @return void
		 */
		public function clear() {
			$clear = array(
				'error'   => array(),
				'warning' => array(),
				'success' => array(),
				'info'    => array(),
				'version' => array(),
			);

			$this->notices = $clear;
		}

		/**
		 * Returns an array of screen IDs where the Charitable notices should be displayed.
		 *
		 * @uses   charitable_admin_screens
		 *
		 * @since  1.7.0
		 *
		 * @return array
		 */
		public function get_charitable_screens() {
			/**
			 * Filter admin screens where Charitable styles & scripts should be loaded.
			 *
			 * @since 1.7.0
			 *
			 * @param string[] $screens List of screen ids.
			 */
			return apply_filters(
				'charitable_admin_notice_screens',
				array(
					'campaign',
					'donation',
					'charitable_page_charitable-settings',
					'edit-campaign',
					'edit-donation',
					'toplevel_page_charitable',
					'charitable_page_charitable-addons',
				)
			);
		}
	}

endif;
