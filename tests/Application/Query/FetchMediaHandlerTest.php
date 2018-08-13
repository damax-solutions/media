<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Query;

use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Query\FetchMedia;
use Damax\Media\Application\Query\FetchMediaHandler;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Tests\Domain\Model\PendingImageMedia;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Damax\Media\Application\Query\MediaQuery
 * @covers \Damax\Media\Application\Query\FetchMedia
 * @covers \Damax\Media\Application\Query\MediaHandler
 * @covers \Damax\Media\Application\Query\FetchMediaHandler
 */
class FetchMediaHandlerTest extends TestCase
{
    /**
     * @var MediaRepository|MockObject
     */
    private $repository;

    /**
     * @var FetchMediaHandler
     */
    private $handler;

    protected function setUp()
    {
        $this->repository = $this->createMock(MediaRepository::class);
        $this->handler = new FetchMediaHandler($this->repository, new Assembler());
    }

    /**
     * @test
     */
    public function it_fails_on_missing_media()
    {
        $this->expectException(MediaNotFound::class);
        $this->expectExceptionMessage('Media by id "64c2c4b7-33f5-11e8-97f3-005056806fb2" not found.');

        call_user_func($this->handler, new FetchMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2'));
    }

    /**
     * @test
     */
    public function it_executes_handler()
    {
        $media = new PendingImageMedia();

        $this->repository
            ->expects($this->once())
            ->method('byId')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2')
            ->willReturn($media)
        ;

        $query = new FetchMedia('64c2c4b7-33f5-11e8-97f3-005056806fb2');

        /** @var MediaDto $dto */
        $dto = call_user_func($this->handler, $query);

        $this->assertEquals('64c2c4b7-33f5-11e8-97f3-005056806fb2', $dto->id);
        $this->assertEquals('pending', $dto->status);
    }
}
