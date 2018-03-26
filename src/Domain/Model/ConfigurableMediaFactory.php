<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use Assert\Assert;

class ConfigurableMediaFactory implements MediaFactory
{
    private $repository;

    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data, User $creator = null): Media
    {
        Assert::that($data)
            ->keyIsset('type')
            ->keyIsset('name')
            ->keyIsset('mime_type')
            ->keyIsset('size')
        ;

        $info = new MediaInfo($data['mime_type'], $data['size']);

        return new Media($this->repository->nextId(), $data['type'], $data['name'], $info, $creator);
    }
}
