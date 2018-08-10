<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\FileInfo;

final class FileFactory
{
    public function createPdf(): File
    {
        return new File('xyz/abc/filename.pdf', 's3', new FileInfo('application/pdf', 1024));
    }

    public function createPng(): File
    {
        return new File('xyz/abc/filename.png', 's3', new FileInfo('image/png', 1024));
    }
}
