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
}
