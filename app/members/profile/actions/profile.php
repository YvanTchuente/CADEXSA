<?php
// Handles profile related tasks
require_once dirname(__DIR__, 3) . '/config/index.php';

use Application\Database\Connection;
use Application\Membership\MemberManager;
use Application\MiddleWare\{
    Constants,
    ServerRequest
};

define('STANDARD_FILES', array('limitSize' => 3 * (1024 * 1024), 'allowedTypes' => ['image/jpeg' => 'jpg']));

$incoming_request =  (new ServerRequest())->initialize();

switch ($incoming_request->getMethod()) {
    case Constants::METHOD_GET:
        $param = $incoming_request->getParsedBody();
        $action = $param['action'];
        switch ($action) {
            case 'fetchPicture':
                $memberID = $param['memberID'];
                $picture = MemberManager::Instance()->getMember($memberID)->getPicture();
                echo $picture;
                break;
        }

    case Constants::METHOD_POST:
        $param = $incoming_request->getParsedBody();
        $action = $param['action'];
        switch ($action) {
            case 'updateAvatar':
                $memberID = $param['memberID'];
                $username = $param['username'];
                $uploadedfile = $incoming_request->getUploadedFiles()['input_picture'];
                $fileSize = $uploadedfile->getStream()->getSize();
                $fileType = $uploadedfile->getClientMediaType();
                $fileTempName = $uploadedfile->getTempName();

                if ($fileSize < STANDARD_FILES['limitSize']) {
                    if (in_array($fileType, array_keys(STANDARD_FILES['allowedTypes']))) {
                        // Manipulating the image
                        $img_php = imagecreatefromjpeg($fileTempName);
                        $sizex = imagesx($img_php);
                        $sizey = imagesy($img_php);
                        $img_php = imagecrop($img_php, ['x' => $sizex * 0.1, 'y' => $sizey * 0.1, 'width' => $sizex * 0.8, 'height' => $sizey * 0.8]);

                        if (imagejpeg($img_php, $fileTempName, 100)) {
                            $content = $uploadedfile->getStream()->getContents();
                            $filename = sha1($content);
                            $ext = STANDARD_FILES['allowedTypes'][$fileType];
                            $targetpath = dirname(__DIR__, 3) . "/static/images/profile_pictures/" . $filename . "." . $ext;
                            // Query statements
                            $fetch_sql = "SELECT * FROM profile_pictures WHERE memberID='$memberID'";
                            $insert_sql = "INSERT INTO profile_pictures (memberID, name) VALUES ('$memberID','$filename')";
                            $update_sql = "UPDATE profile_pictures SET name = '$filename' WHERE memberID='$memberID'";

                            $query = Connection::Instance()->getConnection()->query($fetch_sql);
                            if (!($row = $query->fetch(\PDO::FETCH_ASSOC))) {
                                if (Connection::Instance()->getConnection()->query($insert_sql)) {
                                    if ($uploadedfile->moveTo($targetpath)) {
                                        imagedestroy($img_php);
                                        echo "Successful";
                                    } else {
                                        throw new RuntimeException("Move operation failed");
                                    }
                                } else {
                                    throw new RuntimeException("Error Processing INSERT query request");
                                }
                            } else {
                                $previousName = dirname(__DIR__, 3) . "/static/images/profile_pictures/" . $row['name'] . ".jpg";
                                $previousName = str_replace("/", "\\", $previousName);
                                if (unlink($previousName)) {
                                    if (Connection::Instance()->getConnection()->query($update_sql)) {
                                        if ($uploadedfile->moveTo($targetpath)) {
                                            imagedestroy($img_php);
                                            echo "Successful";
                                        } else {
                                            throw new RuntimeException("Move operation failed");
                                        }
                                    } else {
                                        throw new RuntimeException("Error Processing UPDATE query request");
                                    }
                                } else {
                                    throw new RuntimeException("File could not be deleted");
                                }
                            }
                        } else {
                            throw new RuntimeException("Error Outputing GDimage to the uploaded file");
                        }
                    } else {
                        echo "File type is not supported, please try again";
                    }
                } else {
                    echo "The image size exceeds the limit of 3MB";
                }
                break;

            case 'updateProfile':
                $connection = Connection::Instance()->getConnection();
                $memberID = $param['memberID'];
                $fields = [];
                $referer = $_SERVER['HTTP_REFERER'];
                $update_sql = "UPDATE members SET ";
                foreach ($param as $key => $value) {
                    if ($key == 'memberID' || $key == 'action') {
                        continue;
                    }
                    if (strlen($value) < 1) {
                        continue;
                    }
                    $fields[$key] = $value;
                }
                foreach ($fields as $key => $value) {
                    if ($key == 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $err_msg = "Invalid email address";
                        break;
                    }
                    if ($key == 'contact' && !strlen($value) == 9) {
                        $err_msg = "Invalid phone number";
                        break;
                    }
                    $update_sql .= "$key = :$key, ";
                }
                if ($err_msg) {
                    die($err_msg);
                }
                $update_sql = substr($update_sql, 0, -2);
                $update_sql .= " WHERE ID = '$memberID'";
                $stmt = $connection->prepare($update_sql);
                if ($stmt->execute($fields)) {
                    header('Location: ' . $referer);
                }
                break;
        }
        break;
}
