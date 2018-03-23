<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

use RuntimeException;
use Throwable;

class StorageFailure extends RuntimeException
{
    public static function invalidWrite(string $key, Throwable $e = null): self
    {
        return new self(sprintf('Unable to write key "%s".', $key), 0, $e);
    }
}
