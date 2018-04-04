<?php

declare(strict_types=1);

namespace Damax\Media\Doctrine\Orm;

use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaRepository as MediaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MediaRepository implements MediaRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var string
     */
    private $className;

    public function __construct(EntityManagerInterface $em, string $mediaClassName)
    {
        $this->em = $em;
        $this->className = $mediaClassName;
    }

    public function nextId(): UuidInterface
    {
        return Uuid::uuid4();
    }

    public function byId(UuidInterface $id): ?Media
    {
        /** @var Media $media */
        $media = $this->em->find($this->className, $id);

        return $media;
    }

    public function save(Media $media): void
    {
        $this->em->persist($media);
        $this->em->flush($media);
    }

    public function remove(Media $media): void
    {
        $this->em->remove($media);
        $this->em->flush($media);
    }
}
