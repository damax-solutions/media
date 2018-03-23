<?php

declare(strict_types=1);

namespace Damax\Media\Application\Exception;

use RuntimeException;

class MediaNotFound extends RuntimeException
{
    public static function byId(string $id): self
    {
        return new self(sprintf('Media by id "%s" not found.', $id));
    }
}
