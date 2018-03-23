<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

interface ExtensionGuesser
{
    public function guess(string $mimeType): ?string;
}
