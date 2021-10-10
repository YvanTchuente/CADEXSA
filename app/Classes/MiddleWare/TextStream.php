<?php

declare(strict_types=1);

namespace Classes\MiddleWare;

use Psr\Http\Message\StreamInterface;

class TextStream extends Stream implements StreamInterface
{
    /**
     * @param string $content Text to wrap as a stream
     */
    public function __construct(string $content)
    {
        $stream = fopen("php://temp", "w+");
        fwrite($stream, $content);
        rewind($stream);
        $this->stream = $stream;
        $this->metadata = stream_get_meta_data($this->stream);
        $this->uri = $this->metadata['uri'];
        $this->seekable = $this->metadata['seekable'];
        $this->size = $this->setSize($stream);
        $this->readable = true;
        $this->writable = true;
    }
}
