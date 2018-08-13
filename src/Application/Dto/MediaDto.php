<?php

declare(strict_types=1);

namespace Damax\Media\Application\Dto;

use DateTime;

final class MediaDto
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
     * @var string
     */
    public $mimeType;

    /**
     * @var int
     */
    public $fileSize;

    /**
     * @var DateTime
     */
    public $createdAt;

    /**
     * @var DateTime
     */
    public $updatedAt;

    /**
     * @var string[]
     */
    public $metadata;
}
