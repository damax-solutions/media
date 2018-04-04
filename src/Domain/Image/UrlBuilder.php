<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Image;

interface UrlBuilder
{
    public function build(string $mediaId, array $params): string;
}
