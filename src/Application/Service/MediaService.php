<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Domain\Model\MediaFactory;
use Damax\Media\Domain\Model\MediaRepository;

class MediaService
{
    private $repository;
    private $mediaFactory;
    private $assembler;

    public function __construct(MediaRepository $repository, MediaFactory $mediaFactory, Assembler $assembler)
    {
        $this->repository = $repository;
        $this->mediaFactory = $mediaFactory;
        $this->assembler = $assembler;
    }

    public function create(CreateMedia $command): MediaDto
    {
        $media = $this->mediaFactory->create($command);

        $this->repository->save($media);

        return $this->assembler->toMediaDto($media);
    }
}
