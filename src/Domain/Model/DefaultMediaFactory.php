<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use Assert\Assert;

final class DefaultMediaFactory implements MediaFactory
{
    public function create($data): Media
    {
        Assert::that($data)
            ->keyIsset('id')
            ->keyIsset('type')
            ->keyIsset('name')
            ->keyIsset('mime_type')
            ->keyIsset('file_size')
        ;

        $info = FileInfo::fromArray($data);

        return new Media($data['id'], $data['type'], $data['name'], $info, $data['user_id'] ?? null);
    }
}
