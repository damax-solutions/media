<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_file()
    {
        $file = (new FileFactory())->createPdf();

        $this->assertEquals('xyz/abc/filename.pdf', $file->key());
        $this->assertEquals('s3', $file->storage());
        $this->assertEquals('filename.pdf', $file->basename());
        $this->assertEquals('pdf', $file->extension());
        $this->assertEquals('application/pdf', $file->info()->mimeType());
        $this->assertEquals(1024, $file->info()->fileSize());
    }
}
