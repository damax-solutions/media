<?php

declare(strict_types=1);

namespace Damax\Media\Application\Command;

use Damax\Common\Domain\Model\Metadata;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaUploadFailure;
use Damax\Media\Domain\Metadata\Reader;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;
use Throwable;

final class UploadMediaHandler
{
    private $repository;
    private $storage;
    private $metadata;

    public function __construct(MediaRepository $repository, Storage $storage, Reader $metadata)
    {
        $this->repository = $repository;
        $this->storage = $storage;
        $this->metadata = $metadata;
    }

    /**
     * @throws MediaNotFound
     * @throws MediaUploadFailure
     */
    public function __invoke(UploadMedia $command): void
    {
        $mediaId = $command->mediaId();

        if (null === $media = $this->repository->byId($mediaId)) {
            throw MediaNotFound::byId($mediaId);
        }

        if ($this->metadata->supports($command->upload())) {
            $metadata = $this->metadata->extract($command->upload());
        } else {
            $metadata = Metadata::create();
        }

        try {
            $file = $this->storage->write($media, $command->upload());

            $media->upload($file, $metadata);
        } catch (Throwable $e) {
            throw new MediaUploadFailure('Upload failed.');
        }

        $this->repository->update($media);
    }
}
