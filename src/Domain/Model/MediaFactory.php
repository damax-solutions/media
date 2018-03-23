<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

interface MediaFactory
{
    /**
     * @throws InvalidMediaInput
     */
    public function create($data, User $creator = null): Media;
}
