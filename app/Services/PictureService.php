<?php

declare(strict_types=1);

namespace Cadexsa\Services;

use Cadexsa\Domain\Model\Persistence;
use Cadexsa\Domain\Model\Picture\Picture;
use Psr\Http\Message\UploadedFileInterface;
use Cadexsa\Domain\Factories\PictureFactory;

class PictureService
{
    private const PICTURE_SIZE_LIMIT = 3 * (1024 * 1024);

    private const ALLOWED_PICTURE_EXTENSIONS = ['image/jpeg' => 'jpg'];

    /**
     * Stores an uploaded gallery picture.
     *
     * @param UploadedFileInterface $picture The uploaded picture.
     * @param string $shotOn The timestamp at which the picture was shot.
     * @param string $description A description of the events at which the picture was taken.
     * 
     * @return Picture The stored gallery picture.
     * 
     * @throws \RuntimeException If an error occurs.
     */
    public function storePicture(UploadedFileInterface $picture, string $shotOn, string $description): Picture
    {
        $size = $picture->getSize();
        $type = $picture->getClientMediaType();
        if ($size >= self::PICTURE_SIZE_LIMIT) {
            $size = self::PICTURE_SIZE_LIMIT / (1024 * 1024);
            $message = sprintf("The picture's size is above the size limit of %d MB)", $size);
            throw new InvalidPictureException($message);
        }
        $allowedTypes = array_keys(self::ALLOWED_PICTURE_EXTENSIONS);
        if (!in_array($type, $allowedTypes)) {
            $message = "Only uploads of pictures with these extensions: " . implode(",", self::ALLOWED_PICTURE_EXTENSIONS) . " are accepted.";
            throw new InvalidPictureException($message);
        }
        $ext = self::ALLOWED_PICTURE_EXTENSIONS[$type];
        $name = implode(".", [sha1($picture->getStream()->getContents()), $ext]);
        $storedPicture = PictureFactory::create($name, $description, $shotOn);
        Persistence::pictureRepository()->add($storedPicture);
        $destination = public_path("/images/gallery/$name");
        $picture->moveTo($destination);
        return $storedPicture;
    }
}
