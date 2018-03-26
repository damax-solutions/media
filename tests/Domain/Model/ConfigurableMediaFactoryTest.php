<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\ConfigurableMediaFactory;
use Damax\Media\Domain\Model\MediaRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ConfigurableMediaFactoryTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $repository;

    /**
     * @var ConfigurableMediaFactory
     */
    private $factory;

    protected function setUp()
    {
        $this->repository = $this->createMock(MediaRepository::class);
        $this->factory = new ConfigurableMediaFactory($this->repository);
    }

    /**
     * @test
     */
    public function it_creates_media()
    {
        $this->repository
            ->expects($this->once())
            ->method('nextId')
            ->willReturn($id = Uuid::uuid4())
        ;

        $media = $this->factory->create([
            'type' => 'document',
            'name' => 'Test PDF document',
            'mime_type' => 'application/pdf',
            'size' => 1024,
        ]);

        $this->assertSame($id, $media->id());
        $this->assertEquals('pending', $media->status());
        $this->assertEquals('document', $media->type());
        $this->assertEquals('Test PDF document', $media->name());
        $this->assertEquals('application/pdf', $media->info()->mimeType());
        $this->assertEquals(1024, $media->info()->size());
    }
}
