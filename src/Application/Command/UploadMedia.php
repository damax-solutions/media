<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Common\Application\AsArrayTrait;

class UploadMedia extends MediaCommand
{
    use AsArrayTrait;

    /**
     * @var string
     */
    public $mimeType;

    /**
     * @var int
     */
    public $size;

    /**
     * @var resource
     */
    public $stream;
}
