<?php

declare(strict_types=1);

namespace Damax\Media\Domain;

class FileFormatter
{
    private const UNITS = ['B', 'KB', 'MB', 'GB'];
    private const PRECISION = [0, 0, 1, 2];

    public function formatSize(int $value): string
    {
        $len = count(self::UNITS) - 1;

        for ($index = 0; $value >= 1024 && $index < $len; ++$index) {
            $value = $value / 1024;
        }

        return number_format($value, self::PRECISION[$index], '.', '') . ' ' . self::UNITS[$index];
    }
}
