<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Storage;

class RandomKeys implements Keys
{
    private $guesser;
    private $keyLength;

    public function __construct(ExtensionGuesser $guesser, int $keyLength)
    {
        $this->guesser = $guesser;
        $this->keyLength = $keyLength;
    }

    public function nextKey($context = []): string
    {
        $key = bin2hex(random_bytes(intdiv($this->keyLength, 2)));
        $ext = '';

        if (isset($context['mime_type']) && ($ext = $this->guesser->guess($context['mime_type']))) {
            $ext = '.' . $ext;
        }

        return date('Y/m/d', time()) . '/' . $key . $ext;
    }
}
