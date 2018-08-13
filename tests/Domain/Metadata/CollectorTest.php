<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Metadata;

use Damax\Common\Domain\Model\Metadata;
use Damax\Media\Domain\Metadata\Collector;
use Damax\Media\Domain\Metadata\Reader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CollectorTest extends TestCase
{
    /**
     * @var Reader|MockObject
     */
    private $reader1;

    /**
     * @var Reader|MockObject
     */
    private $reader2;

    /**
     * @var Collector
     */
    private $collector;

    protected function setUp()
    {
        $this->reader1 = $this->createMock(Reader::class);
        $this->reader2 = $this->createMock(Reader::class);
        $this->collector = new Collector([$this->reader1, $this->reader2]);
    }

    /**
     * @test
     */
    public function it_adds_reader()
    {
        $reader = $this->createMock(Reader::class);

        $this->collector->add($reader);

        $this->assertAttributeCount(3, 'items', $this->collector);
        $this->assertAttributeSame([$this->reader1, $this->reader2, $reader], 'items', $this->collector);
    }

    /**
     * @test
     */
    public function it_does_not_support_context()
    {
        $context = [];

        $this->reader1
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($context))
            ->willReturn(false)
        ;
        $this->reader2
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($context))
            ->willReturn(false)
        ;

        $this->assertFalse($this->collector->supports($context));
    }

    /**
     * @test
     */
    public function it_supports_context()
    {
        $context = [];

        $this->reader1
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($context))
            ->willReturn(true)
        ;
        $this->reader2
            ->expects($this->never())
            ->method('supports')
        ;

        $this->assertTrue($this->collector->supports($context));
    }

    /**
     * @test
     */
    public function it_extracts_metadata()
    {
        $context = [];

        $reader = $this->createMock(Reader::class);
        $reader
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($context))
            ->willReturn(true)
        ;
        $reader
            ->expects($this->once())
            ->method('extract')
            ->with($this->identicalTo($context))
            ->willReturn(Metadata::fromArray(['abc' => 'xyz']))
        ;

        $this->reader1
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($context))
            ->willReturn(true)
        ;
        $this->reader1
            ->expects($this->once())
            ->method('extract')
            ->with($this->identicalTo($context))
            ->willReturn(Metadata::fromArray(['foo' => 'bar', 'baz' => 'qux']))
        ;

        $this->reader2
            ->expects($this->once())
            ->method('supports')
            ->with($this->identicalTo($context))
            ->willReturn(false)
        ;
        $this->reader2
            ->expects($this->never())
            ->method('extract')
        ;

        $this->collector->add($reader);

        $metadata = $this->collector->extract($context);

        $this->assertEquals(['abc' => 'xyz', 'foo' => 'bar', 'baz' => 'qux'], $metadata->all());
    }
}
