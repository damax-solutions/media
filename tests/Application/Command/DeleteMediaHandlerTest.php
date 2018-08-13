<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Command;

use Damax\Media\Application\Command\DeleteMedia;
use Damax\Media\Application\Command\DeleteMediaHandler;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Tests\Domain\Model\PendingImageMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Damax\Media\Application\Command\DeleteMedia
 * @covers \Damax\Media\Application\Command\DeleteMediaHandler
 * @covers \Damax\Media\Application\Exception\MediaNotFound
 */
class DeleteMediaHandlerTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $repository;

    /**
     * @var DeleteMediaHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->repository = $this->createMock(MediaRepository::class);
        $this->handler = new DeleteMediaHandler($this->repository);
    }

    /**
     * @test
     */
    public function it_removes_media()
    {
        $command = new DeleteMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2');

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2')
            ->willReturn($media = new PendingImageMedia())
        ;

        $this->repository
            ->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($media))
        ;

        call_user_func($this->handler, $command);
    }

    /**
     * @test
     */
    public function it_fails_to_remove_media()
    {
        $command = new DeleteMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2');

        $this->repository
            ->expects($this->never())
            ->method('remove')
        ;

        $this->expectException(MediaNotFound::class);
        $this->expectExceptionMessage('Media by id "64c2c4b7-33f5-11e8-97f3-005056806fb2" not found.');

        call_user_func($this->handler, $command);
    }
}
