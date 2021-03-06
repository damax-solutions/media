<?php

declare(strict_types=1);

namespace Damax\Media\Bridge\Symfony\Bundle\Controller\Api;

use Damax\Common\Bridge\Symfony\Bundle\Annotation\Deserialize;
use Damax\Media\Application\Command\CreateMedia;
use Damax\Media\Application\Command\DeleteMedia;
use Damax\Media\Application\Command\UploadMedia;
use Damax\Media\Application\Dto\NewMediaDto;
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
     *         @OpenApi\Schema(ref=@Model(type=NewMediaDto::class))
     *     ),
     *     @OpenApi\Response(
     *         response=202,
     *         description="Request accepted.",
     *         @OpenApi\Header(
     *             header="Location",
     *             type="string",
     *             description="New media resource."
     *         )
     *     )
     * )
     *
     * @Route("", methods={"POST"})
     * @Deserialize(NewMediaDto::class, validate=true, param="media")
     */
    public function create(IdGenerator $idGenerator, NewMediaDto $media): Response
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
     *         description="Request accepted.",
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
     *
     * @return Response|ConstraintViolationListInterface
     *
     * @throws LengthRequiredHttpException
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function upload(Request $request, string $id, ValidatorInterface $validator)
    {
        if (!($length = $request->headers->get('Content-Length'))) {
            throw new LengthRequiredHttpException();
        }

        $upload = new UploadDto();
        $upload->stream = $request->getContent(true);
        $upload->mimeType = $request->headers->get('Content-Type');
        $upload->fileSize = (int) $length;

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
    public function delete(string $id): Response
    {
        try {
            $this->commandBus->handle(new DeleteMedia($id));
        } catch (MediaNotFound $e) {
            throw new NotFoundHttpException();
        }

        return Response::create('', Response::HTTP_NO_CONTENT);
    }
}
