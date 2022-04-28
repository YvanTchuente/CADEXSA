<?php

define('STANDARD_FILES', array('limitSize' => 3 * (1024 * 1024), 'allowedTypes' => ['image/jpeg' => 'jpg']));

require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\Database\Connection;
use Application\MiddleWare\Constants;
use Application\Membership\MemberManager;
use Application\MiddleWare\ServerRequest;

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming_request = (new ServerRequest())->initialize();
if ($incoming_request->getMethod() == Constants::METHOD_POST) {
    $payload = $incoming_request->getParsedBody();
    $description = $payload['description'];
    $snapshotDate = $payload['snapshot-date'];
    $snapshotTime = $payload['snapshot-time'];
    $snapshotDatetime = implode(" ", [$snapshotDate, $snapshotTime]);

    $picture = $incoming_request->getUploadedFiles()['picture'];
    $fileSize = $picture->getStream()->getSize();
    $fileType = $picture->getClientMediaType();

    if ($fileSize <= STANDARD_FILES['limitSize']) {
        if (in_array($fileType, array_keys(STANDARD_FILES['allowedTypes']))) {
            $connection = Connection::Instance()->getConnection();
            $content = $picture->getStream()->getContents();
            $ext = STANDARD_FILES['allowedTypes'][$fileType];
            $filename = implode(".", [sha1($content), $ext]);
            $targetPath = dirname(__DIR__, 2) . "/static/images/gallery/";
            $file = $targetPath . $filename;
            // SQL statement
            $sql = "INSERT INTO gallery_pictures (name, description, snapshot_date) VALUES (?,?,?)";
            $stmt = $connection->prepare($sql);
            if ($stmt->execute([$filename, $description, $snapshotDatetime])) {
                if ($picture->moveTo($file)) {
                    $id = $connection->lastInsertId();
                    header('Location: /gallery/pictures/' . $id);
                }
            }
        } else {
            $err_msg = "Upload only JPEG images";
        }
    } else {
        $fileSize = (STANDARD_FILES['limitSize']) / (1024 * 1024);
        $err_msg = sprintf("The image file is too heavy ( > %d MB)", $fileSize);
    }
}
?>
<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CADEXSA Gallery pictures">
    <meta name="author" content="Yvan Tchuente">
    <title>Upload pictures - CADEXSA</title>
    <?php require_once dirname(__DIR__, 2) . "/includes/head_tag_includes.php"; ?>
    <script type="module" src="/static/dist/js/pages/cms_picture_uploader.js"></script>
</head>

<body id="cms-picture-uploader">
    <div id="loader">
        <div>
            <div class="spinner"></div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/header.php"; ?>
    <?php require_once dirname(__DIR__, 2) . "/includes/cms-header.php"; ?>
    <div class="ws-container">
        <?php if (isset($err_msg)) : ?><span class="error_msg"><?= $err_msg; ?></span><?php endif; ?>
        <div id="upload-wrapper">
            <div id="header">
                <h3>Upload Gallery Pictures</h3>
            </div>
            <div id="content">
                <div>
                    <div id="dropbox">
                        <button>Drop pictures to upload or browse</button>
                    </div>
                </div>
                <div style="height: fit-content;">
                    <h6>Image description</h6>
                    <form action="/cms/pictures/upload" method="POST" id="upload-form" enctype="multipart/form-data">
                        <div class="form-grouping">
                            <div class="form-group">
                                <div>
                                    <label for="snapshot-date">Snapshot date</label>
                                    <input type="date" name="snapshot-date" id="snapshot-date" class="form-control">
                                </div>
                                <div>
                                    <label for="snapshot-time">Snapshot time</label>
                                    <input type="time" name="snapshot-time" id="snapshot-time" class="form-control">
                                </div>
                            </div>
                        </div>
                        <input type="file" name="picture" style="display: none;">
                        <div class="form-grouping">
                            <label for="desc">Description</label>
                            <textarea name="description" id="desc" class="form-control"></textarea>
                        </div>
                        <div class="form-grouping">
                            <button type="submit">Upload Pictures</button>
                            <button type="reset">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/footer.php"; ?>
</body>

</html>