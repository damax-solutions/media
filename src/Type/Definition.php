<?php

declare(strict_types=1);

namespace Damax\Media\Type;

final class Definition
{
    private $storage;
    private $maxFileSize;
    private $mimeTypes;

    public function __construct(string $storage, int $maxFileSize, array $mimeTypes)
    {
        $this->storage = $storage;
        $this->maxFileSize = $maxFileSize;
        $this->mimeTypes = $mimeTypes;
    }

    public function storage(): string
    {
        return $this->storage;
    }

    public function maxFileSize(): int
    {
        return $this->maxFileSize;
    }

    public function mimeTypes(): array
    {
        return $this->mimeTypes;
    }
}
