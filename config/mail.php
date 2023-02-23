<?php

return [
    'host' => env('MAIL_HOST'),

    'port' => env('MAIL_PORT'),

    'username' => env('MAIL_USERNAME'),

    'password' => env('MAIL_PASSWORD'),

    'accounts' => [
        'admin' => env('MAIL_ADMIN_ACCOUNT'),
        'members' => env('MAIL_MEMBERS_ACCOUNT'),
        'newsletter' => env('MAIL_NEWSLETTER_ACCOUNT'),
        'info' => env('MAIL_INFO_ACCOUNT')
    ]
];
