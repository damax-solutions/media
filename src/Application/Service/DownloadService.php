<?php

declare(strict_types=1);

namespace Damax\Media\Application\Service;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaNotUploaded;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadService
{
    use MediaServiceTrait;

    private $mediaRepository;
    private $storage;

    public function __construct(MediaRepository $mediaRepository, Storage $storage)
    {
        $this->mediaRepository = $mediaRepository;
        $this->storage = $storage;
    }

    /**
     * @throws MediaNotFound
     * @throws MediaNotUploaded
     */
    public function download(string $mediaId): Response
    {
        $media = $this->getMedia($mediaId);

        if (null === $media->file()) {
            throw MediaNotUploaded::byId($mediaId);
        }

        $output = fopen('php://output', 'wb');

        $response = new StreamedResponse(function () use ($media, $output) {
            $this->storage->streamTo($media, $output);
        });

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $media->file()->basename());

        $response->headers->set('Content-Length', $media->file()->size());
        $response->headers->set('Content-Type', $media->file()->mimeType());
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
