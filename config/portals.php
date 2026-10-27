<?php

return [
    'admin' => [
        'email' => env('PORTAL_ADMIN_EMAIL', 'admin@demo.local'),
        'seed_password' => env('PORTAL_ADMIN_SEED_PASSWORD'),
    ],
    'developer' => [
        'email' => env('PORTAL_DEVELOPER_EMAIL', 'developer@demo.local'),
        'seed_password' => env('PORTAL_DEVELOPER_SEED_PASSWORD'),
    ],
];
