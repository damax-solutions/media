<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Twig;

use Damax\Media\Domain\FileFormatter;
use Damax\Media\Domain\Image\UrlBuilder;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MediaExtension extends \Twig_Extension
{
    private $urlBuilder;
    private $fileFormatter;

    public function __construct(UrlBuilder $urlBuilder, FileFormatter $fileFormatter)
    {
        $this->urlBuilder = $urlBuilder;
        $this->fileFormatter = $fileFormatter;
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
            new TwigFilter('media_file_size', [$this, 'formatFileSize']),
        ];
    }

    public function buildImageUrl(string $mediaId, array $params = []): string
    {
        return $this->urlBuilder->build($mediaId, $params);
    }

    public function formatFileSize(int $size): string
    {
        return $this->fileFormatter->formatSize($size);
    }
}
