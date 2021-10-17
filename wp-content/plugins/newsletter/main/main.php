<?php
defined('ABSPATH') || exit;

@include_once NEWSLETTER_INCLUDES_DIR . '/controls.php';
$controls = new NewsletterControls();
$module = Newsletter::instance();

if (!$controls->is_action()) {
    $controls->data = get_option('newsletter_main');
    if (!isset($controls->data['roles'])) {
        $controls->data['roles'] = array();
        if (!empty($controls->data['editor'])) $controls->data['roles'] = 'editor';
    }
} else {

    if ($controls->is_action('save')) {
        $errors = null;
        
        if (!isset($controls->data['roles'])) $controls->data['roles'] = array();

        // Validation
        $controls->data['sender_email'] = $module->normalize_email($controls->data['sender_email']);
        if (!$module->is_email($controls->data['sender_email'])) {
            $controls->errors .= __('The sender email address is not correct.', 'newsletter') . '<br>';
        } else {
            $controls->data['sender_email'] = $module->normalize_email($controls->data['sender_email']);
        }

        if (!$module->is_email($controls->data['return_path'], true)) {
            $controls->errors .= __('Return path email is not correct.', 'newsletter') . '<br>';
        } else {
            $controls->data['return_path'] = $module->normalize_email($controls->data['return_path']);
        }


        if (!$module->is_email($controls->data['reply_to'], true)) {
            $controls->errors .= __('Reply to email is not correct.', 'newsletter') . '<br>';
        } else {
            $controls->data['reply_to'] = $module->normalize_email($controls->data['reply_to']);
        }

        if (!empty($controls->data['contract_key'])) {
            $controls->data['contract_key'] = trim($controls->data['contract_key']);
        }

        if (empty($controls->errors)) {
            $module->merge_options($controls->data);
            $controls->add_message_saved();
            $module->logger->debug('Main options saved');
        }

        update_option('newsletter_log_level', $controls->data['log_level']);

        //$module->hook_newsletter_extension_versions(true);
        delete_transient("tnp_extensions_json");
        delete_transient('newsletter_license_data');
    }

    if ($controls->is_action('create')) {
        $page = array();
        $page['post_title'] = 'Newsletter';
        $page['post_content'] = '[newsletter]';
        $page['post_status'] = 'publish';
        $page['post_type'] = 'page';
        $page['comment_status'] = 'closed';
        $page['ping_status'] = 'closed';
        $page['post_category'] = array(1);
        
        $current_language = $module->get_current_language();
        $module->switch_language('');
        // Insert the post into the database
        $page_id = wp_insert_post($page);
        $module->switch_language($current_language);

        $controls->data['page'] = $page_id;
        $module->merge_options($controls->data);
        
        $controls->messages = 'A new page has been created';
    }
}

$license_data = $module->get_license_data(true);

if (is_wp_error($license_data)) {
        $controls->errors .= esc_html('[' . $license_data->get_error_code()) . '] - ' . esc_html($license_data->get_error_message());
} else {
    if ($license_data !== false) {
        if ($license_data->expire == 0) {
            $controls->messages = 'Your FREE license is valid';
        } elseif ($license_data->expire >= time()) {
            $controls->messages = 'Your license is valid and expires on ' . esc_html(date('Y-m-d', $license_data->expire));
        } else {
            $controls->errors = 'Your license is expired on ' . esc_html(date('Y-m-d', $license_data->expire));
        }
    }
}

$return_path = $module->options['return_path'];

if (!empty($return_path)) {
    list($return_path_local, $return_path_domain) = explode('@', $return_path);

    $sender = $module->options['sender_email'];
    list($sender_local, $sender_domain) = explode('@', $sender);

    if ($sender_domain != $return_path_domain) {
        $controls->warnings[] = __('Your Return Path domain is different from your Sender domain. Providers may require them to match.', 'newsletter');
    }
}

?>

<?php include NEWSLETTER_INCLUDES_DIR . '/codemirror.php'; ?>
<style>
    .CodeMirror {
        border: 1px solid #ddd;
    }
</style>

<script>
    jQuery(function () {
        var editor = CodeMirror.fromTextArea(document.getElementById("options-css"), {
            lineNumbers: true,
            mode: 'css',
            extraKeys: {"Ctrl-Space": "autocomplete"}
        });
    });
</script>

