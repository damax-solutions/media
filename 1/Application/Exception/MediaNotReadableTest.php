<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Exception;

use Damax\Media\Domain\Exception\MediaNotReadable;
use PHPUnit\Framework\TestCase;

class MediaNotReadableTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_for_missing_file()
    {
        $e = MediaNotReadable::missingFile();

        $this->assertEquals('File is missing.', $e->getMessage());
    }
}
