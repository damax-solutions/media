<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\InvalidMediaInput;
use Damax\Media\Domain\Model\Media;

interface Storage
{
    /**
     * @throws InvalidMediaInput
     * @throws StorageFailure
     */
    public function write(Media $media, $context = []): File;
}
