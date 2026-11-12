<?php

defined('ABSPATH') or exit('Restricted access!');

if (! class_exists('SkuadsElementorFormDiscovery')) {
    class SkuadsElementorFormDiscovery
    {
        public static function all()
        {
            $postTypes = get_post_types(['show_ui' => true], 'names');
            $postTypes[] = 'elementor_library';

            $postIds = get_posts([
                'fields' => 'ids',
                'meta_key' => '_elementor_data',
                'no_found_rows' => true,
                'order' => 'ASC',
                'orderby' => 'ID',
                'post_status' => ['publish', 'private'],
                'post_type' => array_values(array_unique($postTypes)),
                'posts_per_page' => -1,
                'suppress_filters' => false,
            ]);

            $forms = [];

            foreach ($postIds as $postId) {
                $elements = json_decode((string) get_post_meta($postId, '_elementor_data', true), true);

                if (! is_array($elements)) {
                    continue;
                }

                foreach (self::findInElements($elements) as $form) {
                    $forms[$form['id']] = $form;
                }
            }

            return array_values($forms);
        }

        public static function findInElements(array $elements)
        {
            $forms = [];
            self::collectForms($elements, $forms);

            return array_values($forms);
        }

        private static function collectForms(array $elements, array &$forms)
        {
            foreach ($elements as $element) {
                if (! is_array($element)) {
                    continue;
                }

                if (($element['widgetType'] ?? null) === 'form' && ! empty($element['id'])) {
                    $formId = (string) $element['id'];
                    $formName = trim((string) ($element['settings']['form_name'] ?? ''));

                    $forms[$formId] = [
                        'id' => $formId,
                        'name' => $formName !== '' ? $formName : 'Form '.$formId,
                    ];
                }

                if (! empty($element['elements']) && is_array($element['elements'])) {
                    self::collectForms($element['elements'], $forms);
                }
            }
        }
    }
}
