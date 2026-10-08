<?php

return [
    // VAPID (RFC 8292) application server identity.
    // Generate with: php artisan webpush:vapid --write
    'subject' => env('VAPID_SUBJECT', env('MAIL_FROM_ADDRESS', 'mailto:admin@example.com')),
    'public_key' => env('VAPID_PUBLIC_KEY'),
    'private_key' => env('VAPID_PRIVATE_KEY'),

    // Message TTL (seconds) and HTTP timeout for the push service request.
    'ttl' => (int) env('WEBPUSH_TTL', 2419200),
    'timeout' => (int) env('WEBPUSH_TIMEOUT', 10),
];
