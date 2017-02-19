<?php
/**
 * Fixin Framework
 *
 * @copyright  Copyright (c) 2016 Attila Jenei
 */

namespace Fixin\Base\FileSystem;

use Fixin\Resource\ResourceInterface;

interface FileSystemInterface extends ResourceInterface
{
    /**
     * Delete file
     */
    public function delete(string $filename): bool;

    /**
     * Get extension of a filename
     */
    public function getExtension(string $path): ?string;

    /**
     * Get the contents of a file
     */
    public function getFileContents(string $filename): string;

    /**
     * Get the contents of a file with lock
     */
    public function getFileContentsWithLock(string $filename): string;

    /**
     * Get file size
     */
    public function getFileSize(string $filename): ?int;

    /**
     * Get real path
     */
    public function getRealPath(string $path): ?string;

    /**
     * Include files recursive
     */
    public function includeFilesRecursive(string $path, string $extension): FileSystemInterface;

    /**
     * Determine if path is a file
     */
    public function isDirectory(string $path): bool;

    /**
     * Determine if existence
     */
    public function isExisting(string $path): bool;

    /**
     * Determine if path is a file
     */
    public function isFile(string $path): bool;

    /**
     * Determine if path is a file and is readable
     */
    public function isReadable(string $filename): bool;

    /**
     * Put the contents of a file
     */
    public function putFileContents(string $filename, string $contents): ?int;

    /**
     * Put the contents of a file with lock
     */
    public function putFileContentsWithLock(string $filename, string $contents): ?int;
}
