<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Controller;

use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Service\DownloadService;
use Damax\Media\Application\Service\MediaService;
use Damax\Media\Application\Service\UploadService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaController
{
    public function createAction(Request $request, MediaService $service)
    {
        $command = new CreateMedia();

        $command->mimeType = $request->headers->get('X-Upload-Content-Type');
        $command->size = (int) $request->headers->get('X-Upload-Content-Length');

        $service->create($command);
    }

    public function uploadAction(Request $request, string $id, UploadService $service)
    {
        $command = new UploadMedia();

        $command->mediaId = $id;
        $command->stream = $request->getContent(true);
        $command->mimeType = $request->headers->get('Content-Type');
        $command->size = (int) $request->headers->get('Content-Length');

        $service->upload($command);
    }

    public function downloadAction(string $id, DownloadService $service): Response
    {
        return $service->download($id);
    }
}
