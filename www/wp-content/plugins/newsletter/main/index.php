<?php
/* @var $this NewsletterMainAdmin */
/* @var $controls NewsletterControls */

defined('ABSPATH') || exit;

wp_enqueue_script('tnp-chart');

if ($controls->is_action('feed_enable')) {
    delete_option('newsletter_feed_demo_disable');
    $controls->messages = 'Feed by Mail demo panels enabled. On next page reload it will show up.';
}

if ($controls->is_action('feed_disable')) {
    update_option('newsletter_feed_demo_disable', 1);
    $controls->messages = 'Feed by Mail demo panel disabled. On next page reload it will disappear.';
}

$emails_module = NewsletterEmailsAdmin::instance();
$statistics_module = NewsletterStatisticsAdmin::instance();
$emails = $wpdb->get_results("select * from " . NEWSLETTER_EMAILS_TABLE . " where type='message' order by id desc limit 5");

$users_module = NewsletterUsersAdmin::instance();
$query = "select * from " . NEWSLETTER_USERS_TABLE . " order by id desc limit 10";
$subscribers = $wpdb->get_results($query);

// Retrieves the last standard newsletter
$last_email = $wpdb->get_row(
        $wpdb->prepare("select * from " . NEWSLETTER_EMAILS_TABLE . " where type='message' and status in ('sent', 'sending') and send_on<%d order by id desc limit 1", time()));

if ($last_email) {
    $report = $statistics_module->get_statistics($last_email);
    $last_email_sent = $report->total;
    $last_email_opened = $report->open_count;
    $last_email_notopened = $last_email_sent - $last_email_opened;
    $last_email_clicked = $report->click_count;
    $last_email_opened -= $last_email_clicked;

    $overall_sent = $wpdb->get_var("select sum(sent) from " . NEWSLETTER_EMAILS_TABLE . " where type='message' and status in ('sent', 'sending')");

    $overall_opened = $wpdb->get_var("select count(distinct user_id,email_id) from " . NEWSLETTER_STATS_TABLE);
    $overall_notopened = $overall_sent - $overall_opened;
    $overall_clicked = $wpdb->get_var("select count(distinct user_id,email_id) from " . NEWSLETTER_STATS_TABLE . " where url<>''");
    $overall_opened -= $overall_clicked;
} else {
    $last_email_opened = 500;
    $last_email_notopened = 400;
    $last_email_clicked = 200;

    $overall_opened = 500;
    $overall_notopened = 400;
    $overall_clicked = 200;
}

$months = $wpdb->get_results("select count(*) as c, concat(year(created), '-', date_format(created, '%m')) as d "
        . "from " . NEWSLETTER_USERS_TABLE . " where status='C' "
        . "group by concat(year(created), '-', date_format(created, '%m')) order by d desc limit 12");
$values = array();
$labels = array();
foreach ($months as $month) {
    $values[] = (int) $month->c;
    $labels[] = date("M y", date_create_from_format("Y-m", $month->d)->getTimestamp());
}
$values = array_reverse($values);
$labels = array_reverse($labels);

$lists = $this->get_lists();

$sources = [];

$active = class_exists('NewsletterWoocommerce');
$url = $active ? '?page=newsletter_woocommerce_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'WC Registration', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterWoocommerce');
$url = $active ? '?page=newsletter_woocommerce_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'WC Checkout', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterCF7');
$url = $active ? '?page=newsletter_cf7_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'CF7 Forms', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterWPForms');
$url = $active ? '?page=newsletter_wpnlforms_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'WP Forms', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterNinjaForms');
$url = $active ? '?page=newsletter_ninjaforms_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'Ninja Forms', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterGravityForms');
$url = $active ? '?page=newsletter_gravityforms_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'Gravity Forms', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterElementor');
$url = $active ? '?page=newsletter_elementor_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'Elementor Forms', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterForminator');
$url = $active ? '?page=newsletter_forminator_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'Forminator Forms', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterFormidable');
$url = $active ? '?page=newsletter_formidable_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'Formidable Forms', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterFluentForms');
$url = $active ? '?page=newsletter_fluentforms_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'Fluent Forms', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterApi');
$url = $active ? '?page=newsletter_api_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'Newsletter API', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterPmpro');
$url = $active ? '?page=newsletter_pmpro_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'Paid Memb. Pro', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterEdd');
$url = $active ? '?page=newsletter_edd_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'EDD Checkout', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterComments');
$url = $active ? '?page=newsletter_comments_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'WP Comments', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterAutomated');
$url = $active ? '?page=newsletter_automated_index' : '?page=newsletter_main_extensions';
$automated = ['title' => 'Automated', 'active' => $active, 'url' => $url, 'usable' => true];

