<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_file()
    {
        $file = (new FileFactory())->createPdf();

        $this->assertEquals('application/pdf', $file->mimeType());
        $this->assertEquals(1024, $file->size());
        $this->assertEquals('xyz/abc/filename.pdf', $file->key());
        $this->assertEquals('s3', $file->storage());
        $this->assertEquals('filename.pdf', $file->basename());
        $this->assertEquals('pdf', $file->extension());
    }

    /**
     * @test
     */
    public function it_checks_if_file_is_defined()
    {
        $file = new File('application/pdf', 1024, '', 's3');

        $this->assertFalse($file->defined());
    }
}
