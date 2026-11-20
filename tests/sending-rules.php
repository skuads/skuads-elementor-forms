<?php

// Submissions are sent to Skuads only once the plugin is enabled and
// configured, and only for the forms the administrator selected.

namespace ElementorPro\Modules\Forms\Submissions\Database {
    class Query
    {
        public static $submissions = [];

        public static function get_instance()
        {
            return new self();
        }

        public function get_submissions($arguments)
        {
            return ['data' => self::$submissions];
        }
    }
}

namespace {
    define('ABSPATH', __DIR__);
    define('MINUTE_IN_SECONDS', 60);

    $options = ['active_plugins' => ['elementor-pro/elementor-pro.php']];
    $sentRequests = [];

    function get_option($name, $default = false)
    {
        global $options;

        return array_key_exists($name, $options) ? $options[$name] : $default;
    }

    function update_option($name, $value)
    {
        global $options;
        $options[$name] = $value;
    }

    function apply_filters($hook, $value)
    {
        return $value;
    }

    function add_action()
    {
    }

    function register_uninstall_hook()
    {
    }

    function register_activation_hook()
    {
    }

    function plugin_dir_path($file)
    {
        return dirname($file).'/';
    }

    function plugin_dir_url($file)
    {
        return 'https://example.test/';
    }

    function current_user_can()
    {
        return true;
    }

    function check_admin_referer()
    {
        return true;
    }

    function wp_json_encode($data)
    {
        return json_encode($data);
    }

    function is_wp_error()
    {
        return false;
    }

    function set_transient()
    {
    }

    function wp_remote_post($url, $arguments)
    {
        global $sentRequests;
        $sentRequests[] = json_decode($arguments['body'], true);

        return ['response' => ['code' => 200, 'message' => 'OK']];
    }

    // The plugin reads its endpoints from the generated includes/settings.php.
    $pluginDir = sys_get_temp_dir().'/skuads-sending-rules-'.getmypid();
    exec('rm -rf '.escapeshellarg($pluginDir));
    exec('cp -R '.escapeshellarg(__DIR__.'/../skuads-elementor-forms').' '.escapeshellarg($pluginDir));
    copy(__DIR__.'/../settings/dev.php', $pluginDir.'/includes/settings.php');
    register_shutdown_function(function () use ($pluginDir) {
        exec('rm -rf '.escapeshellarg($pluginDir));
    });

    require $pluginDir.'/skuads-elementor-forms.php';

    class FakeRecord
    {
        private $formId;

        public function __construct($formId)
        {
            $this->formId = $formId;
        }

        public function get_form_settings($key)
        {
            return $key === 'id' ? $this->formId : 'Contact';
        }

        public function get($key)
        {
            return ['email' => ['value' => 'visitor@example.test']];
        }
    }

    function databaseRecord($formId, $id)
    {
        return [
            'id' => $id,
            'created_at_gmt' => '2026-11-20 10:00:00',
            'form' => ['element_id' => $formId, 'name' => 'Contact', 'fields' => [['id' => 'email']]],
            'values' => [['key' => 'email', 'value' => 'visitor@example.test']],
        ];
    }

    function check($condition, $message)
    {
        if (! $condition) {
            fwrite(STDERR, $message.PHP_EOL);
            exit(1);
        }
    }

    function configure(array $values)
    {
        global $options, $sentRequests;
        $options = $values + ['active_plugins' => ['elementor-pro/elementor-pro.php']];
        $sentRequests = [];
    }

    $plugin = new SkuadsForms();
    $configured = [
        SkuadsFormsHelper::ENABLED => 1,
        SkuadsFormsHelper::SITE_KEY => '6f1c2b3a-4d5e-4f60-8a7b-9c0d1e2f3a4b',
        SkuadsFormsHelper::API_TOKEN => str_repeat('a', 64),
        SkuadsFormsHelper::FORMS => ['abc123'],
    ];

    configure([]);
    $plugin->post_webhook(new FakeRecord('abc123'));
    check($sentRequests === [], 'Nothing may be sent before the plugin is enabled and configured.');

    configure([SkuadsFormsHelper::ENABLED => 1, SkuadsFormsHelper::FORMS => ['abc123']]);
    $plugin->post_webhook(new FakeRecord('abc123'));
    check($sentRequests === [], 'Nothing may be sent without a valid Site UUID and API key.');

    configure([SkuadsFormsHelper::ENABLED => 0] + $configured);
    $plugin->post_webhook(new FakeRecord('abc123'));
    check($sentRequests === [], 'Nothing may be sent while the plugin is disabled.');

    configure($configured);
    $plugin->post_webhook(new FakeRecord('other99'));
    check($sentRequests === [], 'Submissions of unselected forms may not be sent.');

    configure($configured);
    $plugin->post_webhook(new FakeRecord('abc123'));
    check(count($sentRequests) === 1, 'A submission of a selected form must be sent.');

    configure([SkuadsFormsHelper::FORMS => ['42_abc123']] + $configured);
    $plugin->post_webhook(new FakeRecord('abc123'));
    check(count($sentRequests) === 1, 'Forms saved as "<post ID>_<element ID>" must match their element ID.');

    \ElementorPro\Modules\Forms\Submissions\Database\Query::$submissions = [
        databaseRecord('other99', 3),
        databaseRecord('abc123', 2),
        databaseRecord('other99', 1),
    ];

    configure([SkuadsFormsHelper::FORMS => []] + $configured);
    $plugin->sync_manually();
    check($sentRequests === [], 'The manual sync may not send anything when no form is selected.');

    configure($configured);
    $plugin->sync_manually();
    check(count($sentRequests) === 1, 'The manual sync must send one request.');
    check(
        array_column($sentRequests[0]['submissions'], 'external_submission_id') === ['2'],
        'The manual sync must send only submissions of the selected forms.'
    );

    echo "Sending rules tests passed.\n";
}
