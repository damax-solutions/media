<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain;

use Damax\Media\Domain\Storage\ExtensionGuesser;
use Damax\Media\Domain\Storage\RandomKeys;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

/**
 * @group time-sensitive
 */
class RandomKeysTest extends TestCase
{
    /**
     * @var ExtensionGuesser|MockObject
     */
    private $guesser;

    /**
     * @var RandomKeys
     */
    private $keys;

    protected function setUp()
    {
        ClockMock::register(RandomKeys::class);
        ClockMock::withClockMock(strtotime('2018-01-20 06:10:00'));

        $this->guesser = $this->createMock(ExtensionGuesser::class);
        $this->keys = new RandomKeys($this->guesser, 8);
    }

    /**
     * @test
     */
    public function it_fetches_next_key_without_mime_type()
    {
        $this->guesser
            ->expects($this->never())
            ->method('guess')
        ;

        $key1 = $this->keys->nextKey();
        $key2 = $this->keys->nextKey();

        $this->assertNotEquals($key1, $key2);
        $this->assertStringStartsWith('2018/01/20/', $key1);
        $this->assertStringStartsWith('2018/01/20/', $key2);
        $this->assertEquals(8, strlen(explode('/', $key1)[3]));
        $this->assertEquals(8, strlen(explode('/', $key2)[3]));
    }

    /**
     * @test
     */
    public function it_fetches_next_key_with_mime_type()
    {
        $this->guesser
            ->expects($this->exactly(2))
            ->method('guess')
            ->withConsecutive(
                ['application/pdf'],
                ['application/json']
            )
            ->willReturnOnConsecutiveCalls('pdf', 'json')
        ;

        $key1 = $this->keys->nextKey(['mime_type' => 'application/pdf']);
        $key2 = $this->keys->nextKey(['mime_type' => 'application/json']);

        $this->assertNotEquals($key1, $key2);
        $this->assertStringStartsWith('2018/01/20/', $key1);
        $this->assertStringStartsWith('2018/01/20/', $key2);
        $this->assertStringEndsWith('.pdf', $key1);
        $this->assertStringEndsWith('.json', $key2);
        $this->assertEquals(12, strlen(explode('/', $key1)[3]));
        $this->assertEquals(13, strlen(explode('/', $key2)[3]));
    }
}
