<?php

return [
    'key' => env('SHOPIFY_KEY'),
    'secret' => env('SHOPIFY_SECRET'),
    'redirect' => env('SHOPIFY_REDIRECT'),
    'webhook_url' => env('SHOPIFY_WEBHOOK_URL'),
    'webhook_version' => '2019-07',
    'webhook_registration_endpoint' => 'https://%s/admin/api/%s/webhooks.json',
    'webhook_routes' => [
        'app/uninstalled' => 'webhook-uninstall-app',
        'products/create' => 'webhook-products-create',
        'products/update' => 'webhook-products-update',
        'products/delete' => 'webhook-products-delete',
    ],
    'scopes' => [
        'read_products',
        'write_products'
    ]
];
