<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use RuntimeException;

class InvalidMediaInput extends RuntimeException
{
    public static function unregisteredType(string $type): self
    {
        return new self(sprintf('Media type "%s" is not registered.', $type));
    }

    public static function unsupportedStorage(string $storage): self
    {
        return new self(sprintf('Storage "%s" is not supported.', $storage));
    }
}
