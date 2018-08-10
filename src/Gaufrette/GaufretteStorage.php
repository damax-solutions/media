<?php

declare(strict_types=1);

namespace Damax\Media\Gaufrette;

use Damax\Media\Domain\Exception\StorageFailure;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Storage\AbstractStorage;
use Damax\Media\Domain\Storage\Keys\Keys;
use Damax\Media\Type\Types;
use Gaufrette\Exception as GaufretteException;
use Gaufrette\FilesystemMap;
use RuntimeException;

final class GaufretteStorage extends AbstractStorage
{
    private $filesystems;

    public function __construct(Types $types, Keys $keys, FilesystemMap $filesystems)
    {
        parent::__construct($types, $keys);

        $this->filesystems = $filesystems;
    }

    protected function readFile(File $file): string
    {
        return $this->filesystems
            ->get($file->storage())
            ->get($file->key())
            ->getContent()
        ;
    }

    protected function streamFile(File $file)
    {
        return fopen('gaufrette://' . $file->storage() . '/' . $file->key(), 'rb');
    }

    protected function deleteFile(File $file): void
    {
        $this->filesystems
            ->get($file->storage())
            ->delete($file->key())
        ;
    }

    protected function writeFile(string $key, string $storage, $stream): void
    {
        if (!$this->filesystems->has($storage)) {
            throw StorageFailure::unsupported($storage);
        }

        try {
            $this->filesystems
                ->get($storage)
                ->write($key, stream_get_contents($stream))
            ;
        } catch (GaufretteException | RuntimeException $e) {
            throw StorageFailure::invalidWrite($key, $e);
        }
    }
}
