<?php
/*
Plugin Name: Skuads Elementor Forms
Description: Skuads Elementor Forms
Author: Skuads
Version: 0.0.3
Author URI: https://www.skuads.com
License: GPLv2
Plugin URI: https://www.skuads.com/
*/

use ElementorPro\Modules\Forms\Submissions\Database\Query as ElementorQuery;
use ElementorPro\Modules\Forms\Submissions\Database\Repositories\Form_Snapshot_Repository;
use SkuadsFormsHelper as GlobalSkuadsFormsHelper;

defined('ABSPATH') or exit('Restricted access!');

if (! class_exists('SkuadsFormsHelper')) {
    class SkuadsFormsHelper
    {
        const SLUG = 'skuads-elementor-forms';

        const DO_MANUAL_SYNC = self::SLUG.'-domanualsync';

        const OPTIONS_GROUP = self::SLUG.'-options';

        const SAVED = self::SLUG.'-saved';

        const SITE_KEY = self::SLUG.'-site-key';

        const API_TOKEN = self::SLUG.'-api-token';

        const FORMS = self::SLUG.'-forms';

        const CONNECTED = self::SLUG.'-connected';

        const ENABLED = self::SLUG.'-enabled';

        const AUTOSYNC = self::SLUG.'-autosync';

        const options = [
            self::SAVED,
            self::ENABLED,
            self::SITE_KEY,
            self::API_TOKEN,
            self::CONNECTED,
            self::AUTOSYNC,
            self::FORMS,
        ];

        public static function elementorEnabled()
        {
            return in_array(
                'elementor-pro/elementor-pro.php',
                apply_filters('active_plugins', get_option('active_plugins'))
            );
        }

        public static function elementorForms()
        {
            $forms = [];

            foreach (SkuadsElementorFormDiscovery::all() as $form) {
                $forms[$form['id']] = $form;
            }

            foreach (Form_Snapshot_Repository::instance()->all() as $form) {
                $snapshot = $form->jsonSerialize();

                if (! isset($forms[$snapshot['id']])) {
                    $forms[$snapshot['id']] = $snapshot;
                }
            }

            return array_values($forms);
        }

        public static function elementorSubmissions()
        {
            return ElementorQuery::get_instance();
        }

        public static function log($log)
        {
            if (true === WP_DEBUG) {
                if (is_array($log) || is_object($log)) {
                    error_log(print_r($log, true));
                } else {
                    error_log($log);
                }
            }
        }

        public static function feedbackMessage($section)
        {

            $feedbackAvailable = get_transient(self::SLUG."-feedback_message_{$section}");
            if (empty($_GET['page']) || $_GET['page'] !== self::SLUG || empty($feedbackAvailable)) {
                return '';
            }

            $data = json_decode($feedbackAvailable, true);

            delete_transient(self::SLUG."-feedback_message_{$section}");

            $group = empty($data['group']) ? null : $data['group'];
            $code = empty($data['code']) ? null : $data['code'];

            $messages = [
                'connect' => [
                    '200' => [
                        'message' => 'Connection is working properly',
                        'type' => 'success',
                    ],
                    '422' => [
                        'message' => 'Please supply a valid Site UUID and API Key',
                        'type' => 'error',
                    ],
                    '500' => [
                        'message' => 'There was an error submitting the orders',
                        'type' => 'error',
                    ],
                ],
                'manualsync' => [
                    '200' => [
                        'message' => 'Forms synced successfully',
                        'type' => 'success',
                    ],
                    '422' => [
                        'message' => 'Enabled Skuads needs to sync orders',
                        'type' => 'error',
                    ],
                    '424' => [
                        'message' => 'Please supply a valid Site UUID and API Key',
                        'type' => 'error',
                    ],
                    '500' => [
                        'message' => 'Error connecting elementor forms',
                        'type' => 'error',
                    ],
                ],
            ];

            if (empty($messages[$group][$code])) {
                return '';
            }

            return '
                    <div class="notice is-dismissible notice-'.$messages[$group][$code]['type'].'">
                        <p><b>'.$messages[$group][$code]['message'].'</b></p>
                    </div>
                ';
        }

        public static function asset($path = '')
        {
            return plugin_dir_url(__FILE__).$path;
        }

        public static function filePath($path = '')
        {
            return plugin_dir_path(__FILE__).$path;
        }
    }
}

