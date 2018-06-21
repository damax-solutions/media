<?php

declare(strict_types=1);

namespace Damax\Media\Application\Dto;

use ArrayAccess;
use Damax\Common\Application\AsArrayTrait;

class MediaCreationDto implements ArrayAccess
{
    use AsArrayTrait;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $mimeType;

    /**
     * @var int
     */
    public $size;
}
