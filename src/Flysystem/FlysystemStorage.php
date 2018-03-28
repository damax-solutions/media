<?php

declare(strict_types=1);

namespace Damax\Media\Flysystem;

use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Storage\AbstractStorage;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\StorageFailure;
use Damax\Media\Type\Types;
use League\Flysystem\FilesystemNotFoundException;
use League\Flysystem\MountManager;
use Throwable;

class FlysystemStorage extends AbstractStorage
{
    private $mountManager;

    public function __construct(MountManager $mountManager, Types $types, Keys $keys)
    {
        parent::__construct($types, $keys);

        $this->mountManager = $mountManager;
    }

    protected function readFile(File $file): string
    {
        return $this->mountManager
            ->getFilesystem($file->storage())
            ->read($file->key())
        ;
    }

    protected function streamFile(File $file)
    {
        return $this->mountManager
            ->getFilesystem($file->storage())
            ->readStream($file->key())
        ;
    }

    protected function writeFile(string $key, string $storage, $stream): void
    {
        try {
            $result = $this->mountManager
                ->getFilesystem($storage)
                ->writeStream($key, $stream)
            ;
        } catch (FilesystemNotFoundException $e) {
            throw InvalidMediaInput::unsupportedStorage($storage);
        } catch (Throwable $e) {
            throw StorageFailure::invalidWrite($key, $e);
        }

        if (!$result) {
            throw StorageFailure::invalidWrite($key);
        }
    }
}
