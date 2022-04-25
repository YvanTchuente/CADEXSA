<?php

declare(strict_types=1);

namespace Application\MiddleWare;

use Psr\Http\Message\{
    StreamInterface,
    UploadedFileInterface
};

class UploadedFile implements UploadedFileInterface
{
    private const UPLOAD_ERRORS = [
        UPLOAD_ERR_OK,
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE,
        UPLOAD_ERR_PARTIAL,
        UPLOAD_ERR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR,
        UPLOAD_ERR_CANT_WRITE,
        UPLOAD_ERR_EXTENSION,
    ];

    /**
     * Stream representing the uploaded file
     * 
     * @var StreamInterface
     */
    private $stream;

    /**
     * Upload error code
     * 
     * @var string
     */
    private $error;

    /**
     * Size in bytes
     * 
     * @var int|null
     */
    private $size;

    /**
     * Original name of the uploaded file
     * 
     * @var string|null
     */
    private $clientFilename;

    /**
     * Mime type of the uploaded file
     * 
     * @var string|null
     */
    private $clientMediaType;

    /** 
     * @var bool 
     */
    private $moved = false;

    /**
     * @param StreamInterface|resource|string $stream Uploaded file or its filename
     * @param int $size Size in bytes of the uploaded file
     * @param int $error Error status of the uploaded file, must be one of PHP's UPLOAD_ERR_XXX constants.
     * @param string $clientFilename Client name of the uploaded file
     * @param string $clientMediaType Media type of the uploaded file
     * 
     * @throws \InvalidArgumentException If an invalid stream or error code is passed
     **/
    public function __construct(
        $stream,
        int $size = null,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ) {
        if (!in_array($error, self::UPLOAD_ERRORS, true)) {
            throw new \InvalidArgumentException("Invalid error status argument");
        }
        $this->error = $error;
        if (!($stream instanceof StreamInterface) && !is_resource($stream) && !is_string($stream)) {
            throw new \InvalidArgumentException("Stream must either be a StreamInterface, resource or its filename");
        }
        if (is_resource($stream) || is_string($stream)) {
            $stream = new Stream($stream);
        }
        $this->stream = $stream;
        $this->size = $size ?? $this->stream->getSize();
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    public function getStream()
    {
        if ($this->moved) {
            throw new \RuntimeException('Uploaded file has been already moved');
        }
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("No stream is available due to an upload error");
        }
        return $this->stream;
    }

    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new \RuntimeException('Uploaded file has been already moved');
        }
        if (!is_string($targetPath) || is_dir($targetPath)) {
            throw new \InvalidArgumentException("A path to a file name must be provided not to a directory");
        }
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("No stream is available due to an upload error");
        }
        $originalFilename = $this->stream->getMetadata('uri');
        if (is_null($originalFilename)) {
            throw new \RuntimeException("Stream was detached");
        }
        // If the body is a PHP I/O stream
        if (preg_match('/php:\/{2}\w+/', $originalFilename)) {
            $tmpFilename = sys_get_temp_dir() . '/' . random_int(1000000, 1000000000) . '.tmp';
            $file = fopen($tmpFilename, 'w');
            $contents = (string) $this->stream;
            fwrite($file, $contents);
            $originalFilename = $tmpFilename;
            fclose($file);
        }
        $this->moved = rename($originalFilename, $targetPath);
        if (!$this->moved) {
            throw new \RuntimeException(sprintf("Could not move the uploaded file to %s", $targetPath));
        }
        unlink($originalFilename);
        $this->error = UPLOAD_ERR_OK;
        return true;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
}
