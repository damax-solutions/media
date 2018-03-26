<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Type;

use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use PHPUnit\Framework\TestCase;

class TypesTest extends TestCase
{
    /**
     * @test
     */
    public function it_adds_definitions_on_types_creation()
    {
        $def1 = new Definition('s3', 1, []);
        $def2 = new Definition('s3', 2, []);

        $types = new Types(['foo' => $def1, 'bar' => $def2]);

        $this->assertAttributeSame(['foo' => $def1, 'bar' => $def2], 'definitions', $types);

        return $types;
    }

    /**
     * @test
     *
     * @depends it_adds_definitions_on_types_creation
     */
    public function it_checks_definition_existence(Types $types)
    {
        $this->assertTrue($types->hasDefinition('foo'));
        $this->assertTrue($types->hasDefinition('bar'));
        $this->assertFalse($types->hasDefinition('baz'));
    }

    /**
     * @test
     *
     * @depends it_adds_definitions_on_types_creation
     */
    public function it_retrieves_definition(Types $types)
    {
        $def1 = $types->definition('foo');
        $def2 = $types->definition('bar');

        $this->assertEquals('s3', $def1->storage());
        $this->assertSame(1, $def1->maxFileSize());

        $this->assertEquals('s3', $def2->storage());
        $this->assertSame(2, $def2->maxFileSize());
    }
}
