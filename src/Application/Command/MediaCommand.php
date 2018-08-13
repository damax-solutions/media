<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Media\Domain\Model\MediaId;

abstract class MediaCommand
{
    private $mediaId;

    public function __construct(string $mediaId)
    {
        $this->mediaId = $mediaId;
    }

    public function mediaId(): MediaId
    {
        return MediaId::fromString($this->mediaId);
    }
}
