<?php

return [
    'client' => [
        'hosts' => [
            [
                'host'              => env('ELASTICSEARCH_HOST', 'localhost'),
                'port'              => env('ELASTICSEARCH_PORT', 9200),
                'scheme'            => env('ELASTICSEARCH_SCHEME', null),
                'user'              => env('ELASTICSEARCH_USER', null),
                'pass'              => env('ELASTICSEARCH_PASS', null),
            ]
        ],
    ],
    'update_mapping' => env('SCOUT_ELASTIC_UPDATE_MAPPING', true),
    'indexer' => env('SCOUT_ELASTIC_INDEXER', 'single'),
    'document_refresh' => env('SCOUT_ELASTIC_DOCUMENT_REFRESH'),
];
