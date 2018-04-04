<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use JsonSerializable;

final class Metadata implements JsonSerializable
{
    private $data;

    public static function blank(): self
    {
        return self::fromArray([]);
    }

    public static function fromArray(array $data): self
    {
        return new self($data);
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

    public function add(array $data): self
    {
        return new self(array_merge($this->data, $data));
    }

    public function merge(self $metadata): self
    {
        return new self(array_merge($this->data, $metadata->data));
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }

    private function __construct(array $data)
    {
        $this->data = $data;
    }
}
