<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

class MediaInfo
{
    protected $mimeType;
    protected $size;

    public function __construct(string $mimeType, int $size)
    {
        $this->mimeType = $mimeType;
        $this->size = $size;
    }

    public function mimeType(): string
    {
        return $this->mimeType;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function sameAs(self $info): bool
    {
        return $this->mimeType === $info->mimeType && $this->size === $info->size;
    }
}
