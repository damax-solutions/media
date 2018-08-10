<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

final class File
{
    private $key;
    private $storage;
    private $info;

    public function __construct(string $key, string $storage, FileInfo $info)
    {
        $this->key = $key;
        $this->storage = $storage;
        $this->info = $info;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function storage(): string
    {
        return $this->storage;
    }

    public function info(): FileInfo
    {
        return $this->info;
    }

    public function basename(): ?string
    {
        return pathinfo($this->key, PATHINFO_BASENAME);
    }

    public function extension(): ?string
    {
        return pathinfo($this->key, PATHINFO_EXTENSION);
    }
}
