<?php
/**
 * Payment class management panel.
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

if ( ! class_exists( 'Charitable_Builder_Panel_Payment' ) ) :

	/**
	 * Design management panel.
	 *
	 * @since 1.8.0
	 */
	class Charitable_Builder_Panel_Payment extends Charitable_Builder_Panel {

		/**
		 * Form data and payment.
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
			$this->name    = esc_html__( 'Payment', 'charitable' );
			$this->slug    = 'payment';
			$this->icon    = 'panel_payment.svg';
			$this->order   = 50;
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
				'charitable_builder_panels_payment_',
				array(
					'stripe',
					'paypal',
					'braintree',
					'mollie',
					'gocardless',
					'authorize-net',
					'payfast',
					'payrexx',
					'paystack',
					'windcave',
					'square',
					'request',
				)
			);

			foreach ( $this->submenu_panels as $panel ) {
				$panel = sanitize_file_name( $panel );
				$file  = charitable()->get_path( 'includes' ) . "admin/campaign-builder/panels/payment/class-payment-{$panel}.php";

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

			do_action( 'charitable_campaign_builder_payment_sidebar', $this->campaign_data );
		}

		/**
		 * Process payment for campaigns, mostly via the builder.
		 *
		 * @since 1.8.0
		 *
		 * @param string $field Field.
		 * @param string $section Payment section.
		 * @param string $top_level Level.
		 * @param string $meta_key Alt legacy location to try to pull payment.
		 */
		public function campaign_data_payment( $field = 'title', $section = 'general', $top_level = 'payment', $meta_key = false ) {
		}

		/**
		 * Process education payment text.
		 *
		 * @since 1.8.0
		 *
		 * @param string $label Reader friendly output of object.
		 * @param string $slug Object slug.
		 * @param string $type Type of request.
		 */
		public function education_payment_text( $label = false, $slug = false, $type = false ) {

			$learn_more_url = charitable_ga_url(
				'https://wpcharitable.com/lite-vs-pro/', // base url.
				urlencode( esc_html( $label ) . ' Payment Page' ), // utm-medium.
				urlencode( 'Learn More' ) // utm-content.
			);

			?>

			<?php

			echo '<img class="charitable-builder-sidebar-icon" src="' . charitable()->get_path( 'assets', false ) . 'images/campaign-builder/settings/payment/' . esc_attr( $slug ) . '_big.png" wdith="178" height="178" />';

			?>

		<section class="header-content">

			<?php if ( $slug === 'stripe' || $slug === 'paypal' ) : ?>

			<h2><?php echo esc_html__( 'Charitable has ', 'charitable' ); ?><span><?php echo esc_html( $label ); ?></span> <?php echo esc_html__( 'built in.', 'charitable' ); ?></h2>

			<p><?php echo esc_html( $label ); ?> <?php echo esc_html__( 'allows you to easily reach more supporters and increase donations with payment platforms including PayPal, Venmo, Apple Pay and Google Pay.', 'charitable' ); ?></p>

		<?php elseif ( 'request' === $type ) : ?>

			<h2><?php echo esc_html__( 'Don\'t see an integration with ', 'charitable' ); ?><strong><?php echo esc_html__( 'Charitable', 'charitable' ); ?></strong> <?php echo esc_html__( 'and your favorite payment method?', 'charitable' ); ?></h2>
			<h2><?php echo esc_html__( 'Let us know!', 'charitable' ); ?></h2>

		<?php else : ?>

		<h2><strong>Charitable Pro</strong> <?php echo esc_html__( 'allows you to integrate seamlessly with ', 'charitable' ); ?><span><?php echo esc_html( $label ); ?></span>.</h2>

		<p><?php echo esc_html( $label ); ?> <?php echo esc_html__( 'allows you to easily reach more supporters and increase donations with payment platforms including PayPal, Venmo, Apple Pay and Google Pay.', 'charitable' ); ?></p>

		<div class="education-buttons education-buttons-top">
				<a class="button-link" target="_blank" href="<?php echo esc_url( $learn_more_url ); ?>"><?php echo esc_html__( 'Learn More', 'charitable' ); ?></a> <button type="button" class="btn btn-confirm update-to-pro-link"><?php echo esc_html__( 'Upgrade to PRO', 'charitable' ); ?></button>
		</div>

		<?php endif; ?>

		</section>

			<?php if ( $slug === 'stripe' || $slug === 'paypal' ) : ?>

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

					<input type="hidden" class="charitable-feedback-form-type" value="payment" />

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

		<div id="charitable-marketing-form-confirmation" class="charitable-form charitable-marketing-form charitable-form-confirmation charitable-hidden">
			<h2><?php echo esc_html__( 'Thank you!', 'charitable' ); ?></h2>
			<p><?php echo esc_html__( 'Your feedback has been sent to our team. Stay tuned to our updates on', 'charitable' ); ?> <a href="https://wpcharitable.com" target="_blank">wpcharitable.com</a>.</p>
		</div>

		<section class="main-content">

			<p><?php echo esc_html__( 'Upgrading to ', 'charitable' ); ?><strong><?php echo esc_html__( 'Pro', 'charitable' ); ?></strong> <?php echo esc_html__( 'gives you the following capabilities...', 'charitable' ); ?></p>

			<ul>
				<li><?php echo esc_html__( 'Intergrate with a wide variety of gateways including', 'charitable' ); ?> <strong><?php echo esc_html__( 'Payfast', 'charitable' ); ?></strong>, <strong><?php echo esc_html__( 'Mollie', 'charitable' ); ?></strong>, <strong><?php echo esc_html__( 'Authorize.Net', 'charitable' ); ?></strong> <?php echo esc_html__( 'and more.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'With the <strong>Gift Aid</strong> addon, a tax incentive for UK charities, you can boost your donations by 25%.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'Allow your donors to donate anonmously.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'Give your donors the ability to share a message when they donate.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'Help your donors with their record keeping by providing downloadable annual receipts.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'Make life easy for your donors by providing them with a PDF receipt for their donation.', 'charitable' ); ?></li>
			</ul>

		</section>

		<?php else : ?>

		<div class="education-images">

		<img class="charitable-payment-image charitable-payment-image-1 charitable-payment-image-<?php echo esc_attr( $slug ); ?>" src="<?php echo charitable()->get_path( 'assets', false ) . 'images/campaign-builder/settings/payment/education/' . esc_attr( $slug ) . '.gif'; ?>" alt="<?php echo esc_html( $label ); ?>" />

		</div>

		<section class="main-content">

			<p><?php echo esc_html__( 'Upgrading to ', 'charitable' ); ?><strong><?php echo esc_html__( 'Pro', 'charitable' ); ?></strong> <?php echo esc_html__( 'gives you the following capabilities...', 'charitable' ); ?></p>

			<ul>
				<li><?php echo esc_html__( 'Intergrate with a wide variety of gateways including', 'charitable' ); ?> <strong><?php echo esc_html__( 'Payfast', 'charitable' ); ?></strong>, <strong><?php echo esc_html__( 'Mollie', 'charitable' ); ?></strong>, <strong><?php echo esc_html__( 'Authorize.Net', 'charitable' ); ?></strong> <?php echo esc_html__( 'and more.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'With the <strong>Gift Aid</strong> addon, a tax incentive for UK charities, you can boost your donations by 25%.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'Allow your donors to donate anonmously.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'Give your donors the ability to share a message when they donate.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'Help your donors with their record keeping by providing downloadable annual receipts.', 'charitable' ); ?></li>
				<li><?php echo esc_html__( 'Make life easy for your donors by providing them with a PDF receipt for their donation.', 'charitable' ); ?></li>
			</ul>

		</section>

		<?php endif; ?>

		<div class="education-buttons">

			<?php if ( $slug === 'stripe' || $slug === 'paypal' ) { ?>

				<?php

				$helper = charitable_get_helper( 'gateways' );

				if ( $slug === 'stripe' ) {

					$gateway   = new Charitable_Gateway_Stripe_AM();
					$is_active = $helper->is_active_gateway( $gateway->get_gateway_id() );

					if ( $is_active ) {

						$action_url = admin_url( 'admin.php?page=charitable-settings&tab=gateways&group=gateways_stripe' );

						echo '<a class="button-link" href="' . esc_url( $action_url ) . '" target="_blank">' . esc_html__( 'Go To Settings', 'charitable' ) . '</a>';

					} else {

						$action_url = esc_url(
							add_query_arg(
								array(
									'charitable_action' => 'enable_gateway',
									'gateway_id'        => $gateway->get_gateway_id(),
									'_nonce'            => wp_create_nonce( 'gateway' ),
								),
								admin_url( 'admin.php?page=charitable-settings&tab=gateways' )
							)
						);

						echo '<a class="button-link" href="' . esc_url( $action_url ) . '" target="_blank">' . esc_html__( 'Enable Stripe', 'charitable' ) . '</a>';
					}

					// $link = admin_url('admin.php?page=charitable-settings&tab=gateways&group=gateways_stripe');

				}

				if ( $slug === 'paypal' ) {

					$gateway   = new Charitable_Gateway_Paypal();
					$is_active = $helper->is_active_gateway( $gateway->get_gateway_id() );

					if ( $is_active ) {

						$action_url = admin_url( 'admin.php?page=charitable-settings&tab=gateways&group=gateways_paypal' );

						echo '<a class="button-link" href="' . esc_url( $action_url ) . '" target="_blank">' . esc_html__( 'Go To Settings', 'charitable' ) . '</a>';

					} else {

						$action_url = esc_url(
							add_query_arg(
								array(
									'charitable_action' => 'enable_gateway',
									'gateway_id'        => $gateway->get_gateway_id(),
									'_nonce'            => wp_create_nonce( 'gateway' ),
								),
								admin_url( 'admin.php?page=charitable-settings&tab=gateways' )
							)
						);

						echo '<a class="button-link" href="' . esc_url( $action_url ) . '" target="_blank">' . esc_html__( 'Enable Paypal', 'charitable' ) . '</a>';
					}

				}

				?>

		<?php } else { ?>

		<h2><?php echo esc_html__( 'Get the most out of your campaigns with ', 'charitable' ); ?><strong><?php echo esc_html__( 'Pro', 'charitable' ); ?></strong>.</h2>

		<a class="button-link" href="<?php echo esc_url( $learn_more_url ); ?>"><?php echo esc_html__( 'Learn More', 'charitable' ); ?></a> <button type="button" class="btn btn-confirm"><?php echo esc_html__( 'Upgrade to PRO', 'charitable' ); ?></button>

		<?php } ?>

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

			do_action( 'charitable_campaign_builder_payment_panels', $this->campaign_data );
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

new Charitable_Builder_Panel_Payment();
