<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Glide;

use Damax\Media\Domain\Model\MediaId;
use Damax\Media\Glide\SignedUrlBuilder;
use League\Glide\Signatures\SignatureInterface;
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
     * @var SignatureInterface|MockObject
     */
    private $signature;

    /**
     * @var SignedUrlBuilder
     */
    private $urlBuilder;

    protected function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->signature = $this->createMock(SignatureInterface::class);
        $this->urlBuilder = new SignedUrlBuilder($this->urlGenerator, $this->signature, 'media_image');
    }

    /**
     * @test
     */
    public function it_builds_media_url()
    {
        $this->urlGenerator
            ->expects($this->exactly(2))
            ->method('generate')
            ->withConsecutive(
                ['media_image', ['id' => '183702c5-30de-11e8-97f3-005056806fb2']],
                ['media_image', ['id' => '183702c5-30de-11e8-97f3-005056806fb2', 'signed' => 'params']]
            )
            ->willReturnOnConsecutiveCalls(
                'media-url',
                'signed-media-url'
            )
        ;

        $this->signature
            ->expects($this->once())
            ->method('addSignature')
            ->with('media-url', ['w' => 200, 'h' => 200])
            ->willReturn(['signed' => 'params'])
        ;

        $mediaId = MediaId::fromString('183702c5-30de-11e8-97f3-005056806fb2');

        $this->assertEquals('signed-media-url', $this->urlBuilder->build($mediaId, ['w' => 200, 'h' => 200]));
    }
}
