<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Service;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaNotUploaded;
use Damax\Media\Application\Service\DownloadService;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;
use Damax\Media\Tests\Domain\Model\FileFactory;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DownloadServiceTest extends TestCase
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
     * @var DownloadService
     */
    private $service;

    protected function setUp()
    {
        $this->mediaRepository = $this->createMock(MediaRepository::class);
        $this->storage = $this->createMock(Storage::class);
        $this->service = new DownloadService($this->mediaRepository, $this->storage);
    }

    /**
     * @test
     */
    public function it_throws_exception_when_downloading_missing_media()
    {
        $this->expectException(MediaNotFound::class);
        $this->expectExceptionMessage('Media by id "183702c5-30de-11e8-97f3-005056806fb2" not found.');

        $this->service->download('183702c5-30de-11e8-97f3-005056806fb2');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_downloading_media_with_missing_file()
    {
        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media = new PendingPdfMedia())
        ;

        $this->expectException(MediaNotUploaded::class);
        $this->expectExceptionMessage('Media by id "183702c5-30de-11e8-97f3-005056806fb2" was not uploaded.');

        $this->service->download('183702c5-30de-11e8-97f3-005056806fb2');
    }

    /**
     * @test
     */
    public function it_downloads_media()
    {
        $file = (new FileFactory())->createPdf();

        $media = new PendingPdfMedia();
        $media->upload($file);

        fputs($source = fopen('php://temp', 'rb'), 'Binary ...');

        $this->mediaRepository
            ->expects($this->once())
            ->method('byId')
            ->with('183702c5-30de-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;
        $this->storage
            ->expects($this->once())
            ->method('streamTo')
            ->willReturnCallback(function (Media $item, $output) use ($media, $source) {
                $this->assertSame($item, $media);

                stream_copy_to_stream($source, $output);
            })
        ;

        $response = $this->service->download('183702c5-30de-11e8-97f3-005056806fb2');

        $this->assertEquals(1024, $response->headers->get('Content-Length'));
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertEquals('attachment; filename="filename.pdf"', $response->headers->get('Content-Disposition'));

        $response->sendContent();

        fclose($source);
    }
}
