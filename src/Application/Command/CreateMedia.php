<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Media\Application\Dto\NewMediaDto;

final class CreateMedia extends MediaCommand
{
    private $media;

    public function __construct(string $mediaId, NewMediaDto $media)
    {
        parent::__construct($mediaId);

        $this->media = $media;
    }

    public function media(): NewMediaDto
    {
        return $this->media;
    }
}
