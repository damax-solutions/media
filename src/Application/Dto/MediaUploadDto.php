<?php

declare(strict_types=1);

namespace Damax\Media\Application\Dto;

use ArrayAccess;
use Damax\Common\Application\AsArrayTrait;

class MediaUploadDto implements ArrayAccess
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
