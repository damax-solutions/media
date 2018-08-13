<?php

declare(strict_types=1);

namespace Damax\Media\Doctrine\Orm;

use Damax\Common\Doctrine\Orm\OrmRepositoryTrait;
use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaId;
use Damax\Media\Domain\Model\MediaRepository as MediaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final class MediaRepository implements MediaRepositoryInterface
{
    use OrmRepositoryTrait;

    public function __construct(EntityManagerInterface $em, string $mediaClassName)
    {
        $this->em = $em;
        $this->className = $mediaClassName;
    }

    public function byId(MediaId $id): ?Media
    {
        /** @var Media $media */
        $media = $this->em->find($this->className, (string) $id);

        return $media;
    }

    public function add(Media $media): void
    {
        $this->em->persist($media);
        $this->em->flush($media);
    }

    public function update(Media $media): void
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
