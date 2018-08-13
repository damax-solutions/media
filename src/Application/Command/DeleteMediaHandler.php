<?php

declare(strict_types=1);

namespace Application\Command;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Model\MediaRepository;

final class DeleteMediaHandler
{
    private $repository;

    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
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

        $this->repository->remove($media);
    }
}
