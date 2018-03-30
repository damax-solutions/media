<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\File;

class FileFactory
{
    public function createPdf(): File
    {
        return new File('application/pdf', 1024, 'xyz/abc/filename.pdf', 's3');
    }

    public function createPng(): File
    {
        return new File('image/png', 1024, 'xyz/abc/filename.png', 's3');
    }
}
