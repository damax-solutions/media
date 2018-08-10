<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Service;

use Damax\Media\Application\Exception\ImageProcessingFailure;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaNotUploaded;
use Damax\Media\Application\Service\ImageService;
use Damax\Media\Domain\Exception\InvalidMediaInput;
use Damax\Media\Domain\Exception\MediaNotReadable;
use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingImageMedia;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ImageServiceTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $mediaRepository;

    /**
     * @var Manipulator|MockObject
     */
    private $manipulator;

    /**
     * @var ImageService
     */
    private $service;

    protected function setUp()
    {
        $this->mediaRepository = $this->createMock(MediaRepository::class);
        $this->manipulator = $this->createMock(Manipulator::class);
        $this->service = new ImageService($this->mediaRepository, $this->manipulator);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_processing_missing_media()
    {
        $this->expectException(MediaNotFound::class);
        $this->expectExceptionMessage('Media by id "183702c5-30de-11e8-97f3-005056806fb2" not found.');

        $this->service->process('183702c5-30de-11e8-97f3-005056806fb2', ['w' => 200, 'h' => 200]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_processing_media_with_invalid_params()
    {
        $this->expectException(ImageProcessingFailure::class);
        $this->expectExceptionMessage('Invalid transformation parameters.');

        $this->service->process('183702c5-30de-11e8-97f3-005056806fb2', ['foo' => 'bar', 'baz' => 'qux']);
    }

    /**
     * @test
     */
    public function it_processes_media()
    {
        $file = (new FileFactory())->createPng();

        $media = new PendingImageMedia();
        $media->upload($file);

        $response = new Response();

        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;
        $this->manipulator
            ->expects($this->once())
            ->method('processImage')
            ->with($this->identicalTo($media), ['w' => 200, 'h' => 200])
            ->willReturn($response)
        ;

        $this->assertSame($response, $this->service->process('183702c5-30de-11e8-97f3-005056806fb2', ['w' => 200, 'h' => 200]));
    }

    /**
     * @test
     */
    public function it_throws_exception_when_processing_media_with_unreadable_file()
    {
        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media = new PendingPdfMedia())
        ;
        $this->manipulator
            ->expects($this->once())
            ->method('processImage')
            ->with($this->identicalTo($media), ['w' => 200, 'h' => 200])
            ->willThrowException(MediaNotReadable::missingFile())
        ;

        $this->expectException(MediaNotUploaded::class);
        $this->expectExceptionMessage('Media by id "183702c5-30de-11e8-97f3-005056806fb2" was not uploaded.');

        $this->service->process('183702c5-30de-11e8-97f3-005056806fb2', ['w' => 200, 'h' => 200]);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_processing_media_with_unsupported_mime_type()
    {
        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media = new PendingPdfMedia())
        ;
        $this->manipulator
            ->expects($this->once())
            ->method('processImage')
            ->with($this->identicalTo($media), ['w' => 200, 'h' => 200])
            ->willThrowException(InvalidMediaInput::unregisteredType('application/pdf'))
        ;

        $this->expectException(ImageProcessingFailure::class);
        $this->expectExceptionMessage('Manipulation failed.');

        $this->service->process('183702c5-30de-11e8-97f3-005056806fb2', ['w' => 200, 'h' => 200]);
    }
}
