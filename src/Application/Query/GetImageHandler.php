<?php

declare(strict_types=1);

namespace Damax\Media\Application\Query;

use Damax\Media\Application\Exception\ImageProcessingFailure;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Exception\InvalidFile;
use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Model\MediaRepository;
use Symfony\Component\HttpFoundation\Response;

final class GetImageHandler extends MediaHandler
{
    private $manipulator;

    public function __construct(MediaRepository $repository, Manipulator $manipulator)
    {
        parent::__construct($repository);

        $this->manipulator = $manipulator;
    }

    /**
     * @throws MediaNotFound
     * @throws ImageProcessingFailure
     */
    public function __invoke(GetImage $query): Response
    {
        $media = $this->getMedia($query);

        if (!Manipulator::validParams($query->params())) {
            throw new ImageProcessingFailure('Invalid parameters.');
        }

        try {
            return $this->manipulator->processImage($media, $query->params());
        } catch (InvalidFile $e) {
            throw new ImageProcessingFailure('Manipulation failed.');
        }
    }
}
