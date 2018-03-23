<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Common\Application\AsArrayTrait;
use Damax\Media\Application\Dto\FileDto;

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
     * @var FileDto
     */
    public $file;
}
