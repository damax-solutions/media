<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

interface MediaFactory
{
    public function create($data, User $creator = null): Media;
}
