<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Exception;

use RuntimeException;

final class InvalidMediaInput extends RuntimeException
{
    public static function unsupportedMimeType(string $mimeType): self
    {
        return new self(sprintf('Media with mime type "%s" is not supported.', $mimeType));
    }
}
