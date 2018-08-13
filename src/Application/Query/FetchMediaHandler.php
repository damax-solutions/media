<?php

declare(strict_types=1);

namespace Application\Query;

use Damax\Media\Application\Dto\Assembler;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Model\MediaRepository;

final class FetchMediaHandler extends MediaHandler
{
    private $assembler;

    public function __construct(MediaRepository $repository, Assembler $assembler)
    {
        parent::__construct($repository);

        $this->assembler = $assembler;
    }

    /**
     * @throws MediaNotFound
     */
    public function __invoke(FetchMedia $query): MediaDto
    {
        $media = $this->getMedia($query);

        return $this->assembler->toMediaDto($media);
    }
}
