<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\FileInfo;
use PHPUnit\Framework\TestCase;

class FileInfoTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_from_array()
    {
        $info = FileInfo::fromArray([
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ]);

        $this->assertEquals('application/pdf', $info->mimeType());
        $this->assertEquals(1024, $info->fileSize());
    }

    /**
     * @test
     */
    public function it_creates_file_info()
    {
        $info = new FileInfo('application/pdf', 1024);

        $this->assertEquals('application/pdf', $info->mimeType());
        $this->assertEquals(1024, $info->fileSize());
    }

    /**
     * @test
     */
    public function it_compares_file_info()
    {
        $info1 = new FileInfo('application/pdf', 1024);
        $info2 = new FileInfo('application/pdf', 1024);
        $info3 = new FileInfo('application/json', 1024);

        $this->assertNotSame($info1, $info2);
        $this->assertTrue($info1->sameAs($info2));
        $this->assertFalse($info1->sameAs($info3));
    }

    /**
     * @test
     */
    public function it_identifies_as_image()
    {
        $this->assertTrue((new FileInfo('image/png', 1024))->image());
        $this->assertTrue((new FileInfo('image/jpg', 1024))->image());
        $this->assertFalse((new FileInfo('text/plain', 1024))->image());
    }
}
