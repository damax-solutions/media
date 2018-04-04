<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

use Assert\Assert;
use Damax\Media\Domain\Exception\FileAlreadyExists;
use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Exception\MediaNotReadable;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaInfo;
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
        if (null === $file = $media->file()) {
            throw MediaNotReadable::missingFile();
        }

        return $this->readFile($media->file());
    }

    public function streamTo(Media $media, $stream): void
    {
        if (null === $file = $media->file()) {
            throw MediaNotReadable::missingFile();
        }

        stream_copy_to_stream($this->streamFile($file), $stream);
    }

    public function dump(Media $media, string $filename): void
    {
        file_put_contents($filename, $this->read($media));
    }

    public function remove(Media $media): void
    {
        if (null === $file = $media->file()) {
            return;
        }

        $this->removeFile($file);
    }

    public function write(Media $media, $context = []): File
    {
        Assert::that($context)
            ->keyIsset('mime_type')
            ->keyIsset('size')
            ->keyIsset('stream')
        ;

        if (null !== $media->file()) {
            throw new FileAlreadyExists('File already exists.');
        }

        $info = new MediaInfo($context['mime_type'], $context['size']);
        $type = $media->type();

        if (!$media->info()->sameAs($info)) {
            throw new InvalidMediaInput('Invalid file.');
        }

        if (!$this->types->hasDefinition($type)) {
            throw InvalidMediaInput::unregisteredType($type);
        }

        $storage = $this->types->definition($type)->storage();

        // Prefix all keys with media type?
        $key = $type . '/' . $this->keys->nextKey(['mime_type' => $context['mime_type']]);

        $this->writeFile($key, $storage, $context['stream']);

        return new File($info->mimeType(), $info->size(), $key, $storage);
    }

    abstract protected function readFile(File $file): string;

    /**
     * @return resource
     */
    abstract protected function streamFile(File $file);

    abstract protected function removeFile(File $file): void;

    abstract protected function writeFile(string $key, string $storage, $stream): void;
}
