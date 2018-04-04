<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Glide;

use Damax\Media\Glide\SignedUrlBuilder;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SignedUrlBuilderTest extends TestCase
{
    /**
     * @var UrlGeneratorInterface|MockObject
     */
    private $urlGenerator;

    /**
     * @var SignedUrlBuilder
     */
    private $urlBuilder;

    protected function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->urlBuilder = new SignedUrlBuilder($this->urlGenerator, 'media_image');
    }

    /**
     * @test
     */
    public function it_builds_media_url()
    {
        $media = new PendingPdfMedia();

        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('media_image', ['id' => '183702c5-30de-11e8-97f3-005056806fb2', 'w' => 200, 'h' => 200])
            ->willReturn('generated-url')
        ;

        $this->assertEquals('generated-url', $this->urlBuilder->build($media, ['w' => 200, 'h' => 200]));
    }
}
