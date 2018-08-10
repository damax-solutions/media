<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Domain\Storage\Guesser;

use Damax\Media\Domain\Storage\Guesser\SymfonyGuesser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;

class SymfonyGuesserTest extends TestCase
{
    /**
     * @var ExtensionGuesserInterface|MockObject
     */
    private $mimeTypes;

    /**
     * @var SymfonyGuesser
     */
    private $guesser;

    protected function setUp()
    {
        $this->mimeTypes = $this->createMock(ExtensionGuesserInterface::class);
        $this->guesser = new SymfonyGuesser($this->mimeTypes);
    }

    /**
     * @test
     */
    public function it_guesses_extension()
    {
        $this->mimeTypes
            ->method('guess')
            ->with('application/pdf')
            ->willReturn('pdf')
        ;

        $this->assertEquals('pdf', $this->guesser->guessExtension('application/pdf'));
    }
}
