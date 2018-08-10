<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage\Guesser;

interface Guesser
{
    public function guessExtension(string $mimeType): ?string;
}
