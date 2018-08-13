<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Query;

use Damax\Media\Application\Query\DownloadMedia;
use Damax\Media\Application\Query\DownloadMediaHandler;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Tests\Domain\Model\UploadedPdfMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \Damax\Media\Application\Query\MediaQuery
 * @covers \Damax\Media\Application\Query\DownloadMedia
 * @covers \Damax\Media\Application\Query\DownloadMediaHandler
 */
class DownloadMediaHandlerTest extends TestCase
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
     * @var DownloadMediaHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->repository = $this->createMock(MediaRepository::class);
        $this->storage = $this->createMock(Storage::class);
        $this->handler = new DownloadMediaHandler($this->repository, $this->storage);
    }

    /**
     * @test
     */
    public function it_executes_handler()
    {
        $media = new UploadedPdfMedia();

        $result = null;
        $stream = null;

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;
        $this->storage
            ->expects($this->once())
            ->method('streamTo')
            ->willReturnCallback(function (Media $media, $output) use (&$result, &$stream) {
                $result = $media;
                $stream = $output;
            })
        ;

        $query = new DownloadMedia('183702c5-30de-11e8-97f3-005056806fb2');

        /** @var Response $response */
        $response = call_user_func($this->handler, $query);

        $this->assertEquals(1024, $response->headers->get('Content-Length'));
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename=filename.pdf', $response->headers->get('Content-Disposition'));

        $response->sendContent();

        $this->assertSame($result, $media);
        $this->assertTrue(is_resource($stream));
    }
}
