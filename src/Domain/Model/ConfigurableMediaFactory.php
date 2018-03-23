<?php

declare(strict_types=1);

namespace Damax\Media\Domain\Model;

use Assert\Assert;
use Damax\Media\Type\Configuration;

class ConfigurableMediaFactory implements MediaFactory
{
    private $repository;
    private $types;

    public function __construct(MediaRepository $repository, Configuration $types)
    {
        $this->repository = $repository;
        $this->types = $types;
    }

    public function create($data, User $creator = null): Media
    {
        Assert::that($data)
            ->keyIsset('type')
            ->keyIsset('name')
            ->keyIsset('file')
        ;

        if (!$this->types->hasDefinition($data['type'])) {
            throw InvalidMediaInput::unregisteredType($data['type']);
        }

        $file = File::metadata($data['file']);

        return new Media($this->repository->nextId(), $data['type'], $data['name'], $file, $creator);
    }
}
