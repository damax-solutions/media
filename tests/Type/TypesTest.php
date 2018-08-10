<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Type;

use Damax\Media\Type\Definition;
use Damax\Media\Type\Types;
use PHPUnit\Framework\TestCase;

class TypesTest extends TestCase
{
    /**
     * @var Definition
     */
    private $def1;

    /**
     * @var Definition
     */
    private $def2;

    /**
     * @var Types
     */
    private $types;

    protected function setUp()
    {
        $this->def1 = new Definition('s3', 1, []);
        $this->def2 = new Definition('s3', 2, []);
        $this->types = new Types(['foo' => $this->def1, 'bar' => $this->def2]);
    }

    /**
     * @test
     */
    public function it_creates_types()
    {
        $this->assertSame(['foo', 'bar'], $this->types->names());

        $this->assertSame($this->def1, $this->types->definition('foo'));
        $this->assertSame($this->def2, $this->types->definition('bar'));

        $this->assertTrue($this->types->hasDefinition('foo'));
        $this->assertTrue($this->types->hasDefinition('bar'));
        $this->assertFalse($this->types->hasDefinition('baz'));
    }

    /**
     * @test
     */
    public function it_resets_types()
    {
        $this->types->reset();

        $this->assertEquals([], $this->types->names());
    }
}
