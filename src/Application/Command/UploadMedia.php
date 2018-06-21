<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Media\Application\Dto\MediaUploadDto;

class UploadMedia
{
    /**
     * @var string
     */
    public $mediaId;

    /**
     * @var MediaUploadDto
     */
    public $upload;
}
