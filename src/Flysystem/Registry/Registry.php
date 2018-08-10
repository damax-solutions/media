<?php

declare(strict_types=1);

namespace Damax\Media\Flysystem\Registry;

use League\Flysystem\FilesystemInterface;

interface Registry
{
    public function has(string $name): bool;

    public function get(string $name): FilesystemInterface;
}
