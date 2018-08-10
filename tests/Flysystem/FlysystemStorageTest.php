<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Flysystem;

use Damax\Common\Domain\Model\Metadata;
use Damax\Media\Domain\Exception\StorageFailure;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Storage\Keys\FixedKeys;
use Damax\Media\Flysystem\FlysystemStorage;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use Damax\Media\Tests\Flysystem\Registry\TestRegistry;
use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;

/**
 * @covers \Damax\Media\Domain\Storage\AbstractStorage
 * @covers \Damax\Media\Flysystem\FlysystemStorage
 * @covers \Damax\Media\Domain\Exception\InvalidFile
 * @covers \Damax\Media\Domain\Exception\StorageFailure
 */
class FlysystemStorageTest extends TestCase
{
    /**
     * @var TestRegistry
     */
    private $registry;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var FlysystemStorage
     */
    private $storage;

    protected function setUp()
    {
        $this->filesystem = new Filesystem(new Local(__DIR__ . '/_data'));
        $this->filesystem->write('xyz/abc/filename.pdf', '__binary__');

        $this->registry = new TestRegistry('s3', $this->filesystem);

        $document = new Definition('s3', 2048, ['application/pdf']);

        $this->storage = new FlysystemStorage(new Types(['document' => $document]), new FixedKeys(['new_file.pdf']), $this->registry);
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
    public function it_dumps_media()
    {
        $filename = tempnam(sys_get_temp_dir(), uniqid());

        $this->storage->dump($this->getMedia(), $filename);

        $this->assertEquals('__binary__', file_get_contents($filename));

        unlink($filename);
    }

    /**
     * @test
     */
    public function it_deletes_media()
    {
        $this->storage->delete($this->getMedia());

        $this->assertFalse($this->filesystem->has('xyz/abc/filename.pdf'));
    }

    /**
     * @test
     */
    public function it_fails_to_write_to_unsupported_storage()
    {
        $this->expectException(StorageFailure::class);
        $this->expectExceptionMessage('Storage "s3" is not supported.');

        $this->registry->changeName('local');

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
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->expects($this->once())
            ->method('writeStream')
            ->willThrowException(new RuntimeException('invalid write'))
        ;

        $this->registry->changeFilesystem($filesystem);

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
    public function it_fails_to_write_on_filesystem_write_error()
    {
        $filesystem = $this->createMock(Filesystem::class);
        $filesystem
            ->expects($this->once())
            ->method('writeStream')
            ->willReturn(false)
        ;

        $this->registry->changeFilesystem($filesystem);

        $this->expectException(StorageFailure::class);
        $this->expectExceptionMessage('Unable to write key "new_file.pdf".');

        $this->storage->write(new PendingPdfMedia(), [
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
            'stream' => 'stream',
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
        $this->assertEquals('__binary__', $this->filesystem->read('new_file.pdf'));
    }

    private function getMedia(): Media
    {
        $file = (new FileFactory())->createPdf();

        $media = new PendingPdfMedia();
        $media->upload($file, Metadata::create());

        return $media;
    }
}
