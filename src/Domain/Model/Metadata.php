<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use JsonSerializable;

final class Metadata implements JsonSerializable
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
