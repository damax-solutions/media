<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Common\Application\AsArrayTrait;

class CreateMedia
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
