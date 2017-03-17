<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\Stream;

class Stream implements StreamInterface
{
    protected const
        EXCEPTION_INVALID_STREAM = 'Invalid stream',
        EXCEPTION_INVALID_STREAM_REFERENCE = 'Invalid stream reference',
        EXCEPTION_NOT_READABLE = 'Stream is not readable',
        EXCEPTION_NOT_SEEKABLE = 'Stream is not seekable',
        EXCEPTION_NOT_WRITABLE = 'Stream is not writable',
        EXCEPTION_READ_ERROR = 'Stream read error',
        EXCEPTION_SEEK_ERROR = 'Stream seek error',
        EXCEPTION_UNABLE_TO_DETERMINE_POSITION = 'Unable to determine position',
        EXCEPTION_WRITE_ERROR = 'Stream write error';

    /**
     * @var resource
     */
    protected $resource;

    /**
     * @param string|resource $stream
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($stream, string $mode = 'r')
    {
        // By reference
        if (is_string($stream)) {
            $stream = $this->resourceByReference($stream, $mode);
        }

        // Stream
        if (is_resource($stream) && get_resource_type($stream) === 'stream') {
            $this->resource = $stream;

            return;
        }

        throw new Exception\InvalidArgumentException(static::EXCEPTION_INVALID_STREAM);
    }

    public function __destruct()
    {
        fclose($this->resource);
    }

    public function __toString(): string
    {
        $this->rewind();
        return $this->getRemainingContents();
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function getCurrentPosition(): int
    {
        $result = ftell($this->resource);

        if (is_int($result)) {
            return $result;
        }

        throw new Exception\RuntimeException(static::EXCEPTION_UNABLE_TO_DETERMINE_POSITION);
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function getRemainingContents(): string
    {
        if (!$this->isReadable()) {
            throw new Exception\RuntimeException(static::EXCEPTION_NOT_READABLE);
        }

        if (false !== $result = stream_get_contents($this->resource)) {
            return $result;
        }

        throw new Exception\RuntimeException(static::EXCEPTION_READ_ERROR);
    }

    public function getMetadata(string $key = null)
    {
        $metadata = stream_get_meta_data($this->resource);

        if (isset($key)) {
            return $metadata[$key] ?? null;
        }

        return $metadata;
    }

    public function getSize(): ?int
    {
        return fstat($this->resource)['size'] ?? null;
    }

    public function isEof(): bool
    {
        return feof($this->resource);
    }

    public function isReadable(): bool
    {
        $mode = stream_get_meta_data($this->resource)['mode'];

        return strcspn($mode, 'r+') < strlen($mode);
    }

    public function isSeekable(): bool
    {
        return stream_get_meta_data($this->resource)['seekable'];
    }

    public function isWritable(): bool
    {
        $mode = stream_get_meta_data($this->resource)['mode'];

        return strcspn($mode, 'xwca+') < strlen($mode);
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function read(int $length): string
    {
        if (!$this->isReadable()) {
            throw new Exception\RuntimeException(static::EXCEPTION_NOT_READABLE);
        }

        if (false !== $result = fread($this->resource, $length)) {
            return $result;
        }

        throw new Exception\RuntimeException(static::EXCEPTION_READ_ERROR);
    }

    /**
     * Open resource
     *
     * @throws Exception\InvalidArgumentException
     */
    protected function resourceByReference(string $reference, string $mode)
    {
        $error = null;

        // Suppress warnings
        set_error_handler(function($e) use (&$error) {
            $error = $e;
        });

        // Open
        $stream = fopen($reference, $mode);

        restore_error_handler();

        if ($error) {
            throw new Exception\InvalidArgumentException(static::EXCEPTION_INVALID_STREAM_REFERENCE);
        }

        return $stream;
    }

    /**
     * @return static
     */
    public function rewind(): StreamInterface
    {
        return $this->seek(0);
    }

    /**
     * @throws Exception\RuntimeException
     * @return static
     */
    public function seek(int $offset, int $whence = SEEK_SET): StreamInterface
    {
        if (!$this->isSeekable()) {
            throw new Exception\RuntimeException(static::EXCEPTION_NOT_SEEKABLE);
        }

        if (fseek($this->resource, $offset, $whence) === 0) {
            return $this;
        }

        throw new Exception\RuntimeException(static::EXCEPTION_SEEK_ERROR);
    }

    /**
     * @throws Exception\RuntimeException
     */
    public function write(string $string): int
    {
        if (!$this->isWritable()) {
            throw new Exception\RuntimeException(static::EXCEPTION_NOT_WRITABLE);
        }

        if (false !== $result = fwrite($this->resource, $string)) {
            return $result;
        }

        throw new Exception\RuntimeException(static::EXCEPTION_WRITE_ERROR);
    }
}
