<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage\Keys;

use Damax\Media\Domain\Storage\Guesser\Guesser;

final class RandomKeys implements Keys
{
    private $guesser;
    private $keyLength;

    public function __construct(Guesser $guesser, int $keyLength)
    {
        $this->guesser = $guesser;
        $this->keyLength = $keyLength;
    }

    public function nextKey($context = []): string
    {
        $key = bin2hex(random_bytes(intdiv($this->keyLength, 2)));

        $ext = isset($context['mime_type']) ? $this->guesser->guessExtension($context['mime_type']) : null;

        $dir = ltrim(($context['prefix'] ?? '') . '/' . date('Y/m/d', time()), '/');

        return rtrim(sprintf('%s/%s.%s', $dir, $key, $ext), '.');
    }
}
