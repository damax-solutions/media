<?php

declare(strict_types=1);

namespace Application\Command;

use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Domain\Model\MediaRepository;

final class CreateMediaHandler
{
    private $repository;
    private $factory;

    public function __construct(MediaRepository $repository, MediaFactory $factory)
    {
        $this->repository = $repository;
        $this->factory = $factory;
    }

    public function __invoke(CreateMedia $command): void
    {
        $media = $this->factory->create($command->media());

        $this->repository->add($media);
    }
}
