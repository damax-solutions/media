<?php

declare(strict_types=1);

namespace Damax\Media\Gaufrette;

use Assert\Assert;
use Damax\Media\Domain\Exception\FileAlreadyExists;
use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Exception\MediaNotReadable;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaInfo;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Domain\Storage\StorageFailure;
use Damax\Media\Type\Types;
use Gaufrette\Exception as GaufretteException;
use Gaufrette\FilesystemMap;
use RuntimeException;

class GaufretteStorage implements Storage
{
    private $filesystems;
    private $types;
    private $keys;

    public function __construct(FilesystemMap $filesystems, Types $types, Keys $keys)
    {
        $this->filesystems = $filesystems;
        $this->types = $types;
        $this->keys = $keys;
    }

    public function read(Media $media): string
    {
        if (null === $file = $media->file()) {
            throw MediaNotReadable::missingFile();
        }

        return $this->filesystems
            ->get($file->storage())
            ->get($file->key())
            ->getContent()
        ;
    }

    public function streamTo(Media $media, $stream): void
    {
        if (null === $file = $media->file()) {
            throw MediaNotReadable::missingFile();
        }

        $source = fopen('gaufrette://' . $file->storage() . '/' . $file->key(), 'rb');

        stream_copy_to_stream($source, $stream);

        rewind($stream);
    }

    public function dump(Media $media, string $filename): void
    {
        file_put_contents($filename, $this->read($media));
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

        if (!$this->filesystems->has($storage)) {
            throw InvalidMediaInput::unsupportedStorage($storage);
        }

        // Prefix all keys with media type?
        $key = $type . '/' . $this->keys->nextKey(['mime_type' => $context['mime_type']]);

        try {
            $this->filesystems
                ->get($storage)
                ->write($key, stream_get_contents($context['stream']))
            ;
        } catch (GaufretteException | RuntimeException $e) {
            throw StorageFailure::invalidWrite($key, $e);
        }

        return new File($info->mimeType(), $info->size(), $key, $storage);
    }
}
