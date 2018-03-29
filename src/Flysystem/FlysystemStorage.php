<?php

declare(strict_types=1);

namespace Damax\Media\Flysystem;

use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Storage\AbstractStorage;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\StorageFailure;
use Damax\Media\Type\Types;
use Throwable;

class FlysystemStorage extends AbstractStorage
{
    private $registry;

    public function __construct(Registry $registry, Types $types, Keys $keys)
    {
        parent::__construct($types, $keys);

        $this->registry = $registry;
    }

    protected function readFile(File $file): string
    {
        return $this->registry
            ->get($file->storage())
            ->read($file->key())
        ;
    }

    protected function streamFile(File $file)
    {
        return $this->registry
            ->get($file->storage())
            ->readStream($file->key())
        ;
    }

    protected function writeFile(string $key, string $storage, $stream): void
    {
        if (!$this->registry->has($storage)) {
            throw InvalidMediaInput::unsupportedStorage($storage);
        }

        try {
            $result = $this->registry
                ->get($storage)
                ->writeStream($key, $stream)
            ;
        } catch (Throwable $e) {
            throw StorageFailure::invalidWrite($key, $e);
        }

        if (!$result) {
            throw StorageFailure::invalidWrite($key);
        }
    }
}
