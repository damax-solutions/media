<?php

declare(strict_types=1);

namespace Damax\Media\Tests\Application\Command;

use Damax\Media\Bridge\Twig\MediaExtension;
use Damax\Media\Domain\FileFormatter;
use Damax\Media\Domain\Image\UrlBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig_Environment;
use Twig_Loader_Array;

class MediaExtensionTest extends TestCase
{
    /**
     * @var Twig_Loader_Array
     */
    private $loader;

    /**
     * @var UrlBuilder|MockObject
     */
    private $urlBuilder;

    /**
     * @var Twig_Environment
     */
    private $twig;

    protected function setUp()
    {
        $this->urlBuilder = $this->createMock(UrlBuilder::class);

        $this->loader = new Twig_Loader_Array();

        $this->twig = new Twig_Environment($this->loader, ['debug' => true, 'cache' => false]);
        $this->twig->addExtension(new MediaExtension(new FileFormatter(), $this->urlBuilder));
    }

    /**
     * @test
     */
    public function it_formats_file_size()
    {
        $this->loader->setTemplate('index', '<p>{{ fileSize | media_file_size }}</p>');

        $this->assertEquals('<p>1.2 MB</p>', $this->twig->render('index', ['fileSize' => 1310719]));
        $this->assertEquals('<p>1.3 MB</p>', $this->twig->render('index', ['fileSize' => 1310720]));
    }

    /**
     * @test
     */
    public function it_builds_image_url()
    {
        $this->loader->setTemplate('index', '<p>{{ media_image_url(mediaId, { w: 240, h: 180 }) }}</p>');

        $this->urlBuilder
            ->expects($this->once())
            ->method('build')
            ->with('64c2c4b7-33f5-11e8-97f3-005056806fb2', ['w' => 240, 'h' => 180])
            ->willReturn('image-url')
        ;

        $this->assertEquals('<p>image-url</p>', $this->twig->render('index', ['mediaId' => '64c2c4b7-33f5-11e8-97f3-005056806fb2']));
    }
}
