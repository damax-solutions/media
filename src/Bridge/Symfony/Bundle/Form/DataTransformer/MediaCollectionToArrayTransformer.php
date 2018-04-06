<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class MediaCollectionToArrayTransformer implements DataTransformerInterface
{
    private $mediaTransformer;

    public function __construct(DataTransformerInterface $mediaTransformer)
    {
        $this->mediaTransformer = $mediaTransformer;
    }

    public function transform($value): array
    {
        if (null === $value) {
            return [];
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return array_map([$this->mediaTransformer, 'transform'], $value);
    }

    public function reverseTransform($value): array
    {
        if ('' === $value) {
            return [];
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return array_map([$this->mediaTransformer, 'reverseTransform'], $value);
    }
}
