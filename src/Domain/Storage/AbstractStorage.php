<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

use Assert\Assert;
use Damax\Media\Domain\Exception\FileAlreadyExists;
use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Domain\Exception\StorageFailure;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\FileInfo;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Storage\Keys\Keys;
use Damax\Media\Type\Types;

abstract class AbstractStorage implements Storage
{
    private $types;
    private $keys;

    public function __construct(Types $types, Keys $keys)
    {
        $this->types = $types;
        $this->keys = $keys;
    }

    public function read(Media $media): string
    {
        return $this->readFile($media->file());
    }

    public function streamTo(Media $media, $stream): void
    {
        stream_copy_to_stream($this->streamFile($media->file()), $stream);
    }

    public function dump(Media $media, string $filename): void
    {
        file_put_contents($filename, $this->read($media));
    }

    public function delete(Media $media): void
    {
        if ($media->uploaded()) {
            $this->deleteFile($media->file());
        }
    }

    public function write(Media $media, $context = []): File
    {
        Assert::that($context)
            ->keyIsset('mime_type')
            ->keyIsset('file_size')
            ->keyIsset('stream')
        ;

        $info = FileInfo::fromArray($context);

        if (!$media->matchesInfo($info)) {
            throw InvalidFile::unmatchedInfo();
        }

        if ($media->uploaded()) {
            throw new FileAlreadyExists();
        }

        $key = $this->keys->nextKey([
            'prefix' => $media->type(),
            'mime_type' => $info->mimeType(),
        ]);

        $storage = $this->types
            ->definition($media->type())
            ->storage()
        ;

        $this->writeFile($key, $storage, $context['stream']);

        return new File($key, $storage, $info);
    }

    abstract protected function readFile(File $file): string;

    /**
     * @return resource
     */
    abstract protected function streamFile(File $file);

    abstract protected function deleteFile(File $file): void;

    /**
     * @throws StorageFailure
     */
    abstract protected function writeFile(string $key, string $storage, $stream): void;
}
