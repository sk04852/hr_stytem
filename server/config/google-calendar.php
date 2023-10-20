<?php

return [

    'default_auth_profile' => env('GOOGLE_CALENDAR_AUTH_PROFILE', 'service_account'),

    'auth_profiles' => [

        /*
         * Authenticate using a service account.
         */
        'service_account' => [
            /*
             * Path to the json file containing the credentials.
             */
            'credentials_json' => storage_path('app/google-calendar/hr-agency-363812-e6d03c8e8c9e.json'),
        ],

        /*
         * Authenticate with actual google user account.
         */
        'oauth' => [
            /*
             * Path to the json file containing the oauth2 credentials.
             */
            'credentials_json' => storage_path('app/google-calendar/aneeq_google_calendar_oauth.json'),

            /*
             * Path to the json file containing the oauth2 token.
             */
            'token_json' => storage_path('app/google-calendar/oauth-token.json'),
        ],
    ],

    /*
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID', 'ba03b48dfce7a83f9fef4f5bf55a13138eefa9236cc41b5c37315e7cd6392f9c@group.calendar.google.com'),

     /*
     *  The email address of the user account to impersonate.
     */
    'user_to_impersonate' => env('GOOGLE_CALENDAR_IMPERSONATE'),
];
