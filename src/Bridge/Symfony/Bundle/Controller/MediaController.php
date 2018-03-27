<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Controller;

use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Service\DownloadService;
use Damax\Media\Application\Service\MediaService;
use Damax\Media\Application\Service\UploadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/media")
 */
class MediaController
{
    /**
     * @Method("POST")
     * @Route("")
     *
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function createAction(Request $request, MediaService $service, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        /* @var CreateMedia $command */
        try {
            $command = $serializer->deserialize($request->getContent(), CreateMedia::class, 'json');
        } catch (ExceptionInterface $e) {
            throw new UnprocessableEntityHttpException();
        }

        $command->mimeType = $request->headers->get('X-Upload-Content-Type');
        $command->size = (int) $request->headers->get('X-Upload-Content-Length');

        foreach ($validator->validate($command) as $error) {
            throw new BadRequestHttpException(sprintf('%s: %s', $error->getPropertyPath(), $error->getMessage()));
        }

        $service->create($command);
    }

    /**
     * @Method("PUT")
     * @Route("/{id}/upload")
     */
    public function uploadAction(Request $request, string $id, UploadService $service)
    {
        $command = new UploadMedia();

        $command->mediaId = $id;
        $command->stream = $request->getContent(true);
        $command->mimeType = $request->headers->get('Content-Type');
        $command->size = (int) $request->headers->get('Content-Length');

        $service->upload($command);
    }

    /**
     * @Method("GET")
     * @Route("/{id}/download")
     */
    public function downloadAction(string $id, DownloadService $service): Response
    {
        return $service->download($id);
    }
}
