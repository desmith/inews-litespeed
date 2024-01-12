<?php
/* @var $this NewsletterSubscriptionAdmin */
/* @var $controls NewsletterControls */
/* @var $logger NewsletterLogger */

defined('ABSPATH') || exit;

if (!$controls->is_action()) {
    
    $controls->data = $this->get_options('', $language);

    if (empty($controls->data['welcome_email_id'])) {
        $email = [];
        $email['type'] = 'welcome';
        $email['editor'] = NewsletterEmails::EDITOR_COMPOSER;
        $email['message'] = '';
        $email['track'] = Newsletter::instance()->get_option('track');
        $email['subject'] = 'Welcome';
        $email['status'] = 'sent';
        $email = NewsletterEmails::instance()->save_email($email);
        $controls->data['welcome_email_id'] = $email->id;
        $controls->data['welcome_email'] = '';
        $this->save_options($controls->data, '', $language);
    }
    $email = Newsletter::instance()->get_email($controls->data['welcome_email_id']);
    TNP_Composer::prepare_controls($controls, $email);
    $controls->data['welcome_email'] = $controls->data['welcome_email'];
} else {
    if ($controls->is_action('save')) {
        $options = $this->get_options('', $language);
        $options['welcome_email'] = $controls->data['welcome_email'];
        $this->save_options($options, '', $language);
        $email = Newsletter::instance()->get_email($options['welcome_email_id']);
        TNP_Composer::update_email($email, $controls);
        $email = NewsletterEmails::instance()->save_email($email);
        $controls->add_message_saved();
        TNP_Composer::prepare_controls($controls, $email);
    }
}

?>
<script>
    var tnp_preset_show = false;
    jQuery(function () {
        jQuery('#options-welcome_email').on('change', function () {
            if (this.value === '1') {
                jQuery('#tnp-builder').show();
            } else {
                jQuery('#tnp-builder').hide();
            }
        });
        if (document.getElementById('options-welcome_email').value === '1') {
            jQuery('#tnp-builder').show();
        } else {
            jQuery('#tnp-builder').hide();
        }
    });
</script>
<div class="wrap" id="tnp-wrap">
    <?php include NEWSLETTER_ADMIN_HEADER ?>
    <div id="tnp-heading">

        <h2><?php echo esc_html($form->title) ?></h2>
        <?php include __DIR__ . '/nav.php' ?>

    </div>

    <div id="tnp-body" class="tnp-automated-edit">


        <?php $controls->show(); ?>


        <div class="tnp-automated-edit">

            <form method="post" id="tnpc-form" action="" onsubmit="tnpc_save(this); return true;">
                <?php $controls->init(); ?>

                <p>
                    <?php $controls->button_save() ?>
                    <?php $controls->select('welcome_email', ['' => 'Use the default welcome email', '1' => 'Use this custom welcome email', '2' => 'Do not send']); ?>
                    <?php $controls->button_icon_statistics(NewsletterStatisticsAdmin::instance()->get_statistics_url($form_options['welcome_email_id']), ['secondary'=>true]) ?>
                </p>
                <?php $controls->composer_fields_v2() ?>

            </form>
            <?php $controls->composer_load_v2(true, false, 'automated') ?>

        </div>

    </div>
</div>

