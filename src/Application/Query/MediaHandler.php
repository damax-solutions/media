<?php

declare(strict_types=1);

namespace Application\Query;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaRepository;

abstract class MediaHandler
{
    protected $repository;

    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws MediaNotFound
     */
    protected function getMedia(MediaQuery $query): Media
    {
        $mediaId = $query->mediaId();

        if (null === $media = $this->repository->byId($mediaId)) {
            throw MediaNotFound::byId($mediaId);
        }

        return $media;
    }
}
