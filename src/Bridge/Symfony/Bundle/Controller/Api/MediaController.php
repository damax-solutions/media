<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Controller\Api;

use Damax\Common\Bridge\Symfony\Bundle\Annotation\Deserialize;
use Damax\Common\Bridge\Symfony\Bundle\Annotation\Serialize;
use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Command\DeleteMedia;
use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Dto\MediaCreationDto;
use Damax\Media\Application\Dto\UploadDto;
use Damax\Media\Application\Exception\MediaNotFound;
use Damax\Media\Application\Exception\MediaUploadFailure;
use Damax\Media\Domain\Model\IdGenerator;
use Nelmio\ApiDocBundle\Annotation\Model;
use SimpleBus\Message\Bus\MessageBus;
use Swagger\Annotations as OpenApi;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/media")
 */
final class MediaController
{
    private $commandBus;
    private $urlGenerator;

    public function __construct(MessageBus $commandBus, UrlGeneratorInterface $urlGenerator)
    {
        $this->commandBus = $commandBus;
        $this->urlGenerator = $urlGenerator;
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
     *         response=202,
     *         description="Request result.",
     *         @OpenApi\Header(
     *             header="Location",
     *             type="string",
     *             description="New media resource."
     *         )
     *     )
     * )
     *
     * @Route("", methods={"POST"})
     * @Serialize()
     * @Deserialize(MediaCreationDto::class, validate=true, param="media")
     */
    public function createAction(IdGenerator $idGenerator, MediaCreationDto $media): Response
    {
        // Violation of DDD?
        $mediaId = (string) $idGenerator->mediaId();

        $this->commandBus->handle(new CreateMedia($mediaId, $media));

        $resource = $this->urlGenerator->generate('media_view', ['id' => $mediaId]);

        return Response::create('', Response::HTTP_ACCEPTED, ['location' => $resource]);
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
     *         response=202,
     *         description="Request result.",
     *         @OpenApi\Header(
     *             header="Location",
     *             type="string",
     *             description="Media resource."
     *         )
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
     * @Route("/{id}/upload", methods={"PUT"})
     * @Serialize()
     *
     * @return Response|ConstraintViolationListInterface
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
        $upload->fileSize = (int) $length;

        stream_copy_to_stream($request->getContent(true), $upload->stream);

        if (count($violations = $validator->validate($upload))) {
            return $violations;
        }

        try {
            $this->commandBus->handle(new UploadMedia($id, $upload));
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        } catch (MediaUploadFailure $e) {
            throw new BadRequestHttpException();
        }

        $resource = $this->urlGenerator->generate('media_view', ['id' => $id]);

        return Response::create('', Response::HTTP_ACCEPTED, ['location' => $resource]);
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
     * @Route("/{id}", methods={"DELETE"})
     *
     * @throws NotFoundHttpException
     */
    public function deleteAction(string $id): Response
    {
        try {
            $this->commandBus->handle(new DeleteMedia($id));
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        }

        return Response::create('', Response::HTTP_NO_CONTENT);
    }
}
