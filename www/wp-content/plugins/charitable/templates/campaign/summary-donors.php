<?php
/**
 * Displays the campaign's donor summary.
 *
 * Override this template by copying it to yourtheme/charitable/campaign/summary-donors.php
 *
 * @author  WP Charitable LLC
 * @package Charitable/Templates/Campaign Page
 * @since   1.0.0
 * @version 1.0.0
 */

$campaign = $view_args['campaign'];

?>
<div class="campaign-donors campaign-summary-item">
	<?php
	printf(
		/* translators: %s: number of donors */
		_x( '%s Donors', 'number of donors', 'charitable' ),
		'<span class="donors-count">' . $campaign->get_donor_count() . '</span>'
	);
	?>
</div>
