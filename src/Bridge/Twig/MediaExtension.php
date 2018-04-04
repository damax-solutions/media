<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Twig;

use Damax\Media\Domain\Image\UrlBuilder;
use Damax\Media\Domain\Model\Media;
use Twig\TwigFunction;

class MediaExtension extends \Twig_Extension
{
    private $urlBuilder;

    public function __construct(UrlBuilder $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_url', [$this, 'buildMediaUrl']),
        ];
    }

    public function buildMediaUrl(Media $media, array $params = []): string
    {
        return $this->urlBuilder->build($media, $params);
    }
}
