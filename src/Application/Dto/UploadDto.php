<?php

declare(strict_types=1);

namespace Damax\Media\Application\Dto;

use ArrayAccess;
use Damax\Common\Application\AsArrayTrait;

final class UploadDto implements ArrayAccess
{
    use AsArrayTrait;

    /**
     * @var string
     */
    public $mimeType;

    /**
     * @var int
     */
    public $fileSize;

    /**
     * @var resource
     */
    public $stream;
}
