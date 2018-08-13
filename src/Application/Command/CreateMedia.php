<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Media\Application\Dto\MediaCreationDto;

final class CreateMedia
{
    private $media;

    public function __construct(MediaCreationDto $media)
    {
        $this->media = $media;
    }

    public function media(): MediaCreationDto
    {
        return $this->media;
    }
}
