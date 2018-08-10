<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Glide;

use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Flysystem\Registry\Registry;
use Damax\Media\Glide\GlideManipulator;
use Damax\Media\Tests\Domain\Model\UploadedImageMedia;
use Damax\Media\Tests\Domain\Model\UploadedPdfMedia;
use League\Glide\Signatures\SignatureInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @covers \Damax\Media\Glide\GlideManipulator
 * @covers \Damax\Media\Domain\Exception\InvalidFile
 */
class GlideManipulatorTest extends TestCase
{
    /**
     * @var Registry|MockObject
     */
    private $registry;

    /**
     * @var SignatureInterface|MockObject
     */
    private $signature;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var GlideManipulator
     */
    private $manipulator;

    protected function setUp()
    {
        $this->registry = $this->createMock(Registry::class);
        $this->signature = $this->createMock(SignatureInterface::class);
        $this->requestStack = new RequestStack();
        $this->manipulator = new GlideManipulator($this->registry, $this->signature, $this->requestStack, [
            'cache' => 'cache_filesystem',
            'foo' => 'bar',
            'baz' => 'qux',
        ]);
    }

    /**
     * @test
     */
    public function it_fails_to_process_on_empty_request_stack()
    {
        $this->signature
            ->expects($this->never())
            ->method('validateRequest')
        ;

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Request stack is empty.');

        $this->manipulator->processImage(new UploadedImageMedia(), ['w' => 240, 'h' => 240]);
    }

    /**
     * @test
     */
    public function it_fails_to_process_not_image()
    {
        $this->requestStack->push(Request::create('/path/to/image'));

        $this->signature
            ->expects($this->once())
            ->method('validateRequest')
            ->with('/path/to/image', ['w' => 240, 'h' => 240])
        ;

        $this->expectException(InvalidFile::class);
        $this->expectExceptionMessage('File with mime type "application/pdf" is not supported.');

        $this->manipulator->processImage(new UploadedPdfMedia(), ['w' => 240, 'h' => 240]);
    }
}
