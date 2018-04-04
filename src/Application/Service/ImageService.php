<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Exception\ImageProcessingFailure;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaNotUploaded;
use Damax\Media\Domain\Exception\MediaNotReadable;
use Damax\Media\Domain\Image\Manipulator;
use Damax\Media\Domain\Model\MediaRepository;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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
        if (!Manipulator::validParams($params)) {
            throw new ImageProcessingFailure('Invalid transformation parameters.');
        }

        $media = $this->getMedia($mediaId);

        try {
            return $this->manipulator->processImage($media, $params);
        } catch (MediaNotReadable $e) {
            throw MediaNotUploaded::byId($mediaId);
        } catch (Throwable $e) {
            throw new ImageProcessingFailure('Manipulation failed.', 0, $e);
        }
    }
}
