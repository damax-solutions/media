<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

final class File
{
    private $mimeType;
    private $size;
    private $key;
    private $storage;

    public static function metadata($data): self
    {
        return new self($data['mime_type'], $data['size']);
    }

    public function __construct(string $mimeType, int $size, string $key = null, string $storage = null)
    {
        $this->mimeType = $mimeType;
        $this->size = $size;
        $this->key = $key;
        $this->storage = $storage;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function key(): ?string
    {
        return $this->key;
    }

    public function storage(): ?string
    {
        return $this->storage;
    }

    public function sameAs(File $file): bool
    {
        return $this->mimeType === $file->mimeType && $this->size = $file->size && $this->key === $file->key && $this->storage === $file->storage;
    }

    public function store(string $key, string $storage): self
    {
        return new self($this->mimeType, $this->size, $key, $storage);
    }
}
