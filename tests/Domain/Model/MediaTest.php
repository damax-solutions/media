<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Common\Domain\Model\Metadata;
use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Domain\Model\UserId;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Damax\Media\Domain\Model\Media
 * @covers \Damax\Media\Domain\Exception\InvalidFile
 */
class MediaTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_media()
    {
        $media = new PendingImageMedia();

        $this->assertEquals('64c2c4b7-33f5-11e8-97f3-005056806fb2', (string) $media->id());
        $this->assertEquals('pending', $media->status());
        $this->assertEquals('image', $media->type());
        $this->assertEquals('Test PNG image', $media->name());
        $this->assertEquals('image/png', $media->info()->mimeType());
        $this->assertEquals(1024, $media->info()->fileSize());
        $this->assertEquals([], $media->metadata()->all());
        $this->assertInstanceOf(DateTimeImmutable::class, $media->createdAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $media->updatedAt());
        $this->assertNull($media->createdById());
        $this->assertNull($media->updatedById());
    }

    /**
     * @test
     */
    public function it_fails_to_retrieve_file()
    {
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage('File not uploaded');

        (new PendingImageMedia())->file();
    }

    /**
     * @test
     */
    public function it_retrieves_file()
    {
        $file = (new UploadedImageMedia())->file();

        $this->assertEquals('xyz/abc/filename.png', $file->key());
        $this->assertEquals('s3', $file->storage());
        $this->assertEquals(1024, $file->info()->fileSize());
        $this->assertEquals('image/png', $file->info()->mimeType());
    }

    /**
     * @test
     */
    public function it_matches_file_info()
    {
        $info = (new FileFactory())->createPng()->info();

        $this->assertTrue((new PendingImageMedia())->matchesInfo($info));
        $this->assertFalse((new PendingPdfMedia())->matchesInfo($info));
    }

    /**
     * @test
     */
    public function it_fails_to_upload_file()
    {
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage('Unmatched file info.');

        $file = (new FileFactory())->createPdf();

        (new PendingImageMedia())->upload($file, Metadata::create());
    }

    /**
     * @test
     */
    public function it_uploads_file()
    {
        $file = (new FileFactory())->createPng();
        $metadata = Metadata::fromArray(['foo' => 'bar', 'baz' => 'qux']);
        $userId = UserId::fromString('04907d72-9c88-11e8-add5-0242ac110004');

        $media = new PendingImageMedia();
        $media->upload($file, $metadata, $userId);

        $this->assertEquals('uploaded', $media->status());
        $this->assertEquals('xyz/abc/filename.png', $media->file()->key());
        $this->assertEquals('s3', $media->file()->storage());
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $media->metadata()->all());
        $this->assertEquals('04907d72-9c88-11e8-add5-0242ac110004', (string) $media->updatedById());
    }
}
