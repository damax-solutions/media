<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Form\DataTransformer;

use Damax\Media\Domain\Model\Media;
use Damax\Media\Domain\Model\MediaRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaToIdTransformer implements DataTransformerInterface
{
    private $repository;

    public function __construct(MediaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function transform($value): string
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof Media) {
            throw new TransformationFailedException(sprintf('Expected "%s" class.', Media::class));
        }

        return (string) $value->id();
    }

    public function reverseTransform($value): ?Media
    {
        if ('' === $value || null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string');
        }

        if (!Uuid::isValid($value)) {
            throw new TransformationFailedException('Expected valid UUID.');
        }

        return $this->repository->byId(Uuid::fromString($value));
    }
}
