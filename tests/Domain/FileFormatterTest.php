<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain;

use Damax\Media\Domain\FileFormatter;
use PHPUnit\Framework\TestCase;

class FileFormatterTest extends TestCase
{
    /**
     * @var FileFormatter
     */
    private $formatter;

    protected function setUp()
    {
        $this->formatter = new FileFormatter();
    }

    /**
     * @test
     *
     * @dataProvider provideSizeData
     */
    public function it_formats_size(int $size, string $result)
    {
        $this->assertEquals($result, $this->formatter->formatSize($size));
    }

    public function provideSizeData(): array
    {
        return [
            [0, '0 B'],
            [512, '512 B'],
            [1024, '1 KB'],
            [1310719, '1.2 MB'],
            [1310720, '1.3 MB'],
            [1347545989, '1.25 GB'],
            [1347545990, '1.26 GB'],
        ];
    }
}
