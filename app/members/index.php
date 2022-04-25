<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\MiddleWare\Router\Router;
use Application\Membership\MemberManager;
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
        $url = "http://localhost/members/profile/?id=$memberID";
        if (isset($tab)) {
            $url .= "&tab=$tab";
        }

        $sessid = 'PHPSESSID=' . $_COOKIE['PHPSESSID'];
        $options = [CURLOPT_COOKIE => $sessid, CURLOPT_RETURNTRANSFER => true, CURLOPT_URL => $url];
        $curld = curl_init();
        curl_setopt_array($curld, $options);
        $output = curl_exec($curld);
        curl_close($curld);
        // Display the page
        echo $output;
    });

$router->post('/members/login', function () {
    include 'login.php';
})
    ->post('/members/register', function () {
        include 'register.php';
    })
    ->post('/members/recover', function () {
        include 'recover_account.php';
    });

$router->resolve();
