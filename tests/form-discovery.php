<?php

define('ABSPATH', __DIR__);

$elementorDocuments = [];
$lastGetPostsArguments = [];

function get_post_types($arguments, $output)
{
    return ['page' => 'page', 'post' => 'post'];
}

function get_posts($arguments)
{
    global $lastGetPostsArguments;

    $lastGetPostsArguments = $arguments;

    return [10, 20, 30];
}

function get_post_meta($postId, $key, $single)
{
    global $elementorDocuments;

    return $elementorDocuments[$postId] ?? '';
}

require_once __DIR__.'/../skuads-elementor-forms/includes/class-skuads-elementor-form-discovery.php';

$elements = [
    [
        'id' => 'container-one',
        'elType' => 'container',
        'elements' => [
            [
                'id' => '2dc51e2',
                'widgetType' => 'form',
                'settings' => ['form_name' => 'Contact'],
                'elements' => [],
            ],
            [
                'id' => 'nested-container',
                'elType' => 'container',
                'elements' => [
                    [
                        'id' => '81b91f8',
                        'widgetType' => 'form',
                        'settings' => ['form_name' => 'Quote'],
                        'elements' => [],
                    ],
                ],
            ],
        ],
    ],
    [
        'id' => 'not-a-form',
        'widgetType' => 'heading',
        'settings' => ['form_name' => 'Ignore me'],
        'elements' => [],
    ],
];

$forms = SkuadsElementorFormDiscovery::findInElements($elements);

if ($forms !== [
    ['id' => '2dc51e2', 'name' => 'Contact'],
    ['id' => '81b91f8', 'name' => 'Quote'],
]) {
    fwrite(STDERR, 'Expected both nested Elementor forms to be discovered.'.PHP_EOL);
    exit(1);
}

$unnamedForm = SkuadsElementorFormDiscovery::findInElements([
    ['id' => 'unnamed', 'widgetType' => 'form', 'settings' => [], 'elements' => []],
]);

if ($unnamedForm !== [['id' => 'unnamed', 'name' => 'Form unnamed']]) {
    fwrite(STDERR, 'Expected a stable fallback name for an unnamed form.'.PHP_EOL);
    exit(1);
}

$elementorDocuments = [
    10 => json_encode([$elements[0]['elements'][0]]),
    20 => json_encode([$elements[0]['elements'][1]]),
    30 => '{invalid JSON',
];

$forms = SkuadsElementorFormDiscovery::all();

if ($forms !== [
    ['id' => '2dc51e2', 'name' => 'Contact'],
    ['id' => '81b91f8', 'name' => 'Quote'],
]) {
    fwrite(STDERR, 'Expected forms from every Elementor document to be discovered.'.PHP_EOL);
    exit(1);
}

if (! in_array('elementor_library', $lastGetPostsArguments['post_type'], true)) {
    fwrite(STDERR, 'Expected Elementor templates to be included in discovery.'.PHP_EOL);
    exit(1);
}

echo "Form discovery tests passed.\n";
