<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;

class UploadService
{
    use MediaServiceTrait;

    private $storage;
    private $assembler;

    public function __construct(MediaRepository $mediaRepository, Storage $storage, Assembler $assembler)
    {
        $this->mediaRepository = $mediaRepository;
        $this->storage = $storage;
        $this->assembler = $assembler;
    }

    public function upload(UploadMedia $command): MediaDto
    {
        $media = $this->getMedia($command->mediaId);

        $media->upload($this->storage->write($media, $command));

        $this->mediaRepository->save($media);

        return $this->assembler->toMediaDto($media);
    }
}
