<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Image;

use Damax\Media\Domain\Model\Media;

interface UrlBuilder
{
    public function build(Media $media, array $params): string;
}
