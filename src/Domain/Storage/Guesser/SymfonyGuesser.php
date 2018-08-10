<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage\Guesser;

use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

final class SymfonyGuesser implements Guesser
{
    private $mimeTypes;

    public function __construct(ExtensionGuesserInterface $mimeTypes = null)
    {
        $this->mimeTypes = $mimeTypes ?? new MimeTypeExtensionGuesser();
    }

    public function guessExtension(string $mimeType): ?string
    {
        return $this->mimeTypes->guess($mimeType);
    }
}
