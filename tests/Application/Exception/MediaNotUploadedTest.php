<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Exception;

use Damax\Media\Application\Exception\MediaNotUploaded;
use PHPUnit\Framework\TestCase;

class MediaNotUploadedTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_by_id()
    {
        $e = MediaNotUploaded::byId('183702c5-30de-11e8-97f3-005056806fb2');

        $this->assertEquals('Media by id "183702c5-30de-11e8-97f3-005056806fb2" was not uploaded.', $e->getMessage());
    }
}
