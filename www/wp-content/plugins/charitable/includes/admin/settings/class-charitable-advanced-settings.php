<?php
/**
 * Charitable Advanced Settings UI.
 *
 * @package   Charitable/Classes/Charitable_Advanced_Settings
 * @author    David Bisset
 * @copyright Copyright (c) 2023, WP Charitable LLC
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.0.0
 * @version   1.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Advanced_Settings' ) ) :

	/**
	 * Charitable_Advanced_Settings
	 *
	 * @final
	 * @since   1.0.0
	 */
	final class Charitable_Advanced_Settings {

		/**
		 * The single instance of this class.
		 *
		 * @var     Charitable_Advanced_Settings|null
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
		 * @return  Charitable_Advanced_Settings
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Add the advanced tab settings fields.
		 *
		 * @since   1.0.0
		 *
		 * @return  array<string,array>
		 */
		public function add_advanced_fields() {
			if ( ! charitable_is_settings_view( 'advanced' ) ) {
				return array();
			}

			return array(
				'section'                       => array(
					'title'    => '',
					'type'     => 'hidden',
					'priority' => 10000,
					'value'    => 'advanced',
				),
				'section_dangerous'             => array(
					'title'    => __( 'Advanced Settings', 'charitable' ),
					'type'     => 'heading',
					'class'    => 'section-heading',
					'priority' => 100,
				),
				'delete_data_on_uninstall'      => array(
					'label_for' => __( 'Reset Data', 'charitable' ),
					'type'      => 'checkbox',
					'help'      => '<span style="color:red;font-weight:bold;">' . __( 'DELETE ALL DATA' ) . '</span> ' . __( 'when uninstalling the plugin.', 'charitable' ),
					'priority'  => 105,
				),
				'clear_expire_options'      => array(
					'label_for' => __( 'Clear Cache', 'charitable' ),
					'type'      => 'checkbox',
					'help'      => __( 'This removes and refreshes items in the database specific to Charitable.', 'charitable' ),
					'priority'  => 120,
				),
				'section'  => array(
					'title'    => '',
					'type'     => 'hidden',
					'priority' => 10000,
					'value'    => 'licenses',
					'save'     => false,
				),
				'section_licenses' => array(
					'title'    => __( 'Legacy Licenses', 'charitable' ),
					'type'     => 'heading',
					'class'    => 'section-heading',
					'priority' => 202,
				),
				'licenses' => array(
					'title'    => false,
					'callback' => array( $this, 'render_licenses_table' ),
					'priority' => 204,
				),
			);

			foreach ( charitable_get_helper( 'licenses' )->get_products() as $key => $product ) {
				$fields[ $key ] = array(
					'type'     => 'text',
					'render'   => false,
					'priority' => 206,
				);
			}

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
		 * Removes select options that might be causing trouble or unwanted notices, mostly items that we done pre-1.7.0. Also known as 'clear cache' or 'clearing cache'.
		 *
		 * @since   1.7.0.7
		 *
		 * @param   mixed[] $values The parsed values combining old values & new values.
		 * @param   mixed[] $new_values The newly submitted values.
		 * @return  mixed[]
		 */
		public function clear_expired_options( $values, $new_values ) {

			/* If this option isn't in the return values or isn't checked off, leave. */
			if ( ! isset( $new_values['clear_expire_options'] ) || 0 === intval( $new_values['clear_expire_options'] ) ) {
				return $values;
			}

			// Remove the options.
			delete_option( 'charitable_doing_upgrade' );
			delete_option( 'charitable_third_party_warnings' );
			delete_option( 'charitable_campaign_builder_templates' ); // v1.8.0.

			// Allow an addon to hook into this.
			do_action( 'charitable_clear_expired_options' );

			charitable_get_admin_notices()->add_notice( 'Charitable cache has been cleared.', 'success', false, true );

			$values['clear_expire_options'] = false;

			return $values;
		}

		/**
		 * Checks for updated license and invalidates status field if not set.
		 *
		 * @since   1.0.0
		 *
		 * @param   mixed[] $values The parsed values combining old values & new values.
		 * @param   mixed[] $new_values The newly submitted values.
		 * @return  mixed[]
		 */
		public function save_license( $values, $new_values ) {
			/* If we didn't just submit licenses, stop here. */
			if ( ! isset( $new_values['licenses'] ) ) {
				return $values;
			}

			$re_check = array_key_exists( 'recheck', $_POST );
			$licenses = $new_values['licenses'];

			foreach ( $licenses as $product_key => $license ) {
				$license = trim( $license );

				if ( empty( $license ) ) {
					$values['licenses'][ $product_key ] = '';
					continue;
				}

				$license_data = charitable_get_helper( 'licenses' )->verify_license( $product_key, $license, $re_check );

				if ( empty( $license_data ) ) {
					continue;
				}

				$values['licenses'][ $product_key ] = $license_data;
			}

			return $values;
		}

	}

endif;
