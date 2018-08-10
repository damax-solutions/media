<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Gaufrette;

use Damax\Media\Domain\Exception\FileAlreadyExists;
use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Domain\Exception\StorageFailure;
use Damax\Media\Domain\Storage\Keys\FixedKeys;
use Damax\Media\Gaufrette\GaufretteStorage;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use Damax\Media\Tests\Domain\Model\UploadedPdfMedia;
use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use Gaufrette\Adapter\InMemory;
use Gaufrette\Exception\UnsupportedAdapterMethodException;
use Gaufrette\Filesystem;
use Gaufrette\FilesystemInterface;
use Gaufrette\FilesystemMap;
use Gaufrette\StreamWrapper;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Damax\Media\Domain\Storage\AbstractStorage
 * @covers \Damax\Media\Gaufrette\GaufretteStorage
 * @covers \Damax\Media\Domain\Exception\InvalidFile
 * @covers \Damax\Media\Domain\Exception\StorageFailure
 */
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

        $document = new Definition('s3', 2048, ['application/pdf']);

        $this->storage = new GaufretteStorage(new Types(['document' => $document]), new FixedKeys(['new_file.pdf']), $this->filesystems);
    }

    /**
     * @test
     */
    public function it_reads_media()
    {
        $this->assertEquals('__binary__', $this->storage->read(new UploadedPdfMedia()));
    }

    /**
     * @test
     */
    public function it_streams_media()
    {
        $stream = tmpfile();

        $this->storage->streamTo(new UploadedPdfMedia(), $stream);

        rewind($stream);

        $this->assertEquals('__binary__', stream_get_contents($stream));

        fclose($stream);
    }

    /**
     * @test
     */
    public function it_dumps_media()
    {
        $filename = tempnam(sys_get_temp_dir(), uniqid());

        $this->storage->dump(new UploadedPdfMedia(), $filename);

        $this->assertEquals('__binary__', file_get_contents($filename));

        unlink($filename);
    }

    /**
     * @test
     */
    public function it_deletes_media()
    {
        $this->storage->delete(new UploadedPdfMedia());

        $this->assertFalse($this->filesystem->has('xyz/abc/filename.pdf'));
    }

    /**
     * @test
     */
    public function it_fails_to_write_to_unsupported_storage()
    {
        $this->expectException(StorageFailure::class);
        $this->expectExceptionMessage('Storage "s3" is not supported.');

        $this->filesystems->remove('s3');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'stream' => 'stream',
        ]);
    }

    /**
     * @test
     */
    public function it_fails_to_write_on_filesystem_error()
    {
        $this->filesystems->set('s3', $filesystem = $this->createMock(Filesystem::class));

        $filesystem
            ->expects($this->once())
            ->method('write')
            ->willThrowException(new UnsupportedAdapterMethodException())
        ;

        $this->expectException(StorageFailure::class);
        $this->expectExceptionMessage('Unable to write key "new_file.pdf".');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'stream' => tmpfile(),
        ]);
    }

    /**
     * @test
     */
    public function it_fails_to_write_on_file_mismatch()
    {
        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage('Unmatched file info.');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'file_size' => 2048,
            'stream' => tmpfile(),
        ]);
    }

    /**
     * @test
     */
    public function it_fails_to_write_when_file_exists()
    {
        $this->expectException(FileAlreadyExists::class);

        $this->storage->write(new UploadedPdfMedia(), [
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'stream' => tmpfile(),
        ]);
    }

    /**
     * @test
     */
    public function it_writes_media()
    {
        $stream = tmpfile();
        fwrite($stream, '__binary__');
        rewind($stream);

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'stream' => $stream,
        ]);

        fclose($stream);

        $this->assertTrue($this->filesystem->has('new_file.pdf'));
        $this->assertEquals('__binary__', $this->filesystem->get('new_file.pdf')->getContent());
    }
}
