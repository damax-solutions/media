<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Image;

use Damax\Media\Domain\Image\Manipulator;
use PHPUnit\Framework\TestCase;

class ManipulatorTest extends TestCase
{
    /**
     * @test
     */
    public function it_validates_params()
    {
        $this->assertTrue(Manipulator::validParams([
            'w' => 200,
            'h' => 200,
        ]));
        $this->assertFalse(Manipulator::validParams([
            'w' => 200,
            'h' => 200,
            'foo' => 'bar',
            'baz' => 'qux',
        ]));
    }
}
