<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Type;

use Damax\Media\Type\Definition;
use PHPUnit\Framework\TestCase;

class DefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_definition()
    {
        $definition = new Definition('s3', 1024, [
            'image/jpg',
            'image/png',
            'image/gif',
        ]);

        $this->assertEquals('s3', $definition->storage());
        $this->assertEquals(1024, $definition->maxFileSize());
        $this->assertEquals([
            'image/jpg',
            'image/png',
            'image/gif',
        ], $definition->mimeTypes());
    }
}
