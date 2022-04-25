<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\MiddleWare\Router\Router;
use Application\MiddleWare\ServerRequest;

$incoming = (new ServerRequest())->initialize();
$router = new Router($incoming);

$default_action = function () {
    include DOCUMENT_ROOT . '/PageNotFound.php';
};
$router->setDefaultAction($default_action);

$router->get('/cms/(news|events)/(publish|plan)', function ($matches) {
    include $matches[1] . '/' . $matches[2] . '.php';
})
    ->get('/cms/(news|events)/edit', function ($matches) {
        include $matches[1] . '/edit.php';
    })
    ->get('/cms/delete', function () {
        include 'delete.php';
    });

$router->post('/cms/(news|events)/(publish|plan)', function ($matches) {
    include $matches[1] . '/' . $matches[2] . '.php';
})
    ->post('/cms/(news|events)/edit', function ($matches) {
        include $matches[1] . '/edit.php';
    });

$router->resolve();
