<?php

declare(strict_types=1);

namespace Damax\Media\Flysystem;

use League\Flysystem\FilesystemInterface;
use Psr\Container\ContainerInterface;

class ContainerRegistry implements Registry
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function has(string $name): bool
    {
        return $this->container->has(sprintf('oneup_flysystem.%s_filesystem', $name));
    }

    public function get(string $name): FilesystemInterface
    {
        return $this->container->get(sprintf('oneup_flysystem.%s_filesystem', $name));
    }
}
