<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Gaufrette;

use Damax\Media\Domain\Exception\FileAlreadyExists;
use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Exception\MediaNotReadable;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\StorageFailure;
use Damax\Media\Gaufrette\GaufretteStorage;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use Gaufrette\Adapter\InMemory;
use Gaufrette\Exception\UnsupportedAdapterMethodException;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Gaufrette\FilesystemMap;
use Gaufrette\StreamWrapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GaufretteStorageTest extends TestCase
{
    /**
     * @var FilesystemMap
     */
    private $filesystems;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var Keys|MockObject
     */
    private $keys;

    /**
     * @var GaufretteStorage
     */
    private $storage;

    protected function setUp()
    {
        $this->filesystem = new Filesystem(new InMemory());
        $this->filesystem->write('xyz/abc/filename.pdf', '__binary__');

        $this->filesystems = new FilesystemMap();
        $this->filesystems->set('s3', $this->filesystem);

        StreamWrapper::setFilesystemMap($this->filesystems);
        StreamWrapper::register();

        $types = new Types([
            'document' => new Definition('s3', 2048, ['application/pdf']),
        ]);

        $this->keys = $this->createMock(Keys::class);
        $this->storage = new GaufretteStorage($this->filesystems, $types, $this->keys);
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
        $this->assertEquals('__binary__', $this->storage->read($this->getMedia()));
    }

    /**
     * @test
     */
    public function it_dumps_media_to_file()
    {
        $filename = tempnam(sys_get_temp_dir(), uniqid());

        $this->storage->dump($this->getMedia(), $filename);

        $this->assertEquals('__binary__', file_get_contents($filename));

        unlink($filename);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_streaming_media_with_missing_file()
    {
        $this->expectException(MediaNotReadable::class);
        $this->expectExceptionMessage('File is missing.');

        $stream = tmpfile();

        $this->storage->streamTo(new PendingPdfMedia(), $stream);

        fclose($stream);
    }

    /**
     * @test
     */
    public function it_streams_media()
    {
        $stream = tmpfile();

        $this->storage->streamTo($this->getMedia(), $stream);

        $this->assertEquals('__binary__', stream_get_contents($stream));

        fclose($stream);
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
            'stream' => $stream = tmpfile(),
        ]);

        fclose($stream);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_with_unregistered_type()
    {
        $this->expectException(InvalidMediaInput::class);
        $this->expectExceptionMessage('Media type "document" is not registered.');

        (new GaufretteStorage($this->filesystems, new Types(), $this->keys))->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => $stream = tmpfile(),
        ]);

        fclose($stream);
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
            'stream' => $stream = tmpfile(),
        ]);

        fclose($stream);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_to_unsupported_storage()
    {
        $this->expectException(InvalidMediaInput::class);
        $this->expectExceptionMessage('Storage "s3" is not supported.');

        $this->filesystems->remove('s3');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => $stream = tmpfile(),
        ]);

        fclose($stream);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_with_filesystem_exception()
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->expects($this->once())
            ->method('write')
            ->willThrowException(new UnsupportedAdapterMethodException())
        ;

        $this->filesystems->set('s3', $filesystem);

        $this->keys
            ->expects($this->once())
            ->method('nextKey')
            ->with(['mime_type' => 'application/pdf'])
            ->willReturn('new_file.pdf')
        ;

        $this->expectException(StorageFailure::class);
        $this->expectExceptionMessage('Unable to write key "document/new_file.pdf".');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => $stream = tmpfile(),
        ]);

        fclose($stream);
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

        $stream = tmpfile();
        fwrite($stream, '__binary__');
        rewind($stream);

        $file = $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => $stream,
        ]);
        $this->assertEquals('application/pdf', $file->mimeType());
        $this->assertEquals(1024, $file->size());
        $this->assertEquals('document/new_file.pdf', $file->key());
        $this->assertEquals('s3', $file->storage());

        fclose($stream);

        $this->assertTrue($this->filesystem->has('document/new_file.pdf'));
        $this->assertEquals('__binary__', $this->filesystem->get('document/new_file.pdf')->getContent());
    }

    private function getMedia(): Media
    {
        $file = (new FileFactory())->createPdf();

        $media = new PendingPdfMedia();
        $media->upload($file);

        return $media;
    }
}
