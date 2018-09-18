<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Command;

use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Command\CreateMediaHandler;
use Damax\Media\Application\Dto\NewMediaDto;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Domain\Model\MediaId;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Tests\Domain\Model\PendingImageMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Damax\Media\Application\Command\MediaCommand
 * @covers \Damax\Media\Application\Command\CreateMedia
 * @covers \Damax\Media\Application\Command\CreateMediaHandler
 */
class CreateMediaHandlerTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $repository;

    /**
     * @var MediaFactory|MockObject
     */
    private $factory;

    /**
     * @var CreateMediaHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->repository = $this->createMock(MediaRepository::class);
        $this->factory = $this->createMock(MediaFactory::class);
        $this->handler = new CreateMediaHandler($this->repository, $this->factory);
    }

    /**
     * @test
     */
    public function it_creates_media()
    {
        $dto = new NewMediaDto();
        $dto->type = 'image';
        $dto->name = 'Test PNG image';
        $dto->mimeType = 'application/pdf';
        $dto->fileSize = 1024;

        $command = new CreateMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2', $dto);

        $this->factory
            ->expects($this->once())
            ->method('create')
            ->with([
                'id' => MediaId::fromString('64c2c4b7-33f5-11e8-97f3-005056806fb2'),
                'type' => 'image',
                'name' => 'Test PNG image',
                'mime_type' => 'application/pdf',
                'file_size' => 1024,
            ])
            ->willReturn($media = new PendingImageMedia())
        ;

        $this->repository
            ->expects($this->once())
            ->method('add')
            ->with($this->identicalTo($media))
        ;

        call_user_func($this->handler, $command);
    }
}
