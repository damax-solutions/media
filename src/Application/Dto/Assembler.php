<?php

declare(strict_types=1);

namespace Damax\Media\Application\Dto;

use Damax\Media\Domain\Model\Media;

class Assembler
{
    public function toMediaDto(Media $media): MediaDto
    {
        $dto = new MediaDto();

        $dto->id = $media->id();
        $dto->status = $media->status();
        $dto->type = $media->type();
        $dto->name = $media->name();
        $dto->mimeType = $media->info()->mimeType();
        $dto->size = $media->info()->size();
        $dto->createdAt = $media->createdAt();
        $dto->updatedAt = $media->updatedAt();

        return $dto;
    }
}
