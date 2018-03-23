<?php

declare(strict_types=1);

namespace Damax\Media\Application\Dto;

use Damax\Common\Application\AsArrayTrait;

class FileDto
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
}
