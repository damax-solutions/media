<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;

final class DeleteMediaHandler
{
    private $repository;
    private $storage;

    public function __construct(MediaRepository $repository, Storage $storage)
    {
        $this->repository = $repository;
        $this->storage = $storage;
    }

    /**
     * @throws MediaNotFound
     */
    public function __invoke(DeleteMedia $command): void
    {
        $mediaId = $command->mediaId();

        if (null === $media = $this->repository->byId($mediaId)) {
            throw MediaNotFound::byId($mediaId);
        }

        $this->storage->delete($media);

        $this->repository->remove($media);
    }
}
