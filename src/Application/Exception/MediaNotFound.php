<?php

declare(strict_types=1);

namespace Damax\Media\Application\Exception;

use Damax\Media\Domain\Model\MediaId;
use RuntimeException;

final class MediaNotFound extends RuntimeException
{
    public static function byId(MediaId $id): self
    {
        return new self(sprintf('Media by id "%s" not found.', (string) $id));
    }
}
