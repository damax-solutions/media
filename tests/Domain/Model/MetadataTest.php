<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Model;

use Damax\Media\Domain\Model\Metadata;
use PHPUnit\Framework\TestCase;

class MetadataTest extends TestCase
{
    /**
     * @var Metadata
     */
    private $metadata;

    protected function setUp()
    {
        $this->metadata = new Metadata(['foo' => 'bar', 'baz' => 'qux']);
    }

    /**
     * @test
     */
    public function it_checks_key_existence()
    {
        $this->assertTrue($this->metadata->has('foo'));
        $this->assertTrue($this->metadata->has('baz'));
        $this->assertFalse($this->metadata->has('quux'));
    }

    /**
     * @test
     */
    public function it_retrieves_value_by_key()
    {
        $this->assertEquals('bar', $this->metadata->get('foo'));
        $this->assertEquals('qux', $this->metadata->get('baz'));
        $this->assertEquals('default', $this->metadata->get('quux', 'default'));
    }

    /**
     * @test
     */
    public function it_retrieves_all_values()
    {
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $this->metadata->all());
        $this->assertEquals('{"foo":"bar","baz":"qux"}', json_encode($this->metadata));
    }
}
