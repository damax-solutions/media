<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use Ramsey\Uuid\UuidInterface;

interface MediaRepository
{
    public function nextId(): UuidInterface;

    public function byId(UuidInterface $id): ?Media;

    public function save(Media $media): void;
}