<div class="wrap" id="tnp-wrap">

    <?php include NEWSLETTER_DIR . '/tnp-header.php'; ?>

    <div id="tnp-heading">

        <h2><?php _e('General Settings', 'newsletter') ?></h2>

    </div>
    <div id="tnp-body" class="tnp-main-main">
        

        <form method="post" action="">
            <?php $controls->init(); ?>

            <div id="tabs">

                <ul>
                    <li><a href="#tabs-basic"><?php _e('Basic Settings', 'newsletter') ?></a></li>
                    <li><a href="#tabs-speed"><?php _e('Delivery Speed', 'newsletter') ?></a></li>
                    <li><a href="#tabs-advanced"><?php _e('Advanced Settings', 'newsletter') ?></a></li>
                </ul>

                <div id="tabs-basic">

                    <p>
                        <?php $controls->panel_help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-configuration') ?>
                    </p>


                    <table class="form-table">

                        <tr>
                            <th><?php _e('Sender email address', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text_email('sender_email', 40); ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-configuration#sender') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Sender name', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('sender_name', 40); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Return path', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text_email('return_path', 40); ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-configuration#return-path') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Reply to', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text_email('reply_to', 40); ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-configuration#reply-to') ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Dedicated page', 'newsletter') ?></th>
                            <td>
                                <?php $controls->page('page', __('Unstyled page', 'newsletter'), '', true); ?>
                                <?php
                                if (empty($controls->data['page'])) {
                                    $controls->button('create', __('Create the page', 'newsletter'));
                                }
                                ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/documentation/newsletter-configuration#dedicated-page') ?>

                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('License key', 'newsletter') ?></th>
                            <td>
                                <?php if (defined('NEWSLETTER_LICENSE_KEY')) { ?>
                                    <?php _e('A license key is set', 'newsletter') ?>
                                <?php } else { ?>
                                    <?php $controls->text('contract_key', 40); ?>
                                    <p class="description">
                                        <?php printf(__('Find it in <a href="%s" target="_blank">your account</a> page', 'newsletter'), "https://www.thenewsletterplugin.com/account") ?>
                                    </p>
                                <?php } ?>
                            </td>
                        </tr>

                    </table>
                </div>

                <div id="tabs-speed">

                    <p>
                        <?php $controls->panel_help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-delivery-engine') ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Max emails per hour', 'newsletter') ?></th>
                            <td>
                                <?php $controls->text('scheduler_max', 5); ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-delivery-engine') ?>
                            </td>
                        </tr>
                    </table>
                    
                    <?php do_action('newsletter_panel_main_speed', $controls) ?>
                </div>


                <div id="tabs-advanced">

                    <p>
                        <?php $controls->panel_help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-configuration#advanced') ?>
                    </p>

                    <table class="form-table">
                        <tr>
                            <th><?php _e('Disable standard styles', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('css_disabled'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Custom styles', 'newsletter') ?></th>
                            <td>
                                <?php if (apply_filters('newsletter_enqueue_style', true) === false) { ?>
                                    <p><strong>Warning: Newsletter styles and custom styles are disable by your theme or a plugin.</strong></p>
                                <?php } ?>
                                <?php $controls->textarea('css'); ?>
                            </td>
                        </tr>
                        <tr>
                            <th><?php _e('Allowed roles', 'newsletter') ?></th>
                            <td>
                                <?php 
                                $wp_roles = get_editable_roles();
                                $roles = array();
                                foreach ($wp_roles as $key=>$wp_role) {
                                    if ($key == 'administrator') continue;
                                    if ($key == 'subscriber') continue;
                                    $roles[$key] = $wp_role['name'];
                                }
                                $controls->checkboxes('roles', $roles); 
                                ?>
                                
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <?php _e('Log level', 'newsletter') ?>
                            </th>
                            <td>
                                <?php $controls->log_level('log_level'); ?>
                            </td>
                        </tr>
                        <!--
                        <tr>
                            <th>
                                <?php _e('Disable the scheduler notice', 'newsletter') ?>
                            </th>
                            <td>
                                <?php $controls->yesno('disable_cron_notice'); ?>
                            </td>
                        </tr>
                        -->
                        <tr>
                            <th><?php _e('IP addresses', 'newsletter')?></th>
                            <td>
                                <?php $controls->select('ip', array(''=>__('Store', 'newsletter'), 'anonymize'=> __('Anonymize', 'newsletter'), 'skip'=>__('Do not store', 'newsletter'))); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Newsletters tracking default', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('track'); ?>
                                <p class="description">It can be changed on each newsletter.</p>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Debug mode', 'newsletter') ?></th>
                            <td>
                                <?php $controls->yesno('debug', 40); ?>
                            </td>
                        </tr>

                        <tr>
                            <th><?php _e('Email encoding', 'newsletter') ?></th>
                            <td>
                                <?php $controls->select('content_transfer_encoding', array('' => 'Default', '8bit' => '8 bit', 'base64' => 'Base 64', 'binary' => 'Binary', 'quoted-printable' => 'Quoted printable', '7bit' => '7 bit')); ?>
                                <?php $controls->help('https://www.thenewsletterplugin.com/plugins/newsletter/newsletter-configuration#encoding') ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th>
                                <?php _e('Execute shortcodes on newsletters', 'newsletter') ?>
                                <?php $controls->field_help("https://www.thenewsletterplugin.com/documentation/newsletter-configuration#shortcodes")?>
                            </th>
                            <td>
                                <?php $controls->yesno('do_shortcodes', 40); ?>
                                <?php $controls->field_help("https://www.thenewsletterplugin.com/documentation/newsletter-configuration#shortcodes")?>
                            </td>
                        </tr>
                    </table>

                </div>


            </div> <!-- tabs -->

            <p>
                <?php $controls->button_save(); ?>
            </p>

        </form>

    </div>

    <?php include NEWSLETTER_DIR . '/tnp-footer.php'; ?>

</div>

