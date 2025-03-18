<?php

return [
    'dsn' => env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')),
    'release' => env('SENTRY_RELEASE'),
    'environment' => env('SENTRY_ENVIRONMENT'),

    'breadcrumbs' => [
        'logs' => true,
        'queue_info' => true,
        'command_info' => true,
    ],
    'send_default_pii' => true,

    'traces_sample_rate' => (float)(env('SENTRY_TRACES_SAMPLE_RATE', 1.0)),

    'controllers_base_namespace' => env('SENTRY_CONTROLLERS_BASE_NAMESPACE', 'App\\Http\\Controllers'),
];

