<?php

return [
    'default' => 'theme',
    'manifests' => [
        'theme' => [
            'path' => __DIR__ . '/../build',
            'url' => 'https://example.com/wp-content/themes/fixture/build',
            'assets' => __DIR__ . '/../build/manifest.json',
            'bundles' => __DIR__ . '/../build/entrypoints.json',
        ],
    ],
];
