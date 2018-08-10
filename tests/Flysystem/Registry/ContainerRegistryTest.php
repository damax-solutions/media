<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Flysystem\Registry;

use Damax\Media\Flysystem\Registry\ContainerRegistry;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ContainerRegistryTest extends TestCase
{
    /**
     * @var ContainerInterface|MockObject
     */
    private $container;

    /**
     * @var ContainerRegistry
     */
    private $registry;

    protected function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->registry = new ContainerRegistry($this->container);
    }

    /**
     * @test
     */
    public function it_has_filesystem()
    {
        $this->container
            ->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive(
                ['oneup_flysystem.foo_filesystem'],
                ['oneup_flysystem.bar_filesystem']
            )
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $this->assertTrue($this->registry->has('foo'));
        $this->assertFalse($this->registry->has('bar'));
    }

    /**
     * @test
     */
    public function it_retrieves_filesystem()
    {
        /** @var FilesystemInterface $filesystem */
        $filesystem = $this->createMock(FilesystemInterface::class);

        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('oneup_flysystem.foo_filesystem')
            ->willReturn($filesystem)
        ;

        $this->assertSame($filesystem, $this->registry->get('foo'));
    }
}
