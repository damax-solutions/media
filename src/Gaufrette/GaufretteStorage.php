<?php

declare(strict_types=1);

namespace Damax\Media\Gaufrette;

use Assert\Assert;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\InvalidMediaInput;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Domain\Storage\StorageFailure;
use Damax\Media\Type\Configuration;
use Gaufrette\Exception as GaufretteException;
use Gaufrette\FilesystemMap;
use RuntimeException;

class GaufretteStorage implements Storage
{
    private $filesystems;
    private $types;
    private $keys;

    public function __construct(FilesystemMap $filesystems, Configuration $types, Keys $keys)
    {
        $this->filesystems = $filesystems;
        $this->types = $types;
        $this->keys = $keys;
    }

    public function write(Media $media, $context = []): File
    {
        Assert::that($context)
            ->keyIsset('file')
            ->keyIsset('stream')
        ;

        $file = File::metadata($context['file']);

        if (!$media->file()->sameAs($file)) {
            throw new InvalidMediaInput('Invalid file.');
        }

        $storage = $this->types->definition($media->type())->storage();

        if (!$this->filesystems->has($storage)) {
            throw InvalidMediaInput::unsupportedStorage($storage);
        }

        $key = $this->keys->generateKey($context['file']);

        try {
            $this->filesystems
                ->get($storage)
                ->write($key, stream_get_contents($context['stream']))
            ;
        } catch (GaufretteException | RuntimeException $e) {
            throw StorageFailure::invalidWrite($key, $e);
        }

        return $file->store($key, $storage);
    }
}
