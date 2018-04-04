<?php

declare(strict_types=1);

namespace Damax\Media\Glide;

use Damax\Media\Domain\Image\UrlBuilder;
use Damax\Media\Domain\Model\Media;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SignedUrlBuilder implements UrlBuilder
{
    private $urlGenerator;
    private $routeName;

    public function __construct(UrlGeneratorInterface $urlGenerator, string $routeName)
    {
        $this->urlGenerator = $urlGenerator;
        $this->routeName = $routeName;
    }

    public function build(Media $media, array $params): string
    {
        return $this->urlGenerator->generate($this->routeName, array_merge($params, ['id' => $media->id()]));
    }
}
