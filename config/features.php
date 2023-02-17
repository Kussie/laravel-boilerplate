<?php

return [
    // Authentication based feature flags
    'auth' => [
        'email_verification_required' => env('EMAIL_VERIFICATION_REQUIRED', true),
        'send_email_verification_email' => env('EMAIL_VERIFICATION_NOTIFICATION', true),
        'token_on_register' => env('TOKEN_ON_REGISTER', false), // If using email verification make sure to disable this, or it will result in allowing the bypassing of the verification on login when registering (Or at least used the verified middleware on your routes
        'per_page_default' => env('PER_PAGE_DEFAULT', 15),
    ],
];
