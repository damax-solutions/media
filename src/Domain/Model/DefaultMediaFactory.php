<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use Assert\Assert;

final class DefaultMediaFactory implements MediaFactory
{
    private $repository;

    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create($data): Media
    {
        Assert::that($data)
            ->keyIsset('type')
            ->keyIsset('name')
            ->keyIsset('mime_type')
            ->keyIsset('file_size')
        ;

        $info = FileInfo::fromArray($data);

        $userId = isset($data['user_id']) ? UserId::fromString($data['user_id']) : null;

        return new Media($this->repository->nextId(), $data['type'], $data['name'], $info, $userId);
    }
}
