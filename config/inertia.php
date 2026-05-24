<?php

declare(strict_types=1);

return [

    'ssr' => [
        'enabled' => (bool) env('INERTIA_SSR_ENABLED', true),
        'runtime' => env('INERTIA_SSR_RUNTIME', 'node'),
        'ensure_runtime_exists' => (bool) env('INERTIA_SSR_ENSURE_RUNTIME_EXISTS', false),
        'url' => env('INERTIA_SSR_URL', 'http://'.env('INERTIA_SSR_HOST', '127.0.0.1').':'.env('INERTIA_SSR_PORT', '13714')),
        'ensure_bundle_exists' => (bool) env('INERTIA_SSR_ENSURE_BUNDLE_EXISTS', true),
        'throw_on_error' => (bool) env('INERTIA_SSR_THROW_ON_ERROR', false),
    ],

    'pages' => [
        'ensure_pages_exist' => false,
        'paths' => [
            resource_path('js/pages'),
        ],
        'extensions' => [
            'vue',
        ],
    ],

    'testing' => [
        'ensure_pages_exist' => true,
    ],

    'expose_shared_prop_keys' => true,

    'history' => [
        'encrypt' => (bool) env('INERTIA_ENCRYPT_HISTORY', false),
    ],

];
