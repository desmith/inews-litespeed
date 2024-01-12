<?php
/**
 * Marketing class management panel.
 *
 * @package   Charitable
 * @author    David Bisset
 * @copyright Copyright (c) 2023, WP Charitable LLC
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since     1.8.0
 * @version   1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Charitable_Builder_Panel_Marketing' ) ) :

	/**
	 * Design management panel.
	 *
	 * @since 1.8.0
	 */
	class Charitable_Builder_Panel_Marketing extends Charitable_Builder_Panel {

		/**
		 * Form data and marketing.
		 *
		 * @since 1.4.4.1
		 *
		 * @var array
		 */
		public $campaign_data;

		/**
		 * Panels for the submenu campaign builder.
		 *
		 * @since 1.8.0
		 *
		 * @var array
		 */
		public $submenu_panels;

		/**
		 * All systems go.
		 *
		 * @since 1.8.0
		 */
		public function init() {

			// Define panel information.
			$this->name    = esc_html__( 'Marketing', 'charitable' );
			$this->slug    = 'marketing';
			$this->icon    = 'panel_marketing.svg';
			$this->order   = 40;
			$this->sidebar = true;

			// This should never be called unless we are on the campaign builder page.
			if ( campaign_is_campaign_builder_admin_page() ) {
				$this->load_submenu_panels();
			}
		}

		/**
		 * Enqueue assets for the Design panel.
		 *
		 * @since 1.8.0
		 */
		public function enqueues() {
		}

		/**
		 * Load panels.
		 *
		 * @since 1.8.0
		 */
		public function load_submenu_panels() {

			$this->submenu_panels = apply_filters(
				'charitable_builder_panels_marketing_',
				array(
					'mailchimp',
					'active-campaign',
					'campaign-monitor',
					'mailerlite',
					'mailpoet',
					'mailster',
					'constant-contact',
					'zapier',
					'hubspot',
					'aweber',
					'convert-kit',
					'integromat',
					'zoho-flow',
					'request',
				)
			);

			foreach ( $this->submenu_panels as $panel ) {
				$panel = sanitize_file_name( $panel );
				$file  = charitable()->get_path( 'includes' ) . "admin/campaign-builder/panels/marketing/class-marketing-{$panel}.php";

				if ( file_exists( $file ) ) {
					require_once $file;
				}
			}
		}


		/**
		 * Output the Field panel sidebar.
		 *
		 * @since 1.8.0
		 */
		public function panel_sidebar() {

			// This should never be called unless we are on the campaign builder page.
			if ( ! campaign_is_campaign_builder_admin_page() ) {
				return;
			}

			do_action( 'charitable_campaign_builder_marketing_sidebar', $this->campaign_data );
		}

		/**
		 * Process marketing for campaigns, mostly via the builder.
		 *
		 * @since 1.8.0
		 *
		 * @param string $field Field.
		 * @param string $section Marketing section.
		 * @param string $top_level Level.
		 * @param string $meta_key Alt legacy location to try to pull marketing.
		 */
		public function campaign_data_marketing( $field = 'title', $section = 'general', $top_level = 'marketing', $meta_key = false ) {
		}

		/**
		 * Process education marketing text.
		 *
		 * @since 1.8.0
		 *
		 * @param string $label Reader friendly output of object.
		 * @param string $slug Object slug.
		 * @param string $type Type of request.
		 */
		public function education_marketing_text( $label = false, $slug = false, $type = false ) {

			$learn_more_url = charitable_ga_url(
				'https://wpcharitable.com/lite-vs-pro/', // base url.
				urlencode( esc_html( $label ) . ' Marketing Page' ), // utm-medium.
				urlencode( 'Learn More' ) // utm-content.
			);

			?>

			<?php

			echo '<img class="charitable-builder-sidebar-icon" src="' . charitable()->get_path( 'assets', false ) . 'images/campaign-builder/settings/marketing/' . esc_attr( $slug ) . '_big.png" wdith="178" height="178" />';

			?>

		<section class="header-content">

			<?php

			if ( 'charitable-automation-connect' === $type ) :

				?>

				<?php echo esc_html__( 'Turn one-time donors into ongoing supporters with', 'charitable' ); ?> <span><?php echo esc_html( $label ); ?></span>.</h2>

				<p><strong><?php echo esc_html__( 'Automation Connect', 'charitable' ); ?></strong> <?php echo esc_html__( 'addon allows you to connect', 'charitable' ); ?> <strong><?php echo esc_html__( 'Charitable', 'charitable' ); ?></strong> <?php echo esc_html__( 'with thousands of other apps including', 'charitable' ); ?> <strong><?php echo esc_html( $label ); ?></strong> <?php echo esc_html__( 'by using 3rd party automation platforms like Zapier, Integromat or Zoho Flow.', 'charitable' ); ?></p>

		<?php elseif ( 'request' === $type ) : ?>

			<h2><?php echo esc_html__( 'Don\'t see an integration with', 'charitable' ); ?> <strong><?php echo esc_html__( 'Charitable', 'charitable' ); ?></strong> <?php echo esc_html__( 'and your favorite product or service?', 'charitable' ); ?></h2>
			<h2><?php echo esc_html__( 'Let us know!', 'charitable' ); ?></h2>

		<?php else : ?>

			<h2><?php echo esc_html__( 'Turn one-time donors into ongoing supporters with ', 'charitable' ); ?> <span><?php echo esc_html( $label ); ?></span>.</h2>

			<p><?php echo esc_html( $label ); ?><?php echo esc_html__( '\'s affordable pricing and excellent reputation have made it the go-to hosted email marketing provider for more millions of people.', 'charitable' ); ?> <?php echo esc_html__( 'Charitable Pro', 'charitable' ); ?> <?php echo esc_html__( 'allows you to integrate seamlessly with ', 'charitable' ); ?><span><?php echo esc_html( $label ); ?></span>.</p>


		</section>

		<?php endif; ?>


			<?php

			if ( 'charitable-automation-connect' === $type ) :

				?>

			<div class="education-buttons education-buttons-top">
				<a class="button-link" target="_blank" href="<?php echo esc_url( $learn_more_url ); ?>"><?php echo esc_html__( 'Learn More', 'charitable' ); ?></a> <button type="button" class="btn btn-confirm update-to-pro-link"><?php echo esc_html__( 'Upgrade to PRO', 'charitable' ); ?></button>
			</div>

			<div class="education-images type-custom type-<?php echo esc_attr( $type ); ?>">

				<img class="charitable-marketing-image charitable-marketing-image-1 charitable-marketing-image-<?php echo esc_attr( $slug ); ?>" src="<?php echo charitable()->get_path( 'assets', false ) . 'images/campaign-builder/settings/marketing/education/' . esc_attr( $slug ) . '-1.png'; ?>" alt="<?php echo esc_html( $label ); ?>" />

			</div>

			<section class="main-content">

				<p><?php echo esc_html__( 'Upgrading to <strong>Pro</strong> gives you great capabilities to get more donations...', 'charitable' ); ?></p>

				<ul>
					<li><?php echo esc_html__( 'Give your donors an attractive end of financial year receipt.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Fine-tune the processing fees by setting a fixed fee per donation.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Allow users to raise donations and gain incredible support for their cause.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Boost your fundraising with recurring donations.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Set a location for your campaigns, and display them on a Google Map.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Developer-friendly automation addon that will help you customize it to your needs.', 'charitable' ); ?></li>
				</ul>

			</section>

				<?php
		elseif ( 'request' === $type ) :

			// attempt to prefill name and email fields.
			$current_user = wp_get_current_user();
			$name         = ( $current_user ) ? charitable_get_creator_data() : false;
			$email        = ! empty( $current_user->user_email ) ? $current_user->user_email : false;

			?>

			<!-- start feedback form -->

			<div class="charitable-feedback-form-container">

				<div id="charitable-marketing-form" class="charitable-form charitable-feedback-form">

					<div class="charitable-feedback-form-interior">

						<input type="hidden" class="charitable-feedback-form-type" value="marketing" />

						<div class="charitable-form-row charitable-feedback-form-row">
							<label><?php echo esc_html__( 'Name', 'charitable' ); ?>  <span class="charitable-feedback-form-required">*</span></label>
							<input name="charitable-feedback-form-name" type="text" class="charitable-feedback-form-name" value="<?php echo esc_html( $name ); ?>" />
						</div>
						<div class="charitable-form-row charitable-feedback-form-row">
							<label><?php echo esc_html__( 'Email', 'charitable' ); ?> <span class="charitable-feedback-form-required">*</span></label>
							<input name="charitable-feedback-form-email" type="email" class="charitable-feedback-form-email" value="<?php echo esc_html( $email ); ?>" />
						</div>
						<div class="charitable-form-row charitable-feedback-form-row">
							<label><?php echo esc_html__( 'What intergration(s) would you like to see?', 'charitable' ); ?><span class="charitable-feedback-form-required">*</span></label>
							<textarea name="charitable-feedback-form-feedback" class="charitable-feedback-form-feedback"></textarea>
						</div>
						<div class="charitable-form-row charitable-feedback-form-row">
							<p class="charitable-feedback-form-required">* = <?php echo esc_html__( 'Required', 'charitable' ); ?></p>
						</div>
						<div class="charitable-form-row charitable-feedback-form-row">
							<a class="button-link"><?php echo esc_html__( 'Send Request', 'charitable' ); ?></a>
						</div>
						<i class="charitable-loading-spinner charitable-loading-black charitable-loading-inline charitable-hidden"></i>

					</div>

					<div class="charitable-feedback-form-interior-confirmation">

						<!-- confirmation -->
						<div id="charitable-feedback-form-confirmation" class="charitable-form charitable-feedback-form charitable-form-confirmation charitable-hidden">
							<h2><?php echo esc_html__( 'Thank You!', 'charitable' ); ?></h2>
							<p><?php echo esc_html__( 'Your feedback has been sent to our team. Stay tuned to our updates on', 'charitable' ); ?> <a href="https://wpcharitable.com" target="_blank">wpcharitable.com</a>.</p>
						</div>

					</div>

				</div>

			</div>

			<!-- end feedback form -->

			<section class="main-content">

				<p><?php echo esc_html__( 'Upgrading to', 'charitable' ); ?> <strong><?php echo esc_html__( 'Pro', 'charitable' ); ?></strong> <?php echo esc_html__( 'gives you the following capabilities...', 'charitable' ); ?></p>

				<ul>
					<li><?php echo esc_html__( 'Set a default list, opt-in mode and label that is used for all campaigns.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Have donors subscribed to multiple lists when they donate.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Allow donors automatically added to your mailing list when they give their consent.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Create your own custom / merge fields and map these fields to your donation forms.', 'charitable' ); ?></li>
				</ul>

			</section>

		<?php else : ?>

			<div class="education-buttons education-buttons-top">
				<a class="button-link" target="_blank" href="<?php echo esc_url( $learn_more_url ); ?>"><?php echo esc_html__( 'Learn More', 'charitable' ); ?></a> <button type="button" class="btn btn-confirm update-to-pro-link"><?php echo esc_html__( 'Upgrade to PRO', 'charitable' ); ?></button>
			</div>

			<div class="education-images">

				<img class="charitable-marketing-image charitable-marketing-image-1 charitable-marketing-image-<?php echo esc_attr( $slug ); ?>" src="<?php echo charitable()->get_path( 'assets', false ) . 'images/campaign-builder/settings/marketing/education/' . esc_attr( $slug ) . '-1.png'; ?>" alt="<?php echo esc_html( $label ); ?>" />
				<img class="charitable-marketing-image charitable-marketing-image-2 charitable-marketing-image-<?php echo esc_attr( $slug ); ?>" src="<?php echo charitable()->get_path( 'assets', false ) . 'images/campaign-builder/settings/marketing/education/' . esc_attr( $slug ) . '-2.png'; ?>" alt="<?php echo esc_html( $label ); ?>" />

			</div>

			<section class="main-content">

				<p><?php echo esc_html__( 'Upgrading to', 'charitable' ); ?> <strong><?php echo esc_html__( 'Pro', 'charitable' ); ?></strong> <?php echo esc_html__( 'gives you the following capabilities...', 'charitable' ); ?></p>

				<ul>
					<li><?php echo esc_html__( 'Using more than just ', 'charitable' ); ?><span><?php echo esc_html( $label ); ?></span><?php echo esc_html__( '? Have one or more providers.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Set a default list, opt-in mode and label that is used for all campaigns.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Have donors subscribed to multiple lists when they donate.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Allow donors automatically added to your mailing list when they give their consent.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Create your own custom / merge fields and map these fields to your donation forms.', 'charitable' ); ?></li>
					<li><?php echo esc_html__( 'Just add your ', 'charitable' ); ?><span><?php echo esc_html( $label ); ?></span><?php echo esc_html__( ' API Key to your Charitable settings and you’re ready to start.', 'charitable' ); ?></li>
				</ul>

			</section>

		<?php endif; ?>




		<div class="education-buttons education-buttons-bottom">

		<h2><?php echo esc_html__( 'Get the most out of your campaigns with ', 'charitable' ); ?><strong><?php echo esc_html__( 'Pro', 'charitable' ); ?></strong>.</h2>

		<a class="button-link" target="_blank" href="<?php echo esc_url( $learn_more_url ); ?>"><?php echo esc_html__( 'Learn More', 'charitable' ); ?></a> <button type="button" class="btn btn-confirm update-to-pro-link"><?php echo esc_html__( 'Upgrade to PRO', 'charitable' ); ?></button>

		</div>

			<?php
		}

		/**
		 * Output the Field panel primary content.
		 *
		 * @since 1.8.0
		 */
		public function panel_content() {

			// This should never be called unless we are on the campaign builder page.
			if ( ! campaign_is_campaign_builder_admin_page() ) {
				return;
			}

			do_action( 'charitable_campaign_builder_marketing_panels', $this->campaign_data );
		}

		/**
		 * Builder field buttons.
		 *
		 * @since 1.8.0
		 */
		public function fields() {
		}

		/**
		 * Sort Add Field buttons by order provided.
		 *
		 * @since 1.8.0
		 *
		 * @param array $a First item.
		 * @param array $b Second item.
		 *
		 * @return array
		 */
		public function field_order( $a, $b ) {

			return $a['order'] - $b['order'];
		}
	}

endif;

new Charitable_Builder_Panel_Marketing();
