<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain;

use Damax\Media\Domain\Exception\FileAlreadyExists;
use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Exception\MediaNotReadable;
use Damax\Media\Domain\Model\File;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Storage\AbstractStorage;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AbstractStorageTest extends TestCase
{
    /**
     * @var Types
     */
    private $types;

    /**
     * @var Keys|MockObject
     */
    private $keys;

    /**
     * @var AbstractStorage
     */
    private $storage;

    protected function setUp()
    {
        $this->types = $types = new Types([
            'document' => new Definition('s3', 2048, ['application/pdf']),
        ]);

        $this->keys = $keys = $this->createMock(Keys::class);

        $this->storage = new class($types, $keys) extends AbstractStorage {
            private $args;

            protected function readFile(File $file): string
            {
                $this->args = func_get_args();

                return '__binary__';
            }

            protected function streamFile(File $file)
            {
                $this->args = func_get_args();

                $fp = tmpfile();
                fputs($fp, '__binary__');
                rewind($fp);

                return $fp;
            }

            protected function writeFile(string $key, string $storage, $stream): void
            {
                $this->args = func_get_args();
            }

            protected function removeFile(File $file): void
            {
                $this->args = func_get_args();
            }

            public function appliedArgs(): ?array
            {
                return $this->args;
            }
        };
    }

    /**
     * @test
     */
    public function it_throws_exception_on_reading_media_with_missing_file()
    {
        $this->expectException(MediaNotReadable::class);
        $this->expectExceptionMessage('File is missing.');

        $this->storage->read(new PendingPdfMedia());
    }

    /**
     * @test
     */
    public function it_reads_media()
    {
        $media = $this->getMedia();

        $this->assertEquals('__binary__', $this->storage->read($media));

        $this->assertMediaFile($media);
    }

    /**
     * @test
     */
    public function it_dumps_media_to_file()
    {
        $filename = tempnam(sys_get_temp_dir(), uniqid());

        $this->storage->dump($media = $this->getMedia(), $filename);

        $this->assertEquals('__binary__', file_get_contents($filename));

        unlink($filename);

        $this->assertMediaFile($media);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_streaming_media_with_missing_file()
    {
        $this->expectException(MediaNotReadable::class);
        $this->expectExceptionMessage('File is missing.');

        $this->storage->streamTo(new PendingPdfMedia(), 'stream');
    }

    /**
     * @test
     */
    public function it_streams_media()
    {
        $stream = tmpfile();

        $this->storage->streamTo($media = $this->getMedia(), $stream);

        rewind($stream);

        $this->assertEquals('__binary__', stream_get_contents($stream));

        fclose($stream);

        $this->assertMediaFile($media);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_with_existing_file()
    {
        $this->expectException(FileAlreadyExists::class);
        $this->expectExceptionMessage('File already exists.');

        $this->storage->write($this->getMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => 'stream',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_with_unregistered_type()
    {
        $this->expectException(InvalidMediaInput::class);
        $this->expectExceptionMessage('Media type "document" is not registered.');

        $this->types->reset();

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => 'stream',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_with_invalid_context()
    {
        $this->expectException(InvalidMediaInput::class);
        $this->expectExceptionMessage('Invalid file.');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/json',
            'size' => 1024,
            'stream' => 'stream',
        ]);
    }

    /**
     * @test
     */
    public function it_writes_media()
    {
        $this->keys
            ->expects($this->once())
            ->method('nextKey')
            ->with(['mime_type' => 'application/pdf'])
            ->willReturn('new_file.pdf')
        ;

        $file = $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => 'stream',
        ]);
        $this->assertEquals('application/pdf', $file->mimeType());
        $this->assertEquals(1024, $file->size());
        $this->assertEquals('document/new_file.pdf', $file->key());
        $this->assertEquals('s3', $file->storage());

        $this->assertSame(['document/new_file.pdf', 's3', 'stream'], $this->storage->appliedArgs());
    }

    /**
     * @test
     */
    public function it_removes_media_with_missing_file()
    {
        $this->storage->remove(new PendingPdfMedia());

        $this->assertNull($this->storage->appliedArgs());
    }

    /**
     * @test
     */
    public function it_removes_media()
    {
        $file = (new FileFactory())->createPdf();

        $media = new PendingPdfMedia();
        $media->upload($file);

        $this->storage->remove($media);

        $this->assertSame([$file], $this->storage->appliedArgs());
    }

    private function getMedia(): Media
    {
        $file = (new FileFactory())->createPdf();

        $media = new PendingPdfMedia();
        $media->upload($file);

        return $media;
    }

    private function assertMediaFile(Media $media)
    {
        $this->assertSame([$media->file()], $this->storage->appliedArgs());
    }
}
