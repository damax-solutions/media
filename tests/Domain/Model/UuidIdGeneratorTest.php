<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\UuidIdGenerator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UuidIdGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function it_generates_media_id()
    {
        $generator = new UuidIdGenerator();

        $mediaId = $generator->mediaId();

        $this->assertTrue(Uuid::isValid($mediaId));
        $this->assertNotEquals($mediaId, $generator->mediaId());
    }
}
