<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Twig;

use Damax\Media\Domain\FileFormatter;
use Damax\Media\Domain\Image\UrlBuilder;
use Damax\Media\Domain\Model\MediaId;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig_Extension;

final class MediaExtension extends Twig_Extension
{
    private $fileFormatter;
    private $urlBuilder;

    public function __construct(FileFormatter $fileFormatter, UrlBuilder $urlBuilder = null)
    {
        $this->fileFormatter = $fileFormatter;
        $this->urlBuilder = $urlBuilder;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_image_url', [$this, 'buildImageUrl']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('media_file_size', [$this->fileFormatter, 'formatSize']),
        ];
    }

    public function buildImageUrl(string $imageId, array $params = []): string
    {
        $mediaId = MediaId::fromString($imageId);

        return $this->urlBuilder->build($mediaId, $params);
    }
}
