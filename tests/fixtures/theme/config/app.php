<?php

return [
    'cipher' => 'aes-256-cbc',
    'key' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/',
    'previous_keys' => [],
    'modules' => [
        'no-arg' => \Webshr\Core\Tests\Fixtures\Modules\No_Arg_Module::class,
        'app-aware' => \Webshr\Core\Tests\Fixtures\Modules\App_Aware_Module::class,
    ],
    'aliases' => [
        'encryption' => \Webshr\Core\Utility\Encryption::class,
        'hash' => \Webshr\Core\Utility\Hash::class,
    ],
    'paths' => [
        'base' => __DIR__ . '/..',
        'app' => __DIR__ . '/../app',
        'config' => __DIR__,
        'resources' => __DIR__ . '/../resources',
        'storage' => __DIR__ . '/../storage',
        'bootstrap' => __DIR__ . '/../bootstrap',
        'env' => __DIR__ . '/..',
    ],
];