$active = class_exists('NewsletterAutoresponder');
$url = $active ? '?page=newsletter_autoresponder_index' : '?page=newsletter_main_extensions';
$autoresponder = ['title' => 'Autoresponder', 'active' => $active, 'url' => $url, 'usable' => true];

$active = class_exists('NewsletterWpUsers');
$url = $active ? '?page=newsletter_wpusers_index' : '?page=newsletter_main_extensions';
$sources[] = ['title' => 'WP Signup', 'active' => $active, 'url' => $url, 'usable' => $active];

$active = class_exists('NewsletterReports');
$url = $active ? '?page=newsletter_reports_newsletters' : '?page=newsletter_main_extensions#analytics';
$reports = ['title' => 'Advanced Reports', 'active' => $active, 'url' => $url, 'usable' => true];

$blocks = [];
$active = class_exists('NewsletterBlocks');
$url = $active ? '?page=newsletter_blocks_index' : '?page=newsletter_main_extensions';
$blocks[] = ['title' => 'Extra Blocks', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterWoocommerce');
$url = $active ? '?page=newsletter_blocks_index' : '?page=newsletter_main_extensions';
$blocks[] = ['title' => 'WC Products', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterEdd');
$url = $active ? '?page=newsletter_blocks_index' : '?page=newsletter_main_extensions';
$blocks[] = ['title' => 'EDD Downloads', 'active' => $active, 'url' => $url];

$active = class_exists('NewsletterEvents');
$url = $active ? '?page=newsletter_blocks_index' : '?page=newsletter_main_extensions';
$blocks[] = ['title' => 'Events', 'active' => $active, 'url' => $url];
?>

<style>
<?php include __DIR__ . '/css/dashboard.css' ?>
</style>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-body" class="tnp-main-index">

        <div class="tnp-dashboard">

            <div class="tnp-cards-container">
                <div class="tnp-card">
                    <div class="tnp-flow">

                        <div>
                            <div class="tnp-flow-item title wide">Subscribers start here</div>
                        </div>
                        <div>
                            <div class="tnp-flow-item"><a href="?page=newsletter_subscription_form" target="_blank">Standard form</a></div>

                            <?php foreach ($sources as $source) { ?>
                                <?php if (!$source['active']) continue; ?>
                                <div class="tnp-flow-item <?php echo $source['active'] ? '' : 'inactive' ?>"><a href="<?php echo esc_attr($source['url']) ?>" target="_blank"><?php echo esc_html($source['title']) ?></a></div>
                            <?php } ?>
                        </div>
                        <a href="javascript:void()" onclick="document.getElementById('tnp-other-sources').style.display = 'flex'; return false" style="display: block; text-decoration: underline">Other sources</a>
                        <div id="tnp-other-sources" style="display: none;">


                            <?php foreach ($sources as $source) { ?>
                                <?php if ($source['active']) continue; ?>
                                <div class="tnp-flow-item notusable"><a href="<?php echo esc_attr($source['url']) ?>" target="_blank"><?php echo esc_html($source['title']) ?></a></div>
                            <?php } ?>

                        </div>
                        <div class="tnp-flow-arrow">▼</div>
                        <div>
                            <div class="tnp-flow-item"><a href="?page=newsletter_subscription_antispam" target="_blank">Antispam</a></div>
                        </div>

                        <div>
                            <div class="tnp-flow-item wide"><a href="?page=newsletter_subscription_options" target="_blank">Activation/Welcome messages and emails</a></div>
                        </div>
                        <div class="tnp-flow-arrow">▼</div>
                        <div>
                            <div class="tnp-flow-item <?php echo $automated['active'] ? '' : 'inactive' ?>"><a href="<?php echo esc_attr($automated['url']) ?>" target="_blank">Automated</a></div>
                            <div class="tnp-flow-item"><a href="?page=newsletter_emails_index" target="_blank">Newsletters</a></div>
                            <div class="tnp-flow-item <?php echo $autoresponder['active'] ? '' : 'inactive' ?>"><a href="<?php echo esc_attr($autoresponder['url']) ?>" target="_blank">Autoresponder</a></div>
                        </div>

                        <a href="javascript:void()" onclick="document.getElementById('tnp-other-blocks').style.display = 'flex'; return false" style="display: block; text-decoration: underline">Contents</a>
                        <div id="tnp-other-blocks" style="display: none;">
                            <?php foreach ($blocks as $block) { ?>
                                <div class="tnp-flow-item notusable"><a href="?page=newsletter_main_extensions#tnp-creating<?php //echo esc_attr($block['url']) ?>" target="_blank"><?php echo esc_html($block['title']) ?></a></div>
                            <?php } ?>
                        </div>

                        <div class="tnp-flow-arrow">▼</div>

                        <div>
                            <div class="tnp-flow-item">Standard reports</div>
                            <div class="tnp-flow-item <?php echo $reports['active'] ? '' : 'inactive' ?>"><a href="<?php echo esc_attr($reports['url']) ?>" target="_blank"><a href="<?php echo esc_attr($reports['url']) ?>" target="_blank"><?php echo esc_html($reports['title']) ?></a></a></div>
                        </div>
                    </div>
                </div>

                <div class="tnp-card">
                    <div class="tnp-card-header">
                        <div class="tnp-card-title"><?php esc_html_e('Subscribers', 'newsletter') ?></div>
                        <div class="tnp-card-upper-buttons"><a href="?page=newsletter_users_index"><?php _e('Manage', 'newsletter') ?></a></div>
                    </div>
                    <div class="tnp-card-content">
                        <table class="widefat" style="width: 100%">
                            <thead></thead>
                            <tbody>
                                <?php foreach ($subscribers as $s) { ?>
                                    <tr>
                                        <td>
                                            <?php echo esc_html($s->email) ?>
                                        </td>
                                        <td>
                                            <?php echo esc_html($s->name) ?> <?php echo esc_html($s->surname) ?>
                                        </td>
                                        <td style="text-align: center">
                                            <?php echo $emails_module->get_user_status_label($s, true) ?>
                                        </td>
                                        <td style="text-align: right">
                                            <?php $controls->button_icon_edit('?page=newsletter_users_edit' . '&amp;id=' . $s->id) ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>

            <div class="tnp-cards-container">
                <div class="tnp-card tnp-mimosa">
                    <div class="tnp-card-title"><?php esc_html_e('Form', 'newsletter') ?></div>
                    <div class="tnp-card-description"><?php esc_html_e('Setup the form fields and labels.', 'newsletter') ?></div>
                    <div class="tnp-card-button-container">
                        <a href="?page=newsletter_subscription_form"><?php esc_html_e('Configure', 'newsletter') ?></a>
                    </div>
                </div>

                <div class="tnp-card">
                    <div class="tnp-card-title"><?php esc_html_e('Lists', 'newsletter') ?></div>
                    <div class="tnp-card-description">You have <?php echo count($lists) ?> lists.</div>
                    <div class="tnp-card-button-container">
                        <a href="?page=newsletter_subscription_lists"><?php esc_html_e('Configure', 'newsletter') ?></a>
                    </div>
                </div>

                <div class="tnp-card">
                    <div class="tnp-card-title"><?php esc_html_e('Delivery', 'newsletter') ?></div>
                    <div class="tnp-card-description"><?php esc_html_e('Change the delivery speed, sender name and return path.', 'newsletter') ?></div>
                    <div class="tnp-card-button-container">
                        <a href="?page=newsletter_main_main"><?php esc_html_e('Configure', 'newsletter') ?></a>
                    </div>
                </div>

                <div class="tnp-card">
                    <div class="tnp-card-title"><?php esc_html_e('Company Info', 'newsletter') ?></div>
                    <div class="tnp-card-description"><?php esc_html_e('Set your company name, address, socials.', 'newsletter') ?></div>
                    <div class="tnp-card-button-container">
                        <a href="?page=newsletter_main_info"><?php esc_html_e('Configure', 'newsletter') ?></a>
                    </div>
                </div>
            </div>


            <div class="tnp-cards-container">
                <div class="tnp-card">
                    <div class="tnp-card-header">
                        <div class="tnp-card-title"><?php esc_html_e('Newsletters', 'newsletter') ?></div>
                        <div class="tnp-card-upper-buttons"><a href="?page=newsletter_emails_index"><?php _e('Manage', 'newsletter') ?></a></div>
                    </div>
                    <div class="tnp-card-content">

                        <table class="widefat" style="width: 100%">
                            <thead></thead>
                            <tbody>
                                <?php foreach ($emails as $email) { ?>
                                    <tr>
                                        <td>
                                            <?php echo esc_html($email->subject) ?>
                                        </td>
                                        <td style="text-align: center">
                                            <?php $emails_module->show_email_status_label($email) ?>
                                        </td>
                                        <td style="text-align: center">
                                            <?php $emails_module->show_email_progress_bar($email, array('scheduled' => true)) ?>
                                        </td>
                                        <td style="text-align: right">
                                            <?php
                                            if ($email->status === TNP_Email::STATUS_SENT || $email->status === TNP_Email::STATUS_SENDING) {
                                                echo '<a class="button-primary" href="' . $statistics_module->get_statistics_url($email->id) . '"><i class="fas fa-chart-bar"></i></a>';
                                            } else {
                                                echo $emails_module->get_edit_button($email, true);
                                            }
                                            ?>

                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>

                <div class="tnp-card">
                    <div class="tnp-card-title"><?php _e('Subscriptions', 'newsletter') ?></div>
                    <div class="tnp-canvas">
                        <canvas id="tnp-events-chart-canvas" height="300"></canvas>
                    </div>

                    <script type="text/javascript">
                        var events_data = {
                            labels: <?php echo json_encode($labels) ?>,
                            datasets: [
                                {
                                    label: "<?php _e('Subscriptions', 'newsletter') ?>",
                                    fill: true,
                                    strokeColor: "#27AE60",
                                    backgroundColor: "#eee",
                                    borderColor: "#27AE60",
                                    pointBorderColor: "#27AE60",
                                    pointBackgroundColor: "#ECF0F1",
                                    data: <?php echo json_encode($values) ?>
                                }
                            ]
                        };

                        jQuery(document).ready(function ($) {
                            ctxe = $('#tnp-events-chart-canvas').get(0).getContext("2d");
                            eventsLineChart = new Chart(ctxe, {
                                type: 'line', data: events_data,
                                options: {
                                    maintainAspectRatio: false,
                                    xresponsive: true,
                                    scales: {
                                        xAxes: [{
                                                type: "category",
                                                "id": "x-axis-1",
                                                gridLines: {display: false},
                                                ticks: {fontFamily: "soleil"}
                                            }],
                                        yAxes: [
                                            {
                                                type: "linear",
                                                "id": "y-axis-1",
                                                gridLines: {display: false},
                                                ticks: {fontFamily: "soleil"}
                                            },
                                        ]
                                    },
                                }
                            });
                        });
                    </script>
                </div>
            </div>


            <div class="tnp-cards-container">


                <div class="tnp-card">
                    <div class="tnp-card-header">
                        <div class="tnp-card-title"><?php _e('Documentation', 'newsletter') ?></div>
                    </div>
                    <div>
                        <a href="https://www.thenewsletterplugin.com/documentation/installation/" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Installation
                            </div>
                        </a>
                        <a href="https://www.thenewsletterplugin.com/documentation/subscription/" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Subscription
                            </div>
                        </a>
                        <a href="https://www.thenewsletterplugin.com/category/tips" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Tips & Tricks
                            </div>
                        </a>
                        <a href="https://www.thenewsletterplugin.com/documentation/subscribers-and-management/" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Subscribers and management
                            </div>
                        </a>
                        <a href="https://www.thenewsletterplugin.com/documentation/newsletters/newsletters-module/" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Creating Newsletters
                            </div>
                        </a>
                        <a href="https://www.thenewsletterplugin.com/documentation/addons/" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Premium Addons
                            </div>
                        </a>
                        <a href="https://www.thenewsletterplugin.com/documentation/customization/" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Customization
                            </div>
                        </a>
                        <a href="https://www.thenewsletterplugin.com/documentation/delivery-and-spam/" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Delivery and spam
                            </div>
                        </a>
                        <a href="https://www.thenewsletterplugin.com/documentation/developers/" target="_blank">
                            <div class="tnp-card-documentation-index">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20" height="20"><title>saved items</title><g class="nc-icon-wrapper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"  ><path d="M37,4h3a4,4,0,0,1,4,4V40a4,4,0,0,1-4,4H8a4,4,0,0,1-4-4V8A4,4,0,0,1,8,4h3" fill="none"  stroke-miterlimit="10"/> <polygon points="32 24 24 18 16 24 16 4 32 4 32 24" fill="none" stroke-miterlimit="10" data-color="color-2"/></g></svg>
                                Developers & Advanced Topics
                            </div>
                        </a>
                    </div>
                </div>
            </div>


            <div class="tnp-cards-container">
                <div class="tnp-card">
                    <div class="tnp-card-header">
                        <div class="tnp-card-title"><?php _e('Developers', 'newsletter') ?></div>
                    </div>
                    <div class="tnp-card-description">Extending Newsletter by yourself? There is something for you as well!</div>
                    <div class="tnp-card-button-container">
                        <a href="https://www.thenewsletterplugin.com/documentation/developers/" target="_blank">Developer's love 💛</a>
                    </div>
                </div>
                <div class="tnp-card">
                    <div class="tnp-card-header">
                        <div class="tnp-card-title"><?php _e('Video Tutorials', 'newsletter') ?></div>
                    </div>
                    <div class="tnp-card-description">We have some videos to help gest the most from Newsletter.</div>
                    <div class="tnp-card-video">
                        <iframe width="400" height="200" src="https://www.youtube.com/embed/zmVmW84Bw9A" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <div class="tnp-card-button-container">
                        <a href="https://www.thenewsletterplugin.com/video-tutorials" target="_blank">See the videos</a>
                    </div>
                </div>
            </div>

        </div>

        <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

    </div>
</div>
