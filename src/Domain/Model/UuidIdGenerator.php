<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use Ramsey\Uuid\Uuid;

final class UuidIdGenerator implements IdGenerator
{
    public function mediaId(): MediaId
    {
        return MediaId::fromString((string) Uuid::uuid4());
    }
}
