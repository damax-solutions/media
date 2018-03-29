<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaNotUploaded;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Flysystem\Registry;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use Symfony\Component\HttpFoundation\Response;

class ProcessService
{
    use MediaServiceTrait;

    private $registry;
    private $serverConfig;

    public function __construct(MediaRepository $mediaRepository, Registry $registry, array $serverConfig)
    {
        $this->mediaRepository = $mediaRepository;
        $this->registry = $registry;
        $this->serverConfig = $serverConfig;
    }

    /**
     * @throws MediaNotFound
     * @throws MediaNotUploaded
     */
    public function process(string $mediaId, array $params): Response
    {
        $media = $this->getMedia($mediaId);

        if (null === $file = $media->file()) {
            throw MediaNotUploaded::byId($mediaId);
        }

        $config = array_merge($this->serverConfig, [
            'source' => $this->registry->get($file->storage()),
            'cache' => $this->registry->get($this->serverConfig['cache']),
            'response' => new SymfonyResponseFactory(),
        ]);

        return ServerFactory::create($config)->getImageResponse($file->key(), $params);
    }
}
