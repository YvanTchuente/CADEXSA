<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Database\Connection;
use Application\MiddleWare\ServerRequest;
use Application\CMS\Gallery\PictureManager;

$incoming = (new ServerRequest())->initialize();
$PictureManager = new PictureManager(Connection::Instance());

$params = $incoming->getParsedBody();
$pictureID = $params['id'];
try {
    $picture = $PictureManager->get($pictureID);
} catch (Throwable $e) {
    $url = '/gallery/';
    header('Location: ' . $url);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CADEXSA Gallery pictures">
    <meta name="author" content="Yvan Tchuente">
    <title>Gallery Picture - CADEXSA</title>
    <?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body>
    <div id="loader">
        <div>
            <div class="spinner"></div>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
    <!-- Page Content -->
    <div class="page-content">
        <div class="page-header">
            <div class="ws-container">
                <h1>Gallery</h1>
            </div>
        </div>
        <div class="ws-container">
            <div id="picture_wrapper">
                <div><img src="<?= $picture->getLocation(); ?>" alt="image"></div>
                <div>
                    <div>
                        <h4>Description</h4>
                        <p><?= $picture->getDescription(); ?></p>
                    </div>
                    <div>
                        <h4>Upload date</h4>
                        <p><?= date('l, j F Y', strtotime($picture->getPublicationDate())); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>