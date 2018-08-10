<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Flysystem\Registry;

use Damax\Media\Flysystem\Registry\Registry;
use League\Flysystem\FilesystemInterface;

final class TestRegistry implements Registry
{
    private $name;
    private $filesystem;

    public function __construct(string $name, FilesystemInterface $filesystem)
    {
        $this->name = $name;
        $this->filesystem = $filesystem;
    }

    public function has(string $name): bool
    {
        return $this->name === $name;
    }

    public function get(string $name): FilesystemInterface
    {
        return $this->filesystem;
    }

    public function changeName(string $name)
    {
        $this->name = $name;
    }

    public function changeFilesystem(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}
