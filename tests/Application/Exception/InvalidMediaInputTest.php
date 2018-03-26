<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Exception;

use Damax\Media\Domain\Exception\InvalidMediaInput;
use PHPUnit\Framework\TestCase;

class InvalidMediaInputTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_with_unregistered_type()
    {
        $e = InvalidMediaInput::unregisteredType('document');

        $this->assertEquals('Media type "document" is not registered.', $e->getMessage());
    }

    /**
     * @test
     */
    public function it_creates_with_unsupported_storage()
    {
        $e = InvalidMediaInput::unsupportedStorage('s3');

        $this->assertEquals('Storage "s3" is not supported.', $e->getMessage());
    }
}
