<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Network\Requests;
use Application\Membership\MemberManager;
use Application\MiddleWare\Router\Router;
use Application\MiddleWare\ServerRequest;

$incoming = (new ServerRequest())->initialize();
$router = new Router($incoming);

$default_action = function () {
    include DOCUMENT_ROOT . '/PageNotFound.php';
};
$router->setDefaultAction($default_action);

$router->get('/members/?', function () {
    include 'list.php';
})
    ->get('/members/register', function () {
        include 'register.php';
    })
    ->get('/members/login', function () {
        include 'login.php';
    })
    ->get('/members/recovery', function () {
        include 'recover_account.php';
    })
    ->get('/members/profiles/(\w+)/?(chats|settings)?', function ($matches) {
        $username = $matches[1];
        if (isset($matches[2])) $tab = $matches[2];

        if (!MemberManager::Instance()->is_logged_in()) {
            $goto = urlencode("/members/profiles/$username");
            header('Location: /members/login?goto=' . $goto);
        }

        $memberID = MemberManager::Instance()->getIDByName($username);
        $url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/members/profile/?id=$memberID";
        if (isset($tab)) {
            $url .= "&tab=$tab";
        }

        $output = (new Requests())->get($url);
        // Display the page
        echo $output;
    });

$router->post('/members/login', function () {
    include 'login.php';
})
    ->post('/members/register', function () {
        include 'register.php';
    })
    ->post('/members/recovery', function () {
        include 'recover_account.php';
    });

$router->resolve();
