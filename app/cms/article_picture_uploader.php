<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Database\Connection;
use Application\MiddleWare\ServerRequest;

define('STANDARD_FILES', array('limitSize' => 3 * (1024 * 1024), 'allowedTypes' => ['image/jpeg' => 'jpg']));

$incoming_request = (new ServerRequest())->initialize();

$picture = $incoming_request->getUploadedFiles()['picture'];
$fileSize = $picture->getStream()->getSize();
$fileType = $picture->getClientMediaType();

if ($fileSize <= STANDARD_FILES['limitSize']) {
    if (in_array($fileType, array_keys(STANDARD_FILES['allowedTypes']))) {
        $connection = Connection::Instance()->getConnection();
        $ext = STANDARD_FILES['allowedTypes'][$fileType];
        $filename = sha1($_SESSION['username'] . $_SESSION['ID']) . "." . $ext;
        $targetPath = dirname(__DIR__) . "/static/images/articles_thumbnails/";
        $file = $targetPath . $filename;

        if ($picture->moveTo($file)) {
            $fileURL = "/static/images/articles_thumbnails/" . $filename;
            $response = ['status' => "ok", 'filename' => $fileURL];
            echo json_encode($response);
        }
    }
}
