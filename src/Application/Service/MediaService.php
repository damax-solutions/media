<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaUploadFailure;
use Damax\Media\Domain\Metadata\Reader;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;
use Throwable;

class MediaService
{
    use MediaServiceTrait;

    private $storage;
    private $metadata;
    private $assembler;

    public function __construct(MediaRepository $mediaRepository, Storage $storage, Reader $metadata, Assembler $assembler)
    {
        $this->mediaRepository = $mediaRepository;
        $this->storage = $storage;
        $this->metadata = $metadata;
        $this->assembler = $assembler;
    }

    /**
     * @throws MediaNotFound
     */
    public function fetch(string $mediaId): MediaDto
    {
        return $this->assembler->toMediaDto($this->getMedia($mediaId));
    }

    /**
     * @throws MediaNotFound
     */
    public function delete(string $mediaId): MediaDto
    {
        $media = $this->getMedia($mediaId);

        $this->storage->remove($media);

        $this->mediaRepository->remove($media);

        return $this->assembler->toMediaDto($media);
    }

    /**
     * @throws MediaUploadFailure
     * @throws MediaNotFound
     */
    public function upload(UploadMedia $command): MediaDto
    {
        $media = $this->getMedia($command->mediaId);

        $metadata = $this->metadata->supports($command) ? $this->metadata->extract($command) : null;

        try {
            $media->upload($this->storage->write($media, $command), $metadata);
        } catch (Throwable $e) {
            throw new MediaUploadFailure($e->getMessage(), 0, $e);
        }

        $this->mediaRepository->save($media);

        return $this->assembler->toMediaDto($media);
    }
}
