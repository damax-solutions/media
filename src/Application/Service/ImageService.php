<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Exception\ImageProcessingFailure;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaNotUploaded;
use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Model\MediaRepository;
use Symfony\Component\HttpFoundation\Response;

class ImageService
{
    use MediaServiceTrait;

    private $manipulator;

    public function __construct(MediaRepository $mediaRepository, Manipulator $manipulator)
    {
        $this->mediaRepository = $mediaRepository;
        $this->manipulator = $manipulator;
    }

    /**
     * @throws MediaNotFound
     * @throws MediaNotUploaded
     * @throws ImageProcessingFailure
     */
    public function process(string $mediaId, array $params): Response
    {
        $media = $this->getMedia($mediaId);

        if (null === $file = $media->file()) {
            throw MediaNotUploaded::byId($mediaId);
        }

        if (!$file->image()) {
            throw new ImageProcessingFailure('Only image transformation is supported.');
        }

        if (!Manipulator::validParams($params)) {
            throw new ImageProcessingFailure('Invalid transformation parameters.');
        }

        return $this->manipulator->processFile($file, $params);
    }
}
