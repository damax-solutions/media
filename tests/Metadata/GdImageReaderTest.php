<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Type;

use Damax\Media\Metadata\GdImageReader;
use PHPUnit\Framework\TestCase;

class GdImageReaderTest extends TestCase
{
    /**
     * @var GdImageReader
     */
    private $reader;

    protected function setUp()
    {
        $this->reader = new GdImageReader();
    }

    /**
     * @test
     */
    public function it_checks_support()
    {
        $this->assertFalse($this->reader->supports([]));
        $this->assertFalse($this->reader->supports(['mime_type' => 'application/pdf']));
        $this->assertTrue($this->reader->supports(['mime_type' => 'image/png']));
    }

    /**
     * @test
     */
    public function it_extracts_metadata()
    {
        $metadata = $this->reader->extract([
            'stream' => fopen(__DIR__ . '/fixture.png', 'rb'),
        ]);

        $this->assertEquals(1024, $metadata->get('width'));
        $this->assertEquals(530, $metadata->get('height'));
    }
}
