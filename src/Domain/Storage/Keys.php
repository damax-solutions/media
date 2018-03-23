<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

interface Keys
{
    public function nextKey($context = []): string;
}
