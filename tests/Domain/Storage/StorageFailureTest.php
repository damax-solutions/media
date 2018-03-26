<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain;

use Damax\Media\Domain\Storage\StorageFailure;
use PHPUnit\Framework\TestCase;

class StorageFailureTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_for_invalid_write()
    {
        $e = StorageFailure::invalidWrite('qwe');

        $this->assertEquals('Unable to write key "qwe".', $e->getMessage());
    }
}
