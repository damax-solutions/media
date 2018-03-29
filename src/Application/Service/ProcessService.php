<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaNotUploaded;
use Damax\Media\Application\Exception\MediaProcessingFailure;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Flysystem\Registry;
use Damax\Media\Glide\Manipulations;
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
     * @throws MediaProcessingFailure
     */
    public function process(string $mediaId, array $params): Response
    {
        $media = $this->getMedia($mediaId);

        if (null === $file = $media->file()) {
            throw MediaNotUploaded::byId($mediaId);
        }

        if (!$file->image()) {
            throw new MediaProcessingFailure(sprintf('Only image transformation is supported.'));
        }

        if (!Manipulations::validParams($params)) {
            throw new MediaProcessingFailure('Invalid transformation parameters.');
        }

        $config = array_merge($this->serverConfig, [
            'source' => $this->registry->get($file->storage()),
            'cache' => $this->registry->get($this->serverConfig['cache']),
            'response' => new SymfonyResponseFactory(),
        ]);

        return ServerFactory::create($config)->getImageResponse($file->key(), $params);
    }
}
