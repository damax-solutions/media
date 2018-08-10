<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage\Keys;

interface Keys
{
    public function nextKey($context = []): string;
}
