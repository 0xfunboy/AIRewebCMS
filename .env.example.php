<?php

return [
    'app' => [
        'name' => 'AIRewardrop',
        'env' => 'production',
        'debug' => false,
        'url' => 'https://airewardrop.xyz',
        'timezone' => 'UTC',
        'key' => '',
        'session_name' => 'aire_session',
    ],
    'database' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'airewardrop',
        'username' => '',
        'password' => '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'wallet' => [
        'allowed_addresses' => [
            // '0xYourAdminWalletAddress',
        ],
        'nonce_ttl' => 300,
        'project_id' => '',
        'rpc_url' => '',
    ],
    'mail' => [
        'driver' => 'smtp',
        'host' => 'smtp.yourhost.com',
        'port' => 587,
        'username' => '',
        'password' => '',
        'encryption' => 'tls',
        'from_address' => '',
        'from_name' => 'AIRewardrop',
    ],
];
