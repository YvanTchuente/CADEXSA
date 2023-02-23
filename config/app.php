<?php

return [

    /**
     * Application Name
     */
    'name' => 'CADEXSA',

    /**
     * Application Environment
     */
    'env' => env('APP_ENV', 'production'),

    /**
     * Encryption Key
     */
    'key' => env('APP_KEY'),

    /**
     * Encryption Cipher
     */
    'cipher' => 'AES-256-CBC',

    /**
     * HTTP message factories
     */
    'factories' => [
        'uriFactory' => \Tym\Http\Message\UriFactory::class,
        'streamFactory' => \Tym\Http\Message\StreamFactory::class,
        'requestFactory' => \Tym\Http\Message\RequestFactory::class,
        'responseFactory' => \Tym\Http\Message\ResponseFactory::class,
        'uploadedFileFactory' => \Tym\Http\Message\UploadedFileFactory::class,
        'serverRequestFactory' => \Tym\Http\Message\ServerRequestFactory::class
    ],

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     */
    'routeMiddleware' => [
        'authorize' => \Cadexsa\Presentation\Http\Middleware\AuthorizeRequests::class,
        'compress' => \Cadexsa\Presentation\Http\Middleware\CompressResponse::class,
        'verified' => \Cadexsa\Presentation\Http\Middleware\VerifyCsrfToken::class,
        'authenticate' => \Cadexsa\Presentation\Http\Middleware\AuthenticateRequests::class
    ],

    /**
     * The application's route middleware groups.
     */
    'middlewareGroups' => [
        'web' => [
            \Cadexsa\Presentation\Http\Middleware\AuthorizeRequests::class,
            \Cadexsa\Presentation\Http\Middleware\CompressResponse::class,
            \Cadexsa\Presentation\Http\Middleware\VerifyCsrfToken::class
        ],

        'api' => [
            \Cadexsa\Presentation\Http\Middleware\AuthenticateRequests::class,
        ]
    ]

];
