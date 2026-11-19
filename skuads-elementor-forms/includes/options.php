<?php
defined('ABSPATH') or exit('Restricted access!');

$skuads_forms_enabled = (bool) get_option(SkuadsFormsHelper::ENABLED);
$skuads_forms_saved = get_option(SkuadsFormsHelper::SAVED, time());
?>
<div class="wrap">
    <h1>Connect Elementor Forms to the Skuads application</h1>

    <hr />

    <h2>Settings</h2>

    <?php if (SkuadsFormsHelper::elementorEnabled()) { ?>
        <form id="<?php echo esc_attr(SkuadsFormsHelper::SLUG); ?>-form-options" name="dofollow" action="options.php" method="post">
            <?php settings_errors(); ?>
            <?php echo wp_kses_post(SkuadsFormsHelper::feedbackMessage('connect')); ?>

            <?php settings_fields(SkuadsFormsHelper::OPTIONS_GROUP); ?>
            <?php do_settings_sections(SkuadsFormsHelper::OPTIONS_GROUP); ?>

            <input type="hidden" name="<?php echo esc_attr(SkuadsFormsHelper::SAVED); ?>" id="<?php echo esc_attr(SkuadsFormsHelper::SAVED); ?>" data-initial="<?php echo esc_attr($skuads_forms_saved); ?>" value="<?php echo esc_attr($skuads_forms_saved); ?>" />

            <?php if (get_option(SkuadsFormsHelper::CONNECTED)) { ?>
                <div class="notice notice-success">
                    <p><strong>Your Elementor forms are connected to the Skuads application.</strong></p>
                </div>
            <?php } ?>


            <table class="form-table">
                <tr>
                    <th>
                        <label for="<?php echo esc_attr(SkuadsFormsHelper::ENABLED); ?>">Enable Skuads Forms</label>
                    </th>
                    <td>
                        <select id="<?php echo esc_attr(SkuadsFormsHelper::ENABLED); ?>" name="<?php echo esc_attr(SkuadsFormsHelper::ENABLED); ?>" data-initial="<?php echo esc_attr($skuads_forms_enabled ? '1' : '0'); ?>" style="width: 10em;">
                            <option value="1" <?php selected($skuads_forms_enabled, true); ?>>Yes</option>
                            <option value="0" <?php selected($skuads_forms_enabled, false); ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr class="depends-on-enabled <?php echo esc_attr($skuads_forms_enabled ? '' : 'hidden'); ?>">
                    <th>
                        <label for="<?php echo esc_attr(SkuadsFormsHelper::SITE_KEY); ?>">Site UUID</label>
                    </th>
                    <td>
                        <input id="<?php echo esc_attr(SkuadsFormsHelper::SITE_KEY); ?>" class="regular-text code" name="<?php echo esc_attr(SkuadsFormsHelper::SITE_KEY); ?>" type="text" value="<?php echo esc_attr(get_option(SkuadsFormsHelper::SITE_KEY)); ?>" />

                        <p class="description">
                            Go to your the <a href="https://app.skuads.com/api-settings" target="_blank" rel="noopener">API Settings page</a> for your site to find your Site UUID.
                        </p>
                    </td>
                </tr>
                <tr class="depends-on-enabled <?php echo esc_attr($skuads_forms_enabled ? '' : 'hidden'); ?>">
                    <th>
                        <label for="<?php echo esc_attr(SkuadsFormsHelper::API_TOKEN); ?>">API Key</label>
                    </th>
                    <td>
                        <input id="<?php echo esc_attr(SkuadsFormsHelper::API_TOKEN); ?>" class="regular-text code" name="<?php echo esc_attr(SkuadsFormsHelper::API_TOKEN); ?>" type="text" value="<?php echo esc_attr(get_option(SkuadsFormsHelper::API_TOKEN)); ?>" />

                        <p class="description">
                            Go to your the <a href="https://app.skuads.com/api-settings" target="_blank" rel="noopener">API Settings page</a> for your site to find your Site UUID.
                        </p>
                    </td>
                </tr>
                <tr class="depends-on-enabled <?php echo esc_attr($skuads_forms_enabled ? '' : 'hidden'); ?>">
                    <th>
                        <label for="<?php echo esc_attr(SkuadsFormsHelper::FORMS); ?>">The Elementor forms to capture</label>
                    </th>
                    <td>
                        <table>
                            <?php
                                $skuads_forms_saved_forms = get_option(SkuadsFormsHelper::FORMS, []);
        if (! is_array($skuads_forms_saved_forms)) {
            $skuads_forms_saved_forms = [];
        }
        foreach (SkuadsFormsHelper::elementorForms() as $skuads_forms_form) {
            ?>
                            <tr>
                                <td style="padding: 0px 10px 15px 0px;">
                                    <label>
                                        <input
                                            type="checkbox"
                                            name="<?php echo esc_attr(SkuadsFormsHelper::FORMS); ?>[]"
                                            value="<?php echo esc_attr($skuads_forms_form['id']); ?>"
                                            <?php checked(in_array($skuads_forms_form['id'], $skuads_forms_saved_forms)); ?>
                                        />
                                        <?php echo esc_html($skuads_forms_form['name']); ?>
                                    </label>
                                </td>
                            </tr>
                            <?php } ?>
                        </table>
                    </td>
                </tr>

                <tr>
                    <th></th>
                    <td>
                        <p class="depends-on-enabled submit <?php echo esc_attr($skuads_forms_enabled ? '' : 'hidden'); ?>">
                            <input type="submit" name="submit" id="<?php echo esc_attr(SkuadsFormsHelper::SLUG); ?>-setup" class="button button-primary" value="Set up connection">
                        </p>
                        <p class="depends-on-enabled submit <?php echo esc_attr($skuads_forms_enabled ? 'hidden' : ''); ?>">
                            <input type="submit" name="submit" id="<?php echo esc_attr(SkuadsFormsHelper::SLUG); ?>-submit" class="button button-primary" value="Save settings">
                        </p>
                    </td>
                </tr>
            </table>
        </form>

        <div class="depends-on-enabled <?php echo esc_attr($skuads_forms_enabled ? '' : 'hidden'); ?>">
            <hr>
            <h2>Sync past submissions</h2>
            <?php echo wp_kses_post(SkuadsFormsHelper::feedbackMessage('manualsync')); ?>
            <form action="<?php echo esc_url(admin_url('admin.php')); ?>" method="post" id="<?php echo esc_attr(SkuadsFormsHelper::SLUG); ?>-form-manualsync">
                <input type="hidden" name="action" value="<?php echo esc_attr(SkuadsFormsHelper::DO_MANUAL_SYNC); ?>" />
                <?php wp_nonce_field(SkuadsFormsHelper::DO_MANUAL_SYNC); ?>
                <table class="form-table">
                    <tr>
                        <th></th>
                        <td>
                            <?php submit_button('Manually sync entries'); ?>
                            <p class="description">
                                This will send the last 30 entries to the Skuads application.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="<?php echo esc_attr(SkuadsFormsHelper::SLUG); ?>-feedback"></td>
                    </tr>
                </table>
            </form>
        </div>
    <?php } else { ?>
        <div class="notice notice-warning">
            <p><strong>This plugin will only work with ElementorPro Forms.</strong></p>
        </div>
    <?php } ?>
</div>
