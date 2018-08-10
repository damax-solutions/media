<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Exception;

use RuntimeException;

final class InvalidFile extends RuntimeException
{
    public static function notUploaded(): self
    {
        return new self('File not uploaded');
    }

    public static function unmatchedInfo(): self
    {
        return new self('Unmatched file info.');
    }

    public static function unsupportedMimeType(string $mimeType): self
    {
        return new self(sprintf('File with mime type "%s" is not supported.', $mimeType));
    }
}
