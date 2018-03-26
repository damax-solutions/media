<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Exception;

use DomainException;

class MediaNotReadable extends DomainException
{
    public static function missingFile(): self
    {
        return new self('File is missing.');
    }
}
