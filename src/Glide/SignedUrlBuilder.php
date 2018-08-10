<?php

declare(strict_types=1);

namespace Damax\Media\Glide;

use Damax\Media\Domain\Image\UrlBuilder;
use Damax\Media\Domain\Model\MediaId;
use League\Glide\Signatures\SignatureInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SignedUrlBuilder implements UrlBuilder
{
    private $urlGenerator;
    private $signature;
    private $routeName;

    public function __construct(UrlGeneratorInterface $urlGenerator, SignatureInterface $signature, string $routeName = 'media_image')
    {
        $this->urlGenerator = $urlGenerator;
        $this->signature = $signature;
        $this->routeName = $routeName;
    }

    public function build(MediaId $mediaId, array $params): string
    {
        $id = ['id' => (string) $mediaId];

        $url = $this->urlGenerator->generate($this->routeName, $id);

        $params = $this->signature->addSignature($url, $params);

        return $this->urlGenerator->generate($this->routeName, $params + $id);
    }
}
