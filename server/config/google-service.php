<?php
return [
    // ...

    'google' => [
        // Our Google API credentials.
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),

        // The URL to redirect to after the OAuth process.
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),

        // The URL that listens to Google webhook notifications (Part 3).
        'webhook_uri' => env('GOOGLE_WEBHOOK_URI'),

        // Let the user know what we will be using from his Google account.
        'scopes' => [
            // Getting access to the user's email.
            \Google\Service\Oauth2::USERINFO_PROFILE,
            \Google\Service\Oauth2::USERINFO_EMAIL,
            \Google\Service\Oauth2::OPENID,

            // Managing the user's calendars and events.
            \Google_Service_Calendar::CALENDAR,
        ],

        // Enables automatic token refresh.
        'approval_prompt' => 'force',
        'access_type' => 'offline',

        // Enables incremental scopes (useful if in the future we need access to another type of data).
        'include_granted_scopes' => true,
    ],
];
