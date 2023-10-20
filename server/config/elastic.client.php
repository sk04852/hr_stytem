<?php

declare(strict_types=1);

return [
    'hosts' => [
        // env('ELASTIC_HOST', 'localhost:9200'),
        [
            'host' => env('ELASTIC_HOST', 'localhost'),
            'port' => env('ELASTIC_PORT', '9200'),
            'scheme' => env('ELASTIC_SCHEME', 'http'),
            'path' => env('ELASTIC_PATH', ''),
            'user' => env('ELASTIC_USER', 'elastic'),
            'pass' => env('ELASTIC_PASSWORD', '')
        ]
    ],
    'sslVerification' => false,
];
