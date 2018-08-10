<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Service;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Service\MediaServiceTrait;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MediaServiceTraitTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $mediaRepository;

    /**
     * @var MediaServiceTrait
     */
    private $service;

    protected function setUp()
    {
        $this->mediaRepository = $this->createMock(MediaRepository::class);
        $this->service = new class($this->mediaRepository) {
            use MediaServiceTrait;

            public function __construct(MediaRepository $repository)
            {
                $this->mediaRepository = $repository;
            }

            public function fetchMedia(string $mediaId): Media
            {
                return $this->getMedia($mediaId);
            }
        };
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

        $this->assertSame($media, $this->service->fetchMedia('183702c5-30de-11e8-97f3-005056806fb2'));
    }

    /**
     * @test
     */
    public function it_throws_exception_when_media_is_missing()
    {
        $this->expectException(MediaNotFound::class);
        $this->expectExceptionMessage('Media by id "183702c5-30de-11e8-97f3-005056806fb2" not found.');

        $this->service->fetchMedia('183702c5-30de-11e8-97f3-005056806fb2');
    }
}
