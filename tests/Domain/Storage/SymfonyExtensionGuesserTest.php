<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain;

use Damax\Media\Domain\Storage\SymfonyExtensionGuesser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

class SymfonyExtensionGuesserTest extends TestCase
{
    /**
     * @var ExtensionGuesserInterface|MockObject
     */
    private $delegate;

    /**
     * @var SymfonyExtensionGuesser
     */
    private $guesser;

    protected function setUp()
    {
        $this->delegate = $this->createMock(ExtensionGuesserInterface::class);
        $this->guesser = new SymfonyExtensionGuesser($this->delegate);
    }

    /**
     * @test
     */
    public function it_guesses_extension()
    {
        $this->delegate
            ->method('guess')
            ->with('application/pdf')
            ->willReturn('pdf')
        ;

        $this->assertEquals('pdf', $this->guesser->guess('application/pdf'));
    }
}
