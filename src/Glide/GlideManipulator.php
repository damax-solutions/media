<?php

declare(strict_types=1);

namespace Damax\Media\Glide;

use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Flysystem\Registry\Registry;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

final class GlideManipulator extends Manipulator
{
    private $registry;
    private $signature;
    private $requestStack;
    private $serverConfig;

    public function __construct(Registry $registry, SignatureInterface $signature, RequestStack $requestStack, array $serverConfig)
    {
        $this->registry = $registry;
        $this->signature = $signature;
        $this->requestStack = $requestStack;
        $this->serverConfig = $serverConfig;
    }

    public function processImage(Media $media, array $params): Response
    {
        if (!($request = $this->requestStack->getCurrentRequest())) {
            throw new RuntimeException('Request stack is empty.');
        }

        $this->signature->validateRequest($request->getPathInfo(), $params);

        $file = $media->file();
        $info = $file->info();

        if (!$info->image()) {
            throw InvalidFile::unsupportedMimeType($info->mimeType());
        }

        $config = array_merge($this->serverConfig, [
            'source' => $this->registry->get($file->storage()),
            'cache' => $this->registry->get($this->serverConfig['cache']),
            'response' => new SymfonyResponseFactory($request),
        ]);

        return ServerFactory::create($config)->getImageResponse($file->key(), $params);
    }
}
