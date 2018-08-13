<?php

declare(strict_types=1);

namespace Application\Query;

use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Domain\Model\MediaRepository;
use Damax\Media\Domain\Storage\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class DownloadMediaHandler extends MediaHandler
{
    private $storage;

    public function __construct(MediaRepository $repository, Storage $storage)
    {
        parent::__construct($repository);

        $this->storage = $storage;
    }

    /**
     * @throws MediaNotFound
     */
    public function __invoke(DownloadMedia $query): Response
    {
        $media = $this->getMedia($query);

        $output = fopen('php://output', 'wb');

        $response = new StreamedResponse(function () use ($media, $output) {
            $this->storage->streamTo($media, $output);
        });

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $media->file()->basename());

        $response->headers->set('Content-Length', $media->info()->fileSize());
        $response->headers->set('Content-Type', $media->info()->mimeType());
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
