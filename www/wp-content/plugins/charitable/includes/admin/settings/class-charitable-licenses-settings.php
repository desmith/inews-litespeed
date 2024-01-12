<?php
/**
 * Charitable Licenses Settings UI.
 *
 * @package     Charitable/Classes/Charitable_Licenses_Settings
 * @version     1.0.0
 * @author      David Bisset
 * @copyright   Copyright (c) 2023, WP Charitable LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Licenses_Settings' ) ) :

	/**
	 * Charitable_Licenses_Settings
	 *
	 * @final
	 * @since   1.0.0
	 */
	final class Charitable_Licenses_Settings {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Licenses_Settings|null
		 */
		private static $instance = null;

		/**
		 * Create object instance.
		 *
		 * @since   1.0.0
		 */
		private function __construct() {
		}

		/**
		 * Returns and/or create the single instance of this class.
		 *
		 * @since   1.2.0
		 *
		 * @return  Charitable_Licenses_Settings
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Optionally add the licenses tab.
		 *
		 * @since   1.4.7
		 *
		 * @param   string[] $tabs Settings tabs.
		 * @return  string[]
		 */
		public function maybe_add_licenses_tab( $tabs ) {

			$products = charitable_get_helper( 'licenses' )->get_products();

			if ( empty( $products ) ) {
				return $tabs;
			}

			$show_licenses_tab = apply_filters( 'charitable_show_old_license_tab', false );

			if ( $show_licenses_tab ) :

				$tabs = charitable_add_settings_tab(
					$tabs,
					'licenses',
					__( 'Licenses', 'charitable' ),
					array(
						'index' => 4,
					)
				);

			endif;

			return $tabs;
		}

		/**
		 * Add the licenses tab settings fields.
		 *
		 * @since   1.0.0
		 *
		 * @return  array
		 */
		public function add_licenses_fields() {
			if ( ! charitable_is_settings_view( 'licenses' ) ) {
				return array();
			}

			$fields = array(
				'section'  => array(
					'title'    => '',
					'type'     => 'hidden',
					'priority' => 10000,
					'value'    => 'licenses',
					'save'     => false,
				),
				'licenses' => array(
					'title'    => false,
					'callback' => array( $this, 'render_licenses_table' ),
					'priority' => 4,
				),
			);

			foreach ( charitable_get_helper( 'licenses' )->get_products() as $key => $product ) {
				$fields[ $key ] = array(
					'type'     => 'text',
					'render'   => false,
					'priority' => 6,
				);
			}

			return $fields;
		}

		/**
		 * Add the licenses group.
		 *
		 * @since   1.0.0
		 *
		 * @param   string[] $groups Settings groups.
		 * @return  string[]
		 */
		public function add_licenses_group( $groups ) {
			$groups['licenses'] = array();
			return $groups;
		}

		/**
		 * Render the licenses table.
		 *
		 * @since   1.0.0
		 *
		 * @return  void
		 */
		public function render_licenses_table() {
			charitable_admin_view( 'settings/licenses' );
		}

		/**
		 * Add an extra button to the Licenses tab to re-check licenses.
		 *
		 * @since  1.6.0
		 *
		 * @param  string $button The button HTML.
		 * @return string
		 */
		public function add_license_recheck_button( $button ) {
			$licenses = array_filter( charitable_get_helper( 'licenses' )->get_licenses(), 'is_array' );

			if ( empty( $licenses ) ) {
				return $button;
			}

			$slug = Charitable_Addons_Directory::get_current_plan_slug();

			if ( $slug === false || ( is_string( $slug ) && strtolower( $slug ) === 'lite' ) ) {
				// there is no valid updated license so allow this button to output.

				$html = '<input style="margin-left:8px;" type="submit" class="button button-secondary" name="recheck" value="' . esc_attr__( 'Save & Re-check All Licenses', 'charitable' ) . '" /></p>';

				return str_replace(
					'</p>',
					$html,
					$button
				);

			}

			return $button;
		}

		/**
		 * Checks for updated license and invalidates status field if not set.
		 *
		 * @since   1.0.0
		 * @version 1.7.0.4
		 *
		 * @param   mixed[] $values The parsed values combining old values & new values.
		 * @param   mixed[] $new_values The newly submitted values.
		 * @return  mixed[]
		 */
		public function save_license( $values, $new_values ) {

			/* If we didn't just submit licenses, stop here. */
			if ( ! isset( $new_values['licenses_legacy'] ) ) {
				return $values;
			}

			$re_check = array_key_exists( 'recheck', $_POST );
			$licenses = $new_values['licenses_legacy'];

			// Remember that legacy licenses are passed into values differently in this hook. $values[licenses_legacy].
			foreach ( $licenses as $product_key => $license ) {
				$license = trim( $license );

				if ( empty( $license ) ) {
					$values['licenses_legacy'][ $product_key ] = '';
					continue;
				}

				$license_data = charitable_get_helper( 'licenses' )->verify_license( $product_key, $license, $re_check, true ); // the true added to make sure we let the server know we are attempting to vertify a legacy license.

				// updated info should look like: Array ( [license] => 123123123 [expiration_date] => 2023-08-10 23:59:59 [plan_id] => 1 [valid] => 1 [license_limit] => 1 [comm_success] => 1 ).

				if ( empty( $license_data ) ) {
					continue;
				}

				$values['licenses'][ $product_key ] = $license_data; // this is ok because this follows previous versions of where the licenses would be going.
			}

			return $values;
		}

		/**
		 * Outputs the "new" license HTML for the general settings tab.
		 *
		 * @since   1.7.0.4
		 *
		 * @param   string $has_valid_license Allows us to control HTML based on valid license already in the system.
		 * @return  string
		 */
		public function generate_license_check_html( $has_valid_license = 'false' ) {

			$slug              = Charitable_Addons_Directory::get_current_plan_slug();
			$is_legacy         = Charitable_Addons_Directory::is_current_plan_legacy();
			$readonly          = false;
			$show_license_form = true;

			if ( $slug === false || strtolower( $slug ) === 'lite' ) {
				$has_valid_license = false;
			}

			$output = '<div id="charitable-license-message" class="license-message license-valid-' . $has_valid_license . '">';
			if ( $has_valid_license && ! $is_legacy ) {
				$output  .= $this->get_licensed_message();
				$readonly = 'readonly value="xxxxxxxxxxxxxxxxxxxx"';
			} else {
				// Getting the current plan slug might be only applicate for newer licenses, so we need to account for "legacy" licenses to see if the install is licensed or not.
				if ( charitable_is_pro() && $is_legacy ) {
					$output           .= $this->get_legacy_licensed_message();
					$show_license_form = false;
				} else {
					$output .= $this->get_unlicensed_message();
				}
			}
			if ( $show_license_form ) :
				$output .= '<p>';
				$output .= '<input type="password" autocomplete="off" name="license-key" id="charitable-settings-upgrade-license-key" ' . $readonly . ' placeholder="' . esc_attr__( 'Paste license key here', 'charitable' ) . '" value="" />';
				if ( ! $has_valid_license ) {
					$output .= '<button data-action="verify" type="button" class="charitable-btn charitable-btn-md charitable-btn-green charitable-btn-activate" id="charitable-settings-connect-btn">' . esc_html__( 'Verify Key', 'charitable' ) . '</button>';
				}
				if ( $has_valid_license ) {
					$output .= '<button data-action="deactivate" type="button" class="charitable-btn charitable-btn-md charitable-btn-orange charitable-btn-deactivate" id="charitable-settings-connect-btn">' . esc_html__( 'Deactivate Key', 'charitable' ) . '</button>';
				}
				$output .= '</p>';
			endif;
			$output . '</div>';

			return $output;
		}

		/**
		 * Outputs a message for unlicnensed users for the general settings tab.
		 *
		 * @since   1.7.0.4
		 *
		 * @param   boolean $valid Valid license.
		 * @param   array   $license_data Available license information.
		 * @return  string
		 */
		public function get_unlicensed_message( $valid = false, $license_data = false ) {

			$output  = '<p>' . esc_html__( 'You\'re using ', 'charitable' );
			$output .= '<strong>Charitable Lite</strong>';
			$output .= esc_html__( ' - no license needed. Enjoy!', 'charitable' ) . ' 🙂</p>';
			$output .=
				'<p>' .
				sprintf(
					wp_kses(
						/* translators: %s - charitable.com upgrade URL. */
						__( 'To unlock more features consider <strong><a href="%s" target="_blank" rel="noopener noreferrer" class="charitable-upgrade-modal">upgrading to PRO</a></strong>.', 'charitable' ),
						[
							'a'      => [
								'href'   => [],
								'class'  => [],
								'target' => [],
								'rel'    => [],
							],
							'strong' => [],
						]
					),
					esc_url( charitable_pro_upgrade_url( 'settings-upgrade' ) )
				) .
				'</p>';
			$output .=
				'<p class="discount-note">' .
					wp_kses(
						__( 'As a valued charitable Lite user, you receive up to <strong>$300 off</strong>, automatically applied at checkout!', 'charitable' ),
						[
							'strong' => [],
							'br'     => [],
						]
					) .
				'</p>';

			if ( $valid && false === $error ) {
				$output .= '<p>' . esc_html__( 'Already registered? You might have an expired or invalid license. Reach out to us for support.', 'charitable' ) . '</p>';
			} elseif ( ! $valid && isset( $license_data['license_limit'] ) && false !== $license_data['license_limit'] ) {
				$output .= '<p>' . esc_html__( 'There was an error attempting to validate your license key. Check and see if you have exceeded your license activations.', 'charitable' ) . '</p>';
			} elseif ( ! $valid && isset( $license_data['comm_success'] ) && false !== $license_data['comm_success'] ) {
				$output .= '<p>' . esc_html__( 'There was an error attempting to contact the license server. Please try again later.', 'charitable' ) . '</p>';
			} elseif ( isset( $_GET['valid'] ) && 'invalid' === esc_html( $_GET['valid'] ) && isset( $_GET['comm_success'] ) && 0 === intval( $_GET['comm_success'] ) ) {
				$output .= '<p style="color:red;">' . esc_html__( 'There was an error attempting to validate your license key. Please try again later.', 'charitable' ) . '</p>';
			} elseif ( isset( $_GET['valid'] ) && 'invalid' === esc_html( $_GET['valid'] ) && isset( $_GET['license_limit'] ) && false !== $_GET['license_limit'] ) {
				$output .= '<p style="color:red;">' . esc_html__( 'There was an error attempting to validate your license key. Check and see if you have exceeded your license activations.', 'charitable' ) . '</p>';
			} elseif ( isset( $_GET['valid'] ) && 'invalid' === esc_html( $_GET['valid'] ) ) {
				$output .= '<p style="color:red;" data-invalid="Unknown">' . esc_html__( 'There was a problem attempting to validate your license key. Please try again later.', 'charitable' ) . '</p>';
			} else {
				$output .= '<hr><p>' . esc_html__( 'Already purchased? Simply enter your license key below to enable Charitable PRO!', 'charitable' ) . '</p>';
			}

			return $output;
		}

		/**
		 * Outputs a message for licnensed users for the general settings tab.
		 *
		 * @since   1.7.0.4
		 *
		 * @param   boolean $force_valid ????.
		 * @return  string
		 */
		public function get_licensed_message( $force_valid = true ) { // phpcs:ignore

			$settings        = get_option( 'charitable_settings' );
			$price_id        = intval( $settings['licenses']['charitable-v2']['plan_id'] );
			$license_expires = $settings['licenses']['charitable-v2']['expiration_date'];
			$valid           = $settings['licenses']['charitable-v2']['valid'];
			$output          = '';

			if ( $valid ) {

				$plan_name = $this->get_license_label_from_plan_id( $price_id );

				$output  = '<p>' . esc_html__( 'You\'re using ', 'charitable' );
				$output .= '<strong>Charitable ' . $plan_name . '</strong>';
				$output .= esc_html__( '. Your license expires on ', 'charitable' );
				$output .= date( 'M d, Y', strtotime( $license_expires ) );
				$output .= esc_html__( '. Enjoy!', 'charitable' ) . ' 🙂</p>';

				if ( defined( 'CHARITABLE_DEBUG' ) && CHARITABLE_DEBUG ) {
					$output .= print_r( $settings['licenses'], true );
					error_log( 'get_licensed_message' );
					error_log( print_r( $settings, true ) );
					error_log( print_r( $plan_name, true ) );
				}
			} else {
				$plan_name = 'Lite';
				if ( intval( $price_id ) > 0 ) {
					$valid = esc_html__( 'Your license is not valid or has expired.', 'charitable' );
				} else {
					$valid = false;
				}
				$output .= $this->get_unlicensed_message( $valid );
				if ( defined( 'CHARITABLE_DEBUG' ) && CHARITABLE_DEBUG ) {
					error_log( 'get_licensed_message - not valid' );
					error_log( print_r( $valid, true ) );
				}

			}

			return $output;
		}

		/**
		 * Get license label from plan id.
		 *
		 * @since   1.8.0
		 *
		 * @param   boolean $plan_id Plan ID.
		 * @return  string
		 */
		public function get_license_label_from_plan_id( $plan_id = false ) {

			return charitable_get_license_label_from_plan_id( $plan_id );
		}

		/**
		 * Outputs a message for unlicnensed users for the general settings tab.
		 *
		 * @since   1.7.0.4
		 * @return  string
		 */
		public function get_legacy_licensed_message() {

			$output =
				'<p>' .
				sprintf(
					wp_kses(
						/* translators: %s - charitable.com upgrade URL. */
						__( 'You\'re using <strong>Charitable</strong> with one or more <a href="%s">activated legacy licenses</a>. Enjoy! 🙂', 'charitable' ),
						[
							'a'      => [
								'href'   => [],
								'class'  => [],
								'target' => [],
								'rel'    => [],
							],
							'br'     => [],
							'strong' => [],
						]
					),
					esc_url( admin_url( 'admin.php?page=charitable-settings&tab=advanced' ) )
				) .
				'</p>';

			// display a potential upsell
			$licenses = array_filter( charitable_get_helper( 'licenses' )->get_licenses(), 'is_array' );
			// remove the two charitable 'keys' so we can see if anything licensed is left (say for example someone only has recurring donations licensed/activated )
			if ( isset( $licenses['charitable'] ) ) {
				unset( $licenses['charitable'] );
			}
			if ( isset( $licenses['charitable-v2'] ) ) {
				unset( $licenses['charitable-v2'] );
			}
			// todo: perhaps a better/more effective check for this.
			if ( count( $licenses ) > 0 ) {
				$output .=
				'<p>' .
				sprintf(
					wp_kses(
						/* translators: %s - charitable.com upgrade URL. */
						__( 'To unlock more features consider <strong><a href="%s" target="_blank" rel="noopener noreferrer" class="charitable-upgrade-modal">upgrading to PRO</a></strong>.', 'charitable' ),
						[
							'a'      => [
								'href'   => [],
								'class'  => [],
								'target' => [],
								'rel'    => [],
							],
							'strong' => [],
						]
					),
					esc_url( charitable_pro_upgrade_url( 'settings-upgrade' ) )
				) .
				'</p>';
				$output .=
				'<p class="discount-note">' .
					wp_kses(
						__( 'As a valued Charitable user, you receive up to <strong>$300 off</strong>, automatically applied at checkout!', 'charitable' ),
						[
							'strong' => [],
							'br'     => [],
						]
					) .
				'</p>';

			}

			return $output;
		}
	}

endif;
