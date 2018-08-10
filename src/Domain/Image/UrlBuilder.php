<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Image;

use Damax\Media\Domain\Model\MediaId;

interface UrlBuilder
{
    public function build(MediaId $mediaId, array $params): string;
}
