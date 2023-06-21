<?php
defined('ABSPATH') or exit('Restricted access!');
?>
<div class="wrap">
    <h1>Connect Elementor Forms to Skuads </h1>

    <hr />

    <h2>Settings</h2>

    <?php if (SkuadsFormsHelper::elementorEnabled()) { ?>
        <form id="<?= SkuadsFormsHelper::SLUG ?>-form-options" name="dofollow" action="options.php" method="post">
            <?php settings_errors(); ?>
            <?php echo SkuadsFormsHelper::feedbackMessage('connect'); ?>

            <?php settings_fields(SkuadsFormsHelper::OPTIONS_GROUP); ?>
            <?php do_settings_sections(SkuadsFormsHelper::OPTIONS_GROUP); ?>

            <input type="hidden" name="<?= SkuadsFormsHelper::SAVED ?>" id="<?= SkuadsFormsHelper::SAVED ?>" data-initial="<?= get_option(SkuadsFormsHelper::SAVED, time()) ?>" value="<?= get_option(SkuadsFormsHelper::SAVED, time()) ?>" />

            <?php if (get_option(SkuadsFormsHelper::CONNECTED)) { ?>
                <div class="notice notice-success">
                    <p><strong>Your Elementor forms are connected to Skuads.</strong></p>
                </div>
            <?php } ?>


            <table class="form-table">
                <tr>
                    <th>
                        <label for="<?= SkuadsFormsHelper::ENABLED ?>">Enable Skuads Forms</label>
                    </th>
                    <td>
                        <select id="<?= SkuadsFormsHelper::ENABLED ?>" name="<?= SkuadsFormsHelper::ENABLED ?>" data-initial="<?= get_option(SkuadsFormsHelper::ENABLED) ?>" style="width: 10em;">
                            <option value="1" <?= get_option(SkuadsFormsHelper::ENABLED) == 1 ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= get_option(SkuadsFormsHelper::ENABLED) == 0 ? 'selected' : '' ?>>No</option>
                        </select>
                    </td>
                </tr>
                <tr class="depends-on-enabled <?= get_option(SkuadsFormsHelper::ENABLED) ? '' : 'hidden' ?>">
                    <th>
                        <label for="<?= SkuadsFormsHelper::SITE_KEY ?>">Site UUID</label>
                    </th>
                    <td>
                        <input id="<?= SkuadsFormsHelper::SITE_KEY ?>" class="regular-text code" name="<?= SkuadsFormsHelper::SITE_KEY ?>" type="text" value="<?= esc_html(get_option(SkuadsFormsHelper::SITE_KEY)); ?>" />

                        <p class="description">
                            Go to your the <a href="https://app.skuads.com/api-settings" target="_blank" rel="noopener">API Settings page</a> for your site to find your Site UUID.
                        </p>
                    </td>
                </tr>
                <tr class="depends-on-enabled <?= get_option(SkuadsFormsHelper::ENABLED) ? '' : 'hidden' ?>">
                    <th>
                        <label for="<?= SkuadsFormsHelper::API_TOKEN ?>">API Key</label>
                    </th>
                    <td>
                        <input id="<?= SkuadsFormsHelper::API_TOKEN ?>" class="regular-text code" name="<?= SkuadsFormsHelper::API_TOKEN ?>" type="text" value="<?= esc_html(get_option(SkuadsFormsHelper::API_TOKEN)); ?>" />

                        <p class="description">
                            Go to your the <a href="https://app.skuads.com/api-settings" target="_blank" rel="noopener">API Settings page</a> for your site to find your Site UUID.
                        </p>
                    </td>
                </tr>
                <tr class="depends-on-enabled <?= get_option(SkuadsFormsHelper::ENABLED) ? '' : 'hidden' ?>">
                    <th>
                        <label for="<?= SkuadsFormsHelper::FORMS ?>">The Elementor forms to capture</label>
                    </th>
                    <td>
                        <table>
                            <?php
                                $savedForms = get_option(SkuadsFormsHelper::FORMS, []);
        if (! is_array($savedForms)) {
            $savedForms = [];
        }
        foreach (SkuadsFormsHelper::elementorForms() as $form) {
            ?>
                            <tr>
                                <td style="padding: 0px 10px 15px 0px;">
                                    <label>
                                        <input 
                                            type="checkbox" 
                                            name="<?= SkuadsFormsHelper::FORMS ?>[]" 
                                            value="<?= $form['id'] ?>" 
                                            <?= in_array($form['id'], $savedForms) ? 'checked' : ''; ?>
                                        />
                                        <?= $form['name'] ?>
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
                        <p class="depends-on-enabled submit <?= get_option(SkuadsFormsHelper::ENABLED) ? '' : 'hidden' ?>">
                            <input type="submit" name="submit" id="<?= SkuadsFormsHelper::SLUG ?>-setup" class="button button-primary" value="Set up connection">
                        </p>
                        <p class="depends-on-enabled submit <?= get_option(SkuadsFormsHelper::ENABLED) ? 'hidden' : '' ?>">
                            <input type="submit" name="submit" id="<?= SkuadsFormsHelper::SLUG ?>-submit" class="button button-primary" value="Save settings">
                        </p>
                    </td>
                </tr>
            </table>
        </form>

        <div class="depends-on-enabled <?= get_option(SkuadsFormsHelper::ENABLED) ? '' : 'hidden' ?>">
            <hr>
            <h2>Sync past submissions</h2>
            <?php echo SkuadsFormsHelper::feedbackMessage('manualsync'); ?>
            <form action="<?php echo admin_url('admin.php'); ?>" method="post" id="<?= SkuadsFormsHelper::SLUG ?>-form-manualsync">
                <input type="hidden" name="action" value="<?php echo SkuadsFormsHelper::DO_MANUAL_SYNC; ?>" />
                <table class="form-table">
                    <tr>
                        <th></th>
                        <td>
                            <?php submit_button('Manually sync entries'); ?>
                            <p class="description">
                                This will send the last 30 entries to Skuads.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="<?= SkuadsFormsHelper::SLUG ?>-feedback"></td>
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
<script>
    jQuery('#<?= SkuadsFormsHelper::SLUG ?>-form-manualsync').on('submit', function(event) {
        event.preventDefault();
        var form = jQuery('#<?= SkuadsFormsHelper::SLUG ?>-form-manualsync');
        var button = form.find('input[type=submit]');
        var buttonText = button.val();
        var feedback = form.find('.<?= SkuadsFormsHelper::SLUG ?>-feedback');

        jQuery.ajax({
            type: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            beforeSend: function() {
                button.prop('disabled', true);
                button.val('Syncing ...');
                feedback.html('');
            },
            success: function(data) {
                button.prop('disabled', false);
                button.val(buttonText);
                feedback.html(`
                    <div class="notice is-dismissible inline notice-success" style="max-width:25em">
                        <p><b>Entries synced successfully</b></p>
                    </div>
                `);
            }
        });
    });

    jQuery('#<?= SkuadsFormsHelper::SLUG ?>-form-options').on('submit', function() {
        var button = jQuery('#<?= SkuadsFormsHelper::SLUG ?>-form-options').find('#<?= SkuadsFormsHelper::SLUG ?>-setup');
        button.prop('disabled', true);
        button.val('Connecting ...');
    });

    jQuery('#<?= SkuadsFormsHelper::SITE_KEY ?>,#<?= SkuadsFormsHelper::API_TOKEN ?>').on('change', function(event) {
        jQuery('#<?= SkuadsFormsHelper::SAVED ?>').val(Date.now());
    });

    jQuery('#<?= SkuadsFormsHelper::ENABLED ?>').on('change', function(event) {
        if (event.target.value) {
            jQuery('#<?= SkuadsFormsHelper::SAVED ?>').val(Date.now());
        }
        jQuery('.depends-on-enabled').toggleClass('hidden');
    });
</script>
