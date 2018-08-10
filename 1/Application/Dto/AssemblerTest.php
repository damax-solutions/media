<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Dto;

use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Tests\Domain\Model\PendingPdfMedia;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class AssemblerTest extends TestCase
{
    /**
     * @var Assembler
     */
    private $assembler;

    protected function setUp()
    {
        $this->assembler = new Assembler();
    }

    /**
     * @test
     */
    public function it_assembles_media_dto()
    {
        $dto = $this->assembler->toMediaDto(new PendingPdfMedia());

        $this->assertEquals('183702c5-30de-11e8-97f3-005056806fb2', $dto->id);
        $this->assertEquals('pending', $dto->status);
        $this->assertEquals('document', $dto->type);
        $this->assertEquals('Test PDF document', $dto->name);
        $this->assertEquals('application/pdf', $dto->mimeType);
        $this->assertEquals(1024, $dto->size);
        $this->assertInstanceOf(DateTimeInterface::class, $dto->createdAt);
        $this->assertInstanceOf(DateTimeInterface::class, $dto->updatedAt);
        $this->assertEquals([], $dto->metadata);
    }
}
