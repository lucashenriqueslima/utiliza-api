<?php

return [
    'api_url' => env('FIREBASE_API_URL', 'https://www.googleapis.com/auth/firebase.messaging'),
    'api_url_scope' => env('FIREBASE_API_URL_SCOPE', 'https://www.googleapis.com/auth/firebase.messaging'),
    'app_id' => env('FIREBASE_APP_ID', 'utiliza-push-notification'),
];
