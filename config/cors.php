<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure CORS settings for your application. This tells
    | the server which origins are allowed to make requests, which HTTP
    | methods are allowed, and more.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [
        '#.*#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'Authorization',
        'X-Pagination-Total',
        'X-Pagination-Per-Page',
        'X-Pagination-Current-Page',
        'X-Pagination-Last-Page',
    ],

    'max_age' => 0,

    'supports_credentials' => false,

];
