<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

interface MediaRepository
{
    public function byId(MediaId $id): ?Media;

    public function add(Media $media): void;

    public function update(Media $media): void;

    public function remove(Media $media): void;
}
