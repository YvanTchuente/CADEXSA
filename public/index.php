<?php

use Tym\Http\Message\ServerRequestFactory;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Retrieve the incoming request
$request = ServerRequestFactory::createFromGlobals();

TransactionManager::beginTransaction();

// Handle the request and send the response
$response = $app->handle($request);

TransactionManager::commit();

$app->send($response);
