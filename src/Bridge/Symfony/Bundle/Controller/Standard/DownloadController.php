<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Controller\Standard;

use Damax\Media\Application\Exception\ImageProcessingFailure;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaNotUploaded;
use Damax\Media\Application\Service\DownloadService;
use Damax\Media\Application\Service\ImageService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/media")
 */
class DownloadController
{
    /**
     * @Method("GET")
     * @Route("/{id}/download", name="media_download")
     *
     * @throws NotFoundHttpException
     */
    public function downloadAction(string $id, DownloadService $service): Response
    {
        try {
            return $service->download($id);
        } catch (MediaNotFound | MediaNotUploaded $e) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Method("GET")
     * @Route("/{id}/image", name="media_image")
     *
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function imageAction(Request $request, string $id, ImageService $service): Response
    {
        try {
            return $service->process($id, $request->query->all());
        } catch (MediaNotFound | MediaNotUploaded $e) {
            throw new NotFoundHttpException();
        } catch (ImageProcessingFailure $e) {
            throw new BadRequestHttpException('Processing failure');
        }
    }
}
