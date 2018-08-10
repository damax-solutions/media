<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Exception;

use RuntimeException;
use Throwable;

final class StorageFailure extends RuntimeException
{
    public static function unsupported(string $storage): self
    {
        return new self(sprintf('Storage "%s" is not supported.', $storage));
    }

    public static function invalidWrite(string $key, Throwable $e = null): self
    {
        return new self(sprintf('Unable to write key "%s".', $key), 0, $e);
    }
}
