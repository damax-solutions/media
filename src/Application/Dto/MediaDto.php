<?php

declare(strict_types=1);

namespace Damax\Media\Application\Dto;

use DateTimeInterface;

class MediaDto
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $status;

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

    /**
     * @var DateTimeInterface
     */
    public $createdAt;

    /**
     * @var DateTimeInterface
     */
    public $updatedAt;
}
