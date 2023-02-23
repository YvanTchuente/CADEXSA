<?php

require_once __DIR__ . '/../../bootstrap/load.php';
require_once __DIR__ . '/../../bootstrap/app.php';

use Tym\Http\Message\ServerRequestFactory;

define('STANDARD_FILES', ['limitSize' => 3 * (1024 * 1024), 'allowedTypes' => ['image/jpeg' => 'jpg']]);

$request = ServerRequestFactory::createFromGlobals();

$picture = $request->getUploadedFiles()['picture'];
$fileSize = $picture->getStream()->getSize();
$fileType = $picture->getClientMediaType();

if ($fileSize <= STANDARD_FILES['limitSize']) {
    if (in_array($fileType, array_keys(STANDARD_FILES['allowedTypes']))) {
        $connection = app()->database->getConnection();
        $ext = STANDARD_FILES['allowedTypes'][$fileType];
        $filename = sha1(user()->getUsername() . user()->getId()) . "." . $ext;
        $targetPath = dirname(__DIR__) . "/images/articles_thumbnails/";
        $file = $targetPath . $filename;
        if ($picture->moveTo($file)) {
            $fileURL = "/images/articles_thumbnails/" . $filename;
            $response = ['status' => "ok", 'filename' => $fileURL];
            echo json_encode($response);
        }
    }
}
