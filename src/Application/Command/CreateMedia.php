<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Media\Application\Dto\MediaCreationDto;

final class CreateMedia extends MediaCommand
{
    private $media;

    public function __construct(string $mediaId, MediaCreationDto $media)
    {
        parent::__construct($mediaId);

        $this->media = $media;
    }

    public function media(): MediaCreationDto
    {
        return $this->media;
    }
}
