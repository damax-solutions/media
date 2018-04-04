<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

use Damax\Media\Domain\Exception\FileAlreadyExists;
use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Exception\MediaNotReadable;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\Media;

interface Storage
{
    /**
     * @throws MediaNotReadable
     */
    public function read(Media $media): string;

    /**
     * @throws MediaNotReadable
     */
    public function streamTo(Media $media, $stream): void;

    /**
     * @throws MediaNotReadable
     */
    public function dump(Media $media, string $filename): void;

    public function remove(Media $media): void;

    /**
     * @throws FileAlreadyExists
     * @throws InvalidMediaInput
     * @throws StorageFailure
     */
    public function write(Media $media, $context = []): File;
}
