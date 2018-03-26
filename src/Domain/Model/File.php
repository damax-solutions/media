<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

final class File extends MediaInfo
{
    private $key;
    private $storage;

    public function __construct(string $mimeType, int $size, string $key, string $storage)
    {
        parent::__construct($mimeType, $size);

        $this->key = $key;
        $this->storage = $storage;
    }

    public function defined(): bool
    {
        return (bool) $this->key;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function filename(): ?string
    {
        return pathinfo($this->key, PATHINFO_FILENAME);
    }

    public function extension(): ?string
    {
        return pathinfo($this->key, PATHINFO_EXTENSION);
    }

    public function storage(): string
    {
        return $this->storage;
    }
}
