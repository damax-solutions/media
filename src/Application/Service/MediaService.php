<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Domain\Model\MediaRepository;

class MediaService
{
    use MediaServiceTrait;

    private $mediaFactory;
    private $assembler;

    public function __construct(MediaRepository $mediaRepository, MediaFactory $mediaFactory, Assembler $assembler)
    {
        $this->mediaRepository = $mediaRepository;
        $this->mediaFactory = $mediaFactory;
        $this->assembler = $assembler;
    }

    public function create(CreateMedia $command): MediaDto
    {
        $media = $this->mediaFactory->create($command);

        $this->mediaRepository->save($media);

        return $this->assembler->toMediaDto($media);
    }

    /**
     * @throws MediaNotFound
     */
    public function fetch(string $mediaId): MediaDto
    {
        return $this->assembler->toMediaDto($this->getMedia($mediaId));
    }
}
