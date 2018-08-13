<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Controller\Api;

use Damax\Common\Bridge\Symfony\Bundle\Annotation\Deserialize;
use Damax\Common\Bridge\Symfony\Bundle\Annotation\Serialize;
use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Dto\MediaCreationDto;
use Damax\Media\Application\Dto\MediaDto;
use Damax\Media\Application\Dto\UploadDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaUploadFailure;
use Damax\Media\Application\Service\FactoryService;
use Damax\Media\Application\Service\MediaService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swagger\Annotations as OpenApi;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/media")
 */
class MediaController
{
    private $service;

    public function __construct(MediaService $service)
    {
        $this->service = $service;
    }

    /**
     * @OpenApi\Post(
     *     tags={"media"},
     *     summary="Create media.",
     *     security={
     *         {"Bearer"=""}
     *     },
     *     @OpenApi\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @OpenApi\Schema(ref=@Model(type=MediaCreationDto::class))
     *     ),
     *     @OpenApi\Response(
     *         response=201,
     *         description="Media info.",
     *         @OpenApi\Schema(ref=@Model(type=MediaDto::class))
     *     )
     * )
     *
     * @Method("POST")
     * @Route("")
     * @Serialize()
     * @Deserialize(MediaCreationDto::class, validate=true, param="media")
     */
    public function createAction(FactoryService $service, MediaCreationDto $media): MediaDto
    {
        $command = new CreateMedia();
        $command->media = $media;

        return $service->createMedia($command);
    }

    /**
     * @OpenApi\Put(
     *     tags={"media"},
     *     summary="Upload media.",
     *     security={
     *         {"Bearer"=""}
     *     },
     *     consumes={"application/octet-stream", "application/pdf", "image/jpg", "image/png"},
     *     @OpenApi\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @OpenApi\Schema(type="string", format="binary")
     *     ),
     *     @OpenApi\Response(
     *         response=200,
     *         description="Media info.",
     *         @OpenApi\Schema(ref=@Model(type=MediaDto::class))
     *     ),
     *     @OpenApi\Response(
     *         response=404,
     *         description="Media not found."
     *     ),
     *     @OpenApi\Response(
     *         response=400,
     *         description="Upload failure."
     *     )
     * )
     *
     * @Method("PUT")
     * @Route("/{id}/upload")
     * @Serialize()
     *
     * @return MediaDto|ConstraintViolationListInterface
     *
     * @throws LengthRequiredHttpException
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function uploadAction(Request $request, string $id, ValidatorInterface $validator)
    {
        if (!($length = $request->headers->get('Content-Length'))) {
            throw new LengthRequiredHttpException();
        }

        $upload = new UploadDto();
        $upload->stream = fopen('php://temp', 'wb');
        $upload->mimeType = $request->headers->get('Content-Type');
        $upload->size = (int) $length;

        stream_copy_to_stream($request->getContent(true), $upload->stream);

        if (count($violations = $validator->validate($upload))) {
            return $violations;
        }

        $command = new UploadMedia();
        $command->mediaId = $id;
        $command->upload = $upload;

        try {
            return $this->service->upload($command);
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        } catch (MediaUploadFailure $e) {
            throw new BadRequestHttpException('Upload failure.');
        }
    }

    /**
     * @OpenApi\Get(
     *     tags={"media"},
     *     summary="Get media.",
     *     security={
     *         {"Bearer"=""}
     *     },
     *     @OpenApi\Response(
     *         response=200,
     *         description="Media info.",
     *         @OpenApi\Schema(ref=@Model(type=MediaDto::class))
     *     ),
     *     @OpenApi\Response(
     *         response=404,
     *         description="Media not found."
     *     )
     * )
     *
     * @Method("GET")
     * @Route("/{id}")
     * @Serialize()
     *
     * @throws NotFoundHttpException
     */
    public function getAction(string $id): MediaDto
    {
        try {
            return $this->service->fetch($id);
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @OpenApi\Delete(
     *     tags={"media"},
     *     summary="Delete media.",
     *     security={
     *         {"Bearer"=""}
     *     },
     *     @OpenApi\Response(
     *         response=204,
     *         description="Media deleted."
     *     ),
     *     @OpenApi\Response(
     *         response=404,
     *         description="Media not found."
     *     )
     * )
     *
     * @Method("DELETE")
     * @Route("/{id}")
     *
     * @throws NotFoundHttpException
     */
    public function deleteAction(string $id): Response
    {
        try {
            $this->service->delete($id);
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        }

        return Response::create('', Response::HTTP_NO_CONTENT);
    }
}
