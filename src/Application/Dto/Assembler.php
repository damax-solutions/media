<?php

declare(strict_types=1);

namespace Damax\Media\Application\Dto;

use Damax\Media\Domain\Model\File;
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
        $dto->file = $this->toFileDto($media->file());
        $dto->createdAt = $media->createdAt();
        $dto->updatedAt = $media->updatedAt();

        return $dto;
    }

    public function toFileDto(File $file): FileDto
    {
        $dto = new FileDto();

        $dto->mimeType = $file->mimeType();
        $dto->size = $file->size();

        return $dto;
    }
}
