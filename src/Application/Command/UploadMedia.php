<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Common\Application\AsArrayTrait;
use Damax\Media\Application\Dto\FileDto;

class UploadMedia extends MediaCommand
{
    use AsArrayTrait;

    /**
     * @var FileDto
     */
    public $file;

    /**
     * @var resource
     */
    public $stream;
}
