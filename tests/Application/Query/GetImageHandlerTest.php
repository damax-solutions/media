<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Query;

use Damax\Media\Application\Exception\ImageProcessingFailure;
use Damax\Media\Application\Query\GetImage;
use Damax\Media\Application\Query\GetImageHandler;
use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Tests\Domain\Model\UploadedImageMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \Damax\Media\Application\Query\MediaQuery
 * @covers \Damax\Media\Application\Query\GetImage
 * @covers \Damax\Media\Application\Query\GetImageHandler
 */
class GetImageHandlerTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $repository;

    /**
     * @var Manipulator|MockObject
     */
    private $manipulator;

    /**
     * @var GetImageHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->repository = $this->createMock(MediaRepository::class);
        $this->manipulator = $this->createMock(Manipulator::class);
        $this->handler = new GetImageHandler($this->repository, $this->manipulator);
    }

    /**
     * @test
     */
    public function it_fails_on_invalid_params()
    {
        $media = new UploadedImageMedia();

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;

        $this->expectException(ImageProcessingFailure::class);
        $this->expectExceptionMessage('Invalid parameters.');

        $query = new GetImage('64c2c4b7-33f5-11e8-97f3-005056806fb2', ['foo' => 'bar', 'baz' => 'qux']);

        call_user_func($this->handler, $query);
    }

    /**
     * @test
     */
    public function it_fails_on_invalid_file()
    {
        $media = new UploadedImageMedia();

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;

        $this->manipulator
            ->expects($this->once())
            ->method('processImage')
            ->with($this->identicalTo($media), ['w' => 200, 'h' => 200])
            ->willThrowException(InvalidFile::notUploaded())
        ;

        $this->expectException(ImageProcessingFailure::class);
        $this->expectExceptionMessage('Manipulation failed.');

        $query = new GetImage('64c2c4b7-33f5-11e8-97f3-005056806fb2', ['w' => 200, 'h' => 200]);

        call_user_func($this->handler, $query);
    }

    /**
     * @test
     */
    public function it_executes_handler()
    {
        $media = new UploadedImageMedia();

        $response = new Response();

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;

        $this->manipulator
            ->expects($this->once())
            ->method('processImage')
            ->with($this->identicalTo($media), ['w' => 200, 'h' => 200])
            ->willReturn($response)
        ;

        $query = new GetImage('64c2c4b7-33f5-11e8-97f3-005056806fb2', ['w' => 200, 'h' => 200]);

        $this->assertSame($response, call_user_func($this->handler, $query));
    }
}
