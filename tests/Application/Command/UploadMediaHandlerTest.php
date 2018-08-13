<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Command;

use Damax\Common\Domain\Model\Metadata;
use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Command\UploadMediaHandler;
use Damax\Media\Application\Dto\UploadDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaUploadFailure;
use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Domain\Metadata\Reader;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingImageMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Damax\Media\Application\Command\UploadMedia
 * @covers \Damax\Media\Application\Command\UploadMediaHandler
 * @covers \Damax\Media\Application\Exception\MediaNotFound
 */
class UploadMediaHandlerTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $repository;

    /**
     * @var Storage|MockObject
     */
    private $storage;

    /**
     * @var Reader|MockObject
     */
    private $metadata;

    /**
     * @var UploadMediaHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->repository = $this->createMock(MediaRepository::class);
        $this->storage = $this->createMock(Storage::class);
        $this->metadata = $this->createMock(Reader::class);
        $this->handler = new UploadMediaHandler($this->repository, $this->storage, $this->metadata);
    }

    /**
     * @test
     */
    public function it_fails_to_upload_for_missing_media()
    {
        $this->metadata
            ->expects($this->never())
            ->method('supports')
        ;
        $this->storage
            ->expects($this->never())
            ->method('write')
        ;
        $this->repository
            ->expects($this->never())
            ->method('update')
        ;

        $this->expectException(MediaNotFound::class);
        $this->expectExceptionMessage('Media by id "64c2c4b7-33f5-11e8-97f3-005056806fb2" not found.');

        call_user_func($this->handler, new UploadMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2', new UploadDto()));
    }

    /**
     * @test
     */
    public function it_fails_to_upload_on_filesystem_error()
    {
        $media = new PendingImageMedia();

        $command = new UploadMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2', $dto = new UploadDto());

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;
        $this->repository
            ->expects($this->never())
            ->method('update')
        ;
        $this->metadata
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($dto))
            ->willReturn(false)
        ;
        $this->storage
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($media), $this->identicalTo($dto))
            ->willThrowException(InvalidFile::notUploaded())
        ;

        $this->expectException(MediaUploadFailure::class);
        $this->expectExceptionMessage('Upload failed.');

        call_user_func($this->handler, $command);
    }

    /**
     * @test
     */
    public function it_writes_file_to_storage_with_no_metadata()
    {
        $media = new PendingImageMedia();

        $command = new UploadMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2', $dto = new UploadDto());

        $file = (new FileFactory())->createPng();

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;
        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($this->identicalTo($media))
        ;
        $this->metadata
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($dto))
            ->willReturn(false)
        ;
        $this->metadata
            ->expects($this->never())
            ->method('extract')
        ;
        $this->storage
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($media), $this->identicalTo($dto))
            ->willReturn($file)
        ;

        call_user_func($this->handler, $command);

        $this->assertEquals($file, $media->file());
        $this->assertEquals([], $media->metadata()->all());
    }

    /**
     * @test
     */
    public function it_writes_file_to_storage_with_metadata()
    {
        $media = new PendingImageMedia();

        $command = new UploadMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2', $dto = new UploadDto());

        $file = (new FileFactory())->createPng();

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;
        $this->metadata
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($dto))
            ->willReturn(true)
        ;
        $this->metadata
            ->expects($this->once())
            ->method('extract')
            ->with($this->identicalTo($dto))
            ->willReturn(Metadata::fromArray(['foo' => 'bar', 'baz' => 'qux']))
        ;
        $this->storage
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($media), $this->identicalTo($dto))
            ->willReturn($file)
        ;

        call_user_func($this->handler, $command);

        $this->assertEquals($file, $media->file());
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $media->metadata()->all());
    }
}
