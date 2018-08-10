<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Storage\Keys;

use Damax\Media\Domain\Storage\Keys\FixedKeys;
use Damax\Media\Domain\Storage\Keys\Keys;
use PHPUnit\Framework\TestCase;

class FixedKeysTest extends TestCase
{
    /**
     * @var Keys
     */
    private $keys;

    protected function setUp()
    {
        $this->keys = new FixedKeys(['foo', 'bar']);
    }

    /**
     * @test
     */
    public function it_retrieves_key()
    {
        $this->assertEquals('foo', $this->keys->nextKey());
        $this->assertEquals('bar', $this->keys->nextKey());
        $this->assertEquals('foo', $this->keys->nextKey());
    }
}
