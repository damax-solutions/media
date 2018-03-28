<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Flysystem;

use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\StorageFailure;
use Damax\Media\Flysystem\FlysystemStorage;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class FlysystemStorageTest extends TestCase
{
    /**
     * @var MountManager
     */
    private $mountManager;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var Types
     */
    private $types;

    /**
     * @var Keys|MockObject
     */
    private $keys;

    /**
     * @var FlysystemStorage
     */
    private $storage;

    protected function setUp()
    {
        $this->filesystem = new Filesystem(new Local(__DIR__ . '/_data'));
        $this->filesystem->write('xyz/abc/filename.pdf', '__binary__');

        $this->mountManager = new MountManager(['s3' => $this->filesystem]);

        $this->types = new Types([
            'document' => new Definition('s3', 2048, ['application/pdf']),
        ]);

        $this->keys = $this->createMock(Keys::class);
        $this->storage = new FlysystemStorage($this->mountManager, $this->types, $this->keys);
    }

    protected function tearDown()
    {
        (new SymfonyFilesystem())->remove(__DIR__ . '/_data');
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
    public function it_streams_media()
    {
        $stream = tmpfile();

        $this->storage->streamTo($this->getMedia(), $stream);

        rewind($stream);

        $this->assertEquals('__binary__', stream_get_contents($stream));

        fclose($stream);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_to_unsupported_storage()
    {
        $this->expectException(InvalidMediaInput::class);
        $this->expectExceptionMessage('Storage "s3" is not supported.');

        (new FlysystemStorage(new MountManager(), $this->types, $this->keys))->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => 'stream',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_with_filesystem_exception()
    {
        $filesystem = $this->createMock(FilesystemInterface::class);
        $filesystem
            ->expects($this->once())
            ->method('writeStream')
            ->willThrowException(new RuntimeException('invalid write'))
        ;

        $this->mountManager->mountFilesystem('s3', $filesystem);

        $this->keys
            ->method('nextKey')
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
            ->method('nextKey')
            ->willReturn('new_file.pdf')
        ;

        $stream = tmpfile();
        fwrite($stream, '__binary__');
        rewind($stream);

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => $stream,
        ]);

        fclose($stream);

        $this->assertTrue($this->filesystem->has('document/new_file.pdf'));
        $this->assertEquals('__binary__', $this->filesystem->read('document/new_file.pdf'));
    }

    private function getMedia(): Media
    {
        $file = (new FileFactory())->createPdf();

        $media = new PendingPdfMedia();
        $media->upload($file);

        return $media;
    }
}
