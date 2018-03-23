<?php

declare(strict_types=1);

namespace Damax\Media\Type;

final class Configuration
{
    private $definitions = [];

    public function __construct(array $definitions = [])
    {
        foreach ($definitions as $name => $definition) {
            $this->addDefinition($name, $definition);
        }
    }

    public function names(): array
    {
        return array_keys($this->definitions);
    }

    public function addDefinition(string $name, Definition $definition): void
    {
        $this->definitions[$name] = $definition;
    }

    public function definition(string $name): Definition
    {
        return $this->definitions[$name];
    }

    public function hasDefinition(string $name): bool
    {
        return isset($this->definitions[$name]);
    }
}
