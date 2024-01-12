<?php
/**
 * Displays the campaign description.
 *
 * Override this template by copying it to yourtheme/charitable/campaign/description.php
 *
 * @author  WP Charitable LLC
 * @package Charitable/Templates/Campaign Page
 * @since   1.0.0
 * @version 1.7.0.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Prior the description output as $view_args['campaign']->description but since 1.7.0.4 as visual editor can add html.
$campaign = isset( $view_args['campaign'] ) ? $view_args['campaign'] : false;

?>
<div class="campaign-description">
	<?php echo apply_filters( 'charitable_campaign_description_template_content', $view_args['campaign']->description, $campaign ); ?>
</div>
