<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Controller\Standard;

use Damax\Media\Application\Exception\ImageProcessingFailure;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Query\DownloadMedia;
use Damax\Media\Application\Query\GetImage;
use League\Tactician\CommandBus as QueryBus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/media")
 */
final class DownloadController
{
    private $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * @Route("/{id}/download", methods={"GET"}, name="media_download")
     *
     * @throws NotFoundHttpException
     */
    public function downloadAction(string $id): Response
    {
        try {
            return $this->queryBus->handle(new DownloadMedia($id));
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/{id}/image", methods={"GET"}, name="media_image")
     *
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function imageAction(Request $request, string $id): Response
    {
        try {
            return $this->queryBus->handle(new GetImage($id, $request->query->all()));
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        } catch (ImageProcessingFailure $e) {
            throw new BadRequestHttpException();
        }
    }
}
