<?php

declare(strict_types=1);

namespace Damax\Media\Application\Query;

use Damax\Media\Domain\Model\MediaId;

abstract class MediaQuery
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
