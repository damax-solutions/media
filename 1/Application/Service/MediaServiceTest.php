<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Service;

use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Application\Dto\MediaUploadDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaUploadFailure;
use Damax\Media\Application\Service\MediaService;
use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Metadata\Reader;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Model\Metadata;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MediaServiceTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $mediaRepository;

    /**
     * @var Storage|MockObject
     */
    private $storage;

    /**
     * @var Metadata|MockObject
     */
    private $metadata;

    /**
     * @var Assembler|MockObject
     */
    private $assembler;

    /**
     * @var MediaService
     */
    private $service;

    protected function setUp()
    {
        $this->mediaRepository = $this->createMock(MediaRepository::class);
        $this->storage = $this->createMock(Storage::class);
        $this->metadata = $this->createMock(Reader::class);
        $this->assembler = $this->createMock(Assembler::class);
        $this->service = new MediaService($this->mediaRepository, $this->storage, $this->metadata, $this->assembler);
    }

    /**
     * @test
     */
    public function it_fetches_media()
    {
        $media = new PendingPdfMedia();

        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;
        $this->assembler
            ->expects($this->once())
            ->method('toMediaDto')
            ->with($this->identicalTo($media))
            ->willReturn($dto = new MediaDto())
        ;

        $this->assertSame($dto, $this->service->fetch('183702c5-30de-11e8-97f3-005056806fb2'));
    }

    /**
     * @test
     */
    public function it_throws_exception_when_uploading_media_on_missing_media()
    {
        $command = new UploadMedia();
        $command->mediaId = '183702c5-30de-11e8-97f3-005056806fb2';

        $this->expectException(MediaNotFound::class);
        $this->expectExceptionMessage('Media by id "183702c5-30de-11e8-97f3-005056806fb2" not found.');

        $this->service->upload($command);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_uploading_media_on_invalid_media_input()
    {
        $command = new UploadMedia();
        $command->mediaId = '183702c5-30de-11e8-97f3-005056806fb2';
        $command->upload = new MediaUploadDto();

        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media = new PendingPdfMedia())
        ;
        $this->storage
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($media), $this->identicalTo($command->upload))
            ->willThrowException(new InvalidMediaInput('Invalid media input.'))
        ;

        $this->expectException(MediaUploadFailure::class);
        $this->expectExceptionMessage('Invalid media input.');

        $this->service->upload($command);
    }

    /**
     * @test
     */
    public function it_uploads_media()
    {
        $command = new UploadMedia();
        $command->mediaId = '183702c5-30de-11e8-97f3-005056806fb2';
        $command->upload = new MediaUploadDto();

        $file = (new FileFactory())->createPdf();

        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media = new PendingPdfMedia())
        ;
        $this->storage
            ->expects($this->once())
            ->method('write')
            ->with($this->identicalTo($media), $this->identicalTo($command->upload))
            ->willReturn($file)
        ;
        $this->mediaRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->identicalTo($media))
        ;
        $this->assembler
            ->expects($this->once())
            ->method('toMediaDto')
            ->with($this->identicalTo($media))
            ->willReturn($dto = new MediaDto())
        ;

        $this->assertSame($dto, $this->service->upload($command));

        $this->assertSame($file, $media->file());
        $this->assertEquals('uploaded', $media->status());
    }

    /**
     * @test
     */
    public function it_deletes_media()
    {
        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media = new PendingPdfMedia())
        ;
        $this->mediaRepository
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($media))
        ;
        $this->storage
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($media))
        ;
        $this->assembler
            ->expects($this->once())
            ->method('toMediaDto')
            ->with($this->identicalTo($media))
            ->willReturn($dto = new MediaDto())
        ;

        $this->assertSame($dto, $this->service->delete('183702c5-30de-11e8-97f3-005056806fb2'));
    }
}