require_once SkuadsFormsHelper::filePath('includes/class-skuads-elementor-form-discovery.php');
require_once SkuadsFormsHelper::filePath('includes/class-skuads-elementor-submission-identity.php');
require_once SkuadsFormsHelper::filePath('includes/settings.php');

if (! class_exists('SkuadsForms')) {
    class SkuadsForms
    {
        public function __construct()
        {
            add_action('admin_init', [$this, 'init_settings']);
            add_action('admin_menu', [$this, 'init_menu']);

            add_action('add_option_'.SkuadsFormsHelper::SITE_KEY, [$this, 'connect'], 999, 0);
            add_action('add_option_'.SkuadsFormsHelper::API_TOKEN, [$this, 'connect'], 999, 0);
            add_action('add_option_'.SkuadsFormsHelper::FORMS, [$this, 'connect'], 999, 0);
            add_action('update_option_'.SkuadsFormsHelper::SITE_KEY, [$this, 'connect'], 999, 0);
            add_action('update_option_'.SkuadsFormsHelper::API_TOKEN, [$this, 'connect'], 999, 0);
            add_action('update_option_'.SkuadsFormsHelper::FORMS, [$this, 'connect'], 999, 0);

            if (! SkuadsFormsHelper::elementorEnabled()) {
                return;
            }

            add_action('elementor_pro/forms/new_record', [$this, 'post_webhook']);
            add_action('admin_action_'.SkuadsFormsHelper::DO_MANUAL_SYNC, [$this, 'sync_manually']);
        }

        public function init_settings()
        {
            register_setting(SkuadsFormsHelper::OPTIONS_GROUP, SkuadsFormsHelper::SAVED, 'empty');
            register_setting(SkuadsFormsHelper::OPTIONS_GROUP, SkuadsFormsHelper::SITE_KEY, [$this, 'sanitise_site_key']);
            register_setting(SkuadsFormsHelper::OPTIONS_GROUP, SkuadsFormsHelper::API_TOKEN, [$this, 'sanitise_api_key']);
            register_setting(SkuadsFormsHelper::OPTIONS_GROUP, SkuadsFormsHelper::FORMS, 'empty');
            register_setting(SkuadsFormsHelper::OPTIONS_GROUP, SkuadsFormsHelper::CONNECTED, 'intval');
            register_setting(SkuadsFormsHelper::OPTIONS_GROUP, SkuadsFormsHelper::ENABLED, 'intval');
            register_setting(SkuadsFormsHelper::OPTIONS_GROUP, SkuadsFormsHelper::AUTOSYNC, 'intval');
        }

        public function init_menu()
        {
            add_menu_page(
                'Skuads Forms',
                'Skuads Forms',
                'manage_options',
                SkuadsFormsHelper::SLUG,
                [$this, 'options_form'],
                SkuadsFormsHelper::asset('assets/icon.svg'),
            );
        }

        public function configuration_notice()
        {
            if (get_option(SkuadsFormsHelper::CONNECTED) && $_GET['page'] !== SkuadsFormsHelper::SLUG) {
                return;
            }

            echo implode("\n", [
                '<div class="notice notice-error is-dismissible">',
                '<p>You need to complete your Skuads Forms set-up <a href="admin.php?page='.SkuadsFormsHelper::SLUG.'">Complete set-up</a></p>',
                '</div>',
            ]);
        }

        public function post_webhook($record, $handler = null)
        {
            $enabled = get_option(SkuadsFormsHelper::ENABLED, true);

            if (! (SkuadsFormsHelper::elementorEnabled() && $enabled)) {
                return;
            }

            $siteKey = get_option(SkuadsFormsHelper::SITE_KEY);
            $apiToken = get_option(SkuadsFormsHelper::API_TOKEN);

            $data = [
                'platform' => 'elementor',
                'action' => 'auto-sync',
                'site_key' => $siteKey,
                'submissions' => [],
            ];

            $data['submissions'][] = $this->prepare_record_data($record);

            $response = $this->post(SKUADS_FORMS_ENDPOINT_WEBHOOK, $data, $apiToken);

            return $response['successful'];
        }

        public function sync_manually()
        {
            $enabled = get_option(SkuadsFormsHelper::ENABLED, true);

            if (! (SkuadsFormsHelper::elementorEnabled() && $enabled)) {
                return;
            }

            $siteKey = get_option(SkuadsFormsHelper::SITE_KEY);
            $apiToken = get_option(SkuadsFormsHelper::API_TOKEN);

            $submissions = SkuadsFormsHelper::elementorSubmissions()->get_submissions([
                'per_page' => 30,
                'with_meta' => true,
                'with_form_fields' => true,
            ]);

            $data = [
                'platform' => 'elementor',
                'action' => 'manual-sync',
                'site_key' => $siteKey,
                'submissions' => [],
            ];

            foreach (($submissions['data'] ?? []) as $record) {
                $data['submissions'][] = $this->prepare_record_data($record);
            }

            $response = $this->post(SKUADS_FORMS_ENDPOINT_WEBHOOK, $data, $apiToken);

            return $response['successful'];
        }

        public function connect()
        {
            $siteKey = get_option(SkuadsFormsHelper::SITE_KEY);
            $apiToken = get_option(SkuadsFormsHelper::API_TOKEN);
            $enabled = get_option(SkuadsFormsHelper::ENABLED, true);
            $forms = get_option(SkuadsFormsHelper::FORMS, []);

            // it has been disabled.
            if (! $enabled) {
                return;
            }

            if (empty($siteKey) || empty($apiToken)) {
                update_option(SkuadsFormsHelper::CONNECTED, 0);

                return;
            }

            if (! is_array($forms)) {
                $forms = [];
            }

            $selectedForms = [];

            foreach (GlobalSkuadsFormsHelper::elementorForms() as $form) {
                if (in_array($form['id'], $forms)) {
                    $selectedForms[] = [
                        'form_id' => $form['id'],
                        'form_name' => $form['name'],
                    ];
                }
            }

            $data = [
                'platform' => 'elementor',
                'action' => 'connect',
                'site_key' => $siteKey,
                'forms' => $selectedForms,
            ];

            $response = $this->post(SKUADS_FORMS_ENDPOINT_AUTH, $data, $apiToken);

            if ($response['successful']) {
                update_option(SkuadsFormsHelper::CONNECTED, 1);
            } else {
                update_option(SkuadsFormsHelper::CONNECTED, 0);
            }

            $this->add_feedback_message('connect', $response['code']);

            return true;
        }

        public function sanitise_site_key($siteKey)
        {
            $siteKey = trim($siteKey);

            if (! $this->is_valid_site_key($siteKey) || empty($siteKey)) {
                add_settings_error(
                    SkuadsFormsHelper::SITE_KEY,
                    esc_attr('settings_updated'),
                    'Please make sure you provide the correct site key',
                    'error'
                );
            }

            return $siteKey;
        }

        public function sanitise_api_key($apiToken)
        {
            $apiToken = trim($apiToken);

            if (strlen($apiToken) !== 64 && ! empty($apiToken)) {
                add_settings_error(
                    SkuadsFormsHelper::API_TOKEN,
                    esc_attr('settings_updated'),
                    'Please make sure you provide the correct API key',
                    'error'
                );
            }

            return $apiToken;
        }

        public function options_form()
        {
            require_once SkuadsFormsHelper::filePath('includes/options.php');
        }

        public function add_feedback_message($group, $code)
        {
            $data = json_encode([
                'group' => $group,
                'code' => $code,
            ]);

            set_transient(SkuadsFormsHelper::SLUG."_feedback_message_{$group}", $data, 5 * MINUTE_IN_SECONDS);
        }

        private function log($message, $data = [])
        {
            $data['debug'] = [
                'site_url' => get_site_url(),
                'php_version' => PHP_VERSION,
                'wordpress_version' => get_bloginfo('version'),
            ];

            SkuadsFormsHelper::log($message.' '.json_encode($data));
        }

        private function prepare_record_data($record)
        {
            // for handling when we fetch directly from database
            if (is_array($record)) {
                $formId = $record['form']['element_id'];
                $formName = $record['form']['name'];
                $fieldNames = $record['form']['fields'];
                $values = $record['values'];
                $submittedAt = SkuadsElementorSubmissionIdentity::submittedAt(
                    $record['created_at_gmt'] ?? $record['created_at'] ?? $record['date'] ?? null
                );

                $fields = [];

                foreach ($fieldNames as $field) {
                    $fieldId = $field['id'];
                    $matchingValue = array_filter($values, function ($value) use ($fieldId) {
                        return $value['key'] === $fieldId;
                    });
                    $fieldValue = reset($matchingValue);

                    $fields[$fieldId] = $fieldValue['value'] ?? null;
                }

                return [
                    'form_id' => $formId,
                    'form_name' => $formName,
                    'fields' => $fields,
                    'external_submission_id' => isset($record['id']) ? (string) $record['id'] : null,
                    'submitted_at' => $submittedAt,
                ];
            }

            // handling from the webhook
            $formId = $record->get_form_settings('id');
            $formName = $record->get_form_settings('form_name');
            $rawFields = $record->get('fields');
            $fields = [];
            foreach ($rawFields as $id => $field) {
                $fields[$id] = $field['value'];
            }
            $submittedAt = SkuadsElementorSubmissionIdentity::submittedAt();

            return [
                'form_id' => $formId,
                'form_name' => $formName,
                'fields' => $fields,
                'external_submission_id' => null,
                'submitted_at' => $submittedAt,
            ];
        }

        private function is_valid_site_key($siteKey)
        {
            $UUIDv4 = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/Di';

            return preg_match($UUIDv4, $siteKey);
        }

        private function post($url, $data, $apiToken)
        {
            $response = wp_remote_post($url, [
                'body' => wp_json_encode($data),
                'headers' => [
                    'Authorization' => "Bearer $apiToken",
                    'Content-Type' => 'application/json',
                ],
            ]);

            if (is_wp_error($response)) {
                $this->log("[SkuadsForms] $url", [
                    'data' => $data,
                    'errors' => $response->get_error_messages(),
                ]);
                $output = [
                    'code' => 500,
                    'successful' => false,
                    'message' => 'Error completing action',
                ];
            } else {
                $output = $response['response'];
                $output['successful'] = $output['code'] == 200;
            }

            return $output;
        }
    }

    $skuadsForms = new SkuadsForms();
}

