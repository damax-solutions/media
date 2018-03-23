<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

class SymfonyExtensionGuesser implements ExtensionGuesser
{
    private $guesser;

    public function __construct(ExtensionGuesserInterface $guesser = null)
    {
        $this->guesser = $guesser ?? new MimeTypeExtensionGuesser();
    }

    public function guess(string $mimeType): ?string
    {
        return $this->guesser->guess($mimeType);
    }
}
