<?php

declare(strict_types=1);

return [

    'ssr' => [
        'enabled' => (bool) env('INERTIA_SSR_ENABLED', true),
        'url' => env('INERTIA_SSR_URL', 'http://'.env('INERTIA_SSR_HOST', '127.0.0.1').':'.env('INERTIA_SSR_PORT', '13714')),
        'ensure_bundle_exists' => (bool) env('INERTIA_SSR_ENSURE_BUNDLE_EXISTS', true),
    ],

    'ensure_pages_exist' => false,

    'page_paths' => [
        resource_path('js/pages'),
    ],

    'page_extensions' => [
        'vue',
    ],

    'use_script_element_for_initial_page' => (bool) env('INERTIA_USE_SCRIPT_ELEMENT_FOR_INITIAL_PAGE', false),

    'testing' => [
        'ensure_pages_exist' => true,
        'page_paths' => [
            resource_path('js/pages'),
        ],
        'page_extensions' => [
            'vue',
        ],
    ],

    'history' => [
        'encrypt' => (bool) env('INERTIA_ENCRYPT_HISTORY', false),
    ],

];
