<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

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
        $dto = $command->media();

        $media = $this->factory->create([
            'id' => $command->mediaId(),
            'type' => $dto->type,
            'name' => $dto->name,
            'mime_type' => $dto->mimeType,
            'file_size' => $dto->fileSize,
        ]);

        $this->repository->add($media);
    }
}