register_uninstall_hook(__FILE__, 'skuads_forms_uninstall_hook');
register_activation_hook(__FILE__, 'skuads_forms_activation_hook');

function skuads_forms_uninstall_hook()
{
    if (! current_user_can('activate_plugins')) {
        return;
    }

    foreach (SkuadsFormsHelper::options as $option) {
        delete_option($option);
    }
}

function skuads_forms_activation_hook()
{
    if (! current_user_can('activate_plugins')) {
        return;
    }

    update_option(SkuadsFormsHelper::SITE_KEY, get_option(SkuadsFormsHelper::SITE_KEY, ''));
    update_option(SkuadsFormsHelper::API_TOKEN, get_option(SkuadsFormsHelper::API_TOKEN, ''));
    update_option(SkuadsFormsHelper::CONNECTED, get_option(SkuadsFormsHelper::CONNECTED, 0));
    update_option(SkuadsFormsHelper::ENABLED, get_option(SkuadsFormsHelper::ENABLED, 1));
    update_option(SkuadsFormsHelper::AUTOSYNC, get_option(SkuadsFormsHelper::AUTOSYNC, 1));
    update_option(SkuadsFormsHelper::SAVED, get_option(SkuadsFormsHelper::SAVED, time()));
    update_option(SkuadsFormsHelper::FORMS, get_option(SkuadsFormsHelper::FORMS, '[]'));
}
