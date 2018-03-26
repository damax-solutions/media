<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\MediaInfo;
use PHPUnit\Framework\TestCase;

class MediaInfoTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_media_info()
    {
        $info = new MediaInfo('application/pdf', 1024);

        $this->assertEquals('application/pdf', $info->mimeType());
        $this->assertEquals(1024, $info->size());
    }

    /**
     * @test
     */
    public function it_compares_media_info()
    {
        $info1 = new MediaInfo('application/pdf', 1024);
        $info2 = new MediaInfo('application/pdf', 1024);
        $info3 = new MediaInfo('application/json', 1024);

        $this->assertNotSame($info1, $info2);
        $this->assertTrue($info1->sameAs($info2));
        $this->assertFalse($info1->sameAs($info3));
    }
}
