<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
    'finish_redirect' => env('MIDTRANS_FINISH_REDIRECT', '/payment/finish'),
    'unfinish_redirect' => env('MIDTRANS_UNFINISH_REDIRECT', '/payment/unfinish'),
    'error_redirect' => env('MIDTRANS_ERROR_REDIRECT', '/payment/error'),
];
