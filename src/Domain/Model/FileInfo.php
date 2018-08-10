<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

final class FileInfo
{
    private $mimeType;
    private $fileSize;

    public static function fromArray($data): self
    {
        return new self($data['mime_type'], $data['file_size']);
    }

    public function __construct(string $mimeType, int $fileSize)
    {
        $this->mimeType = $mimeType;
        $this->fileSize = $fileSize;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function fileSize(): int
    {
        return $this->fileSize;
    }

    public function image(): bool
    {
        return 0 === strpos($this->mimeType, 'image/');
    }

    public function sameAs(self $info): bool
    {
        return $this->mimeType === $info->mimeType && $this->fileSize === $info->fileSize;
    }
}
