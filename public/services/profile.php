<?php
// Handles account related tasks
require_once dirname(__DIR__, 2) . '/bootstrap/app.php';

use Cadexsa\Domain\Model\INull;
use Cadexsa\Domain\Model\Persistence;
use Tym\Http\Message\ServerRequestFactory;
use Cadexsa\Infrastructure\Transaction\TransactionManager;

define('STANDARD_FILES', ['limitSize' => 3 * (1024 * 1024), 'allowedExtensions' => ['image/jpeg' => 'jpg']]);

$request =  ServerRequestFactory::createFromGlobals();
$params = $request->getParsedBody();
$action = $params['action'];
switch ($action) {
    case 'updateAvatar':
        $exStudentId = (int) $params['exStudentId'];
        $picture = $request->getUploadedFiles()['picture'];
        $size = $picture->getStream()->getSize();
        $type = $picture->getClientMediaType();
        $tempName = $picture->getStream()->getMetadata()['uri'];
        try {
            if ($size > STANDARD_FILES['limitSize']) {
                throw new \RuntimeException("The image's size exceeds the limit of " . STANDARD_FILES['limitSize'] . "MB");
            }
            if (!in_array($type, array_keys(STANDARD_FILES['allowedExtensions']))) {
                throw new \RuntimeException("The file type is not supported");
            }
            // Crop down the picture
            $img = imagecreatefromjpeg($tempName);
            $img_width = imagesx($img);
            $img_height = imagesy($img);
            $img = imagecrop($img, ['x' => $img_width * 0.1, 'y' => $img_height * 0.1, 'width' => $img_width * 0.8, 'height' => $img_height * 0.8]);
            // Save the modified picture
            $saved = imagejpeg($img, $tempName, 100);
            if (!$saved) {
                throw new RuntimeException("An error occurred during processing, please retry.");
            }

            $filename = sha1($picture->getStream()->getContents());
            $ext = STANDARD_FILES['allowedExtensions'][$type];
            $avatar = "/images/profile_pictures/" . $filename . "." . $ext;
            $destination = APP_DOCUMENT_ROOT . $avatar;

            TransactionManager::beginTransaction();
            $exstudent = Persistence::exStudentRepository()->findById($exStudentId);
            if ($exstudent->getAvatar() !== DEFAULT_PROFILE_PICTURE) {
                $formerAvatar = APP_DOCUMENT_ROOT . $exstudent->getAvatar();
                $formerAvatar = str_replace("/", "\\", $formerAvatar);
                if (!unlink($formerAvatar)) {
                    throw new RuntimeException("$formerAvatar could not be deleted.");
                }
            }
            $exstudent->setAvatar($avatar);
            TransactionManager::dirty($exstudent);
            $picture->moveTo($destination);
            imagedestroy($img);
            TransactionManager::commit();
        } catch (\Throwable $e) {
            echo $e->getMessage();
        }
        break;

    case 'editProfile':
        extract($params);
        TransactionManager::beginTransaction();
        try {
            $exstudent = Persistence::exStudentRepository()->findById((int) $exStudentId);
            if ($exstudent instanceof INull) {
                throw new \RuntimeException("Ex-student not found.");
            }
            if (isset($firstname) and $firstname) {
                $exstudent->setName($exstudent->getName()->withFirstname($firstname));
            }
            if (isset($lastname) and $lastname) {
                $exstudent->setName($exstudent->getName()->withLastname($lastname));
            }
            if (isset($username) and $username) {
                $exstudent->setUsername($username);
            }
            if (isset($email) and $email) {
                $exstudent->setEmailAddress($email);
            }
            if (isset($country) and $country) {
                $exstudent->setAddress($exstudent->getAddress()->withCountry($country));
            }
            if (isset($city) and $city) {
                $exstudent->setAddress($exstudent->getAddress()->withCity($city));
            }
            if (isset($phone_number) and $phone_number) {
                $exstudent->setPhoneNumber($phone_number);
            }
            if (isset($batch_year) and $batch_year) {
                $exstudent->setBatchYear($batch_year);
            }
            if (isset($biography) and $biography) {
                $exstudent->setDescription($biography);
            }
            TransactionManager::dirty($exstudent);
            TransactionManager::commit();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        break;
}
