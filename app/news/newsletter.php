<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Database\Connection;
use Application\MiddleWare\ServerRequest;

$connection = Connection::Instance()->getConnection();
$incoming_request = (new ServerRequest())->initialize();
$params = $incoming_request->getParsedBody();

$userName = $params['name'];
$userEmail = $params['email'];

$insert_sql = "INSERT INTO newsletter (name, email) VALUES (:name, :email)";
$check_sql = "SELECT name, email FROM newsletter WHERE email=:email";
$newsletter_user_info = ["name" => $userName, "email" => $userEmail];

$check_stmt = $connection->prepare($check_sql);
$check_stmt->execute(["email" => $userEmail]);
$check_data = $check_stmt->fetch(\PDO::FETCH_ASSOC);
if (!$check_data) {
    $insert_stmt = $connection->prepare($insert_sql);
    $insert_query = $insert_stmt->execute($newsletter_user_info);
}
echo "ok";
