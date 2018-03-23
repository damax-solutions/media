<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaRepository;
use Ramsey\Uuid\Uuid;

trait MediaServiceTrait
{
    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * @throws MediaNotFound
     */
    private function getMedia(string $id): Media
    {
        if (null === $media = $this->mediaRepository->byId(Uuid::fromString($id))) {
            throw MediaNotFound::byId($id);
        }

        return $media;
    }
}
