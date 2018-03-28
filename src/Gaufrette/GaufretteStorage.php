<?php

declare(strict_types=1);

namespace Damax\Media\Gaufrette;

use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Storage\AbstractStorage;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\StorageFailure;
use Damax\Media\Type\Types;
use Gaufrette\Exception as GaufretteException;
use Gaufrette\FilesystemMap;
use RuntimeException;

class GaufretteStorage extends AbstractStorage
{
    private $filesystems;

    public function __construct(FilesystemMap $filesystems, Types $types, Keys $keys)
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

    protected function writeFile(string $key, string $storage, $stream): void
    {
        if (!$this->filesystems->has($storage)) {
            throw InvalidMediaInput::unsupportedStorage($storage);
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
