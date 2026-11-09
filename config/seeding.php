<?php

return [
    'bootstrap_password' => env('SEED_BOOTSTRAP_PASSWORD'),
    'test_bootstrap_password' => env('TEST_BOOTSTRAP_PASSWORD', 'test-only-password-not-for-production'),
];
