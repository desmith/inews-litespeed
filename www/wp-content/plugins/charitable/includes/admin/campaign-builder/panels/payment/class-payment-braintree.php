<?php
/**
 * The class that defines a subpanel for the payment area for the campaign builder.
 *
 * @package   Charitable/Admin/Charitable_Campaign_Meta_Boxes
 * @author    David Bisset
 * @copyright Copyright (c) 2023, WP Charitable LLC
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.8.0
 * @version.  1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Builder_Panel_Payment_Braintree' ) ) :

	/**
	 * Braintree subpanel for Marketing Panel for campaign builder.
	 *
	 * @since 1.8.0
	 */
	class Charitable_Builder_Panel_Payment_Braintree {

		/**
		 * Slug.
		 *
		 * @since 1.8.0
		 *
		 * @var string
		 */
		private $slug = 'braintree';

		/**
		 * The label/headline at the top of the panel.
		 *
		 * @since 1.8.0
		 *
		 * @var string
		 */
		private $primary_label = '';

		/**
		 * Determines if the tab is initially active on a fresh new page load.
		 *
		 * @since 1.8.0
		 *
		 * @var string
		 */
		private $active = false;

		/**
		 * Get things going. Add action hooks for the sidebar menu and the panel itself.
		 *
		 * @since 1.8.0
		 */
		public function __construct() {

			$this->primary_label = esc_html__( 'Braintree', 'charitable' );

			add_action( 'charitable_campaign_builder_payment_sidebar', array( $this, 'sidebar_tab' ) );
			add_action( 'charitable_campaign_builder_payment_panels', array( $this, 'panel_content' ) );
		}

		/**
		 * Generate sidebar html.
		 *
		 * @since 1.8.0
		 */
		public function sidebar_tab() {

			$active = ( true === apply_filters( 'charitable_campaign_builder_payment_sidebar_active', $this->active, $this->slug ) ) ? 'active' : false;

			echo '<a href="#" class="charitable-panel-sidebar-section charitable-panel-sidebar-section-' . esc_attr( $this->slug ) . ' ' . $active . '" data-section="' . esc_attr( $this->slug ) . '">'
				. '<img class="charitable-builder-sidebar-icon" src="' . charitable()->get_path( 'assets', false ) . 'images/campaign-builder/settings/payment/' . $this->slug . '.png" />'
				. esc_html( $this->primary_label )
				. ' <i class="fa fa-angle-right charitable-toggle-arrow"></i></a>';
		}

		/**
		 * Generate panel content.
		 *
		 * @since 1.8.0
		 */
		public function panel_content() {

			$active = ( true === apply_filters( 'charitable_campaign_builder_settings_sidebar_active', $this->active, $this->slug ) ) ? 'active' : false;
			$style  = ( true === apply_filters( 'charitable_campaign_builder_settings_sidebar_active', $this->active, $this->slug ) ) ? 'display: block;' : false;

			$panel = new Charitable_Builder_Panel_Payment();

			ob_start();

			?>

			<div class="charitable-panel-content-section charitable-panel-content-section-parent-payment charitable-panel-content-section-<?php echo $this->slug; ?> <?php echo $active; ?>" style="<?php echo $style; ?>">

				<div class="charitable-panel-content-section-title"><?php echo $this->primary_label; ?> <?php echo esc_html__( 'Settings', 'charitable' ); ?></div>

				<div class="charitable-panel-content-section-interior">

				<?php

					do_action( 'charitable_campaign_builder_before_payment_' . $this->slug );

				?>

				<?php $panel->education_payment_text( $this->primary_label, $this->slug ); ?>


				<?php

					do_action( 'charitable_campaign_builder_after_payment_' . $this->slug );

				?>

				</div>

			</div>

			<?php

			$html = ob_get_clean();

			echo $html;
		}
	}

	new Charitable_Builder_Panel_Payment_Braintree();

endif;