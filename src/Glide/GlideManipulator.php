<?php

declare(strict_types=1);

namespace Damax\Media\Glide;

use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Model\File;
use Damax\Media\Flysystem\Registry;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\ServerFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class GlideManipulator extends Manipulator
{
    private $registry;
    private $requestStack;
    private $serverConfig;

    public function __construct(Registry $registry, RequestStack $requestStack, array $serverConfig)
    {
        $this->registry = $registry;
        $this->requestStack = $requestStack;
        $this->serverConfig = $serverConfig;
    }

    public function processFile(File $file, array $params): Response
    {
        $request = $this->requestStack->getCurrentRequest();

        $config = array_merge($this->serverConfig, [
            'source' => $this->registry->get($file->storage()),
            'cache' => $this->registry->get($this->serverConfig['cache']),
            'response' => new SymfonyResponseFactory($request),
        ]);

        return ServerFactory::create($config)->getImageResponse($file->key(), $params);
    }
}
