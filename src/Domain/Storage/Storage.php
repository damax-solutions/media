<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

use Damax\Media\Domain\Exception\FileAlreadyExists;
use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Domain\Exception\StorageFailure;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\Media;

interface Storage
{
    /**
     * @throws InvalidFile
     */
    public function read(Media $media): string;

    /**
     * @throws InvalidFile
     */
    public function streamTo(Media $media, $stream): void;

    /**
     * @throws InvalidFile
     */
    public function dump(Media $media, string $filename): void;

    /**
     * @throws InvalidFile
     */
    public function delete(Media $media): void;

    /**
     * @throws InvalidFile
     * @throws FileAlreadyExists
     * @throws StorageFailure
     */
    public function write(Media $media, $context = []): File;
}
