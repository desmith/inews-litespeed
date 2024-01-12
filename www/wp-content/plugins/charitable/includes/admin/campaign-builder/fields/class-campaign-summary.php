<?php
/**
 * Class to add info bar field to a campaign form in the builder.
 *
 * @package   Charitable/Classes/Charitable_Field_Campaign_Summary
 * @author    David Bisset
 * @copyright Copyright (c) 2023, WP Charitable LLC
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.8.0
 * @version   1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Field_Campaign_Summary' ) ) :

	/**
	 * Class to add campaign summary field to a campaign form in the builder.
	 */
	class Charitable_Field_Campaign_Summary extends Charitable_Builder_Field {

		/**
		 * Primary class constructor.
		 *
		 * @since 1.8.0
		 */
		public function init() {

			// Basic information.
			$this->name  = esc_html__( 'Campaign Summary', 'charitable' );
			$this->type  = 'campaign-summary';
			$this->icon  = 'fa-list-ul';
			$this->order = 50;

			$this->align_default = 'center';

			// Edit/Duplication information.
			$this->can_be_edited     = true;
			$this->can_be_deleted    = true;
			$this->can_be_duplicated = true;
			$this->edit_label        = 'Edit Campaign Summary';
			$this->edit_type         = 'campaign-summary';
			$this->edit_section      = 'recommended';

			// Misc.
			$this->tooltip = '';

			// Define additional field properties.
			add_action( 'charitable_frontend_js', [ $this, 'frontend_js' ] );
		}

		/**
		 * Social Sharing options panel inside the builder.
		 *
		 * @since 1.8.0
		 *
		 * @param array $field Field settings.
		 */
		public function field_options( $field ) {
			/*
			 * Basic field options.
			 */

			// Options open markup.
		}

		/**
		 * Render the field.
		 *
		 * @since 1.8.0
		 *
		 * @param array   $field_data     Any field data.
		 * @param array   $campaign_data  Amount data and settings.
		 * @param integer $field_id       The field ID.
		 * @param string  $mode           Where the field is being displayed ("preview" or "template").
		 * @param array   $template_data  Tempalate data.
		 */
		public function render( $field_data = false, $campaign_data = false, $field_id = false, $mode = 'template', $template_data = false ) {

			$campaign = isset( $campaign_data['id'] ) && 0 !== intval( $campaign_data['id'] ) ? charitable_get_campaign( $campaign_data['id'] ) : false;

			if ( ! $campaign ) {
				// use default settings?
				$percent_donated          = '0%';
				$donation_summary_content = '<span class="amount">$0.00</span> Donated';
				$donor_count              = '0';
				$time_left                = false;
				$is_time_left             = false;
			} else {
				$percent_donated          = ( false !== $campaign->get_percent_donated() ) ? esc_html( $campaign->get_percent_donated() ) : '&nbsp;&nbsp;';
				$donation_summary_content = ( method_exists( $campaign, 'get_builder_donation_summary' ) ) ? $campaign->get_builder_donation_summary() : $campaign->get_donation_summary();
				$donor_count              = esc_html( $campaign->get_donor_count() );
				$time_left                = trim( $campaign->get_time_left() );
				$is_time_left             = ( '' !== $time_left ) ? true : false;
			}

			ob_start();

			$headline = ! empty( $field_data['headline'] ) ? esc_html( $field_data['headline'] ) : false;

			?>

			<?php

				$preview_css_class = $this->maybe_display_field( $campaign_data, 'campaign_hide_percent_raised' ) ? false : 'charitable-hidden';
				$preview_css_class = $percent_donated !== '&nbsp;&nbsp;' ? $preview_css_class : 'charitable-hidden';

			?>
		<div class="campaign-raised campaign-summary-item campaign_hide_percent_raised <?php echo esc_attr( $preview_css_class ); ?>">
				<?php

				printf(
					/* translators: %s: percentage raised */
					_x( '%s Raised', 'percentage raised', 'charitable' ),
					'<span class="amount">' . esc_html( $percent_donated ) . '</span>'
				);

				?>
		</div>

			<?php

			$preview_css_class = $this->maybe_display_field( $campaign_data, 'campaign_hide_amount_donated' ) ? false : 'charitable-hidden';

			if ( '' !== trim( $donation_summary_content ) ) :
				?>
				<div class="campaign-figures campaign-summary-item campaign_hide_amount_donated <?php echo esc_attr( $preview_css_class ); ?>"><?php echo $donation_summary_content; ?></div>
			<?php endif; ?>

			<?php

			$preview_css_class = $this->maybe_display_field( $campaign_data, 'campaign_hide_number_of_donors' ) ? false : 'charitable-hidden';

			?>

		<div class="campaign-donors campaign-summary-item campaign_hide_number_of_donors <?php echo esc_attr( $preview_css_class ); ?>">
			<?php

			if ( intval( $donor_count ) === 1 ) {

				printf(
					/* translators: %s: number of donors */
					_x( '%s Donor', 'number of donors', 'charitable' ),
					'<span class="donors-count">' . esc_html( $donor_count ) . '</span>'
				);

			} else {

				printf(
					/* translators: %s: number of donors */
					_x( '%s Donors', 'number of donors', 'charitable' ),
					'<span class="donors-count">' . esc_html( $donor_count ) . '</span>'
				);

			}
			?>
		</div>

			<?php

			$preview_css_class = $is_time_left ? false : 'charitable-hidden';

			?>

		<div class="campaign-time-left campaign-summary-item campaign_hide_time_remaining <?php echo esc_attr( $preview_css_class ); ?>">
			<?php
			if ( $is_time_left ) {
				echo $time_left;
			} else {
				echo '<span class="amount time-left days-left">XX</span> Days Left';
			}
			?>
		</div>

				<?php

				$pre_html = ob_get_clean();

				$html = '<div class="charitable-field-' . $mode . '-campaign-summary charitable-prevent-select"><div class="placeholder"><h5 class="charitable-field-' . $mode . '-headline">' . $headline . '</h5></div>' . $pre_html . '</div>';

				return $html;
		}

			/**
			 * Social Sharing preview inside the builder.
			 *
			 * @since 1.8.0
			 *
			 * @param array  $field_data Field data and settings.
			 * @param array  $campaign_data Campaign data and settings.
			 * @param array  $field_id Field ID.
			 * @param string $theme Template data.
			 */
		public function field_preview( $field_data = false, $campaign_data = false, $field_id = false, $theme = '' ) {

			if ( empty( $field_data['align'] ) ) {
				$field_data['align'] = $this->align_default;
			}

			$html  = $this->field_title( $this->name );
			$html .= $this->field_wrapper( $this->render( $field_data, $campaign_data, $field_id, 'preview' ), $field_data );

			echo $html;
		}

			/**
			 * The display on the campaign front-end.
			 *
			 * @since 1.8.0
			 *
			 * @param string $field_type     The passed field type.
			 * @param array  $field_data     Any field data.
			 * @param array  $campaign_data  Amount data and settings.
			 */
		public function field_display( $field_type = '', $field_data = false, $campaign_data = false ) {

			$html = $this->field_display_wrapper( $this->render( $field_data, $campaign_data ), $field_data );

			echo apply_filters( 'charitable_campaign_builder_' . $this->type . '_field_display', $html, $campaign_data );
		}

			/**
			 * The display on the form settings backend when the user clicks on the field/block.
			 *
			 * @since 1.8.0
			 *
			 * @param array $field          Social Sharing settings.
			 * @param array $campaign_data  Campaign data and settings.
			 */
		public function settings_display( $field_id = false, $campaign_data = false ) {

			if ( ! class_exists( 'Charitable_Builder_Form_Fields' ) || false === $field_id ) {
				return;
			}

			$charitable_builder_form_fields = new Charitable_Builder_Form_Fields();

			$settings = isset( $campaign_data['fields'][ $field_id ] ) ? $campaign_data['fields'][ $field_id ] : false;
			$field_id = intval( $field_id );

			ob_start();

			?>

		<h4 class="charitable-panel-field" data-field-id="<?php echo intval( $field_id ); ?>"><?php echo esc_html( $this->name ); ?> (ID: <?php echo intval( $field_id ); ?>)</h4>

			<div class="charitable-panel-field charitable-panel-field-section" data-field-id="<?php echo intval( $field_id ); ?>">

				<?php echo do_action( 'charitable_builder_' . $this->type . '_settings_display_start', $field_id, $campaign_data ); ?>

			</div>

				<?php

				echo $charitable_builder_form_fields->generate_text(
					isset( $settings['headline'] ) ? $settings['headline'] : '',
					esc_html__( 'Headline', 'charitable' ),
					array(
				'id'       => 'field_' . esc_attr( $this->type ) . '_headline' . '_' . intval( $field_id ), // phpcs:ignore
					'name'     => array( '_fields', intval( $field_id ), 'headline' ),
					'field_id' => intval( $field_id ),
					'tooltip'  => esc_html__( 'Add a headline to this field.', 'charitable' ),
					'class'    => 'charitable-campaign-builder-headline',
					)
				);

				echo $charitable_builder_form_fields->generate_checkboxes(
					isset( $settings['show_hide'] ) ? $settings['show_hide'] : false,
					esc_html__( 'Choose what information to show:', 'charitable' ),
					array(
				'id'       => 'field_' . esc_attr( $this->type ) . '_show_hide' . '_' . intval( $field_id ), // phpcs:ignore
					'name'        => array( '_fields', intval( $field_id ), 'show_hide' ),
					'field_id'    => intval( $field_id ),
					'tooltip'     => esc_html( $this->tooltip ),
					'class'       => 'charitable-campaign-summary-checkboxes',
					'description' => esc_html__( 'Note: Some items might not be visible or enabled depending on campaign goal and date settings. Save and refresh the builder to see updated values.', 'charitable' ),
					'options'     => array(
						'Amount Donated'   => 'campaign_hide_amount_donated',
						'Number of Donors' => 'campaign_hide_number_of_donors',
						'Percent Raised'   => 'campaign_hide_percent_raised',
						'Time Remaining'   => 'campaign_hide_time_remaining',
					),
					'defaults'    => array(
						'campaign_hide_amount_donated',
						'campaign_hide_number_of_donors',
					),
					)
				);

				echo $charitable_builder_form_fields->generate_divider( false, false, array( 'field_id' => $field_id ) );

				echo $charitable_builder_form_fields->generate_number_slider(
					isset( $settings['width_percentage'] ) ? $settings['width_percentage'] : 100,
					esc_html__( 'Width', 'charitable' ),
					array(
				'id'         => 'field_' . esc_attr( $this->type ) . '_width_percentage' . '_' . intval( $field_id ), // phpcs:ignore
					'name'       => array( '_fields', intval( $field_id ), 'width_percentage' ),
					'field_type' => esc_attr( $this->type ),
					'field_id'   => intval( $field_id ),
					'symbol'     => '%',
					'min'        => 0,
					'min_actual' => 30,
					'css_class'  => 'charitable-indicator-on-hover',
					'tooltip'    => esc_html__( 'Adjust the width of the field within the column.', 'charitable' ),
					)
				);

				echo $charitable_builder_form_fields->generate_align(
					isset( $settings['align'] ) ? $settings['align'] : esc_attr( $this->align_default ),
					esc_html__( 'Align', 'charitable' ),
					array(
				'id'       => 'field_' . esc_attr( $this->type ) . '_align' . '_' . intval( $field_id ), // phpcs:ignore
					'name'     => array( '_fields', intval( $field_id ), 'align' ),
					'field_id' => intval( $field_id ),
					)
				);

				/* CSS CLASS */

				echo $charitable_builder_form_fields->generate_text(
					isset( $settings['css_class'] ) ? $settings['css_class'] : false,
					esc_html__( 'CSS Class', 'charitable' ),
					array(
						'id'       => 'field_' . esc_attr( $this->type ) . '_css_class' . '_' . intval( $field_id ),
						'name'     => array( '_fields', intval( $field_id ), 'css_class' ),
						'field_id' => intval( $field_id ),
						'tooltip'  => esc_html__( 'Add CSS classes (seperated by a space) for this field to customize it\'s appearance in your theme.', 'charitable' ),
					)
				);

				?>

			<div class="charitable-panel-field charitable-panel-field-section" data-field-id="<?php echo intval( $field_id ); ?>">

				<?php do_action( 'charitable_builder_' . $this->type . '_settings_display_end', $field_id, $campaign_data ); ?>

			</div>

				<?php

				$html = ob_get_clean();

				return $html;
		}

		/**
		 * Enqueue frontend limit option js.
		 *
		 * @since 1.8.0
		 *
		 * @param array $forms Forms on the current page.
		 */
		public function frontend_js( $forms ) {
		}

		/**
		 * Format and sanitize field.
		 *
		 * @since 1.8.0
		 *
		 * @param int   $field_id     Field ID.
		 * @param mixed $field_submit Field value that was submitted.
		 * @param array $campaign_data    Form data and settings.
		 */
		public function format( $field_id, $field_submit, $campaign_data ) {
		}

		/**
		 * Validate field on form submit.
		 *
		 * @since 1.8.0
		 *
		 * @param int   $field_id     Field ID.
		 * @param mixed $field_submit Field value that was submitted.
		 * @param array $campaign_data    Form data and settings.
		 */
		public function validate( $field_id, $field_submit, $campaign_data ) {
		}

		/**
		 * Easier to read field/function to determine to show a part of the summary based on settings.
		 *
		 * @since 1.8.0
		 *
		 * @param array  $campaign_data Campaign Data.
		 * @param string $slug Slug.
		 */
		public function maybe_display_field( $campaign_data = false, $slug = false ) {

			if ( false === $slug ) {
				return false;
			}

			$slug = strtolower( $slug );

			if ( $slug === 'campaign_hide_percent_raised' && ( empty( $campaign_data ) || empty( $campaign_data['settings']['general']['goal'] ) || '' === $campaign_data['settings']['general']['goal'] ) ) {
				return false;
			}

			if ( ! isset( $campaign_data['settings']['campaign-summary'][ $slug ] ) ) {
				return true;
			}

			if ( ! empty( $campaign_data['settings']['campaign-summary'][ $slug ] ) && 1 !== intval( $campaign_data['settings']['campaign-summary'][ $slug ] ) ) {
				return true;
			}

			return false;
		}
	}

		new Charitable_Field_Campaign_Summary();
endif;