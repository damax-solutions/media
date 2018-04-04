<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Flysystem;

use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Storage\Keys;
use Damax\Media\Domain\Storage\StorageFailure;
use Damax\Media\Flysystem\FlysystemStorage;
use Damax\Media\Flysystem\Registry;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

class FlysystemStorageTest extends TestCase
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var Registry
     */
    private $registry;

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
        $this->filesystem = $filesystem = new Filesystem(new Local(__DIR__ . '/_data'));
        $this->filesystem->write('xyz/abc/filename.pdf', '__binary__');

        $this->registry = new class() implements Registry {
            private $filesystem;

            public function has(string $name): bool
            {
                return 's3' === $name;
            }

            public function get(string $name): FilesystemInterface
            {
                return $this->filesystem;
            }

            public function set(string $name, FilesystemInterface $filesystem): void
            {
                $this->filesystem = $filesystem;
            }
        };

        $this->registry->set('s3', $this->filesystem);

        $this->types = new Types([
            'document' => new Definition('s3', 2048, ['application/pdf']),
        ]);

        $this->keys = $this->createMock(Keys::class);
        $this->storage = new FlysystemStorage($this->registry, $this->types, $this->keys);
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

        /** @var Registry $registry */
        $registry = $this->createMock(Registry::class);

        (new FlysystemStorage($registry, $this->types, $this->keys))->write(new PendingPdfMedia(), [
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
        $this->registry->set('s3', $filesystem = $this->createMock(FilesystemInterface::class));

        $filesystem
            ->expects($this->once())
            ->method('writeStream')
            ->willThrowException(new RuntimeException('invalid write'))
        ;
        $this->keys
            ->method('nextKey')
            ->willReturn('new_file.pdf')
        ;

        $this->expectException(StorageFailure::class);
        $this->expectExceptionMessage('Unable to write key "document/new_file.pdf".');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'size' => 1024,
            'stream' => 'stream',
        ]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_writing_media_with_error()
    {
        $this->registry->set('s3', $filesystem = $this->createMock(FilesystemInterface::class));

        $this->keys
            ->method('nextKey')
            ->willReturn('new_file.pdf')
        ;
        $filesystem
            ->expects($this->once())
            ->method('writeStream')
            ->willReturn(false)
        ;

        $this->expectException(StorageFailure::class);
        $this->expectExceptionMessage('Unable to write key "document/new_file.pdf".');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
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

    /**
     * @test
     */
    public function it_removes_media()
    {
        $file = (new FileFactory())->createPdf();

        $media = new PendingPdfMedia();
        $media->upload($file);

        $this->storage->remove($media);

        $this->assertFalse($this->filesystem->has('xyz/abc/filename.pdf'));
    }

    private function getMedia(): Media
    {
        $file = (new FileFactory())->createPdf();

        $media = new PendingPdfMedia();
        $media->upload($file);

        return $media;
    }
}
