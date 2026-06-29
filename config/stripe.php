<?php

return [
    'api_keys' => [
        'secret_key' => env('STRIPE_SECRET_KEY', null),
        'publisher_key' => env('STRIPE_PUBLISHER_KEY', null),
    ],
    'frontend_url' => env('FRONTEND_URL', null),
];
