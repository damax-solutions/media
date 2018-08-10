<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage\Keys;

class FixedKeys implements Keys
{
    private $keys;
    private $count;
    private $index = 0;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
        $this->count = count($keys);
    }

    public function nextKey($context = []): string
    {
        $key = $this->keys[$this->index];

        $this->index = $this->index + 1 === $this->count ? 0 : $this->index + 1;

        return $key;
    }
}
