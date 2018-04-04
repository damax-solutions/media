<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Twig;

use Damax\Media\Domain\Image\UrlBuilder;
use Twig\TwigFunction;

class MediaExtension extends \Twig_Extension
{
    private $urlBuilder;

    public function __construct(UrlBuilder $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_image_url', [$this, 'buildImageUrl']),
        ];
    }

    public function buildImageUrl(string $mediaId, array $params = []): string
    {
        return $this->urlBuilder->build($mediaId, $params);
    }
}
