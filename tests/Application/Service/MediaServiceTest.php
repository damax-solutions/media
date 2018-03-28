<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Service;

use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Application\Service\MediaService;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Domain\Model\MediaRepository;
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
     * @var MediaFactory|MockObject
     */
    private $mediaFactory;

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
        $this->mediaFactory = $this->createMock(MediaFactory::class);
        $this->assembler = $this->createMock(Assembler::class);
        $this->service = new MediaService($this->mediaRepository, $this->mediaFactory, $this->assembler);
    }

    /**
     * @test
     */
    public function it_creates_media()
    {
        $command = new CreateMedia();

        $this->mediaFactory
            ->expects($this->once())
            ->method('create')
            ->with($this->identicalTo($command))
            ->willReturn($media = $this->createMock(Media::class))
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

        $this->assertSame($dto, $this->service->create($command));
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
}
