<?php

declare(strict_types=1);

namespace Application\MiddleWare;

use Psr\Http\Message\StreamInterface;

class Stream implements StreamInterface
{
    /**
     * Valid mode parameters to the fopen function
     * 
     * @var string
     */
    private const ALLOWED_MODES = '/^r\+?|w\+?|a\+?|x\+?|c\+$/';

    /**
     * Access types of read-only streams
     * 
     * @var string
     */
    private const READABLE_ONLY = '/r(b|t)?/';

    /**
     * Access types of write-only streams
     * 
     * @var string
     */
    private const WRITABLE_ONLY = '/(w|a|x|c)(b|t)?/';

    /**
     * Access types of both readable and writable stream
     *  
     * @var string
     */
    private const BOTH_READABLE_WRITABLE = '/(r+|w+|a+|x+|c+)(b|t)?/';

    /**
     * The stream resource
     * 
     * @var resource
     */
    protected $stream;

    /**
     * The size in bytes of the stream
     * 
     * @var int|null
     */
    protected $size;

    /**
     * @var bool
     */
    protected $readable;

    /**
     * @var bool
     */
    protected $writable;

    /** 
     * @var bool
     */
    protected $seekable;

    /**
     * Metadata of the stream
     * 
     * @var array
     */
    protected $metadata;

    /**
     * Initializes the stream
     * 
     * It accepts an associative array of options, these options applies only if `stream` is a string.
     * 
     * - isText: (bool) If set to true, then `stream` is considered to be the content of the stream to wrap.
     *                  It defaults to false, meaning `stream` is considered the URI/filename of the stream.
     * 
     * - mode: (string|null) If the isText option set to false, mode is provided as argument to the fopen function. 
     *         It must be a valid mode. If the option is unset, it defaults to 'r+' mode.
     * 
     * @param resource|string $stream Stream to wrap
     * @param array $options Associative array of options
     * 
     * @throws \InvalidArgumentException For invalid streams
     * @throws \RuntimeException If an error occurs while processing the stream
     **/
    public function __construct($stream, $options = [])
    {
        if (!is_resource($stream) && !is_string($stream)) {
            throw new \InvalidArgumentException("Stream argument must be a resource or a string");
        }
        $resource = $stream;
        if (is_string($stream)) {
            $this->verifyOptions($options);
            extract($options);
            if ($isText) {
                $resource = fopen("php://temp", "r+");
                fwrite($resource, $stream);
                rewind($resource);
            } else {
                $resource = fopen($stream, $mode);
                if (!is_resource($resource)) {
                    throw new \RuntimeException(sprintf('%s could not be opened', $stream));
                }
            }
        }
        $this->stream = $resource;
        $this->metadata = stream_get_meta_data($this->stream);
        $this->readable = $this->isAccessType('readable');
        $this->writable = $this->isAccessType('writable');
        $this->seekable = $this->metadata['seekable'];
        $this->size = $this->retrieveSize($this->stream);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function __toString()
    {
        try {
            $this->rewind();
            $contents = $this->getContents();
            $this->rewind();
            return $contents;
        } catch (\Throwable $e) {
            trigger_error(sprintf("%s::__toString exception: %s", self::class, $e));
            return '';
        }
    }

    public function close()
    {
        if ($this->stream) {
            fclose($this->stream);
        }
        $this->detach();
    }

    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }
        $result = $this->stream;
        unset($this->stream);
        $this->metadata = $this->size = null;
        $this->readable = $this->writable = $this->seekable = false;
        return $result;
    }

    public function getSize()
    {
        if (!isset($this->stream)) {
            return null;
        }
        $this->size = $this->retrieveSize($this->stream);
        if (is_null($this->size)) {
            return null;
        }
        return $this->size;
    }

    public function tell()
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream is detached");
        }
        $current_position = ftell($this->stream);
        if ($current_position === false) {
            throw new \RuntimeException("Could not tell the position of the pointer");
        }
        return $current_position;
    }

    public function eof()
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream is detached");
        }
        return feof($this->stream);
    }

    public function isSeekable()
    {
        return $this->seekable;
    }

    public function seek($offset, $whence = SEEK_SET)
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream is detached");
        }
        $has_sought = fseek($this->stream, $offset, $whence);
        if ($has_sought < 0) {
            throw new \RuntimeException("Could not seek into stream");
        }
    }

    public function rewind()
    {
        $this->seek(0);
    }

    public function isWritable()
    {
        return $this->writable;
    }

    public function write($string)
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream is detached");
        }
        $bytes = fwrite($this->stream, $string);
        if ($bytes === false) {
            throw new \RuntimeException("String could not be written to the stream");
        }
        return $bytes;
    }

    public function isReadable()
    {
        return $this->readable;
    }

    public function read($length)
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream is detached");
        }
        $content = fread($this->stream, $length);
        if (!$content) {
            throw new \RuntimeException("Stream could not be read");
        }
        $this->metadata = stream_get_meta_data($this->stream);
        return $content;
    }

    public function getContents()
    {
        if (!isset($this->stream)) {
            throw new \RuntimeException("Stream is detached");
        }
        $contents = stream_get_contents($this->stream);
        if ($contents === false) {
            throw new \RuntimeException("Stream could not be read");
        }
        return $contents;
    }

    public function getMetadata($key = null)
    {
        if (!isset($this->stream)) {
            return ($key) ? null : [];
        } elseif (!$key) {
            return $this->metadata;
        } else {
            return $this->metadata[$key] ?? null;
        }
    }

    /**
     * @param resource $stream
     */
    protected function retrieveSize($stream)
    {
        $wrapper_type = $this->getMetadata('wrapper_type');
        if ($wrapper_type == 'PHP') {
            $this->rewind();
            $size = strlen($this->getContents());
            $this->rewind();
        } else {
            $size = fstat($stream)['size'];
        }
        $size = (isset($size)) ? $size : null;
        return $size;
    }

    /**
     * @throws \InvalidArgumentException For invalid options
     */
    private function verifyOptions(array &$options)
    {
        // Verify the name and value type of each option
        $optionsValid = true;
        foreach ($options as $key => $value) {
            switch (true) {
                case ($key === 'isText' and !is_bool($value)): // fall-through
                case ($key === 'mode' and !is_string($value)):
                case (!preg_match('/isText|mode/', $key)):
                    $optionsValid = false;
                    break;
            }
        }
        if (!$optionsValid) {
            throw new \InvalidArgumentException("Invalid options");
        }
        // Set options to default values if they are unset
        if (!isset($options['isText'])) {
            $options['isText'] = false;
        }
        if (isset($options['mode']) && !preg_match(self::ALLOWED_MODES, $options['mode'])) {
            throw new \InvalidArgumentException("Invalid mode option");
        } elseif (!isset($options['mode'])) {
            $options['mode'] = 'r+';
        }
    }

    /**
     * Determines if the wrapped stream is readable or writable
     *
     * @param string $type One of the values: "readable", "writable"
     * 
     * @return boolean
     */
    private function isAccessType(string $type)
    {
        $mode = $this->metadata['mode'];
        switch ($type) {
            case 'readable':
                $result = (bool) preg_match(self::BOTH_READABLE_WRITABLE, $mode) or preg_match(self::READABLE_ONLY, $mode);
                break;
            case 'writable':
                $result = (bool) preg_match(self::BOTH_READABLE_WRITABLE, $mode) or preg_match(self::WRITABLE_ONLY, $mode);
                break;
        }
        return $result;
    }
}
